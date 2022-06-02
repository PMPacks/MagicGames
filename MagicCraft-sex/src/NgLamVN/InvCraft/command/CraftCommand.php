<?php

declare(strict_types=1);

namespace NgLamVN\InvCraft\command;

use NgLamVN\InvCraft\Loader;
use pocketmine\player\Player;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use NgLamVN\InvCraft\event\OpenWorkbenchPortableCraftingEvent;

class CraftCommand extends Command
{
	public Loader $loader;

	public function __construct(Loader $loader)
	{
		$this->loader = $loader;
		parent::__construct("craft");
		$this->setDescription("Craft Command");
		$this->setPermission("craft.command");
	}

	/**
	 * @param CommandSender $sender
	 * @param string        $commandLabel
	 * @param array         $args
	 *
	 * @return void
	 */
	public function execute(CommandSender $sender, string $commandLabel, array $args): void
	{
		if ($sender instanceof Player) {
			($ev = new OpenWorkbenchPortableCraftingEvent($sender))->call();
			if (!$ev->isCancelled()) {
				Loader::WORKBENCH()->send($sender);
			}
		}
	}
}
