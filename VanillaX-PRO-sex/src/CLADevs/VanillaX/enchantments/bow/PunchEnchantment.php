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

class PunchEnchantment extends Enchantment
{
    use EnchantmentTrait;

    public function __construct()
    {
        parent::__construct(KnownTranslationFactory::enchantment_arrowKnockback(), Rarity::RARE, ItemFlags::BOW, ItemFlags::NONE, 2);
    }

    public function getId(): string
    {
        return "punch";
    }

    public function getMcpeId(): int
    {
        return EnchantmentIds::PUNCH;
    }

    public function isItemCompatible(Item $item): bool
    {
        return $item->getId() === ItemIds::BOW;
    }
}
