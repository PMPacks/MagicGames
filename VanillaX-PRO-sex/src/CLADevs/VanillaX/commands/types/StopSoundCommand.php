<?php

namespace CLADevs\VanillaX\commands\types;

use pocketmine\player\Player;
use pocketmine\command\CommandSender;
use CLADevs\VanillaX\commands\Command;
use CLADevs\VanillaX\commands\utils\CommandArgs;
use CLADevs\VanillaX\commands\utils\CommandOverload;
use pocketmine\network\mcpe\protocol\StopSoundPacket;
use CLADevs\VanillaX\commands\utils\CommandTargetSelector;
use pocketmine\network\mcpe\protocol\types\PlayerPermissions;

class StopSoundCommand extends Command
{

    public function __construct()
    {
        parent::__construct("stopsound", "Stops a sound.");
        $this->setPermission("stopsound.command");
        $this->commandArg = new CommandArgs(PlayerPermissions::MEMBER);

        $overload = new CommandOverload();
        $overload->addTarget("player");
        $overload->addString("sound", false);
        $this->commandArg->addOverload($overload);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if (!isset($args[1])) {
            $this->sendSyntaxError($sender, "", "/$commandLabel");
            return;
        }
        if (!$players = CommandTargetSelector::getFromString($sender, $args[0], true, true, true)) {
            return;
        }
        $sound = $args[1];

        foreach ($players as $player) {
            if ($player instanceof Player) {
                $player->getNetworkSession()->sendDataPacket(StopSoundPacket::create($sound, false));
                $sender->sendMessage("Stopped sound '$sound' for " . $player->getName());
            }
        }
    }
}
