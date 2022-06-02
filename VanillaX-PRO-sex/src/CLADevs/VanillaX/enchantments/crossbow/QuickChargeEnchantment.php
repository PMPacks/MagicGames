<?php

namespace CLADevs\VanillaX\enchantments\crossbow;

use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\item\enchantment\Rarity;
use pocketmine\data\bedrock\EnchantmentIds;
use CLADevs\VanillaX\enchantments\ItemFlags;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\lang\KnownTranslationFactory;
use CLADevs\VanillaX\enchantments\utils\EnchantmentTrait;
use pocketmine\item\enchantment\ItemFlags as PMItemFlags;

class QuickChargeEnchantment extends Enchantment
{
    use EnchantmentTrait;

    public function __construct()
    {
        parent::__construct(KnownTranslationFactory::enchantment_crossbowPiercing(), Rarity::UNCOMMON, ItemFlags::CROSSBOW, PMItemFlags::NONE, 3);
    }

    public function getId(): string
    {
        return "quick_charge";
    }

    public function getMcpeId(): int
    {
        return EnchantmentIds::QUICK_CHARGE;
    }

    public function isItemCompatible(Item $item): bool
    {
        return $item->getId() === ItemIds::CROSSBOW;
    }
}
