<?php

namespace Quest;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use Quest\commands\QuestCommand;
use Quest\listener\QuestListener;
use Quest\providers\SQLiteProvider;

class Quest extends PluginBase
{
    private static Quest $instance;

    private SQLiteProvider $provider;
    private Config $quest;

    public function onLoad(): void
    {
        self::$instance = $this;
    }

    public function onEnable(): void
    {
        $this->getServer()->getPluginManager()->registerEvents(new QuestListener(),$this);

        $this->getServer()->getCommandMap()->register("quest", new QuestCommand());

        $this->provider = new SQLiteProvider();

        $this->saveResource("quest.yml");

        $this->quest = new Config($this->getDataFolder()."quest.yml",Config::YAML);
    }

    public function getProvider(): SQLiteProvider
    {
        return $this->provider;
    }

    public static function getInstance(): Quest
    {
        return self::$instance;
    }

    public function getQuest(): Config
    {
        return $this->quest;
    }
}