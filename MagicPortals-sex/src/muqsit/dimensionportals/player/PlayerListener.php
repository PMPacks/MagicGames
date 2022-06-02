<?php

declare(strict_types=1);

namespace muqsit\dimensionportals\player;

use Logger;
use pocketmine\event\Listener;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerLoginEvent;

final class PlayerListener implements Listener
{

	public function __construct(
		private Logger $logger
	) {
	}

	/**
	 * @param PlayerLoginEvent $event
	 * @priority MONITOR
	 */
	public function onPlayerLogin(PlayerLoginEvent $event): void
	{
		PlayerManager::create($event->getPlayer(), $this->logger);
	}

	/**
	 * @param PlayerQuitEvent $event
	 * @priority MONITOR
	 */
	public function onPlayerQuit(PlayerQuitEvent $event): void
	{
		PlayerManager::destroy($event->getPlayer());
	}

	public function onBlockBreak(BlockBreakEvent $event): void
	{
		$player = $event->getPlayer();
		$block = $event->getBlock();
	}
}
