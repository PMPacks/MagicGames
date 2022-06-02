<?php

declare(strict_types=1);

namespace cosmicnebula200\MagicTags\players;

use Ramsey\Uuid\Uuid;
use pocketmine\Server;
use pocketmine\player\Player;
use cosmicnebula200\MagicTags\MagicTags;
use cosmicnebula200\MagicTags\queries\Queries;
use cosmicnebula200\MagicTags\players\TagPlayer;

class PlayerManager
{
    /** @var TagPlayer[] */
    private array $players = [];

    public function LoadPlayer(Player $player): void
    {
        MagicTags::getInstance()->getDatabase()->executeSelect(
            Queries::LOAD_DB,
            [
                "uuid" => $player->getUniqueId()->toString()
            ],
            function (array $rows) use ($player): void {
                if (count($rows) == 0) {
                    $this->createPlayer($player);
                    return;
                }
                foreach ($rows as $row) {
                    $this->players[$row["name"]] = new TagPlayer($row["uuid"], $row["name"], $row["tags"], $row["currenttag"]);
                }
            }
        );
    }

    public function unloadPlayer(Player $player): void
    {
        unset($this->players[strtolower($player->getName())]);
    }

    public function createPlayer(Player $player): TagPlayer
    {
        MagicTags::getInstance()->getDatabase()->executeInsert(Queries::CREATE_PLAYER, [
            "uuid" => $player->getUniqueId()->toString(),
            "name" => strtolower($player->getName()),
            "tags" => "",
            "currenttag" => ""
        ]);
        $this->players[strtolower($player->getName())] = new TagPlayer($player->getUniqueId()->toString(), strtolower($player->getName()), '', '');
        return $this->players[strtolower($player->getName())];
    }

    public function getPlayer(Player $player): TagPlayer
    {
        return $this->players[strtolower($player->getName())] ?? $this->createPlayer($player);
    }

    public function getPlayerByUUID(UUID $UUID): ?TagPlayer
    {
        foreach ($this->players as $player) {
            if ($player->getUUID() == $UUID) {
                return $player;
            }
        }
        return null;
    }
}
