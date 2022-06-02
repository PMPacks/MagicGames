<?php

declare(strict_types=1);

namespace NgLamVN\InvCraft;

use pocketmine\Server;
use pocketmine\utils\Config;

class Provider
{
	/** @var Config $config */
	public Config $config;
	/** @var array $recipes */
	public array $recipes;
	/** @var Config */
	public Config $msg;

	//YamlProvider "I am noob at MySQL or SQLite"

	public function __construct()
	{
		//NOTHING.
	}

	public function open(): void
	{
		$this->config = new Config($this->getLoader()->getDataFolder() . "recipes.yml", Config::YAML);
		$this->recipes = $this->config->getAll();
		$this->getLoader()->saveResource("message.yml");
		$this->msg = new Config($this->getLoader()->getDataFolder() . "message.yml", Config::YAML);
	}

	public function getLoader(): Loader
	{
		return Loader::getInstance();
	}

	/**
	 * @throws \JsonException
	 */
	public function save(): void
	{
		$this->config->setAll($this->recipes);
		$this->config->save();
	}

	public function getRecipesData(): array
	{
		return $this->recipes;
	}

	public function getRecipeData(string $name): ?array
	{
		if (isset($this->recipes[$name])) return $this->recipes[$name];
		return null;
	}

	public function setRecipesData(array $recipes): void
	{
		$this->recipes = $recipes;
	}

	public function setRecipeData(string $name, array $data): void
	{
		$this->recipes[$name] = $data;
	}

	public function removeRecipeData(string $name): void
	{
		unset($this->recipes[$name]);
	}

	public function getMessage(string $msg): mixed
	{
		return $this->msg->get($msg);
	}
}
