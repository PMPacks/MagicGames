<?php

declare(strict_types=1);

namespace AndreasHGK\SellAll;

use pocketmine\item\Item;
use pocketmine\utils\Config;
use pocketmine\player\Player;
use pocketmine\command\Command;
use pocketmine\utils\TextFormat;
use pocketmine\plugin\PluginBase;
use onebone\economyapi\EconomyAPI;
use pocketmine\command\CommandSender;

class SellAll extends PluginBase
{
    private static SellAll $instance;

    public Config $messageConfig;
    public Config $settingConfig;

    public array $configValues;
    public array $messageValues;
    public array $settingValues;

    public function onLoad(): void
    {
        self::$instance = $this;
    }

    public function onEnable(): void
    {
        @mkdir($this->getDataFolder());
        $this->saveDefaultConfig();
        $this->configValues = $this->getConfig()->getAll();
        $this->saveResource("messages.yml");
        $this->saveResource("settings.yml");
        $this->messageConfig = new Config($this->getDataFolder() . "messages.yml", Config::YAML, []);
        $this->messageValues = $this->messageConfig->getAll();
        $this->settingConfig = new Config($this->getDataFolder() . "settings.yml", Config::YAML, []);
        $this->settingValues = $this->settingConfig->getAll();

        $this->getLogger()->info(TextFormat::RED . "Plugin Enabled, Please make sure the economy provider in settings.yml is correct!");
    }

    public static function getInstance(): SellAll
    {
        return self::$instance;
    }

    public function onCommand(CommandSender $sender, Command $command, String $label, array $args): bool
    {
        if (!$sender instanceof Player) {
            $sender->sendMessage(TextFormat::colorize("&cPlease execute this command in-game"));
            return true;
        }

        switch ($command->getName()) {
            case "sell":
                if (isset($args[0])) {
                    switch (strtolower($args[0])) {
                        case "hand":
                            $item = $sender->getInventory()->getItemInHand();
                            if (isset($this->configValues[$item->getId() . ":" . $item->getMeta()])) {
                                $price = $this->configValues[$item->getId() . ":" . $item->getMeta()];
                                $count = $item->getCount();
                                $totalprice = $price * $count;
                                $this->addMoney($sender->getName(), (int) $totalprice);
                                $item->setCount($item->getCount() - $count);
                                $sender->getInventory()->setItemInHand($item);
                                $sender->sendMessage(TextFormat::colorize($this->replaceVars($this->messageValues["success.sell"], array(
                                    "AMOUNT" => (string)$count,
                                    "ITEMNAME" => $item->getName(),
                                    "MONEY" => (string)$totalprice
                                ))));
                                return true;
                            } elseif (isset($this->configValues[$item->getId()])) {
                                $price = $this->configValues[$item->getId()];
                                $count = $item->getCount();
                                $totalprice = $price * $count;
                                $this->addMoney($sender->getName(), (int) $totalprice);
                                $item->setCount($item->getCount() - $count);
                                $sender->getInventory()->setItemInHand($item);
                                $sender->sendMessage(TextFormat::colorize($this->replaceVars($this->messageValues["success.sell"], array(
                                    "AMOUNT" => (string)$count,
                                    "ITEMNAME" => $item->getName(),
                                    "MONEY" => (string)$totalprice
                                ))));
                                return true;
                            }
                            $sender->sendMessage(TextFormat::colorize($this->messageValues["error.not-found"]));
                            return true;
                        case "all":
                            $item = $sender->getInventory()->getItemInHand();
                            $inventory = $sender->getInventory();
                            $contents = $inventory->getContents();
                            if (isset($this->configValues[$item->getId() . ":" . $item->getMeta()])) {
                                $price = $this->configValues[$item->getId() . ":" . $item->getMeta()];
                                $count = 0;
                                foreach ($contents as $slot) {
                                    if ($slot->getId() == $item->getId()) {
                                        $count = $count + $slot->getCount();
                                        $inventory->remove($slot);
                                    }
                                }
                                $totalprice = $count * $price;
                                $this->addMoney($sender->getName(), (int)$totalprice);
                                $sender->sendMessage(TextFormat::colorize($this->replaceVars($this->messageValues["success.sell"], array(
                                    "AMOUNT" => (string)$count,
                                    "ITEMNAME" => $item->getName(),
                                    "MONEY" => (string)$totalprice
                                ))));
                                return true;
                            } elseif (isset($this->configValues[$item->getId()])) {
                                $price = $this->configValues[$item->getId()];
                                $count = 0;
                                foreach ($contents as $slot) {
                                    if ($slot->getId() == $item->getId()) {
                                        $count = $count + $slot->getCount();
                                        $inventory->remove($slot);
                                    }
                                }
                                $totalprice = $count * $price;
                                $this->addMoney($sender->getName(), (int)$totalprice);
                                $sender->sendMessage(TextFormat::colorize($this->replaceVars($this->messageValues["success.sell"], array(
                                    "AMOUNT" => (string)$count,
                                    "ITEMNAME" => $item->getName(),
                                    "MONEY" => (string)$totalprice
                                ))));
                                return true;
                            }
                            $sender->sendMessage(TextFormat::colorize($this->messageValues["error.not-found"]));
                            return true;
                        case "inv":
                        case "inventory":
                            $inv = $sender->getInventory()->getContents();
                            $revenue = 0;
                            foreach ($inv as $item) {
                                if (isset($this->configValues[$item->getId() . ":" . $item->getMeta()])) {
                                    $revenue = $revenue + ($item->getCount() * $this->configValues[$item->getId() . ":" . $item->getMeta()]);
                                    $sender->getInventory()->remove($item);
                                } elseif (isset($this->configValues[$item->getId()])) {
                                    $revenue = $revenue + ($item->getCount() * $this->configValues[$item->getId()]);
                                    $sender->getInventory()->remove($item);
                                }
                            }
                            if ($revenue <= 0) {
                                $sender->sendMessage(TextFormat::colorize($this->messageValues["error.no.sellables"]));
                                return true;
                            }
                            $this->addMoney($sender->getName(), (int)$revenue);
                            $sender->sendMessage(TextFormat::colorize($this->replaceVars($this->messageValues["success.sell.inventory"], array(
                                "MONEY" => (string)$revenue
                            ))));
                            return true;
                        case "reload":
                            if ($sender->hasPermission("sellall.reload")) {
                                $this->reloadConfig();
                                $this->configValues = $this->getConfig()->getAll();
                                $this->messageConfig = new Config($this->getDataFolder() . "messages.yml", Config::YAML, []);
                                $this->messageValues = $this->messageConfig->getAll();
                                $this->settingConfig = new Config($this->getDataFolder() . "settings.yml", Config::YAML, []);
                                $this->settingValues = $this->settingConfig->getAll();

                                $sender->sendMessage(TextFormat::colorize($this->messageValues["reload"]));
                            } else {
                                $sender->sendMessage(TextFormat::colorize($this->replaceVars($this->messageValues["error.argument"], array(
                                    "ARGS" => $this->listArguments()
                                ))));
                                return true;
                            }
                            return true;
                        default:
                            if (array_key_exists($args[0], $this->configValues["groups"])) {
                                $group = $this->configValues["groups"][$args[0]];

                                $inv = $sender->getInventory()->getContents();
                                $revenue = 0;
                                foreach ($inv as $item) {
                                    if (isset($this->configValues[$item->getId()])) {
                                        if (in_array($item->getId(), $group["items"]) || in_array($item->getName(), $group["items"])) {
                                            if (isset($this->configValues[$item->getId() . ":" . $item->getMeta()])) {
                                                $revenue = $revenue + ($item->getCount() * $this->configValues[$item->getId() . ":" . $item->getMeta()]);
                                                $sender->getInventory()->remove($item);
                                            } elseif (isset($this->configValues[$item->getId()])) {
                                                $revenue = $revenue + ($item->getCount() * $this->configValues[$item->getId()]);
                                                $sender->getInventory()->remove($item);
                                            }
                                        }
                                    }
                                }
                                if ($revenue <= 0) {
                                    $sender->sendMessage(TextFormat::colorize($group["failed"]));
                                    return true;
                                }
                                $this->addMoney($sender->getName(), (int)$revenue);
                                $sender->sendMessage(TextFormat::colorize($this->replaceVars($group["success"], array(
                                    "MONEY" => (string)$revenue
                                ))));
                                return true;
                            }
                    }
                }
                $sender->sendMessage(TextFormat::colorize($this->replaceVars($this->messageValues["error.argument"], array(
                    "ARGS" => $this->listArguments()
                ))));
                return true;
            default:
                return false;
        }
    }

    public function addMoney(string $player, float|int $amount): bool
    {
        if ($this->settingValues["economy.provider"] === "EconomyAPI") {
            EconomyAPI::getInstance()->addMoney($player, $amount);
            return true;
        } elseif ($this->settingValues["economy.provider"] === "BedrockEconomy") {
            // we wont be using this
            //BedrockEconomyAPI::getInstance()->addToPlayerBalance($player, (int) ceil($amount));
        }
        return false;
    }

    public function replaceVars(string $str, array $vars): string
    {
        foreach ($vars as $key => $value) {
            $str = str_replace("{" . $key . "}", $value, $str);
        }
        return $str;
    }

    public function getSellPrice(Item $item): ?float
    {
        return $this->configValues[$item->getId() . ":" . $item->getMeta()] ?? $this->configValues[$item->getId()] ?? null;
    }

    public function isSellable(Item $item): bool
    {
        return $this->getSellPrice($item) !== null ? true : false;
    }

    public function listArguments(): string
    {
        $seperator = $this->messageValues["separator"];
        $args = "hand" . $seperator . "all" . $seperator . "inv";
        foreach ($this->configValues["groups"] as $name => $group) {
            $args = $args . $seperator . $name;
        }
        return $args;
    }
}
