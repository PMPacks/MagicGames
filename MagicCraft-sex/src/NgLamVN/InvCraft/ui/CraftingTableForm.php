<?php

namespace NgLamVN\InvCraft\ui;

use pocketmine\Server;
use NgLamVN\InvCraft\Loader;
use dktapps\pmforms\FormIcon;
use dktapps\pmforms\MenuForm;
use pocketmine\player\Player;
use dktapps\pmforms\MenuOption;
use NgLamVN\InvCraft\event\OpenWorkbenchPortableCraftingEvent;

class CraftingTableForm extends MenuForm
{

    public function __construct()
    {
        parent::__construct(
            "§l§6TABLE SELECTOR",
            "§dSelect The Table For Open:",
            [
                new MenuOption("§l§eVANNILA CRAFTING TABLE\n§9»» §r§6Tap To Open", new FormIcon("https://i.imgur.com/dGKyUmN.png", FormIcon::IMAGE_TYPE_URL)),
                new MenuOption("§l§eCUSTOM CRAFTING TABLE\n§9»» §r§6Tap To Open", new FormIcon("https://i.imgur.com/fqqQJcb.png", FormIcon::IMAGE_TYPE_URL)),
                new MenuOption("§l§eCUSTOM RECIPES\n§9»» §r§6Tap To Open", new FormIcon("textures/icon/recipe", FormIcon::IMAGE_TYPE_PATH)),
                new MenuOption("§cEXIT\n§9»» §r§cTap To Exit", new FormIcon("textures/ui/redX1", FormIcon::IMAGE_TYPE_PATH))
            ],
            function (Player $player, int $selected): void {
                switch ($selected) {
                    case 0:
                        ($ev = new OpenWorkbenchPortableCraftingEvent($player))->call();
                        if (!$ev->isCancelled()) {
                            Loader::WORKBENCH()->send($player);
                        }
                        break;

                    case 1:
                        Server::getInstance()->dispatchCommand($player, "invcraft");
                        break;

                    case 2:
                        Server::getInstance()->dispatchCommand($player, "recipes");
                        break;
                }
            }
        );
    }
}
