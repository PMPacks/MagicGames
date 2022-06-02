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

class FortuneEnchantment extends Enchantment
{
    use EnchantmentTrait;

    public function __construct()
    {
        parent::__construct(KnownTranslationFactory::enchantment_lootBonusDigger(), Rarity::RARE, ItemFlags::DIG, ItemFlags::NONE, 3);
    }

    public function getId(): string
    {
        return "fortune";
    }

    public function getMcpeId(): int
    {
        return EnchantmentIds::FORTUNE;
    }

    public function getIncompatibles(): array
    {
        return [EnchantmentIds::SILK_TOUCH];
    }

    public function isItemCompatible(Item $item): bool
    {
        return $item instanceof Tool;
    }
}
