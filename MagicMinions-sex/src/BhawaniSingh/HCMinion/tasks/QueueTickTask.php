<?php

declare(strict_types=1);

namespace BhawaniSingh\HCMinion\tasks;

use pocketmine\block\Air;
use pocketmine\scheduler\Task;
use pocketmine\world\format\Chunk;
use BhawaniSingh\HCMinion\BetterMinion;
use BhawaniSingh\HCMinion\entities\MinionEntity;
use pocketmine\network\mcpe\protocol\AnimatePacket;
use pocketmine\network\mcpe\protocol\LevelEventPacket;
use pocketmine\network\mcpe\protocol\types\LevelEvent;

class QueueTickTask extends Task
{
    public function onRun(): void
    {
        $i = 0;
        /** @var MinionEntity $entity */
        foreach (BetterMinion::$minionQueue as $queueNumber => $entity) {
            if ($i > BetterMinion::QUEUE_CYCLE) {
                break;
            }

            if (!$entity->isClosed() && !$entity->isFlaggedForDespawn()) {
                if ($entity->ticksLived % 60 === 0) {
                    $entity->updateTarget();
                }
                if ($entity->isInventoryFull()) {
                    unset(BetterMinion::$minionQueue[$queueNumber]);
                    $i++;
                    continue;
                }

                if (!$entity->isWorking) {
                    $entity->getTarget();
                    $entity->isWorking = true;
                }
                $world = $entity->getPosition()->getWorld();

                $world->requestChunkPopulation($entity->target->getPosition()->getX() >> Chunk::COORD_BIT_SIZE, $entity->target->getPosition()->getZ() >> Chunk::COORD_BIT_SIZE, null);
                if (!$entity->checkTarget()) {
                    $entity->stopWorking();

                    unset(BetterMinion::$minionQueue[$queueNumber]);
                    $i++;
                    continue;
                }

                $entity->setNameTag("§l§6" . strtoupper($entity->getMinionInformation()->getType()->getTargetName()) . "§r\n§e" . $entity->getMinionInformation()->getOwner() . "'s Minion §r(" . $entity->currentAction . "§r)");
                switch ($entity->currentAction) {
                    case MinionEntity::ACTION_INVENTORY_FULL:
                        $entity->setNameTagAlwaysVisible(true);
                        if (!$entity->isInventoryFull()) {
                            $entity->currentAction = MinionEntity::ACTION_IDLE;
                        }
                        break;
                    case MinionEntity::ACTION_IDLE:
                        $entity->setNameTagAlwaysVisible(false);
                        $entity->currentAction = MinionEntity::ACTION_TURNING;
                        break;
                    case MinionEntity::ACTION_TURNING:
                        $entity->setNameTagAlwaysVisible(false);
                        $entity->lookAt($entity->target->getPosition());
                        $entity->currentAction = MinionEntity::ACTION_WORKING;
                        break;
                    case MinionEntity::ACTION_WORKING:
                        $isPlacing = $entity->target instanceof Air;

                        $entity->setNameTagAlwaysVisible(false);
                        if (!$isPlacing) {
                            if ($entity->broadcastPlaceBreak()) {
                                $world->broadcastPacketToViewers($entity->target->getPosition(), LevelEventPacket::create(LevelEvent::BLOCK_START_BREAK, (int) (65535 / 60), $entity->target->getPosition()));
                            }
                            if ($entity->isWorkFast()) {
                                $entity->startWorking();
                            }
                            $pk = new AnimatePacket();
                            $pk->action = AnimatePacket::ACTION_SWING_ARM;
                            $pk->actorRuntimeId = $entity->getId();
                            if ($entity->broadcastPlaceBreak()) {
                                $world->broadcastPacketToViewers($entity->getPosition(), $pk);
                            }
                        } elseif ($entity->broadcastPlaceBreak()) {
                            $world->broadcastPacketToViewers($entity->target->getPosition(), LevelEventPacket::create(LevelEvent::BLOCK_STOP_BREAK, 0, $entity->target->getPosition()));
                        }
                        $entity->startWorking();
                        $entity->stopWorking();
                        break;
                }
                $i++;
            }
            unset(BetterMinion::$minionQueue[$queueNumber]);
        }
    }
}
