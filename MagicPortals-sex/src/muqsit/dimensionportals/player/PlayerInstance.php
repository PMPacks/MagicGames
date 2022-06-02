<?php

declare(strict_types=1);

namespace muqsit\dimensionportals\player;

use Logger;
use PrefixedLogger;
use pocketmine\Server;
use pocketmine\world\World;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\entity\Location;
use blueturk\skyblock\managers\IslandManager;
use muqsit\dimensionportals\world\WorldManager;
use muqsit\dimensionportals\exoblock\PortalExoBlock;
use pocketmine\network\mcpe\protocol\PlayStatusPacket;
use muqsit\dimensionportals\world\end\EndWorldInstance;
use pocketmine\network\mcpe\protocol\ChangeDimensionPacket;
use muqsit\dimensionportals\world\nether\NetherWorldInstance;
use muqsit\dimensionportals\event\player\PlayerEnterPortalEvent;
use muqsit\dimensionportals\event\player\PlayerPortalTeleportEvent;

final class PlayerInstance{

	private Player $player;
	private Logger $logger;
	private ?PlayerPortalInfo $in_portal = null;
	private bool $changing_dimension = false;

	public function __construct(Player $player, Logger $logger){
		$this->player = $player;
		$this->logger = new PrefixedLogger($logger, $player->getName());
	}

	public function onEnterPortal(PortalExoBlock $block) : void{
		($ev = new PlayerEnterPortalEvent($this->player, $block, $this->player->isCreative() ? 0 : $block->getTeleportationDuration()))->call();
		if(!$ev->isCancelled()){
			$this->in_portal = new PlayerPortalInfo($block, $ev->getTeleportDuration());
			PlayerManager::scheduleTicking($this->player);
		}
	}

	public function onLeavePortal() : void{
		PlayerManager::stopTicking($this->player);
		$this->in_portal = null;
	}

	public function onBeginDimensionChange(int $network_dimension_id, Vector3 $position, bool $respawn) : void{
		$session = $this->player->getNetworkSession();

		PlayerManager::$_changing_dimension_sessions[spl_object_id($session)] = true;
		$this->changing_dimension = true;
		//$session->sendDataPacket(ChangeDimensionPacket::create($network_dimension_id, $position, $respawn));
		$this->logger->debug("Started changing dimension (network_dimension_id: {$network_dimension_id}, position: {$position->asVector3()}, respawn: " . ($respawn ? "true" : "false") . ")");
	}

	public function onEndDimensionChange() : void{
		$session = $this->player->getNetworkSession();
		unset(PlayerManager::$_changing_dimension_sessions[spl_object_id($session)]);
		$this->changing_dimension = false;
		//$session->sendDataPacket(PlayStatusPacket::create(PlayStatusPacket::PLAYER_SPAWN));
		$this->logger->debug("Stopped changing dimension");
	}

	/**
	 * Returns whether the player is on the dimension
	 * changing screen.
	 *
	 * @return bool
	 */
	public function isChangingDimension() : bool{
		return $this->changing_dimension;
	}

	public function tick() : void{
		if($this->in_portal->tick()){
			$this->teleport();
			$this->onLeavePortal();
		}
	}

	private function teleport() : void{
		$to = $this->in_portal->getBlock()->getTargetWorldInstance();
		if ($to instanceof EndWorldInstance) {
			IslandManager::teleportToIsland($this->player);
		} else {
			Server::getInstance()->dispatchCommand($this->player, "hub");
		}
		PlayerManager::get($this->player)->onEndDimensionChange();
	}
}