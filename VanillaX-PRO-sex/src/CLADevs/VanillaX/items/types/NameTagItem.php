<?php

namespace CLADevs\VanillaX\items\types;

use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\item\ItemIdentifier;
use CLADevs\VanillaX\entities\utils\EntityInteractable;
use CLADevs\VanillaX\entities\utils\EntityInteractResult;

class NameTagItem extends Item implements EntityInteractable
{

    public function __construct()
    {
        parent::__construct(new ItemIdentifier(ItemIds::NAMETAG, 0), "Name Tag");
    }

    public function onInteract(EntityInteractResult $result): void
    {
        $player = $result->getPlayer();
        $entity = $result->getEntity();

        if ($this->getName() === $this->getVanillaName() || $entity->getNameTag() === $this->getName()) {
            return;
        }
        $entity->setNameTag($this->getName());
        if ($player->isSurvival() || $player->isAdventure()) {
            $this->pop();
            $player->getInventory()->setItemInHand($this);
        }
    }
}
