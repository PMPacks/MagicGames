<?php

namespace blueturk\skyblock\managers;

use pocketmine\Server;
use pocketmine\world\World;
use pocketmine\player\Player;
use blueturk\skyblock\SkyBlock;
use BhawaniSingh\HCMinion\BetterMinion;
use BhawaniSingh\HCMinion\entities\MinionEntity;
use blueturk\skyblock\forms\island\partner\PartnerRequestForm;

class IslandManager
{
    public static function islandVisit(Player $player, string $selectedPlayer): void
    {
        $selectedPlayer = Server::getInstance()->getPlayerExact($selectedPlayer);
        if (!$selectedPlayer instanceof Player) {
            $player->sendMessage(SkyBlock::BT_MARK . "cThe player is not active, you cannot visit!");
            return;
        }
        if (SkyBlock::getInstance()->getConfig()->getNested($selectedPlayer->getName() . "." . "island") === null) {
            $player->sendMessage(SkyBlock::BT_MARK . "cThe player don't have an island");
            return;
        }
        if (SkyBlock::getInstance()->getConfig()->getNested("Visits." . $selectedPlayer->getName()) === false){
            $player->sendMessage(SkyBlock::BT_MARK . "cThe player island is locked");
            return;
        }
        if (!Server::getInstance()->getWorldManager()->isWorldLoaded($selectedPlayer->getName())) Server::getInstance()->getWorldManager()->loadWorld($selectedPlayer->getName());
        $world = Server::getInstance()->getWorldManager()->getWorldByName($selectedPlayer->getName());
        if (!$world instanceof World) {
            return;
        }

        $player->teleport($world->getSpawnLocation());
        $player->sendMessage(SkyBlock::BT_MARK . "bYou visited the island!");
        $selectedPlayer->sendMessage(SkyBlock::BT_MARK . "b" . $player->getName() . " The player visited the island!");
        return;
    }

    public static function partnerRemove(Player $player, string $selectedPlayer): void
    {
        $array = SkyBlock::getInstance()->getConfig()->getNested($player->getName() . ".island" . ".this-partners");
        $array2 = SkyBlock::getInstance()->getConfig()->getNested($selectedPlayer . ".island" . ".other-partners");
        if ($array !== null) {
            $arraySearch = array_search($selectedPlayer, $array);
            if ($arraySearch !== false) {
                unset($array[$arraySearch]);
            }
        }
        if ($array2 !== null) {
            $arraySearch2 = array_search($player->getName(), $array2);
            if ($arraySearch2 !== false) {
                unset($array2[$arraySearch2]);
            }
        }

        SkyBlock::getInstance()->getConfig()->setNested($player->getName() . ".island" . ".this-partners", $array);
        SkyBlock::getInstance()->getConfig()->setNested($selectedPlayer . ".island" . ".other-partners", $array2);
        $player->sendMessage(SkyBlock::BT_MARK . "bYou unaffiliated the player!");
        $selectedPlayer = Server::getInstance()->getPlayerExact($selectedPlayer);
        if ($selectedPlayer instanceof Player) {
            $selectedPlayer->sendMessage(SkyBlock::BT_MARK . "b" . $player->getName() . " The player removed you from the partnership!");
        }
    }

    public static function partnerRequestConfirm(Player $player, string $requestPlayer): void
    {
        $requestPlayer = Server::getInstance()->getPlayerExact($requestPlayer);
        if ($requestPlayer instanceof Player) {
            $array = SkyBlock::getInstance()->getConfig()->getNested($requestPlayer->getName() . ".island" . ".this-partners");
            if (!is_array($array)) {
                $array = [];
            }

            if (!in_array($player->getName(), $array)) {
                array_push($array, $player->getName());
                SkyBlock::getInstance()->getConfig()->setNested($requestPlayer->getName() . ".island" . ".this-partners", $array);
                if (SkyBlock::getInstance()->getConfig()->getNested($player->getName() . ".island") != null) {
                    $array1 = SkyBlock::getInstance()->getConfig()->getNested($player->getName() . ".island" . ".other-partners");
                    if (is_array($array1)) {
                        array_push($array1, $requestPlayer->getName());
                        SkyBlock::getInstance()->getConfig()->setNested($player->getName() . ".island" . ".other-partners", $array1);
                    }
                } else {
                    $array1 = SkyBlock::getInstance()->getConfig()->getNested($player->getName() . ".partners");
                    if (is_array($array1)) {
                        array_push($array1, $requestPlayer->getName());
                        SkyBlock::getInstance()->getConfig()->setNested($player->getName() . ".partners", $array1);
                    }
                }
                $player->sendMessage(SkyBlock::BT_MARK . "bYou accepted the partnership offer!");
                $requestPlayer->sendMessage(SkyBlock::BT_MARK . "bPartnership accepted your offer!");
                return;
            }
            $requestPlayer->sendMessage(SkyBlock::BT_MARK . "cThis player is already your partner!");
            return;
        }
        $player->sendMessage(SkyBlock::BT_MARK . "cThe player is not active!");
    }

    public static function partnerRequest(Player $player, string $selectedPlayer): void
    {
        $selectedPlayer = Server::getInstance()->getPlayerExact($selectedPlayer);
        if ($selectedPlayer instanceof Player) {
            if ($selectedPlayer->getName() === $player->getName()) {
                $player->sendMessage(SkyBlock::BT_MARK . "cYou cannot add yourself as a partner!");
                return;
            }
            $array = SkyBlock::getInstance()->getConfig()->getNested($player->getName() . ".island" . ".this-partners");
            if (!is_array($array)) {
                $array = [];
            }

            if (in_array($selectedPlayer->getName(), $array)) {
                $player->sendMessage(SkyBlock::BT_MARK . "cThis player is already your partner!");
                return;
            }
            $selectedPlayer->sendForm(new PartnerRequestForm($player));
            $player->sendMessage(SkyBlock::BT_MARK . "b" . $selectedPlayer->getName() . " Partnership request has been sent to player!");
            return;
        }
        $player->sendMessage(SkyBlock::BT_MARK . "cThe player is not active!");
    }

    public static function islandUnBanPlayer(Player $player, string $selectedPlayer): void
    {
        $array = SkyBlock::getInstance()->getConfig()->getNested($player->getName() . ".island" . ".banneds");
        if (!is_array($array)) {
            $array = [];
        }

        unset($array[array_search($selectedPlayer, $array)]);
        SkyBlock::getInstance()->getConfig()->setNested($player->getName() . ".island" . ".banneds", $array);
        $player->sendMessage(SkyBlock::BT_MARK . "bYou've unbanned the player!");
    }

    public static function islandBanPlayer(Player $player, string $selectedPlayer): void
    {
        $selectedPlayer = Server::getInstance()->getPlayerExact($selectedPlayer);
        if ($selectedPlayer instanceof Player) {
            $defaultWorld = Server::getInstance()->getWorldManager()->getDefaultWorld();
            if (!$defaultWorld instanceof World) {
                return;
            }
            $array = SkyBlock::getInstance()->getConfig()->getNested($player->getName() . ".island" . ".banneds");
            if (!is_array($array)) {
                $array = [];
            }

            array_push($array, $selectedPlayer->getName());
            SkyBlock::getInstance()->getConfig()->setNested($player->getName() . ".island" . ".banneds", $array);
            $selectedPlayer->teleport($defaultWorld->getSpawnLocation());
            $selectedPlayer->sendMessage(SkyBlock::BT_MARK . "cYou are banned from the island!");
            $player->sendMessage(SkyBlock::BT_MARK . "bYou banned the player!");
            return;
        }
        $player->sendMessage(SkyBlock::BT_MARK . "cThe player is not active!");
    }

    public static function islandKickPlayer(Player $player, string $selectedPlayer): void
    {
        $selectedPlayer = Server::getInstance()->getPlayerExact($selectedPlayer);
        if ($selectedPlayer instanceof Player) {
            if ($selectedPlayer->getName() === $player->getName()) {
                $player->sendMessage(SkyBlock::BT_MARK . "bYou can't clean yourself!");
                return;
            }
            $defaultWorld = Server::getInstance()->getWorldManager()->getDefaultWorld();
            if (!$defaultWorld instanceof World) {
                return;
            }

            $selectedPlayer->teleport($defaultWorld->getSpawnLocation());
            $selectedPlayer->sendMessage(SkyBlock::BT_MARK . "cYou've been kicked off the island!");
            $player->sendMessage(SkyBlock::BT_MARK . "bPlayer kicked!");
        } else {
            $player->sendMessage(SkyBlock::BT_MARK . "cThe player is not active!");
        }
    }

    public static function teleportPartnerIsland(Player $player, string $selectedPlayer): void
    {
        $status = SkyBlock::getInstance()->getConfig()->getNested($selectedPlayer . ".island" . ".settings" . ".de-active-teleport");
        switch ($status) {
            case true:
                if (!Server::getInstance()->getWorldManager()->isWorldLoaded($selectedPlayer)) Server::getInstance()->getWorldManager()->loadWorld($selectedPlayer);
                $world = Server::getInstance()->getWorldManager()->getWorldByName($selectedPlayer);
                if (!$world instanceof World) {
                    break;
                }

                $player->teleport($world->getSpawnLocation());
                $player->sendMessage(SkyBlock::BT_MARK . "bTeleported to partner island!");
                break;
            case false:
                $player->sendMessage(SkyBlock::BT_MARK . "cYou cannot teleport to your island while your partner is inactive!");
                break;
            default:
                $player->sendMessage(SkyBlock::BT_MARK . "cYour partner has deleted their island!");
                break;
        }
    }

    public static function changePartnerSettings(Player $player, bool $interact, bool $place, bool $break, bool $pickingUp, bool $deActiveTeleport): void
    {
        SkyBlock::getInstance()->getConfig()->setNested($player->getName() . ".island" . ".settings" . ".interact", $interact);
        SkyBlock::getInstance()->getConfig()->setNested($player->getName() . ".island" . ".settings" . ".place", $place);
        SkyBlock::getInstance()->getConfig()->setNested($player->getName() . ".island" . ".settings" . ".break", $break);
        SkyBlock::getInstance()->getConfig()->setNested($player->getName() . ".island" . ".settings" . ".picking-up", $pickingUp);
        SkyBlock::getInstance()->getConfig()->setNested($player->getName() . ".island" . ".settings" . ".de-active-teleport", $deActiveTeleport);
        $player->sendMessage(SkyBlock::BT_MARK . "bPartner settings saved!");
    }

    public static function teleportToIsland(Player $player): void
    {
        if (!Server::getInstance()->getWorldManager()->isWorldLoaded($player->getName())) Server::getInstance()->getWorldManager()->loadWorld($player->getName());
        $world = Server::getInstance()->getWorldManager()->getWorldByName($player->getName());
        if (!$world instanceof World) {
            return;
        }

        $player->teleport($world->getSpawnLocation());
        $player->sendMessage(SkyBlock::BT_MARK . "bYou've been teleported to your island!");
    }

    public static function setIslandSpawnLocation(Player $player): void
    {
        if ($player->getWorld()->getFolderName() === $player->getName()) {
            $player->getWorld()->setSpawnLocation($player->getPosition()->asVector3());
            $player->sendMessage(SkyBlock::BT_MARK . "bIsland center set!!");
            return;
        }
        $player->sendMessage(SkyBlock::BT_MARK . "cYou can only do this on your island!");
    }

    public static function changeIslandVisit(Player $player, bool $status): void
    {
        switch ($status) {
            case true:
                SkyBlock::getInstance()->getConfig()->setNested("Visits." . $player->getName(), false);
                $player->sendMessage(SkyBlock::BT_MARK . "bVisit set to closed!");
                break;
            case false:
                SkyBlock::getInstance()->getConfig()->setNested("Visits." . $player->getName(), true);
                $player->sendMessage(SkyBlock::BT_MARK . "bVisit is set to open!");
                break;
            default:
                $player->sendMessage(SkyBlock::BT_MARK . "cAn unknown error has occurred, report it to the authorized team!");
                break;
        }
    }

    public static function islandCreate(Player $player, string $islandType): void
    {
        //Copy Island Word
        $dataPath = SkyBlock::getInstance()->getServer()->getDataPath();
        if (is_dir($dataPath . $islandType)) {
            @mkdir($dataPath . "worlds/" . $player->getName() . "/");
            @mkdir($dataPath . "worlds/" . $player->getName() . "/db/");
            $world = opendir(SkyBlock::getInstance()->getServer()->getDataPath() . $islandType . "/db/");
            if (!is_resource($world)) {
                return;
            }

            while ($file = readdir($world)) {
                if ($file != "." && $file != "..") {
                    copy($dataPath . $islandType . "/db/" . $file, $dataPath . "worlds/" . $player->getName() . "/db/" . $file);
                }
            }
            copy($dataPath . $islandType . "/level.dat", $dataPath . "worlds/" . $player->getName() . "/level.dat");

            //Create YAML Data
            $data = SkyBlock::getInstance()->getConfig();
            $deleteTime = $data->getNested($player->getName() . ".delete-time");
            $partners = $data->getNested($player->getName() . ".partners");
            if ($partners === null) $partners = [];
            if ($deleteTime === null) $deleteTime = null;
            $data->setNested($player->getName() . ".island", [
                "settings" => [
                    "interact" => false,
                    "place" => false,
                    "break" => false,
                    "picking-up" => false,
                    "de-active-teleport" => false,
                    "delete-time" => $deleteTime
                ],
                "banneds" => [],
                "this-partners" => [],
                "other-partners" => $partners
            ]);
            $data->setNested("Visits." . $player->getName(), false);

            //Teleporting
            Server::getInstance()->getWorldManager()->loadWorld($player->getName());
            $world = Server::getInstance()->getWorldManager()->getWorldByName($player->getName());
            if (!$world instanceof World) {
                return;
            }

            $player->teleport($world->getSpawnLocation());
            $player->getWorld()->requestChunkPopulation($player->getPosition()->getFloorX() >> 4, $player->getPosition()->getFloorZ() >> 4, null);
            $player->sendMessage(SkyBlock::BT_MARK . "bYour island has been created, you are being teleported!");
        }
    }

    public static function islandRemove(Player $player): void
    {
        if (SkyBlock::getInstance()->getConfig()->getNested($player->getName() . ".delete-time") === null) {
            $deleteTime = null;
        } else {
            $deleteTime = SkyBlock::getInstance()->getConfig()->getNested($player->getName())["delete-time"];
        }

        if ($deleteTime === null || time() > (int)$deleteTime) {
            self::islandDataDelete($player);
            return;
        }
        $deleteTime = $deleteTime - time();
        $day = floor($deleteTime / 86400);
        $hourSecond = $deleteTime % 86400;
        $hour = floor($hourSecond / 3600);
        $minuteHour = $hourSecond % 3600;
        $minute = floor($minuteHour / 60);
        $player->sendMessage(SkyBlock::BT_MARK . "fYou have to wait §6" . $day . " §fday, §6" . $hour . " §fhour, §6" . $minute . " §fTo be able to delete your island!");
    }

    public static function islandDataDelete(Player $player): void
    {
        $world = Server::getInstance()->getWorldManager()->getWorldByName($player->getName());
        if (!$world instanceof World) {
            return;
        }

        foreach ($world->getPlayers() as $islandPlayer) {
            $defaultWorld = Server::getInstance()->getWorldManager()->getDefaultWorld();
            if (!$defaultWorld instanceof World) {
                return;
            }

            $islandPlayer->teleport($defaultWorld->getSpawnLocation());
            $islandPlayer->sendMessage(SkyBlock::BT_MARK . "bThe island you are on is being deleted..");
        }

        $old = SkyBlock::getInstance()->getConfig()->getNested($player->getName() . ".island" . ".this-partners");
        if ($old != null) {
            foreach ($old as $value) {
                $array = SkyBlock::getInstance()->getConfig()->getNested($value . ".island" . ".other-partners");
                if ($array != null) {
                    $arraySearch = array_search($player->getName(), $array);
                    if ($arraySearch !== false) {
                        unset($array[$arraySearch]);
                    }
                    SkyBlock::getInstance()->getConfig()->setNested($value . ".island" . ".other-partners", $array);
                }
            }
        }
        $old2 = SkyBlock::getInstance()->getConfig()->getNested($player->getName() . ".island" . ".other-partners");
        if ($old2 != null) {
            foreach ($old2 as $value) {
                $array = SkyBlock::getInstance()->getConfig()->getNested($value . ".island" . ".this-partners");
                if ($array != null) {
                    $arraySearch3 = array_search($player->getName(), $array);
                    if ($arraySearch3 !== false) {
                        unset($array[$arraySearch3]);
                    }
                    SkyBlock::getInstance()->getConfig()->setNested($value . ".island" . ".this-partners", $array);
                }
            }
        }
        $worldName = SkyBlock::getInstance()->getServer()->getDataPath() . "/worlds/" . $player->getName();

        Server::getInstance()->getWorldManager()->unloadWorld($world);
        self::worldDelete($worldName);

        SkyBlock::getInstance()->getConfig()->removeNested($player->getName() . ".island");
        SkyBlock::getInstance()->getConfig()->removeNested("Visits." . $player->getName());
        SkyBlock::getInstance()->getConfig()->setNested($player->getName() . ".delete-time", (time() + 7 * 86400));

        $minionData = BetterMinion::getInstance()->getProvider()->getMinionDataFromPlayer($player->getName());
        $minionAmount = 0;
        foreach ($world->getEntities() as $entity) {
            if ($entity instanceof MinionEntity && $entity->getMinionInformation()->getOwner() === $player->getName()) {
                $minionAmount++;
            }
        }

        if ($minionAmount > 0) {
            BetterMinion::getInstance()->getProvider()->updateMinionData($player->getName(), $minionData["minionCount"] - $minionAmount);
        }

        $player->sendMessage(SkyBlock::BT_MARK . "bYou have successfully deleted your island!");
    }

    public static function worldDelete(string $world): int
    {
        $file = 1;
        if (basename($world) == "." || basename($world) == "..") {
            return 0;
        }
        $scanDir = scandir($world);
        if (!$scanDir) {
            return 0;
        }

        foreach ($scanDir as $item) {
            if ($item != "."/* || $item != ".."*/) {
                if (is_dir($world . DIRECTORY_SEPARATOR . $item)) {
                    $file += self::worldDelete($world . DIRECTORY_SEPARATOR . $item);
                }
                if (is_file($world . DIRECTORY_SEPARATOR . $item)) {
                    $file += unlink($world . DIRECTORY_SEPARATOR . $item);
                }
            }
        }
        rmdir($world);
        return $file;
    }
}
