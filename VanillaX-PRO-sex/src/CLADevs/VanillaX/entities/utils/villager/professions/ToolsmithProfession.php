<?php

namespace CLADevs\VanillaX\entities\utils\villager\professions;

use pocketmine\block\BlockLegacyIds;
use CLADevs\VanillaX\entities\utils\villager\VillagerProfession;

class ToolsmithProfession extends VillagerProfession
{

    public function __construct()
    {
        parent::__construct(self::TOOL_SMITH, "Tool Smith", BlockLegacyIds::SMITHING_TABLE);
    }
}
