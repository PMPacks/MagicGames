<?php

namespace Quest\utils;

use pocketmine\player\Player;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\utils\TextFormat;

final class Utils
{
    public static function addSound(Player $player, int $volume = 2, int $pitch = 1, string $sound_name = "note.bell"): void
    {
        $sound = new PlaySoundPacket();
        $sound->soundName = $sound_name;
        $sound->x = $player->getPosition()->getX();
        $sound->y = $player->getPosition()->getY();
        $sound->z = $player->getPosition()->getZ();
        $sound->volume = $volume;
        $sound->pitch = $pitch;
        
        $player->getNetworkSession()->sendDataPacket($sound);
    }

    public static function getProgress(int $progress, int $size): string {
        $divide = $size > 750 ? 50 : ($size > 500 ? 20 : ($size > 300 ? 15 : ($size > 200 ? 10 : ($size > 100 ? 5 : 3)))); // for short bar
        $percentage = number_format(($progress / $size) * 100, 2);
        $progress = (int) ceil($progress / $divide);
        $size = (int) ceil($size / $divide);

        return TextFormat::GRAY . "[" . TextFormat::GREEN . str_repeat("|", $progress) .
            TextFormat::RED . str_repeat("|", (($size - $progress) >= 0) ? ($size - $progress) : 0) . TextFormat::GRAY . "] " .
            TextFormat::AQUA . "$percentage %%";
    }
}
