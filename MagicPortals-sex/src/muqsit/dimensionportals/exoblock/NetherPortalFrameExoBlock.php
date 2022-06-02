<?php

declare(strict_types=1);

namespace muqsit\dimensionportals\exoblock;

use SplQueue;
use pocketmine\item\Item;
use pocketmine\block\Block;
use pocketmine\math\Facing;
use pocketmine\world\World;
use pocketmine\item\ItemIds;
use pocketmine\block\VanillaBlocks;
use pocketmine\world\utils\SubChunkExplorer;
use muqsit\dimensionportals\utils\ArrayUtils;
use pocketmine\world\utils\SubChunkExplorerStatus;
use pocketmine\block\BlockFactory;use pocketmine\block\BlockLegacyIds;
use muqsit\dimensionportals\event\player\PlayerCreateNetherPortalEvent;
use pocketmine\math\Vector2;use pocketmine\math\Vector3;use pocketmine\player\Player;

class NetherPortalFrameExoBlock implements ExoBlock{

	private int $frame_block_id;
	private float $lengthSquared;

	public function __construct(Block $frame_block, int $max_portal_height, int $max_portal_width){
		$this->frame_block_id = $frame_block->getId();
		$this->lengthSquared = (new Vector2($max_portal_height, $max_portal_width))->lengthSquared();
	}

	public function interact(Block $wrapping, Player $player, Item $item, int $face) : bool{
		if($item->getId() === ItemIds::FLINT_AND_STEEL){
			$affectedBlock = $wrapping->getSide($face);
			if($affectedBlock->getId() === BlockLegacyIds::AIR){
				$world = $player->getWorld();
				$pos = $affectedBlock->getPosition()->asVector3();
				$blocks = $this->fill($world, $pos, 10, Facing::WEST);
				if(count($blocks) === 0){
					$blocks = $this->fill($world, $pos, 10, Facing::NORTH);
				}
				if(count($blocks) > 0){
					($ev = new PlayerCreateNetherPortalEvent($player, $wrapping->getPosition()))->call();
					if(!$ev->isCancelled()){
						foreach($blocks as $hash => $block){
							if($block->getId() === BlockLegacyIds::PORTAL){
								World::getBlockXYZ($hash, $x, $y, $z);
								$world->setBlockAt($x, $y, $z, $block, false);
							}
						}
						return true;
					}
				}
			}
		}
		return false;
	}

	public function update(Block $wrapping) : bool{
		$pos = $wrapping->getPosition();
		$world = $pos->getWorld();
		$metadata = $wrapping->getMeta();

		if ($wrapping->getSide(Facing::UP)->getId() === BlockLegacyIds::PORTAL) {
			$this->fill2($world, $wrapping->getSide(Facing::UP)->getPosition(), $metadata);
		} elseif ($wrapping->getSide(Facing::DOWN)->getId() === BlockLegacyIds::PORTAL) {
			$this->fill2($world, $wrapping->getSide(Facing::DOWN)->getPosition(), $metadata);
		} elseif ($wrapping->getSide(Facing::NORTH)->getId() === BlockLegacyIds::PORTAL) {
			$this->fill2($world, $wrapping->getSide(Facing::NORTH)->getPosition(), $metadata);
		} elseif ($wrapping->getSide(Facing::SOUTH)->getId() === BlockLegacyIds::PORTAL) {
			$this->fill2($world, $wrapping->getSide(Facing::SOUTH)->getPosition(), $metadata);
		} elseif ($wrapping->getSide(Facing::EAST)->getId() === BlockLegacyIds::PORTAL) {
			$this->fill2($world, $wrapping->getSide(Facing::EAST)->getPosition(), $metadata);
		} elseif ($wrapping->getSide(Facing::WEST)->getId() === BlockLegacyIds::PORTAL) {
			$this->fill2($world, $wrapping->getSide(Facing::WEST)->getPosition(), $metadata);
		}
		
		return false;
	}

	public function onPlayerMoveInside(Player $player, Block $block) : void{
	}

	public function onPlayerMoveOutside(Player $player, Block $block) : void{
	}

	/**
	 * @param World $world
	 * @param Vector3 $origin
	 * @param int $radius
	 * @param int $direction
	 * @return array<int, Block>
	 */
	public function fill(World $world, Vector3 $origin, int $radius, int $direction) : array{
		$blocks = [];

		$visits = new SplQueue();
		$visits->enqueue($origin);
		while(!$visits->isEmpty()){
			/** @var Vector3 $coordinates */
			$coordinates = $visits->dequeue();
			if($origin->distanceSquared($coordinates) >= $this->lengthSquared){
				return [];
			}

			$coordinates_hash = World::blockHash($coordinates->x, $coordinates->y, $coordinates->z);
			$block = $world->getBlockAt($coordinates->x, $coordinates->y, $coordinates->z);

			if(
				$block->getId() === BlockLegacyIds::AIR &&
				ArrayUtils::firstOrDefault(
					$blocks,
					static function(int $hash, Block $block) use($coordinates_hash) : bool{ return $hash === $coordinates_hash; }
				) === null
			){
				$this->visit($coordinates, $blocks, $direction);
				if($direction === Facing::WEST){
					$visits->enqueue($coordinates->getSide(Facing::NORTH));
					$visits->enqueue($coordinates->getSide(Facing::SOUTH));
				}elseif($direction === Facing::NORTH){
					$visits->enqueue($coordinates->getSide(Facing::WEST));
					$visits->enqueue($coordinates->getSide(Facing::EAST));
				}
				$visits->enqueue($coordinates->getSide(Facing::UP));
				$visits->enqueue($coordinates->getSide(Facing::DOWN));
			}elseif(!$this->isValid($block, $coordinates_hash, $blocks)){
				return [];
			}
		}

		return $blocks;
	}

	public function fill2(World $world, Vector3 $origin, int $metadata) : void{
		$visits = new SplQueue();
		$visits->enqueue($origin);

		$iterator = new SubChunkExplorer($world);
		$air = VanillaBlocks::AIR();

		$block_factory = BlockFactory::getInstance();

		while(!$visits->isEmpty()){
			/** @var Vector3 $coordinates */
			$coordinates = $visits->dequeue();
			if(
				$iterator->moveTo($coordinates->x, $coordinates->y, $coordinates->z) === SubChunkExplorerStatus::INVALID ||
				$block_factory->fromFullBlock($iterator->currentSubChunk->getFullBlock($coordinates->x & 0x0f, $coordinates->y & 0x0f, $coordinates->z & 0x0f))->getId() !== BlockLegacyIds::PORTAL
			){
				continue;
			}

			$world->setBlockAt($coordinates->x, $coordinates->y, $coordinates->z, $air);

			if($metadata === 0){
				$visits->enqueue($coordinates->getSide(Facing::EAST));
				$visits->enqueue($coordinates->getSide(Facing::WEST));
			}
			$visits->enqueue($coordinates->getSide(Facing::NORTH));
			$visits->enqueue($coordinates->getSide(Facing::SOUTH));

			$visits->enqueue($coordinates->getSide(Facing::UP));
			$visits->enqueue($coordinates->getSide(Facing::DOWN));
		}
	}

	/**
	 * @param Vector3 $coordinates
	 * @param array<int, Block> $blocks
	 * @param int $direction
	 */
	public function visit(Vector3 $coordinates, array &$blocks, int $direction) : void{
		$blocks[World::blockHash($coordinates->x, $coordinates->y, $coordinates->z)] = BlockFactory::getInstance()->get(BlockLegacyIds::PORTAL, $direction - 2);
	}

	/**
	 * @param Block $block
	 * @param int $coordinates_hash
	 * @param array<int, Block> $portals
	 * @return bool
	 */
	private function isValid(Block $block, int $coordinates_hash, array $portals) : bool{
		return $block->getId() === $this->frame_block_id ||
			ArrayUtils::firstOrDefault(
				$portals,
				static function(int $hash, Block $b) use($coordinates_hash) : bool{ return $hash === $coordinates_hash && $b->getId() === BlockLegacyIds::PORTAL; }
			) !== null;
	}
}