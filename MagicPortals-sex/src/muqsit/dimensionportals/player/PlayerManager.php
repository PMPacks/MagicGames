<?php

declare(strict_types=1);

namespace muqsit\dimensionportals\player;

use Logger;
use pocketmine\player\Player;
use muqsit\dimensionportals\Loader;
use pocketmine\scheduler\ClosureTask;
use pocketmine\network\mcpe\NetworkSession;
use muqsit\dimensionportals\world\WorldManager;
use pocketmine\network\mcpe\protocol\StartGamePacket;
use pocketmine\network\mcpe\protocol\MovePlayerPacket;
use pocketmine\network\mcpe\protocol\PlayerActionPacket;
use pocketmine\network\mcpe\protocol\types\PlayerAction;
use pocketmine\network\mcpe\protocol\types\SpawnSettings;
use muqsit\dimensionportals\libs\muqsit\simplepackethandler\SimplePacketHandler;

final class PlayerManager{

	/** @var PlayerInstance[] */
	private static array $players = [];

	/** @var int[] */
	private static array $ticking = [];

	/** @var true[] */
	public static array $_changing_dimension_sessions = [];

	public static function init(Loader $plugin) : void{
		$plugin->getServer()->getPluginManager()->registerEvents(new PlayerListener($plugin->getLogger()), $plugin);
		$plugin->getServer()->getPluginManager()->registerEvents(new PlayerDimensionChangeListener(), $plugin);

		SimplePacketHandler::createMonitor($plugin)->monitorIncoming(static function(PlayerActionPacket $packet, NetworkSession $origin) : void{
			if($packet->action === PlayerAction::DIMENSION_CHANGE_ACK){
				$player = $origin->getPlayer();
				if($player !== null && $player->isConnected()){
					PlayerManager::get($player)->onEndDimensionChange();
				}
			}
		});

		$plugin->getScheduler()->scheduleRepeatingTask(new ClosureTask(static function() : void{
			foreach(self::$ticking as $player_id){
				self::$players[$player_id]->tick();
			}
		}), 20);
	}

	public static function create(Player $player, Logger $logger) : void{
		self::$players[$player->getId()] = new PlayerInstance($player, $logger);
	}

	public static function destroy(Player $player) : void{
		self::stopTicking($player);
		unset(self::$players[$player->getId()]);
	}

	public static function get(Player $player) : PlayerInstance{
		return self::getNullable($player);
	}

	public static function getNullable(Player $player) : ?PlayerInstance{
		return self::$players[$player->getId()] ?? null;
	}

	public static function scheduleTicking(Player $player) : void{
		$player_id = $player->getId();
		self::$ticking[$player_id] = $player_id;
	}

	public static function stopTicking(Player $player) : void{
		unset(self::$ticking[$player->getId()]);
		unset(self::$_changing_dimension_sessions[spl_object_id($player->getNetworkSession())]);
	}
}