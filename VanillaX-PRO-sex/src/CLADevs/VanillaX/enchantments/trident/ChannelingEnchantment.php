<?php

namespace CLADevs\VanillaX\enchantments\trident;

use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\item\enchantment\Rarity;
use pocketmine\item\enchantment\ItemFlags;
use pocketmine\data\bedrock\EnchantmentIds;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\lang\KnownTranslationFactory;
use CLADevs\VanillaX\enchantments\utils\EnchantmentTrait;

class ChannelingEnchantment extends Enchantment
{
    use EnchantmentTrait;

    public function __construct()
    {
        parent::__construct(KnownTranslationFactory::enchantment_tridentChanneling(), Rarity::MYTHIC, ItemFlags::TRIDENT, ItemFlags::NONE, 1);
    }

    public function getId(): string
    {
        return "channeling";
    }

    public function getMcpeId(): int
    {
        return EnchantmentIds::CHANNELING;
    }

    public function getIncompatibles(): array
    {
        return [EnchantmentIds::RIPTIDE];
    }

    public function isItemCompatible(Item $item): bool
    {
        return $item->getId() === ItemIds::TRIDENT;
    }
}
