<?php

namespace Stats\task;

use Stats\Main;
use Stats\player\MagicPlayer;
use pocketmine\scheduler\Task;

class ActionbarTask extends Task
{
    public function onRun(): void
    {
        foreach (Main::getInstance()->getServer()->getOnlinePlayers() as $player) {
            if ($player instanceof MagicPlayer) {
                $item = $player->getInventory()->getItemInHand();
                $damage = $player->getDamage() + $item->getAttackPoints();
                $defense = $player->getDefense() + $player->getArmorPoints();
                $heal = $player->getHealth();
                $maxheal = $player->getMaxHealth();

                if ($heal > $maxheal) {
                    $player->setHealth($maxheal);
                }
                $player->sendActionBarMessage("§cHealth: $heal" . "§7/§c$maxheal  §aDefense: §a$defense \n§4Damage: $damage  §bMana: 100 ");
            }
        }
    }
}
