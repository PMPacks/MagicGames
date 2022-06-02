<?php

namespace CLADevs\VanillaX\commands\utils;

use pocketmine\network\mcpe\protocol\types\PlayerPermissions;
use pocketmine\network\mcpe\protocol\types\command\CommandParameter;

class CommandArgs
{

    /** @var CommandParameter[][] */
    private array $overloads = [];

    private int $permission;
    private int $flags;

    public function __construct(int $permission = PlayerPermissions::VISITOR, int $flags = 0)
    {
        $this->permission = $permission;
        $this->flags = $flags;
    }

    public function addOverload(CommandOverload $overload): void
    {
        $this->overloads[] = $overload->getParameters();
    }

    public function getPermission(): int
    {
        return $this->permission;
    }

    public function getFlags(): int
    {
        return $this->flags;
    }

    /**
     * @return CommandParameter[][]
     */
    public function getOverload(): array
    {
        return $this->overloads;
    }
}
