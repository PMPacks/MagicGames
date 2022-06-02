<?php

declare(strict_types=1);

namespace BhawaniSingh\HCMinion\minions;

class MinionUpgrade implements MinionNBT
{
    public const AUTO_SMELT_LEVEL = 2;
    public const AUTO_SELL_LEVEL = 4;
    public const SUPER_COMPACTER_LEVEL = 6;
    public const SUPER_EXPANDER_LEVEL = 8;

    private bool $autoSmelt;
    private bool $autoSell;
    private bool $superCompacter;
    private bool $superExpander;

    public function __construct(bool $autoSmelt, bool $autoSell, bool $superCompacter, bool $superExpander)
    {
        $this->autoSmelt = $autoSmelt;
        $this->autoSell = $autoSell;
        $this->superCompacter = $superCompacter;
        $this->superExpander = $superExpander;
    }

    public function isAutoSmelt(): bool
    {
        return $this->autoSmelt;
    }

    public function setAutoSmelt(bool $autoSmelt): void
    {
        $this->autoSmelt = $autoSmelt;
    }

    public function isAutoSell(): bool
    {
        return $this->autoSell;
    }

    public function setAutoSell(bool $autoSell): void
    {
        $this->autoSell = $autoSell;
    }

    public function isSuperCompacter(): bool
    {
        return $this->superCompacter;
    }

    public function setSuperCompacter(bool $superCompacter): void
    {
        $this->superCompacter = $superCompacter;
    }

    public function isSuperExpander(): bool
    {
        return $this->superExpander;
    }

    public function setSuperExpander(bool $superExpander): void
    {
        $this->superExpander = $superExpander;
    }
}
