<?php

namespace CLADevs\VanillaX\world\gamerule;

use pocketmine\world\World;

class GameRule
{

    const TYPE_BOOL = 0;
    const TYPE_INT = 1;

    const COMMAND_BLOCKS_ENABLED = "commandBlocksEnabled";
    const COMMAND_BLOCK_OUTPUT = "commandBlockOutput";
    const DO_DAY_LIGHT_CYCLE = "doDaylightCycle";
    const DO_ENTITY_DROPS = "doEntityDrops";
    const DO_FIRE_TICK = "doFireTick";
    const DO_INSOMNIA = "doInsomnia";
    const DO_IMMEDIATE_RESPAWN = "doImmediateRespawn";
    const DO_MOB_LOOT = "doMobLoot";
    const DO_MOB_SPAWNING = "doMobSpawning";
    const DO_TILE_DROPS = "doTileDrops";
    const DO_WEATHER_CYCLE = "doWeatherCycle";
    const DROWNING_DAMAGE = "drowningDamage";
    const FALL_DAMAGE = "fallDamage";
    const FIRE_DAMAGE = "fireDamage";
    const FREEZE_DAMAGE = "freezeDamage";
    const FUNCTION_COMMAND_LIMIT = "functionCommandLimit";
    const KEEP_INVENTORY = "keepInventory";
    const MAX_COMMAND_CHAIN_LENGTH = "maxCommandChainLength";
    const MOB_GRIEFING = "mobGriefing";
    const NATURAL_REGENERATION = "naturalRegeneration";
    const PVP = "pvp";
    const RANDOM_TICK_SPEED = "randomTickSpeed";
    const SEND_COMMAND_FEEDBACK = "sendCommandFeedback";
    const SHOW_COORDINATES = "showCoordinates";
    const SHOW_DEATH_MESSAGES = "showDeathMessages";
    const SPAWN_RADIUS = "spawnRadius";
    const TNT_EXPLODES = "tntExplodes";
    const SHOW_TAGS = "showTags";

    private string $name;

    private int $type;

    private int|bool $defaultValue;

    public function __construct(string $name, int|bool $defaultValue, int $type = self::TYPE_BOOL)
    {
        $this->name = $name;
        $this->defaultValue = $defaultValue;
        $this->type = $type;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): int
    {
        return $this->type;
    }

    public function getDefaultValue(): bool|int
    {
        return $this->defaultValue;
    }

    public function handleValue(bool|int $value, World $world): void
    {
    }
}
