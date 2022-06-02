<?php

namespace CLADevs\VanillaX\entities\utils\villager\professions;

use pocketmine\block\BlockLegacyIds;
use CLADevs\VanillaX\entities\utils\villager\VillagerProfession;

class WeaponsmithProfession extends VillagerProfession
{

    public function __construct()
    {
        parent::__construct(self::WEAPON_SMITH, "Weapon Smith", BlockLegacyIds::GRINDSTONE);
    }
}
