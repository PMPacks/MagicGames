<?php

namespace CLADevs\VanillaX\enchantments\weapons\sword;

use pocketmine\item\Item;
use pocketmine\item\Sword;
use pocketmine\item\enchantment\Rarity;
use pocketmine\item\enchantment\ItemFlags;
use pocketmine\data\bedrock\EnchantmentIds;
use pocketmine\lang\KnownTranslationFactory;
use CLADevs\VanillaX\enchantments\utils\EnchantmentTrait;
use pocketmine\item\enchantment\FireAspectEnchantment as PMFireAspectEnchantment;

class FireAspectEnchantment extends PMFireAspectEnchantment
{
    use EnchantmentTrait;

    public function __construct()
    {
        parent::__construct(KnownTranslationFactory::enchantment_fire(), Rarity::RARE, ItemFlags::SWORD, ItemFlags::NONE, 2);
    }

    public function getId(): string
    {
        return "fire_aspect";
    }

    public function getMcpeId(): int
    {
        return EnchantmentIds::FIRE_ASPECT;
    }

    public function isItemCompatible(Item $item): bool
    {
        return $item instanceof Sword;
    }
}
