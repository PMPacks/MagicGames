<?php

namespace CLADevs\VanillaX\entities\utils\villager\professions;

use pocketmine\block\BlockLegacyIds;
use CLADevs\VanillaX\entities\utils\villager\VillagerProfession;

class LibrarianProfession extends VillagerProfession
{

    public function __construct()
    {
        parent::__construct(self::LIBRARIAN, "Librarian", BlockLegacyIds::LECTERN);
    }
}
