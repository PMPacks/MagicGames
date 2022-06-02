<?php

namespace blueturk\skyblock\forms\island;

use dktapps\pmforms\MenuForm;
use pocketmine\player\Player;
use blueturk\skyblock\SkyBlock;
use dktapps\pmforms\MenuOption;
use pocketmine\network\mcpe\protocol\LevelEventPacket;
use pocketmine\network\mcpe\protocol\types\LevelEvent;

class WeatherSettingsForm extends MenuForm
{
    public function __construct()
    {
        parent::__construct(
            SkyBlock::BT_TITLE . "Weather forecast",
            "",
            [
                new MenuOption("Rainy"),
                new MenuOption("Thunder"),
                new MenuOption("Night"),
                new MenuOption("Day")
            ],
            function (Player $player, int $option): void {
                if ($player->getWorld()->getFolderName() === $player->getName()) {
                    switch ($option) {
                        case 0:
                            if (isset(SkyBlock::$weathers[$player->getName()])) {
                                $player->sendMessage(SkyBlock::BT_MARK . "cYou cannot change it until the server is restarted!");
                                return;
                            }
                            SkyBlock::$weathers[$player->getName()] = "rain";
                            $packet = new LevelEventPacket();
                            $packet->eventId = LevelEvent::START_RAIN;
                            $packet->position = null;
                            $packet->eventData = 10000;
                            $player->getNetworkSession()->sendDataPacket($packet);
                            $player->sendMessage(SkyBlock::BT_MARK . "bWeather set to rainy!");
                            break;
                        case 1:
                            if (isset(SkyBlock::$weathers[$player->getName()])) {
                                $player->sendMessage(SkyBlock::BT_MARK . "cYou cannot change it until the server is restarted!");
                                return;
                            }
                            SkyBlock::$weathers[$player->getName()] = "thunder";
                            $packet = new LevelEventPacket();
                            $packet->eventId = LevelEvent::START_THUNDER;
                            $packet->position = null;
                            $packet->eventData = 10000;
                            $player->getNetworkSession()->sendDataPacket($packet);
                            $player->sendMessage(SkyBlock::BT_MARK . "bWeather set to lightning!");
                            break;
                        case 2:
                            $player->getWorld()->setTime(13000);
                            $player->sendMessage(SkyBlock::BT_MARK . "bWeather set to night!");
                            break;
                        case 3:
                            $player->getWorld()->setTime(1000);
                            $player->sendMessage(SkyBlock::BT_MARK . "bWeather set to day!");
                            break;
                    }
                } else {
                    $player->sendMessage(SkyBlock::BT_MARK . "cYou must be on the island to use this feature!");
                }
            }
        );
    }
}
