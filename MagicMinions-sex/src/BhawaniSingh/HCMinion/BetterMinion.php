<?php

declare(strict_types=1);

namespace BhawaniSingh\HCMinion;

use muqsit\invmenu\InvMenu;
use pocketmine\world\World;
use pocketmine\entity\Entity;
use pocketmine\plugin\PluginBase;
use muqsit\invmenu\InvMenuHandler;
use pocketmine\block\BlockFactory;
use pocketmine\block\BlockToolType;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockLegacyIds;
use pocketmine\entity\EntityFactory;
use pocketmine\utils\SingletonTrait;
use pocketmine\block\BlockIdentifier;
use pocketmine\entity\EntityDataHelper;
use pocketmine\item\enchantment\ItemFlags;
use pocketmine\item\enchantment\Enchantment;
use muqsit\invcrashfix\Loader as InvCrashFix;
use pocketmine\data\bedrock\EnchantmentIdMap;
use BhawaniSingh\HCMinion\tasks\QueueTickTask;
use CortexPE\Commando\PacketHooker as Commando;
use BhawaniSingh\HCMinion\entities\MinionEntity;
use BhawaniSingh\HCMinion\commands\MinionCommand;
use BhawaniSingh\HCMinion\providers\SQLiteProvider;
use BhawaniSingh\HCMinion\entities\objects\Farmland;
use BhawaniSingh\HCMinion\entities\types\MiningMinion;
use BhawaniSingh\HCMinion\entities\types\FarmingMinion;
use BhawaniSingh\HCMinion\entities\types\LumberjackMinion;

class BetterMinion extends PluginBase
{
    use SingletonTrait;

    /** @var string[] */
    public static array $minions = [MiningMinion::class, FarmingMinion::class, LumberjackMinion::class];

    /** @var string[] */
    public array $isRemove = [];

    public static array $minionQueue = [];
    public static int $queueNumber = 0;

    private SQLiteProvider $provider;

    public const FAKE_ENCH_ID = -1;
    
    public const MINION_LIMIT = 16;
    public const QUEUE_CYCLE = 20;

    public function onLoad(): void
    {
        self::setInstance($this);
        $this->saveDefaultConfig();
        $this->saveResource('smelts.json');
        $this->saveResource('compacts.json');
        $this->saveResource('minion.png');
        //$this->saveResource('minion.json');
    }

    public function onEnable(): void
    {
        foreach ([InvMenu::class, Commando::class] as $class) {
            if (!class_exists($class)) {
                $this->getLogger()->alert("{$class} not found! Please download this plugin from Poggit CI. Disabling plugin...");
                $this->getServer()->getPluginManager()->disablePlugin($this);
                return;
            }
        }
        if (!class_exists(InvCrashFix::class)) {
            $this->getLogger()->notice('InvCrashFix is required to fix client crashes on 1.16+, download it here: https://poggit.pmmp.io/ci/Muqsit/InvCrashFix');
        }
        foreach (self::$minions as $minion) {
            /** @phpstan-ignore-next-line */
            EntityFactory::getInstance()->register($minion, function (World $world, CompoundTag $nbt) use ($minion): Entity {
                $object = new $minion(EntityDataHelper::parseLocation($nbt, $world), MiningMinion::parseSkinNBT($nbt), $nbt);
                if ($object instanceof MinionEntity) {
                    return $object;
                }
                return new MiningMinion(EntityDataHelper::parseLocation($nbt, $world), MiningMinion::parseSkinNBT($nbt), $nbt);
            }, [$minion]);
        }
        BlockFactory::getInstance()->register(new Farmland(new BlockIdentifier(BlockLegacyIds::FARMLAND, 0), "Farmland", new BlockBreakInfo(2.5, BlockToolType::AXE, 0, 2.5), true), true);
        if (!InvMenuHandler::isRegistered()) {
            InvMenuHandler::register($this);
        }
        if (!Commando::isRegistered()) {
            Commando::register($this);
        }
        EnchantmentIdMap::getInstance()->register(self::FAKE_ENCH_ID, new Enchantment("Glow", 1, ItemFlags::ALL, ItemFlags::NONE, 1));

        $this->provider = new SQLiteProvider();

        $this->getServer()->getCommandMap()->register('Minion', new MinionCommand($this, 'minion', 'MagicMinion Main Command'));
        $this->getServer()->getPluginManager()->registerEvents(new EventListener(), $this);

        $this->getScheduler()->scheduleRepeatingTask(new QueueTickTask(), 1);
    }

    public function getProvider(): SQLiteProvider
    {
        return $this->provider;
    }
}
