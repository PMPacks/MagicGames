<?php

namespace CLADevs\VanillaX\blocks\block;

use Exception;
use pocketmine\Server;
use pocketmine\item\Item;
use pocketmine\block\Block;
use pocketmine\math\Facing;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifier;
use pocketmine\world\BlockTransaction;
use pocketmine\permission\DefaultPermissions;
use CLADevs\VanillaX\blocks\utils\AnyFacingTrait;
use CLADevs\VanillaX\blocks\tile\CommandBlockTile;
use CLADevs\VanillaX\blocks\utils\CommandBlockType;
use CLADevs\VanillaX\utils\item\NonCreativeItemTrait;
use CLADevs\VanillaX\commands\sender\CommandBlockSender;
use pocketmine\network\mcpe\protocol\ContainerOpenPacket;
use pocketmine\network\mcpe\protocol\types\BlockPosition;
use CLADevs\VanillaX\utils\item\NonAutomaticCallItemTrait;
use pocketmine\network\mcpe\protocol\types\inventory\WindowTypes;
use pocketmine\network\mcpe\protocol\types\inventory\ContainerIds;

class CommandBlock extends Block implements NonAutomaticCallItemTrait, NonCreativeItemTrait
{
    use AnyFacingTrait;

    private int $tickDelay = 0;

    public function __construct(CommandBlockType $type, int $meta)
    {
        parent::__construct(new BlockIdentifier($type->getBlockId(), $meta, $type->getBlockId(), CommandBlockTile::class), $type->getDisplayName(), BlockBreakInfo::indestructible());
    }

    public function place(BlockTransaction $tx, Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, ?Player $player = null): bool
    {
        if ($player !== null && $player->hasPermission(DefaultPermissions::ROOT_OPERATOR)) {
            if (in_array($face, [Facing::UP, Facing::DOWN])) {
                $this->facing = $face;
            } else {
                $this->facing = Facing::opposite($player->getHorizontalFacing());
            }
            return parent::place($tx, $item, $blockReplace, $blockClicked, $face, $clickVector, $player);
        }
        return false;
    }

    public function onInteract(Item $item, int $face, Vector3 $clickVector, ?Player $player = null): bool
    {
        if ($player !== null && $player->hasPermission(DefaultPermissions::ROOT_OPERATOR)) {
            $tile = $this->position->getWorld()->getTile($this->position);

            if ($tile instanceof CommandBlockTile) {
                $player->getNetworkSession()->sendDataPacket(
                    ContainerOpenPacket::blockInv(
                        ContainerIds::NONE,
                        WindowTypes::COMMAND_BLOCK,
                        BlockPosition::fromVector3($this->position)
                    )
                );
            }
        }
        return true;
    }

    /**
     * @throws Exception
     */
    public function onScheduledUpdate(): void
    {
        $tile = $this->position->getWorld()->getTile($this->position);

        if (!$tile instanceof CommandBlockTile || $tile->isClosed()) {
            return;
        }
        if ($tile->canRun()) {
            $this->tickDelay++;

            if ($this->tickDelay >= $tile->getTickDelay()) {
                $server = Server::getInstance();
                $sender = new CommandBlockSender($server, $server->getLanguage());
                $server->dispatchCommand($sender, $tile->getCommand());
                $tile->setLastOutput($sender->getOutput());
                $this->clearTickDelay();

                if ($this->getType()->isImpulse()) {
                    $tile->setRanCommand(true);
                } elseif ($this->getType()->isRepeating()) {
                    $this->position->getWorld()->scheduleDelayedBlockUpdate($this->position, 1);
                }
            }
        }
    }

    public function clearTickDelay(): void
    {
        $this->tickDelay = 0;
    }

    public function setTickDelay(int $tickDelay): void
    {
        $this->tickDelay = $tickDelay;
    }

    public function getType(): CommandBlockType
    {
        return CommandBlockType::fromBlock($this);
    }
}
