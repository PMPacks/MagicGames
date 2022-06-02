<?php

namespace blueturk\skyblock;

use pocketmine\plugin\PluginBase;
use blueturk\skyblock\listener\IslandListener;
use blueturk\skyblock\managers\CommandManager;

class SkyBlock extends PluginBase
{
    protected static SkyBlock $api;

    public const BT_TITLE = "§l§6SKYBLOCK§r §l§a»§r §e";
    public const BT_MARK = "§d» §";

    public static array $weathers = [];

    public static function getInstance(): SkyBlock
    {
        return self::$api;
    }

    public function onEnable(): void
    {
        self::$api = $this;
        CommandManager::loadCommands();
        self::$api->getServer()->getPluginManager()->registerEvents(new IslandListener(), self::$api);
        $this->getLogger()->notice(sprintf("%s commands loaded!", CommandManager::commandsCount()));
    }

    public function onDisable(): void
    {
        self::$api->saveConfig();
    }
}
