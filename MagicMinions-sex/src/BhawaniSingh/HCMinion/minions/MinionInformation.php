<?php

declare(strict_types=1);

namespace BhawaniSingh\HCMinion\minions;

use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\StringTag;

class MinionInformation implements MinionNBT
{
    /** @var string */
    private $owner;
    /** @var MinionType */
    private $type;
    /** @var MinionUpgrade */
    private $upgrade;
    /** @var int */
    private $level;
    /** @var int */
    private $resourcesCollected;

    public function __construct(string $owner, MinionType $type, MinionUpgrade $upgrade, int $level, int $resourcesCollected)
    {
        $this->owner = $owner;
        $this->type = $type;
        $this->upgrade = $upgrade;
        $this->level = $level;
        $this->resourcesCollected = $resourcesCollected;
    }

    public function getOwner(): string
    {
        return $this->owner;
    }

    public function getType(): MinionType
    {
        return $this->type;
    }

    public function getUpgrade(): MinionUpgrade
    {
        return $this->upgrade;
    }

    public function getLevel(): int
    {
        return $this->level;
    }

    public function incrementLevel(): void
    {
        ++$this->level;
    }

    public function getResourcesCollected(): int
    {
        return $this->resourcesCollected;
    }

    public function incrementResourcesCollected(): void
    {
        ++$this->resourcesCollected;
    }

    // To Improve
    public function nbtSerialize(): ListTag
    {
        return new ListTag([
            new StringTag($this->getOwner()),
            new StringTag((string) $this->getType()->getActionType()),
            new StringTag((string) $this->getType()->getTargetId()),
            new StringTag((string) $this->getType()->getTargetMeta()),
            new StringTag($this->getUpgrade()->isAutoSmelt() ? 'true' : 'false'),
            new StringTag($this->getUpgrade()->isAutoSell() ? 'true' : 'false'),
            new StringTag($this->getUpgrade()->isSuperCompacter() ? 'true' : 'false'),
            new StringTag($this->getUpgrade()->isSuperExpander() ? 'true' : 'false'),
            new StringTag((string) $this->getLevel()),
            new StringTag((string) $this->getResourcesCollected()),
        ], NBT::TAG_String);
    }

    public static function nbtDeserialize(ListTag $tag): self
    {
        $value = $tag->getValue();
        return new self(
            $value[0]->getValue(),
            new MinionType(
                (int) $value[1]->getValue(),
                (int) $value[2]->getValue(),
                (int) $value[3]->getValue()
            ),
            new MinionUpgrade(
                $value[4]->getValue() === 'true',
                $value[5]->getValue() === 'true',
                $value[6]->getValue() === 'true',
                $value[7]->getValue() === 'true'
            ),
            (int) $value[8]->getValue(),
            (int) $value[9]->getValue()
        );
    }
}
