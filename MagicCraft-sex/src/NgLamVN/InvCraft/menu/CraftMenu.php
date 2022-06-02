<?php

declare(strict_types=1);

namespace NgLamVN\InvCraft\menu;

use Closure;
use pocketmine\Server;
use pocketmine\item\Item;
use muqsit\invmenu\InvMenu;
use NgLamVN\InvCraft\Recipe;
use pocketmine\item\ItemIds;
use pocketmine\player\Player;
use pocketmine\item\ItemFactory;
use JinodkDevTeam\utils\ItemUtils;
use pocketmine\inventory\Inventory;
use muqsit\invmenu\type\InvMenuTypeIds;
use muqsit\invmenu\transaction\InvMenuTransaction;
use muqsit\invmenu\transaction\InvMenuTransactionResult;

class CraftMenu extends BaseMenu
{
	const VIxVI_PROTECTED_SLOT = [6, 7, 8, 15, 16, 17, 24, 25, 26, 33, 35, 42, 43, 44, 51, 52, 53];
	const VIxVI_RESULT_SLOT = 34;
	const IIIxIII_PROTECTED_SLOT = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 14, 15, 16, 17, 18, 19, 23, 24, 26, 27, 28, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53];
	const IIIxIII_FILL_SLOT = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 14, 15, 16, 17, 18, 19, 23, 24, 26, 27, 28, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44];
	const IIIxIII_RESULT_SLOT = 25;

	protected ?Recipe $correct_recipe = null;

	public function menu(Player $player): void
	{
		$this->menu = InvMenu::create(InvMenuTypeIds::TYPE_DOUBLE_CHEST);
		if ($this->mode == self::VIxVI_MODE) {
			$this->menu->setName($this->getLoader()->getProvider()->getMessage("menu.craft6x6"));
		} else {
			$this->menu->setName($this->getLoader()->getProvider()->getMessage("menu.craft3x3"));
		}
		$this->menu->setListener(Closure::fromCallable([$this, "MenuListener"]));
		$this->menu->setInventoryCloseListener(Closure::fromCallable([$this, "MenuCloseListener"]));
		$inv = $this->menu->getInventory();
		$ids = explode(":", $this->getLoader()->getProvider()->getMessage("menu.item"));
		$item = ItemFactory::getInstance()->get((int) $ids[0], (int) $ids[1]);
		$inv->setItem(45, ItemFactory::getInstance()->get(1081, 0, 1)->setCustomName("§r"));
		$inv->setItem(46, ItemFactory::getInstance()->get(1081, 0, 1)->setCustomName("§r"));
		$inv->setItem(47, ItemFactory::getInstance()->get(1081, 0, 1)->setCustomName("§r"));
		$inv->setItem(48, ItemFactory::getInstance()->get(1083, 0, 1)->setCustomName("§r§c§lCLOSE\n\n§r§7Click To Close"));
		$inv->setItem(49, ItemFactory::getInstance()->get(1084, 0, 1)->setCustomName("§r§d§lRECIPES\n\n§r§7Click To View Custom Recipes"));
		$inv->setItem(50, ItemFactory::getInstance()->get(1082, 0, 1)->setCustomName("§r§d§lCUSTOM CRAFTING TABLE\n\n§r§7Click To Open Custom Table"));
		$inv->setItem(51, ItemFactory::getInstance()->get(1081, 0, 1)->setCustomName("§r"));
		$inv->setItem(52, ItemFactory::getInstance()->get(1081, 0, 1)->setCustomName("§r"));
		$inv->setItem(53, ItemFactory::getInstance()->get(1081, 0, 1)->setCustomName("§r"));
		for ($i = 0; $i <= 53; $i++) {
			if (in_array($i, $this->getFillSlot())) {
				$inv->setItem($i, $item);
			}
		}

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
		if (in_array($transaction->getAction()->getSlot(), $this->getProtectedSlot())) {
			return $transaction->discard();
		}
		if ($transaction->getAction()->getSlot() === $this->getResultSlot()) {
			$result = $this->menu->getInventory()->getItem($this->getResultSlot());
			if ($result->getId() == ItemIds::AIR) {
				return $transaction->discard();
			}
			$this->clearCraftItem();
			return $transaction->continue()->then(function () {
				$recipe_data = $this->makeRecipeData();
				foreach ($this->getLoader()->getRecipes() as $recipe) {
					if ($recipe->isEnough($recipe_data)) {
						if ($recipe->getMode() == $this->getMode()) {
							$this->setResult($recipe->getResultItem());
							$this->correct_recipe = $recipe;
						}
					}
				}
			});
		}
		$slot = $transaction->getAction()->getSlot();
		$nextitem = $transaction->getAction()->getTargetItem();
		$recipe_data = $this->makeRecipeData($slot, $nextitem, $transaction->getAction()->getInventory());
		foreach ($this->getLoader()->getRecipes() as $recipe) {
			if ($recipe->getMode() == $this->getMode()) {
				if ($recipe->isEnough($recipe_data)) {
					$this->setResult($recipe->getResultItem());
					$this->correct_recipe = $recipe;
					return $transaction->continue();
				}
			}
		}
		$this->setResult(ItemFactory::getInstance()->get(0));
		$this->correct_recipe = null;
		return $transaction->continue();
	}

	public function getResultSlot(): int
	{
		if ($this->getMode() == self::IIIxIII_MODE) {
			return self::IIIxIII_RESULT_SLOT;
		}
		return self::VIxVI_RESULT_SLOT;
	}

	public function clearCraftItem(): void
	{
		if ($this->correct_recipe !== null) {
			foreach ($this->correct_recipe->getRecipeData() as $item) {
				ItemUtils::removeItem($this->menu->getInventory(), $item);
			}
			return;
		}
		for ($i = 0; $i <= 53; $i++) {
			if ((!in_array($i, $this->getProtectedSlot())) and ($i !== $this->getResultSlot())) {
				$this->menu->getInventory()->setItem($i, ItemFactory::getInstance()->get(ItemIds::AIR));
			}
		}
	}

	/**
	 * @param null|int  $slot
	 * @param null|Item $nextitem
	 * @param null|Inventory $inventory
	 *
	 * @return Item[]
	 */
	public function makeRecipeData(?int $slot = null, ?Item $nextitem = null, ?Inventory $inventory = null): array
	{
		$recipe_data = [];
		for ($i = 0; $i <= 53; $i++) {
			if (!in_array($i, $this->getProtectedSlot()))
				if ($i !== $this->getResultSlot()) {
					if (($slot !== null) and ($nextitem !== null)) {
						if ($i == $slot) {
							$recipe_data[] = $nextitem;
							continue;
						}
					}
					$item = ($inventory instanceof Inventory ? $inventory : $this->menu->getInventory())->getItem($i);
					$recipe_data[] = $item;
				}
		}
		return $recipe_data;
	}

	public function setResult(Item $item): void
	{
		$this->menu->getInventory()->setItem($this->getResultSlot(), $item);
	}

	public function MenuCloseListener(Player $player, Inventory $inventory): void
	{
		for ($i = 0; $i <= 53; $i++) {
			if (!in_array($i, $this->getProtectedSlot()))
				if ($i !== $this->getResultSlot()) {
					$item = $inventory->getItem($i);
					if ($item->getId() !== ItemIds::AIR)
						$player->getInventory()->addItem($item);
				}
		}
	}
}
