<?php

namespace CLADevs\VanillaX\event\player;

use pocketmine\item\Item;
use pocketmine\player\Player;
use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\event\player\PlayerEvent;
use CLADevs\VanillaX\entities\VanillaEntity;

class PlayerEntityPickEvent extends PlayerEvent implements Cancellable
{
    use CancellableTrait;

    private VanillaEntity $entity;

    private Item $resultItem;

    public function __construct(Player $player, VanillaEntity $entity, Item $resultItem)
    {
        $this->player = $player;
        $this->entity = $entity;
        $this->resultItem = $resultItem;
    }

    public function getEntity(): VanillaEntity
    {
        return $this->entity;
    }

    public function getResultItem(): Item
    {
        return $this->resultItem;
    }
}
