<?php

declare(strict_types=1);

namespace BhawaniSingh\HCMinion\entities\objects;

use pocketmine\block\Farmland as PMFarmland;
use pocketmine\block\{BlockIdentifier, BlockBreakInfo};

class Farmland extends PMFarmland
{
    private bool $fromMinion;

    public function __construct(BlockIdentifier $idInfo, string $name, BlockBreakInfo $info, bool $fromMinion = false)
    {
        $this->fromMinion = $fromMinion;

        parent::__construct($idInfo, $name, $info);
    }

    protected function canHydrate(): bool
    {
        if ($this->fromMinion) {
            return true;
        }
        return parent::canHydrate();
    }
}
