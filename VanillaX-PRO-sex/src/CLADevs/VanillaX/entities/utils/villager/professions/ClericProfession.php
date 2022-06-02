<?php

namespace CLADevs\VanillaX\entities\utils\villager\professions;

use pocketmine\block\BlockLegacyIds;
use CLADevs\VanillaX\entities\utils\villager\VillagerProfession;

class ClericProfession extends VillagerProfession
{

    public function __construct()
    {
        parent::__construct(self::CLERIC, "Cleric", BlockLegacyIds::BREWING_STAND_BLOCK);
    }
}
