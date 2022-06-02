<?php

declare(strict_types=1);

namespace BhawaniSingh\HCMinion\commands\subcommands;

use pocketmine\Server;
use pocketmine\item\Item;
use pocketmine\player\Player;
use pocketmine\item\ItemFactory;
use pocketmine\utils\TextFormat;
use pocketmine\block\BlockLegacyIds;
use CortexPE\Commando\BaseSubCommand;
use pocketmine\command\CommandSender;
use BhawaniSingh\HCMinion\BetterMinion;
use pocketmine\item\StringToItemParser;
use BhawaniSingh\HCMinion\minions\MinionType;
use CortexPE\Commando\args\RawStringArgument;
use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\item\LegacyStringToItemParser;
use BhawaniSingh\HCMinion\minions\MinionUpgrade;
use BhawaniSingh\HCMinion\minions\MinionInformation;
use pocketmine\item\enchantment\EnchantmentInstance;
use BhawaniSingh\HCMinion\commands\arguments\TypeArgument;

class GiveCommand extends BaseSubCommand
{
    private EnchantmentInstance $fakeEnchant;

    public function __construct(string $name, string $description = "", array $aliases = [])
    {
        parent::__construct($name, $description, $aliases);

        /** @phpstan-ignore-next-line */
        $this->fakeEnchant = new EnchantmentInstance(EnchantmentIdMap::getInstance()->fromId(BetterMinion::FAKE_ENCH_ID));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if (!$sender->hasPermission('minion.commands')) {
            $sender->sendMessage("You don't have permission to use this command!");
            return;
        }
        $player = !isset($args['player']) ? null : Server::getInstance()->getPlayerByPrefix($args['player']);
        if (!$player instanceof Player) {
            $sender->sendMessage("That player can't be found");
            return;
        }
        if (!isset($args['type']) || !is_numeric($args['type'])) {
            $this->sendUsage();
            return;
        }
        $type = $args['type'];

        try {
            if (!isset($args['target'])) {
                $sender->sendMessage('Item not found!');
                return;
            }
            $target = StringToItemParser::getInstance()->parse($args['target']) ?? LegacyStringToItemParser::getInstance()->parse($args['target']);
            if ($target->getId() > 255) {
                $sender->sendMessage("That item can't be found");
                return;
            }
            $minionType = new MinionType((int) $type, $target->getId(), $target->getMeta());
            $minionUpgrade = new MinionUpgrade(false, false, false, false);
            $level = 1;
            $resourcesCollect = 0;

            $itemData = [$target->getId(), $target->getMeta()];
            $item = match ($itemData) {
                [BlockLegacyIds::COBBLESTONE, 0] => ItemFactory::getInstance()->get(1110, 0, 1),
                [BlockLegacyIds::LOG, 0] => ItemFactory::getInstance()->get(1121, 0, 1),
                [BlockLegacyIds::LOG, 1] => ItemFactory::getInstance()->get(1129, 0, 1),
                [BlockLegacyIds::LOG, 2] => ItemFactory::getInstance()->get(1106, 0, 1),
                [BlockLegacyIds::LOG, 3] => ItemFactory::getInstance()->get(1117, 0, 1),
                [BlockLegacyIds::LOG2, 0] => ItemFactory::getInstance()->get(1105, 0, 1),
                [BlockLegacyIds::LOG2, 1] => ItemFactory::getInstance()->get(1111, 0, 1),
                [BlockLegacyIds::CARROT_BLOCK, 7] => ItemFactory::getInstance()->get(1107, 0, 1),
                [BlockLegacyIds::POTATO_BLOCK, 7] => ItemFactory::getInstance()->get(1123, 0, 1),
                [BlockLegacyIds::WHEAT_BLOCK, 7] => ItemFactory::getInstance()->get(1130, 0, 1),
                [BlockLegacyIds::MELON_BLOCK, 0] => ItemFactory::getInstance()->get(1119, 0, 1),
                [BlockLegacyIds::PUMPKIN, 0] => ItemFactory::getInstance()->get(1124, 0, 1),
                [BlockLegacyIds::CLAY_BLOCK, 0] => ItemFactory::getInstance()->get(1108, 0, 1),
                [BlockLegacyIds::EMERALD_ORE, 0] => ItemFactory::getInstance()->get(1113, 0, 1),
                [BlockLegacyIds::DIAMOND_ORE, 0] => ItemFactory::getInstance()->get(1112, 0, 1),
                [BlockLegacyIds::NETHER_QUARTZ_ORE, 0] => ItemFactory::getInstance()->get(1125, 0, 1),
                [BlockLegacyIds::GOLD_ORE, 0] => ItemFactory::getInstance()->get(1115, 0, 1),
                [BlockLegacyIds::IRON_ORE, 0] => ItemFactory::getInstance()->get(1116, 0, 1),
                [BlockLegacyIds::COAL_ORE, 0] => ItemFactory::getInstance()->get(1109, 0, 1),
                [BlockLegacyIds::LAPIS_ORE, 0] => ItemFactory::getInstance()->get(1118, 0, 1),
                [BlockLegacyIds::REDSTONE_ORE, 0] => ItemFactory::getInstance()->get(1126, 0, 1),
                [BlockLegacyIds::END_STONE, 0] => ItemFactory::getInstance()->get(1114, 0, 1),
                [BlockLegacyIds::NETHERRACK, 0] => ItemFactory::getInstance()->get(1120, 0, 1),
                [BlockLegacyIds::SNOW_BLOCK, 0] => ItemFactory::getInstance()->get(1128, 0, 1),
                [BlockLegacyIds::SAND, 0] => ItemFactory::getInstance()->get(1127, 0, 1),
                [BlockLegacyIds::OBSIDIAN, 0] => ItemFactory::getInstance()->get(1122, 0, 1),
                default => ItemFactory::getInstance()->get(1098, 0, 1)
            };
            $item->addEnchantment($this->fakeEnchant);
            $item->setCustomName(TextFormat::RESET . TextFormat::YELLOW . $minionType->getTargetName() . ' Minion I')->setLore(["§r§7Place this minion and it will\n§r§7start generating and mining blocks!\n§r§7Requires an open area to spawn\n§r§7blocks. Minions also work when you are offline!\n\n§r§eType: §b" . $minionType->getTargetName() . "\n§r§eLevel: §bI\n§r§eResources Collected: §b0"]);
            $item->getNamedTag()->setTag('MinionInformation', (new MinionInformation($player->getName(), $minionType, $minionUpgrade, $level, $resourcesCollect))->nbtSerialize());
            if (!$player->getInventory()->canAddItem($item)) {
                $sender->sendMessage('Player\'s inventory is full');
                return;
            }
            
            $player->getInventory()->addItem($item);
            $player->sendMessage(" §eSuccessfully Got You A §6" . $minionType->getTargetName() . "§e Minion");
        } catch (\InvalidArgumentException $exception) {
            $player->sendMessage("That item can't be found");
            return;
        }
    }

    protected function prepare(): void
    {
        $this->registerArgument(0, new RawStringArgument('player', true));
        $this->registerArgument(1, new TypeArgument('type', true));
        $this->registerArgument(2, new RawStringArgument('target', true));
    }
}
