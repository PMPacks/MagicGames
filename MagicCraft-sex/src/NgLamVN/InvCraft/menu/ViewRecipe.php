<?php

declare(strict_types=1);

namespace NgLamVN\InvCraft\menu;

use Closure;
use muqsit\invmenu\InvMenu;
use NgLamVN\InvCraft\Loader;
use pocketmine\Server;
use NgLamVN\InvCraft\Recipe;
use pocketmine\player\Player;
use pocketmine\item\ItemFactory;
use muqsit\invmenu\type\InvMenuTypeIds;
use muqsit\invmenu\transaction\InvMenuTransaction;
use muqsit\invmenu\transaction\InvMenuTransactionResult;

class ViewRecipe extends BaseMenu
{
	const VIxVI_PROTECTED_SLOT = [6, 7, 8, 15, 16, 17, 24, 25, 26, 33, 35, 42, 43, 44, 51, 52, 53];
	const VIxVI_RESULT_SLOT = 34;
	const IIIxIII_PROTECTED_SLOT = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 14, 15, 16, 17, 18, 19, 23, 24, 26, 27, 28, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53];
	const IIIxIII_FILL_SLOT = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 14, 15, 16, 17, 18, 19, 23, 24, 26, 27, 28, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44];
	const IIIxIII_RESULT_SLOT = 25;

	/** @var Recipe $recipe */
	public Recipe $recipe;

	public function __construct(Player $player, Loader $loader, Recipe $recipe)
	{
		$this->recipe = $recipe;
		$mode = $recipe->getMode();
		parent::__construct($player, $loader, $mode);
	}

	public function menu(Player $player): void
	{
		$this->menu = InvMenu::create(InvMenuTypeIds::TYPE_DOUBLE_CHEST);
		$this->menu->setName($this->getLoader()->getProvider()->getMessage("menu.view"));
		$this->menu->setListener(Closure::fromCallable([$this, "MenuListener"]));
		$inv = $this->menu->getInventory();

		$ids = explode(":", $this->getLoader()->getProvider()->getMessage("menu.item"));
		$item = ItemFactory::getInstance()->get((int) $ids[0], (int) $ids[1]);
		for ($i = 0; $i <= 53; $i++) {
			if (in_array($i, $this->getFillSlot())) {
				$inv->setItem($i, $item);
			}
		}
		$this->pasteRecipe($this->recipe);

		$this->menu->send($player);
	}

	public function getFillSlot(): array
	{
		if ($this->getMode() == self::IIIxIII_MODE) {
			return self::IIIxIII_FILL_SLOT;
		}
		return self::VIxVI_PROTECTED_SLOT;
	}
	public function getProtectedSlot(): array
	{
		if ($this->getMode() == self::IIIxIII_MODE) {
			return self::IIIxIII_PROTECTED_SLOT;
		}
		return self::VIxVI_PROTECTED_SLOT;
	}

	public function pasteRecipe(Recipe $recipe): void
	{
		$recipe_data = $recipe->getRecipeData();
		$result = $recipe->getResultItem();
		$inv = $this->menu->getInventory();
		$inv->setItem($this->getResultSlot(), $result);
		$inv->setItem(45, ItemFactory::getInstance()->get(1081, 0, 1)->setCustomName("§r"));
		$inv->setItem(46, ItemFactory::getInstance()->get(1081, 0, 1)->setCustomName("§r"));
		$inv->setItem(47, ItemFactory::getInstance()->get(1081, 0, 1)->setCustomName("§r"));
		$inv->setItem(48, ItemFactory::getInstance()->get(1083, 0, 1)->setCustomName("§r§c§lCLOSE\n\n§r§7Click To Close"));
		$inv->setItem(49, ItemFactory::getInstance()->get(1084, 0, 1)->setCustomName("§r§d§lRECIPES\n\n§r§7Click To View Custom Recipes"));
		$inv->setItem(50, ItemFactory::getInstance()->get(1082, 0, 1)->setCustomName("§r§d§lCUSTOM CRAFTING TABLE\n\n§r§7Click To Open Custom Table"));
		$inv->setItem(51, ItemFactory::getInstance()->get(1081, 0, 1)->setCustomName("§r"));
		$inv->setItem(52, ItemFactory::getInstance()->get(1081, 0, 1)->setCustomName("§r"));
		$inv->setItem(53, ItemFactory::getInstance()->get(1081, 0, 1)->setCustomName("§r"));
		$j = 0;
		for ($i = 0; $i <= 52; $i++) {
			if (!in_array($i, $this->getProtectedSlot()))
				if ($i !== $this->getResultSlot()) {
					$inv->setItem($i, $recipe_data[$j]);
					$j++;
				}
		}
	}

	public function getResultSlot(): int
	{
		if ($this->getMode() == self::IIIxIII_MODE) {
			return self::IIIxIII_RESULT_SLOT;
		}
		return self::VIxVI_RESULT_SLOT;
	}

	public function MenuListener(InvMenuTransaction $transaction): InvMenuTransactionResult
	{
	  $player = $transaction->getPlayer();
		if ($transaction->getAction()->getSlot() === 48) {
	      $player->removeCurrentWindow();
		}
		if ($transaction->getAction()->getSlot() === 49) {
			 $player->removeCurrentWindow();
		   Server::getInstance()->dispatchCommand($player, "recipes");
		}
		if ($transaction->getAction()->getSlot() === 50) {
			 $player->removeCurrentWindow();
			 Server::getInstance()->dispatchCommand($player, "invcraft");
		}
		return $transaction->discard();
	}
}
