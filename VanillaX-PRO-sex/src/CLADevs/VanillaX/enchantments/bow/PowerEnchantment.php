<?php

namespace CLADevs\VanillaX\enchantments\bow;

use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\item\enchantment\Rarity;
use pocketmine\item\enchantment\ItemFlags;
use pocketmine\data\bedrock\EnchantmentIds;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\lang\KnownTranslationFactory;
use CLADevs\VanillaX\enchantments\utils\EnchantmentTrait;

class PowerEnchantment extends Enchantment
{
    use EnchantmentTrait;

    public function __construct()
    {
        parent::__construct(KnownTranslationFactory::enchantment_arrowDamage(), Rarity::COMMON, ItemFlags::BOW, ItemFlags::NONE, 5);
    }

    public function getId(): string
    {
        return "power";
    }

    public function getMcpeId(): int
    {
        return EnchantmentIds::POWER;
    }

    public function isItemCompatible(Item $item): bool
    {
        return $item->getId() === ItemIds::BOW;
    }
}
