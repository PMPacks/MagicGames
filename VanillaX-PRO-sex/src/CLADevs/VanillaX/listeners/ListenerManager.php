<?php

namespace CLADevs\VanillaX\listeners;

use pocketmine\Server;
use CLADevs\VanillaX\VanillaX;
use CLADevs\VanillaX\utils\Utils;

class ListenerManager
{

    public function startup(): void
    {
        Utils::callDirectory("listeners" . DIRECTORY_SEPARATOR . "types", function (string $namespace): void {
            Server::getInstance()->getPluginManager()->registerEvents(new $namespace(), VanillaX::getInstance());
        });
    }
}
