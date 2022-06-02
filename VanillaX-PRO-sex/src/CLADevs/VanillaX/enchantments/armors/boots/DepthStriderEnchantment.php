<?php

namespace CLADevs\VanillaX\enchantments\armors\boots;

use pocketmine\item\Item;
use pocketmine\item\Armor;
use pocketmine\item\enchantment\Rarity;
use pocketmine\inventory\ArmorInventory;
use pocketmine\item\enchantment\ItemFlags;
use pocketmine\data\bedrock\EnchantmentIds;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\lang\KnownTranslationFactory;
use CLADevs\VanillaX\enchantments\utils\EnchantmentTrait;

class DepthStriderEnchantment extends Enchantment
{
    use EnchantmentTrait;

    public function __construct()
    {
        parent::__construct(KnownTranslationFactory::enchantment_waterWalker(), Rarity::RARE, ItemFlags::FEET, ItemFlags::NONE, 3);
    }

    public function getId(): string
    {
        return "death_strider";
    }

    public function getMcpeId(): int
    {
        return EnchantmentIds::DEPTH_STRIDER;
    }

    public function getIncompatibles(): array
    {
        return [EnchantmentIds::FROST_WALKER];
    }

    public function isItemCompatible(Item $item): bool
    {
        return $item instanceof Armor && $item->getArmorSlot() === ArmorInventory::SLOT_FEET;
    }
}
