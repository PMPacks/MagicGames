<?php

declare(strict_types=1);

namespace cosmicnebula200\MagicTags;

use pocketmine\player\Player;

final class Utils
{
    public static function checkTag(string $tag): bool
    {
        $tags = MagicTags::getInstance()->getTags();
        if (in_array($tag, $tags)) {
            return true;
        }
        return false;
    }

    public static function hasTag(Player $player, string $tag): bool
    {
        $tagPlayer = MagicTags::getInstance()->getPlayerManager()->getPlayer($player);
        if (in_array($tag, $tagPlayer->getTags())) {
            return true;
        }
        return false;
    }

    public static function getTagName(string $name): string
    {
        $tags = MagicTags::getInstance()->getTags();
        if ($name == "") {
            return "[]";
        }
        $name = str_replace([" ", ","] , ["",""], $name);
        return $tags[$name]["Name"];
    }
}
