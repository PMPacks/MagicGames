<?php

declare(strict_types=1);

namespace NgLamVN\InvCraft\event;

use pocketmine\player\Player;
use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\event\player\PlayerEvent;

final class OpenWorkbenchPortableCraftingEvent extends PlayerEvent implements Cancellable
{
	use CancellableTrait;

	public function __construct(Player $player)
	{
		$this->player = $player;
	}
}
