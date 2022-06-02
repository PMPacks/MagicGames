<?php

namespace CLADevs\VanillaX\enchantments\globals;

use pocketmine\item\enchantment\Rarity;
use pocketmine\item\enchantment\ItemFlags;
use pocketmine\data\bedrock\EnchantmentIds;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\lang\KnownTranslationFactory;
use CLADevs\VanillaX\enchantments\utils\EnchantmentTrait;

class MendingEnchantment extends Enchantment
{
    use EnchantmentTrait;

    public function __construct()
    {
        parent::__construct(KnownTranslationFactory::enchantment_mending(), Rarity::RARE, ItemFlags::NONE, ItemFlags::ALL, 1);
    }

    public function getId(): string
    {
        return "mending";
    }

    public function getMcpeId(): int
    {
        return EnchantmentIds::MENDING;
    }

    public function isTreasure(): bool
    {
        return true;
    }

    public function getIncompatibles(): array
    {
        return [EnchantmentIds::INFINITY];
    }
}
