<?php

namespace CLADevs\VanillaX\enchantments\armors;

use pocketmine\item\Item;
use pocketmine\item\Armor;
use pocketmine\item\enchantment\Rarity;
use pocketmine\item\enchantment\ItemFlags;
use pocketmine\data\bedrock\EnchantmentIds;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\lang\KnownTranslationFactory;
use CLADevs\VanillaX\enchantments\utils\EnchantmentTrait;

class BindingEnchantment extends Enchantment
{
    use EnchantmentTrait;

    public function __construct()
    {
        parent::__construct(KnownTranslationFactory::enchantment_curse_binding(), Rarity::MYTHIC, ItemFlags::ARMOR, ItemFlags::ELYTRA, 1);
    }

    public function getId(): string
    {
        return "binding";
    }

    public function getMcpeId(): int
    {
        return EnchantmentIds::BINDING;
    }

    public function isTreasure(): bool
    {
        return true;
    }

    public function isItemCompatible(Item $item): bool
    {
        return $item instanceof Armor;
    }
}
