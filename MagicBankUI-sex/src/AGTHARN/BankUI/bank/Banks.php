<?php

namespace AGTHARN\BankUI\bank;

final class Banks
{
    public const TRANSACTION_TYPE_DEPOSIT = "DEPOSIT";
    public const TRANSACTION_TYPE_WITHDRAW = "WITHDRAW";
    public const TRANSACTION_TYPE_TRANSFER = "TRANSFER";
    public const TRANSACTION_TYPE_CONVERT = "CONVERT";

    public const MONEY_LIMIT = 5000000000.00;

    /** NOTE: I WILL NOT USE BANK NAME AS THE KEY FOR A REASON! */
    public const BANKS = [
        [
            "name" => "Bank of MagicGames",
            "description" => "The most reliable bank for players!",
            "logo" => "https://cdn-icons-png.flaticon.com/128/1086/1086741.png",
            "approvalSeconds" => 5 * 60,
            "interestRate" => 0.001,
            "depositTax" => 50,
            "withdrawTax" => 50,
            "transferTax" => 50,
            "startingMoney" => 100
        ],
        [
            "name" => "Cosmic Banks",
            "description" => "The best bank for growing businesses!",
            "logo" => "https://cdn-icons-png.flaticon.com/128/1138/1138038.png",
            "approvalSeconds" => 30 * 60,
            "interestRate" => 0.003,
            "depositTax" => 10,
            "withdrawTax" => 5,
            "transferTax" => 25,
            "startingMoney" => 0
        ],
        [
            "name" => "Bank of Pushkar",
            "description" => "The best bank for instant approval! Receive a free sum of $1000 after signing up!",
            "logo" => "https://cdn-icons-png.flaticon.com/128/584/584011.png",
            "approvalSeconds" => 0,
            "interestRate" => 0.0005,
            "depositTax" => 100,
            "withdrawTax" => 100,
            "transferTax" => 100,
            "startingMoney" => 1000
        ],
        [
            "name" => "AGTHARN Inc.",
            "description" => "No fees, no taxes, no problems! ",
            "logo" => "https://cdn-icons-png.flaticon.com/128/584/584011.png",
            "approvalSeconds" => 30 * 60,
            "interestRate" => 0.001,
            "depositTax" => 0,
            "withdrawTax" => 0,
            "transferTax" => 0,
            "startingMoney" => 0
        ]
    ];

    public static function getBankData(string $bankName): array
    {
        foreach (self::BANKS as $bankData) {
            if ($bankData["name"] === $bankName) {
                return $bankData;
            }
        }
        return [];
    }

    public static function bankExists(string $bankName): bool
    {
        foreach (self::BANKS as $bankData) {
            if ($bankData["name"] === $bankName) {
                return true;
            }
        }
        return false;
    }
}
