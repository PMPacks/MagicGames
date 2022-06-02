<?php

namespace CLADevs\VanillaX\enchantments\weapons\sword;

use pocketmine\item\Item;
use pocketmine\item\Sword;
use pocketmine\item\enchantment\Rarity;
use pocketmine\item\enchantment\ItemFlags;
use pocketmine\data\bedrock\EnchantmentIds;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\lang\KnownTranslationFactory;
use CLADevs\VanillaX\enchantments\utils\EnchantmentTrait;

class LootingEnchantment extends Enchantment
{
    use EnchantmentTrait;

    public function __construct()
    {
        parent::__construct(KnownTranslationFactory::enchantment_lootBonus(), Rarity::RARE, ItemFlags::SWORD, ItemFlags::NONE, 3);
    }

    public function getId(): string
    {
        return "looting";
    }

    public function getMcpeId(): int
    {
        return EnchantmentIds::LOOTING;
    }

    public function isItemCompatible(Item $item): bool
    {
        return $item instanceof Sword;
    }
}
