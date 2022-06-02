<?php

declare(strict_types=1);

namespace AndreasHGK\EasyKits\listener;

use AndreasHGK\EasyKits\manager\KitManager;
use AndreasHGK\EasyKits\utils\TryClaim;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\nbt\tag\StringTag;

class InteractClaimListener implements Listener {

    public function onInteract(PlayerInteractEvent $ev) : void {
        $item = $ev->getItem();
        if($item->getNamedTag()->getTag("ekit") !== null) {
            $item->getNamedTag()->getString("ekit");
            $kitname = $item->getNamedTag()->getString("ekit");
            if(KitManager::exists($kitname)) {
                $ev->cancel();
                $player = $ev->getPlayer();
                $kit = KitManager::get($kitname);

                TryClaim::TryChestClaim($player, $item, $kit);
            }
        }
    }

}