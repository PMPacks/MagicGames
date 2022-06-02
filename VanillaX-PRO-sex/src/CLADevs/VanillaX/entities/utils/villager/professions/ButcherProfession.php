<?php

namespace CLADevs\VanillaX\entities\utils\villager\professions;

use pocketmine\block\BlockLegacyIds;
use CLADevs\VanillaX\entities\utils\villager\VillagerProfession;

class ButcherProfession extends VillagerProfession
{

    public function __construct()
    {
        parent::__construct(self::BUTCHER, "Butcher", BlockLegacyIds::SMOKER);
    }
}
