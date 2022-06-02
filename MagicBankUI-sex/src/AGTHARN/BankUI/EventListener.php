<?php

namespace AGTHARN\BankUI;

use AGTHARN\BankUI\bank\Banks;
use pocketmine\event\Listener;
use onebone\economyapi\EconomyAPI;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerInteractEvent;

class EventListener implements Listener
{
    public function onPlayerJoin(PlayerJoinEvent $event): void
    {
        $playerSession = Main::getInstance()->getSessionManager()->getSession($event->getPlayer());
        if (!$playerSession->allowed && $playerSession->money >= Banks::MONEY_LIMIT) {
            $playerSession->handleMessage(" §cWe have detected your bank with a large sum of money and, have flagged and frozen your account!");
            $playerSession->frozen = true;
        }
    }

    public function onPlayerQuit(PlayerQuitEvent $event): void
    {
        Main::getInstance()->getSessionManager()->getSession($event->getPlayer())->remove();
    }

    public function onPlayerInteract(PlayerInteractEvent $event): void
    {
        $player = $event->getPlayer();
        $item = $event->getItem();
        if ($item->getNamedTag()->getTag("Amount") !== null) {
            $amount = $item->getNamedTag()->getFloat("Amount");

            $item->setCount($item->getCount() - 1);
            $player->getInventory()->setItemInHand($item);

            EconomyAPI::getInstance()->addMoney($player, $amount);
            $player->sendMessage(" §7You Have Claimed §e$" . $amount . "§7 Note!");
        }
    }
}
