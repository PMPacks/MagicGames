<?php

namespace TitaniumLB\CommandSpy;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerCommandPreprocessEvent;

class EventListener implements Listener
{
    public Main $plugin;

    public function __construct(Main $plugin)
    {
        $this->plugin = $plugin;
    }

    public function getPlugin(): Main
    {
        return $this->plugin;
    }

    public function onPlayerCmd(PlayerCommandPreprocessEvent $event): void
    {
        $player = $event->getPlayer();
        $msg = $event->getMessage();

        if ($msg[0] == "/") {
            $this->getPlugin()->getLogger()->info($player->getName() . "§8: §f" . $msg);
        }
        foreach ($this->getPlugin()->snoopers as $snooper) {
            if ($msg[0] == "/") {
                $snooper->sendMessage("§cSpy-Mode§8» §8:§6" . $player->getName() . "§8: §f" . $msg);
            }
        }
    }

    public function onPlayerQuit(PlayerQuitEvent $event): void
    {
        $player = $event->getPlayer();
        if (isset($this->getPlugin()->snoopers[$player->getUniqueId()->toString()])) {
            unset($this->getPlugin()->snoopers[$player->getUniqueId()->toString()]);
        }
    }
}
