<?php

declare(strict_types=1);

namespace BhawaniSingh\HCMinion\utils;

use GdImage;
use pocketmine\block\Air;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\world\Position;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\CompoundTag;

class Utils
{
    public static function checkPlacement(Player $player, Position $position): bool
    {
        $blocks = 0;
        for ($x = -2; $x <= 2; ++$x) {
            for ($z = -2; $z <= 2; ++$z) {
                if ($x === 0 && $z === 0) {
                    continue;
                }

                $block = $position->getWorld()->getBlock($position->add($x, -1, $z));
                if ($block instanceof Air) {
                    $blocks++;
                }
            }
        }
        if ($blocks === 0) {
            $player->sendMessage(" §cThere is no space for the minions to work! Try placing it 1 block higher and see if that solves the problem.");
            return false;
        }
        return true;
    }

    public static function getRomanNumeral(int $integer): string
    {
        $romanNumeralConversionTable = [
            'M' => 1000,
            'CM' => 900,
            'D' => 500,
            'CD' => 400,
            'C' => 100,
            'XC' => 90,
            'L' => 50,
            'XL' => 40,
            'X' => 10,
            'IX' => 9,
            'V' => 5,
            'IV' => 4,
            'I' => 1,
        ];
        $romanString = '';
        while ($integer > 0) {
            foreach ($romanNumeralConversionTable as $rom => $arb) {
                if ($integer >= $arb) {
                    $integer -= $arb;
                    $romanString .= $rom;

                    break;
                }
            }
        }

        return $romanString;
    }

    public static function createBaseNBT(Vector3 $pos, ?Vector3 $motion = null, float $yaw = 0.0, float $pitch = 0.0): CompoundTag
    {
        return CompoundTag::create()
            ->setTag('Pos', new ListTag([
                new DoubleTag($pos->x),
                new DoubleTag($pos->y),
                new DoubleTag($pos->z)
            ]))
            ->setTag('Motion', new ListTag([
                new DoubleTag($motion !== null ? $motion->x : 0.0),
                new DoubleTag($motion !== null ? $motion->y : 0.0),
                new DoubleTag($motion !== null ? $motion->z : 0.0)
            ]))
            ->setTag('Rotation', new ListTag([
                new FloatTag($yaw),
                new FloatTag($pitch)
            ]));
    }

    public static function createSkin(string $path): string
    {
        $img = @imagecreatefrompng($path);
        if (!$img instanceof GdImage) {
            return '';
        }

        $bytes = '';
        $lc = @getimagesize($path);
        if (!is_array($lc)) {
            return '';
        }

        $l = (int)$lc[1];
        for ($y = 0; $y < $l; $y++) {
            for ($x = 0; $x < 64; $x++) {
                $rgba = @imagecolorat($img, $x, $y);
                $a = ((~($rgba >> 24)) << 1) & 0xff;
                $r = ($rgba >> 16) & 0xff;
                $g = ($rgba >> 8) & 0xff;
                $b = $rgba & 0xff;
                $bytes .= chr($r) . chr($g) . chr($b) . chr($a);
            }
        }
        @imagedestroy($img);
        return $bytes;
    }

    public static function createGeometryData(string $pureGeometryData): array
    {
        foreach (json_decode($pureGeometryData, true) as $geometryName => $geometryData) {
            if (strpos($geometryName, "geometry.") === 0) {
                return [$geometryName, json_encode([$geometryName => $geometryData])];
            }
        }
        return [];
    }
}
