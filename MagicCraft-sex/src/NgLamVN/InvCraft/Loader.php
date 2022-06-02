<?php

declare(strict_types=1);

namespace NgLamVN\InvCraft;

use pocketmine\item\Item;
use muqsit\invmenu\InvMenu;
use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use JinodkDevTeam\utils\ItemUtils;
use muqsit\invmenu\InvMenuHandler;
use NgLamVN\InvCraft\command\CraftCommand;
use NgLamVN\InvCraft\ui\CraftingTableForm;
use pocketmine\item\enchantment\ItemFlags;
use pocketmine\item\enchantment\Enchantment;
use NgLamVN\InvCraft\command\InvCraftCommand;
use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\event\player\PlayerInteractEvent;

class Loader extends PluginBase implements Listener
{
	public const FAKE_ENCH_ID = -1;
	public const INV_MENU_TYPE_WORKBENCH = "portablecrafting:workbench";

	private static Loader $instance;

	/** @var Provider */
	public Provider $provider;
	/** @var Recipe[] */
	public array $recipes = [];

	public static function getInstance(): Loader
	{
		return self::$instance;
	}

	public function onEnable(): void
	{
		self::$instance = $this;

		if (!InvMenuHandler::isRegistered()) {
			InvMenuHandler::register($this);
		}
		InvMenuHandler::getTypeRegistry()->register(self::INV_MENU_TYPE_WORKBENCH, new CraftingTableInvMenuType());

		$this->getServer()->getPluginManager()->registerEvents($this, $this);

		$this->provider = new Provider();
		$this->provider->open();
		EnchantmentIdMap::getInstance()->register(self::FAKE_ENCH_ID, new Enchantment("Glow", 1, ItemFlags::ALL, ItemFlags::NONE, 1));
		$this->loadRecipes();

		$this->getServer()->getCommandMap()->register("invcraft", new InvCraftCommand($this));
		$this->getServer()->getCommandMap()->register("craft", new CraftCommand($this));
		/*$this->getServer()->getCommandMap()->register("viewrecipe", new ViewCraftCommand($this));*/
	}

	public function loadRecipes(): void
	{
		$data = $this->getProvider()->getRecipesData();
		foreach (array_keys($data) as $recipe_name) {
			$recipe_data = [];
			foreach ($data[$recipe_name]["recipe"] as $item) {
				$recipe_data[] = ItemUtils::fromString($item);
			}
			$result = ItemUtils::fromString($data[$recipe_name]["result"]);
			if (!isset($data[$recipe_name]["mode"])) {
				$mode = Recipe::VIxVI_MODE;
			} else {
				$mode = $data[$recipe_name]["mode"];
			}

			if ($result instanceof Item) {
				$recipe = Recipe::makeRecipe($recipe_name, $recipe_data, $result, $mode);
				$this->setRecipe($recipe);
			}
		}
	}

	public function getProvider(): Provider
	{
		return $this->provider;
	}

	public function setRecipe(Recipe $recipe): void
	{
		$this->recipes[$recipe->getRecipeName()] = $recipe;
	}

	public function onDisable(): void
	{
		$this->saveRecipes();
		$this->getProvider()->save();
	}

	public function saveRecipes(): void
	{
		foreach ($this->getRecipes() as $recipe) {
			$data = [];
			$data["result"] = ItemUtils::toString($recipe->getResultItem());
			$recipe_data = [];
			foreach ($recipe->getRecipeData() as $item) {
				$recipe_data[] = ItemUtils::toString($item);
			}
			$data["recipe"] = $recipe_data;
			$data["mode"] = $recipe->getMode();
			$this->getProvider()->setRecipeData($recipe->getRecipeName(), $data);
		}
	}

	/**
	 * @return Recipe[]
	 */
	public function getRecipes(): array
	{
		if (!isset($this->recipes)) return [];
		return $this->recipes;
	}

	/**
	 * @param string $name
	 * @return Recipe|null
	 *
	 */
	public function getRecipe(string $name): ?Recipe
	{
		if (isset($this->recipes[$name])) return $this->recipes[$name];
		return null;
	}

	public function removeRecipe(Recipe $recipe): void
	{
		unset($this->recipes[$recipe->getRecipeName()]);
		$this->getProvider()->removeRecipeData($recipe->getRecipeName());
	}

	public function onInteract(PlayerInteractEvent $event): void
	{
		$sender = $event->getPlayer();
		$block = $event->getBlock();
		switch ($event->getAction()) {
			case PlayerInteractEvent::LEFT_CLICK_BLOCK:
				break;
			case PlayerInteractEvent::RIGHT_CLICK_BLOCK:
				if ($block->getId() == 58) {
					$event->cancel();
					$sender->sendForm(new CraftingTableForm());
				}
				break;
		}
	}

	public static function WORKBENCH(): InvMenu
	{
		return InvMenu::create(self::INV_MENU_TYPE_WORKBENCH);
	}
}
