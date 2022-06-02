<?php

declare(strict_types=1);

namespace cosmicnebula200\MagicTags\players;

use cosmicnebula200\MagicTags\MagicTags;
use cosmicnebula200\MagicTags\queries\Queries;

class TagPlayer
{
    private string $uuid;
    private string $name;
    private string $tags;
    private string $current_tag;

    public function __construct(string $uuid, string $name, string $tags, string $current_tag)
    {
        $this->uuid = $uuid;
        $this->name = $name;
        $this->tags = $tags;
        $this->current_tag = $current_tag;
    }

    public function getUUID(): string
    {
        return $this->uuid;
    }

    public function setUUID(string $uuid): void
    {
        $this->uuid = $uuid;
        $this->updateDB();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
        $this->updateDB();
    }

    public function getTags(): array
    {
        return explode(",", $this->tags);
    }

    public function addTag(string $tag): void
    {
        $tags = $this->getTags();
        $tags[] = $tag;
        
        $this->tags = implode(",", $tags);
        $this->updateDB();
    }

    public function getCurrentTag(): string
    {
        return $this->current_tag;
    }

    public function setCurrentTag(string $tag): void
    {
        $this->current_tag = $tag;
        $this->updateDB();
    }

    public function updateDB(): void
    {
        MagicTags::getInstance()->getDatabase()->executeChange(Queries::UPDATE_PLAYER, [
            "uuid" => $this->uuid,
            "name" => $this->name,
            "tags" => $this->tags,
            "currenttag" => $this->current_tag
        ]);
        MagicTags::getInstance()->getDatabase()->waitAll();
    }
}
