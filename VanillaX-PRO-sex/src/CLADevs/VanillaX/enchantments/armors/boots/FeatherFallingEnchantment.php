<?php

namespace CLADevs\VanillaX\enchantments\armors\boots;

use pocketmine\item\Item;
use pocketmine\item\Armor;
use pocketmine\item\enchantment\Rarity;
use pocketmine\inventory\ArmorInventory;
use pocketmine\item\enchantment\ItemFlags;
use pocketmine\data\bedrock\EnchantmentIds;
use pocketmine\lang\KnownTranslationFactory;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\enchantment\ProtectionEnchantment;
use CLADevs\VanillaX\enchantments\utils\EnchantmentTrait;

class FeatherFallingEnchantment extends ProtectionEnchantment
{
    use EnchantmentTrait;

    public function __construct()
    {
        parent::__construct(KnownTranslationFactory::enchantment_protect_fall(), Rarity::UNCOMMON, ItemFlags::FEET, ItemFlags::NONE, 4, 2.5, [
            EntityDamageEvent::CAUSE_FALL
        ]);
    }

    public function getId(): string
    {
        return "feather_falling";
    }

    public function getMcpeId(): int
    {
        return EnchantmentIds::FEATHER_FALLING;
    }

    public function isItemCompatible(Item $item): bool
    {
        return $item instanceof Armor && $item->getArmorSlot() === ArmorInventory::SLOT_FEET;
    }
}
