<?php

namespace CLADevs\VanillaX\entities\utils\villager\professions;

use pocketmine\block\BlockLegacyIds;
use CLADevs\VanillaX\entities\utils\villager\VillagerProfession;

class MasonProfession extends VillagerProfession
{

    public function __construct()
    {
        parent::__construct(self::MASON, "Stone Mason", BlockLegacyIds::STONECUTTER);
    }
}
