<?php

namespace CLADevs\VanillaX\entities\utils\villager\professions;

use pocketmine\block\BlockLegacyIds;
use CLADevs\VanillaX\entities\utils\villager\VillagerProfession;

class CartographerProfession extends VillagerProfession
{

    public function __construct()
    {
        parent::__construct(self::CARTOGRAPHER, "Cartographer", BlockLegacyIds::CARTOGRAPHY_TABLE);
    }
}
