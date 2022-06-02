<?php

declare(strict_types=1);

namespace BhawaniSingh\HCMinion\minions;

use pocketmine\block\Block;
use pocketmine\block\BlockFactory;
use BhawaniSingh\HCMinion\entities\objects\MinionTree;

class MinionType implements MinionNBT
{
    public const MINING_MINION = 0;
    public const FARMING_MINION = 1;
    public const LUMBERJACK_MINION = 2;

    /** @var int */
    private $actionType;
    /** @var int */
    private $targetId;
    /** @var int */
    private $targetMeta;

    public function __construct(int $actionType, int $targetId, int $targetMeta = 0)
    {
        $this->actionType = $actionType;
        $this->targetId = $targetId;
        $this->targetMeta = $targetMeta;
    }

    public function getActionType(): int
    {
        return $this->actionType;
    }

    public function getTargetId(): int
    {
        return $this->targetId;
    }

    public function getTargetMeta(): int
    {
        return $this->targetMeta;
    }

    public function toBlock(): Block
    {
        return BlockFactory::getInstance()->get($this->getTargetId(), $this->getTargetMeta());
    }

    public function toTree(): MinionTree
    {
        return new MinionTree($this->toBlock());
    }

    public function getTargetName(): string
    {
        return $this->toBlock()->getName();
    }
}
