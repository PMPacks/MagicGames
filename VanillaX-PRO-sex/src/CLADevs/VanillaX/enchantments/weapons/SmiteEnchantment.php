<?php

namespace CLADevs\VanillaX\enchantments\weapons;

use pocketmine\item\Axe;
use pocketmine\item\Item;
use pocketmine\item\Sword;
use pocketmine\item\enchantment\Rarity;
use pocketmine\item\enchantment\ItemFlags;
use pocketmine\data\bedrock\EnchantmentIds;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\lang\KnownTranslationFactory;
use CLADevs\VanillaX\enchantments\utils\EnchantmentTrait;

class SmiteEnchantment extends Enchantment
{
    use EnchantmentTrait;

    public function __construct()
    {
        parent::__construct(KnownTranslationFactory::enchantment_damage_undead(), Rarity::UNCOMMON, ItemFlags::SWORD, ItemFlags::AXE, 5);
    }

    public function getId(): string
    {
        return "smite";
    }

    public function getMcpeId(): int
    {
        return EnchantmentIds::SMITE;
    }

    public function getIncompatibles(): array
    {
        return [EnchantmentIds::BANE_OF_ARTHROPODS, EnchantmentIds::SHARPNESS];
    }

    public function isItemCompatible(Item $item): bool
    {
        return $item instanceof Sword || $item instanceof Axe;
    }
}
