<?php

namespace AGTHARN\BankUI\session;

use pocketmine\Server;
use AGTHARN\BankUI\Main;
use libMarshal\MarshalTrait;
use pocketmine\player\Player;
use AGTHARN\BankUI\bank\Banks;
use libMarshal\attributes\Field;
use pocketmine\item\ItemFactory;
use onebone\economyapi\EconomyAPI;

abstract class Session
{
    use MarshalTrait;

    public ?Player $player = null;
    public string $fileName;

    #[Field]
    public string $name = "";
    #[Field(name: "bank-provider")]
    public string $bankProvider = "";
    #[Field(name: "bank-activate-time")]
    public int $bankActivateTime = 0;
    #[Field(name: "last-closed-time")]
    public int $lastClosedTime = 0;
    #[Field]
    public float $money = 0.00;

    #[Field(name: "transaction-logs")]
    public array $transactionLogs = [];

    #[Field]
    public bool $frozen = false;
    #[Field]
    public bool $allowed = false;

    public function saveData(): int|false
    {
        if (!isset(Main::getInstance()->leaderBoard[$this->name])) {
            Main::getInstance()->leaderBoard[$this->name] = $this->money;
            arsort(Main::getInstance()->leaderBoard);

            $i = 0;
            foreach (Main::getInstance()->leaderBoard as $name => $money) {
                $i++;
                if ($i > 10) {
                    unset(Main::getInstance()->leaderBoard[$name]);
                }
            }
        } else {
            Main::getInstance()->leaderBoard[$this->name] = $this->money;
        }
        return $this->saveToJson($this->fileName);
    }

    public function loadData(): void
    {
        if (!file_exists($this->fileName)) {
            $this->resetData();
            return;
        }

        $contents = file_get_contents($this->fileName);
        if (!$contents) {
            $this->resetData();
            return;
        }
        $data = json_decode($contents, true);

        $this->bankProvider = $data["bank-provider"];
        $this->bankActivateTime = $data["bank-activate-time"];
        $this->lastClosedTime = $data["last-closed-time"];
        $this->money = $data["money"];
        $this->transactionLogs = $data["transaction-logs"];
        $this->frozen = $data["frozen"];
        $this->allowed = $data["allowed"];
    }

    public function resetData(): void
    {
        $this->bankProvider = "";
        $this->bankActivateTime = 0;
        $this->lastClosedTime = time();
        $this->money = 0.00;
        $this->transactionLogs = [];
        $this->frozen = false;
        $this->allowed = false;
        if (is_bool($this->saveData())) {
            $this->handleKick("MagicBankUI: Failed to reset data. Please report this immediately!");
            return;
        }
    }

    public function setBank(string $bankName): bool
    {
        if (Banks::bankExists($bankName)) {
            $this->bankProvider = $bankName;
            $this->bankActivateTime = time();
            $this->money = Banks::getBankData($bankName)["startingMoney"];
            $this->saveData();

            $this->handleMessage(" §aSuccessfully applied for a bank account with $bankName! You may run the command again to check your application status!");
            return true;
        }
        return false;
    }

    public function isBankActivated(): bool
    {
        if (Banks::bankExists($this->bankProvider)) {
            return $this->bankActivateTime + Banks::getBankData($this->bankProvider)["approvalSeconds"] < time();
        }
        return false;
    }

    public function hasBank(): bool
    {
        return $this->bankProvider !== "";
    }

    public function addMoney(mixed $amount): void
    {
        if (!is_numeric($amount)) {
            $this->handleMessage(" §cError encountered - Amount added is not numeric! $amount given!");
            return;
        }
        $amount = (float) $amount;

        $this->money += $amount;
        $this->saveData();
    }

    public function addInterest(bool $notifyPlayer = false): void
    {
        if ($this->hasBank()) {
            $this->addMoney($this->money * Banks::getBankData($this->bankProvider)["interestRate"]);
            $this->saveData();
            if ($notifyPlayer) {
                $this->handleMessage(" §aYour hourly bank interest has been added to your account!");
            }
        }
    }

    public function reduceMoney(mixed $amount): void
    {
        if (!is_numeric($amount)) {
            $this->handleMessage(" §cError encountered - Amount added is not numeric! $amount given!");
            return;
        }
        $amount = (float) $amount;

        $this->money -= $amount;
        $this->saveData();
    }

    public function depositMoney(mixed $amount, bool $amountIncludeTax = true): bool
    {
        if (!is_numeric($amount)) {
            $this->handleMessage(" §cError encountered - Amount added is not numeric! $amount given!");
            return false;
        }
        $depositTax = Banks::getBankData($this->bankProvider)["depositTax"];
        $amount = (float) match ($amountIncludeTax) {
            true => $amount - $depositTax,
            false => $amount
        };

        if ($amount < 100.00) {
            $this->handleMessage(" §cError encountered - Deposit amount must be greater than 100! $amount given!");
            return false;
        }
        if (EconomyAPI::getInstance()->myMoney($this->player ?? $this->name) < $amount) {
            $this->handleMessage(" §cYou do not have enough money to deposit this amount! $amount given!");
            return false;
        }
        if (!$this->allowed && $amount >= Banks::MONEY_LIMIT) {
            $this->handleMessage(" §cA large money transfer has been detected! Your bank account has been frozen by the authorities! Please create a ticket on our Discord server to appeal! $amount given!");
            $this->frozen = true;
            return false;
        }

        if (($status = EconomyAPI::getInstance()->reduceMoney($this->player ?? $this->name, $amount, true, "BANKUI")) === EconomyAPI::RET_SUCCESS) {
            if ($amountIncludeTax) {
                if (!(($status = EconomyAPI::getInstance()->reduceMoney($this->player ?? $this->name, $depositTax, true, "BANKUI")) === EconomyAPI::RET_SUCCESS)) {
                    $this->handleMessage(" §cError encountered - CODE ERROR: $status!");
                    return false;
                }
            }
            $this->addMoney($amount);

            if (is_bool($this->saveData())) {
                $this->handleKick("MagicBankUI: Failed to save when depositing money. Please report this immediately!");
                return false;
            }
            $this->transactionLogs[] = [
                "time" => time(),
                "date" => date("§b[d/m/y]"),
                "type" => Banks::TRANSACTION_TYPE_DEPOSIT,
                "amount" => $amount,
                "balanceBefore" => $this->money - $amount,
                "balanceAfter" => $this->money
            ];
            $this->saveData();

            $this->handleMessage(" §aSuccessfully deposited §f$" . number_format($amount, 2) . "§a into your bank account! Taxes: §f$" . $depositTax);
            return true;
        }
        $this->handleMessage(" §cError encountered - CODE ERROR: $status!");
        return false;
    }

    public function withdrawMoney(mixed $amount, bool $amountIncludeTax = true): bool
    {
        if (!is_numeric($amount)) {
            $this->handleMessage(" §cError encountered - Amount added is not numeric! $amount given!");
            return false;
        }
        $withdrawTax = Banks::getBankData($this->bankProvider)["withdrawTax"];
        $amount = (float) match ($amountIncludeTax) {
            true => $amount - $withdrawTax,
            false => $amount
        };

        if ($amount < 100.00) {
            $this->handleMessage(" §cError encountered - Withdraw amount must be greater than 100! $amount given!");
            return false;
        }
        if ($this->money < $amount) {
            $this->handleMessage(" §cYou do not have enough money in your bank account to withdraw this amount! $amount given!");
            return false;
        }
        if (!$this->allowed && $amount >= Banks::MONEY_LIMIT) {
            $this->handleMessage(" §cA large money transfer has been detected! Your bank account has been frozen by the authorities! Please create a ticket on our Discord server to appeal! $amount given!");
            $this->frozen = true;
            return false;
        }

        if (($status = EconomyAPI::getInstance()->addMoney($this->player ?? $this->name, $amount, true, "BANKUI")) === EconomyAPI::RET_SUCCESS) {
            if (!$amountIncludeTax) {
                if ($this->money < $amount + $withdrawTax) {
                    $this->handleMessage(" §cYou do not have enough money in your bank account to transfer this amount! " . $amount + $withdrawTax . " given!");
                    return false;
                }
                $this->reduceMoney($withdrawTax);
            }
            $this->reduceMoney($amount);

            if (is_bool($this->saveData())) {
                $this->handleKick("MagicBankUI: Failed to save when withdrawing money. Please report this immediately!");
                return false;
            }
            $this->transactionLogs[] = [
                "time" => time(),
                "date" => date("§b[d/m/y]"),
                "type" => Banks::TRANSACTION_TYPE_WITHDRAW,
                "amount" => $amount,
                "balanceBefore" => $this->money + $amount,
                "balanceAfter" => $this->money
            ];
            $this->saveData();

            $this->handleMessage(" §aSuccessfully withdrew §f$" . number_format($amount, 2) . "§a from your bank account! Taxes: §f$" . $withdrawTax);
            return true;
        }
        $this->handleMessage(" §cError encountered - CODE ERROR: $status!");
        return false;
    }

    public function transferMoney(mixed $amount, string $receiverName, bool $amountIncludeTax = true): bool
    {
        if (!is_numeric($amount)) {
            $this->handleMessage(" §cError encountered - Amount added is not numeric! $amount given!");
            return false;
        }
        $transferTax = Banks::getBankData($this->bankProvider)["transferTax"];
        $amount = (float) match ($amountIncludeTax) {
            true => $amount - $transferTax,
            false => $amount
        };

        if ($amount < 100.00) {
            $this->handleMessage(" §cError encountered - TRansfer amount must be greater than 100! $amount given!");
            return false;
        }
        if ($this->money < $amount) {
            $this->handleMessage(" §cYou do not have enough money in your bank account to transfer this amount! $amount given!");
            return false;
        }
        if (!$this->allowed && $amount >= Banks::MONEY_LIMIT) {
            $this->handleMessage(" §cA large money transfer has been detected! Your bank account has been frozen by the authorities! Please create a ticket on our Discord server to appeal! $amount given!");
            $this->frozen = true;
            return false;
        }

        if (($receiver = Server::getInstance()->getPlayerByPrefix($receiverName)) instanceof Player) {
            if (!$amountIncludeTax) {
                if ($this->money < $amount + $transferTax) {
                    $this->handleMessage(" §cYou do not have enough money in your bank account to transfer this amount! " . $amount + $transferTax . " given!");
                    return false;
                }
                $this->reduceMoney($transferTax);
            }
            $this->reduceMoney($amount);

            if (is_bool($this->saveData())) {
                $this->handleKick("MagicBankUI: Failed to save when transferring money. Please report this immediately!");
                return false;
            }
            $this->transactionLogs[] = [
                "time" => time(),
                "date" => date("§b[d/m/y]"),
                "type" => Banks::TRANSACTION_TYPE_TRANSFER,
                "amount" => $amount,
                "receiver" => $receiverName,
                "balanceBefore" => $this->money + $amount,
                "balanceAfter" => $this->money
            ];

            Main::getInstance()->getSessionManager()->getSession($receiver)->addMoney($amount);
            $this->handleMessage(" §aSuccessfully transferred §f$" . number_format($amount, 2) . "§a to $receiverName! Taxes: §f$" . $transferTax);
            return true;
        }
        if (is_file(Main::getInstance()->getDataFolder() . "data/" . $receiverName . ".json")) {
            if (!$amountIncludeTax) {
                if ($this->money < $amount + $transferTax) {
                    $this->handleMessage(" §cYou do not have enough money in your bank account to transfer this amount! " . $amount + $transferTax . " given!");
                    return false;
                }
                $this->reduceMoney($transferTax);
            }
            $this->reduceMoney($amount);

            if (is_bool($this->saveData())) {
                $this->handleKick("MagicBankUI: Failed to save when transferring money. Please report this immediately!");
                return false;
            }
            $this->transactionLogs[] = [
                "time" => time(),
                "date" => date("§b[d/m/y]"),
                "type" => Banks::TRANSACTION_TYPE_TRANSFER,
                "amount" => $amount,
                "balanceBefore" => $this->money + $amount,
                "balanceAfter" => $this->money
            ];

            $receiverSession = Main::getInstance()->getSessionManager()->getSession($receiverName);
            $receiverSession->addMoney($amount);
            $receiverSession->remove();

            $this->handleMessage(" §aSuccessfully transferred §f$" . number_format($amount, 2) . "§a to $receiverName! Taxes: §f$" . $transferTax);
            return true;
        }
        $this->handleMessage(" §cError encountered - Player does not exist! $receiverName given!");
        return false;
    }

    public function convertToNote(mixed $amount, bool $amountIncludeTax = true): bool
    {
        if ($this->player instanceof Player) {
            if (!is_numeric($amount)) {
                $this->handleMessage(" §cError encountered - Amount added is not numeric! $amount given!");
                return false;
            }
            $withdrawTax = Banks::getBankData($this->bankProvider)["withdrawTax"];
            $amount = (float) match ($amountIncludeTax) {
                true => $amount - $withdrawTax,
                false => $amount
            };

            if ($amount < 100.00) {
                $this->handleMessage(" §cError encountered - Deposit amount must be greater than 100! $amount given!");
                return false;
            }
            if ($this->money < $amount) {
                $this->handleMessage(" §cYou do not have enough money in your bank account to withdraw this amount! $amount given!");
                return false;
            }
            if (!$this->allowed && $amount >= Banks::MONEY_LIMIT) {
                $this->handleMessage(" §cA large money transfer has been detected! Your bank account has been frozen by the authorities! Please create a ticket on our Discord server to appeal! $amount given!");
                $this->frozen = true;
                return false;
            }

            if (!$amountIncludeTax) {
                if ($this->money < $amount + $withdrawTax) {
                    $this->handleMessage(" §cYou do not have enough money in your bank account to transfer this amount! " . $amount + $withdrawTax . " given!");
                    return false;
                }
                $this->reduceMoney($withdrawTax);
            }
            $this->reduceMoney($amount);

            if (is_bool($this->saveData())) {
                $this->handleKick("MagicBankUI: Failed to save when converting money to notes. Please report this immediately!");
                return false;
            }
            $item = ItemFactory::getInstance()->get(1091, 0, 1);
            $item->setCustomName("§r§l§6$" . $amount . " §aBANK NOTE");
            $item->setLore(["§r§7Right Click To Redeem This §aBank Note§7\n§r§7Withdrawn By §f" . $this->name . "\n§r§7Date »" . date("§f d/m/y") . "\n\n§r§7Value » §a$" . $amount]);
            $item->getNamedTag()->setFloat("Amount", $amount);
            
            $this->player?->getInventory()->addItem($item);
            $this->transactionLogs[] = [
                "time" => time(),
                "date" => date("§b[d/m/y]"),
                "type" => Banks::TRANSACTION_TYPE_CONVERT,
                "amount" => $amount,
                "balanceBefore" => $this->money + $amount,
                "balanceAfter" => $this->money
            ];
            $this->saveData();

            $this->handleMessage(" §aSuccessfully withdrew §f$" . number_format($amount, 2) . "§a from your bank account! Taxes: §f$" . $withdrawTax);
            return true;
        }
        $this->handleMessage(" §cError encountered - Player does not exist!");
        return false;
    }

    public function getInterestAmount(): float
    {
        if (Banks::bankExists($this->bankProvider)) {
            return $this->money * Banks::getBankData($this->bankProvider)["interestRate"];
        }
        return 0.00;
    }

    public function handleKick(string $message): bool
    {
        if ($this->player instanceof Player && $this->player->isConnected()) {
            $this->player->kick($message);
            return true;
        }
        return false;
    }

    public function handleMessage(string $message): bool
    {
        if ($this->player instanceof Player && $this->player->isConnected()) {
            $this->player->sendMessage($message);
            return true;
        }
        return false;
    }

    public function remove(): void
    {
        $this->saveData();
        Main::getInstance()->getSessionManager()->removeSession($this->player ?? $this->name);
    }
}
