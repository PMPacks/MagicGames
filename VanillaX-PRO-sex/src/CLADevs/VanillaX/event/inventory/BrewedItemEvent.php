<?php

namespace CLADevs\VanillaX\event\inventory;

use pocketmine\item\Item;
use pocketmine\event\Event;
use pocketmine\block\inventory\BrewingStandInventory;

class BrewedItemEvent extends Event
{

    private BrewingStandInventory $inventory;
    private Item $output;
    private Item $potion;
    private Item $ingredient;

    public function __construct(BrewingStandInventory $inventory, Item $output, Item $potion, Item $ingredient)
    {
        $this->inventory = $inventory;
        $this->output = $output;
        $this->potion = $potion;
        $this->ingredient = $ingredient;
    }

    public function getInventory(): BrewingStandInventory
    {
        return $this->inventory;
    }

    public function getOutput(): Item
    {
        return $this->output;
    }

    public function setOutput(Item $output): void
    {
        $this->output = $output;
    }

    public function getPotion(): Item
    {
        return $this->potion;
    }

    public function getIngredient(): Item
    {
        return $this->ingredient;
    }
}
