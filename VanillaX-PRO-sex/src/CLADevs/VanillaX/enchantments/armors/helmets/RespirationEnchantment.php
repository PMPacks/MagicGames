<?php

namespace CLADevs\VanillaX\enchantments\armors\helmets;

use pocketmine\item\Item;
use pocketmine\item\Armor;
use pocketmine\item\enchantment\Rarity;
use pocketmine\inventory\ArmorInventory;
use pocketmine\item\enchantment\ItemFlags;
use pocketmine\data\bedrock\EnchantmentIds;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\lang\KnownTranslationFactory;
use CLADevs\VanillaX\enchantments\utils\EnchantmentTrait;

class RespirationEnchantment extends Enchantment
{
    use EnchantmentTrait;

    public function __construct()
    {
        parent::__construct(KnownTranslationFactory::enchantment_oxygen(), Rarity::RARE, ItemFlags::HEAD, ItemFlags::NONE, 3);
    }

    public function getId(): string
    {
        return "respiration";
    }

    public function getMcpeId(): int
    {
        return EnchantmentIds::RESPIRATION;
    }

    public function isItemCompatible(Item $item): bool
    {
        return $item instanceof Armor && $item->getArmorSlot() === ArmorInventory::SLOT_HEAD;
    }
}
