<?php

namespace CLADevs\VanillaX\entities\utils\villager\professions;

use pocketmine\block\BlockLegacyIds;
use CLADevs\VanillaX\entities\utils\villager\VillagerProfession;

class FletcherProfession extends VillagerProfession
{

    public function __construct()
    {
        parent::__construct(self::FLETCHER, "Fletcher", BlockLegacyIds::FLETCHING_TABLE);
    }
}
