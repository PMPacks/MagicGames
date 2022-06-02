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

class ImpalingEnchantment extends Enchantment
{
    use EnchantmentTrait;

    public function __construct()
    {
        parent::__construct(KnownTranslationFactory::enchantment_tridentImpaling(), Rarity::RARE, ItemFlags::TRIDENT, ItemFlags::NONE, 5);
    }

    public function getId(): string
    {
        return "impaling";
    }

    public function getMcpeId(): int
    {
        return EnchantmentIds::IMPALING;
    }

    public function isItemCompatible(Item $item): bool
    {
        return $item->getId() === ItemIds::TRIDENT;
    }
}
