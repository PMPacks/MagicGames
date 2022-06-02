<?php

namespace CLADevs\VanillaX\enchantments\tools;

use pocketmine\item\Item;
use pocketmine\item\Tool;
use pocketmine\item\enchantment\Rarity;
use pocketmine\item\enchantment\ItemFlags;
use pocketmine\data\bedrock\EnchantmentIds;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\lang\KnownTranslationFactory;
use CLADevs\VanillaX\enchantments\utils\EnchantmentTrait;

class EfficiencyEnchantment extends Enchantment
{
    use EnchantmentTrait;

    public function __construct()
    {
        parent::__construct(KnownTranslationFactory::enchantment_digging(), Rarity::COMMON, ItemFlags::DIG, ItemFlags::SHEARS, 5);
    }

    public function getId(): string
    {
        return "efficiency";
    }

    public function getMcpeId(): int
    {
        return EnchantmentIds::EFFICIENCY;
    }

    public function isItemCompatible(Item $item): bool
    {
        return $item instanceof Tool;
    }
}
