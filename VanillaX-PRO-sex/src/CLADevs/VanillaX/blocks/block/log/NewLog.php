<?php

namespace CLADevs\VanillaX\blocks\block\log;

use pocketmine\block\Log;
use CLADevs\VanillaX\utils\item\NonAutomaticCallItemTrait;

class NewLog extends Log implements NonAutomaticCallItemTrait
{

    protected function getAxisMetaShift(): int
    {
        return 0;
    }
}
