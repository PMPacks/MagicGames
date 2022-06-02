<?php

namespace AGTHARN\BankUI\session;

use pocketmine\player\Player;
use AGTHARN\BankUI\session\type\PlayerSession;
use AGTHARN\BankUI\session\type\OfflineSession;

class SessionManager
{
    public const SESSION_TYPE_PLAYER = 0;
    public const SESSION_TYPE_OFFLINE = 1;

    /** @var PlayerSession[] */
    private array $playerSessions = [];
    /** @var OfflineSession[] */
    private array $offlineSessions = [];

    public function createSession(Player|string $player): Session
    {
        if ($player instanceof Player) {
            return $this->playerSessions[$player->getUniqueId()->toString()] = new PlayerSession($player);
        }
        return $this->offlineSessions[$player] = new OfflineSession($player);
    }

    public function removeSession(Player|string $player): bool
    {
        if ($player instanceof Player) {
            if (isset($this->playerSessions[$player->getUniqueId()->toString()])) {
                $this->playerSessions[$player->getUniqueId()->toString()]->saveData();
                unset($this->playerSessions[$player->getUniqueId()->toString()]);
                return true;
            }
            return false;
        }

        if (isset($this->offlineSessions[$player])) {
            $this->offlineSessions[$player]->saveData();
            unset($this->offlineSessions[$player]);
            return true;
        }
        return false;
    }

    public function getSession(Player|string $player): Session
    {
        if ($player instanceof Player) {
            return $this->playerSessions[$player->getUniqueId()->toString()] ?? $this->createSession($player);
        }
        return $this->offlineSessions[$player] ?? $this->createSession($player);
    }

    /** @return Session[] */
    public function getSessions(int $type): array
    {
        return match ($type) {
            self::SESSION_TYPE_PLAYER => $this->playerSessions,
            self::SESSION_TYPE_OFFLINE => $this->offlineSessions,
            default => []
        };
    }
}
