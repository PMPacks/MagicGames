<?php


namespace blueturk\skyblock\managers;

use pocketmine\Server;
use blueturk\skyblock\commands\player\JoinCommand;
use blueturk\skyblock\commands\island\IslandCommand;
use blueturk\skyblock\commands\island\VisitCommand;
use blueturk\skyblock\commands\player\WeatherCommand;

class CommandManager
{
    public static function loadCommands(): void
    {
        foreach (self::getCommands() as $index => $command) {
            Server::getInstance()->getCommandMap()->register($index, $command);
        }
    }

    public static function getCommands(): array
    {
        return [
            "island" => new IslandCommand(),
            "weather" => new WeatherCommand(),
            "join" => new JoinCommand(),
            "visit" => new VisitCommand()
        ];
    }

    public static function commandsCount(): int
    {
        return count(self::getCommands());
    }
}
