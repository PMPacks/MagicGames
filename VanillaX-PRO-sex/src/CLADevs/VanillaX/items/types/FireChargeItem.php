<?php

namespace CLADevs\VanillaX\items\types;

use pocketmine\block\Air;
use pocketmine\item\Item;
use pocketmine\block\Block;
use pocketmine\item\ItemIds;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\item\ItemUseResult;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\ItemIdentifier;
use CLADevs\VanillaX\session\Session;

class FireChargeItem extends Item
{

    public function __construct()
    {
        parent::__construct(new ItemIdentifier(ItemIds::FIRE_CHARGE, 0), "Fire Charge");
    }

    public function onInteractBlock(Player $player, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector): ItemUseResult
    {
        if ($blockReplace instanceof Air) {
            $blockReplace->getPosition()->getWorld()->setBlock($blockReplace->getPosition(), VanillaBlocks::FIRE(), true);
            if ($player->isSurvival() || $player->isAdventure()) $this->pop();
            Session::playSound($player, "mob.blaze.shoot");
            return ItemUseResult::SUCCESS();
        }
        return ItemUseResult::FAIL();
    }
}
