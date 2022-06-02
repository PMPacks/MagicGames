<?php

declare(strict_types=1);

namespace BhawaniSingh\HCMinion\entities\inventory;

use pocketmine\inventory\SimpleInventory;

class MinionInventory extends SimpleInventory
{
    public function getName(): string
    {
        return 'MinionInventory';
    }

    public function setSize(int $size): void
    {
        $this->slots->setSize($size);
    }
}
