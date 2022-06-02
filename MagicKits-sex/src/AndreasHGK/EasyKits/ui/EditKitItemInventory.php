<?php

declare(strict_types=1);

namespace AndreasHGK\EasyKits\ui;

use AndreasHGK\EasyKits\Kit;
use AndreasHGK\EasyKits\libs\muqsit\invmenu\transaction\InvMenuTransaction;
use AndreasHGK\EasyKits\libs\muqsit\invmenu\transaction\InvMenuTransactionResult;
use AndreasHGK\EasyKits\manager\KitManager;
use AndreasHGK\EasyKits\utils\LangUtils;
use AndreasHGK\EasyKits\libs\muqsit\invmenu\InvMenu;
use pocketmine\inventory\Inventory;
use pocketmine\inventory\transaction\action\SlotChangeAction;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\item\ItemFactory;
use pocketmine\nbt\tag\ByteTag;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\player\Player;

class EditKitItemInventory {

    public static function sendTo(Player $player, Kit $kit) : void {
        $menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
        $menu->setName(LangUtils::getMessage("editkit-items-title", true, ["{NAME}" => $kit->getName()]));
        $menu->setInventoryCloseListener(function(Player $player, Inventory $inventory) use($kit) : void{
            $items = [];
            for($i = 0; $i < 36; $i++) {
                $item = $inventory->getItem($i);
                if($item->getId() !== ItemIds::AIR) $items[$i] = $item;
            }
            $armor = [];
            $armorPiece = $inventory->getItem(47);
            if($armorPiece->getId() !== ItemIds::AIR) {
                $armor[3] = $armorPiece;
            }
            $armorPiece = $inventory->getItem(48);
            if($armorPiece->getId() !== ItemIds::AIR) {
                $armor[2] = $armorPiece;
            }
            $armorPiece = $inventory->getItem(50);
            if($armorPiece->getId() !== ItemIds::AIR) {
                $armor[1] = $armorPiece;
            }
            $armorPiece = $inventory->getItem(51);
            if($armorPiece->getId() !== ItemIds::AIR) {
                $armor[0] = $armorPiece;
            }
            $new = clone $kit;

            $new->setItems($items);
            $new->setArmor($armor);

            if($kit->getItems() === $items && $kit->getArmor() === $armor) {
                EditkitMainForm::sendTo($player, $kit);
            }
            if(KitManager::update($kit, $new)) {
                $player->sendMessage(LangUtils::getMessage("editkit-items-succes", true, ["{COUNT}" => count($items) + count($armor), "{NAME}" => $kit->getName()]));
                EditkitMainForm::sendTo($player, KitManager::get($kit->getName()));
            }
        });
        $menu->setListener(function(InvMenuTransaction $transaction) : InvMenuTransactionResult{
            if($transaction->getItemClicked()->getNamedTag()->getTag("immovable") == null) {
                return $transaction->continue();
            }
            return $transaction->discard();
        });
        $menu->getInventory()->setContents($kit->getItems());
        for($i = 36; $i < 54; $i++) {
            switch($i) {
                case 42:
                    $item = ItemFactory::getInstance()->get(ItemIds::STAINED_GLASS, 14, 1);
                    $item->setCustomName(LangUtils::getMessage("editkit-items-lockedname"));
                    $item->setNamedTag(CompoundTag::create()->setTag("immovable", new StringTag("allowed")));
                    $item->setLore([LangUtils::getMessage("editkit-items-helmet")]);
                    $menu->getInventory()->setItem($i, $item);
                    break;
                case 41:
                    $item = ItemFactory::getInstance()->get(ItemIds::STAINED_GLASS, 14, 1);
                    $item->setCustomName(LangUtils::getMessage("editkit-items-lockedname"));
                    $item->setNamedTag(CompoundTag::create()->setTag("immovable", new StringTag("allowed")));
                    $item->setLore([LangUtils::getMessage("editkit-items-chestplate")]);
                    $menu->getInventory()->setItem($i, $item);
                    break;
                case 39:
                    $item = ItemFactory::getInstance()->get(ItemIds::STAINED_GLASS, 14, 1);
                    $item->setCustomName(LangUtils::getMessage("editkit-items-lockedname"));
                    $item->setNamedTag(CompoundTag::create()->setTag("immovable", new StringTag("allowed")));
                    $item->setLore([LangUtils::getMessage("editkit-items-leggings")]);
                    $menu->getInventory()->setItem($i, $item);
                    break;
                case 38:
                    $item = ItemFactory::getInstance()->get(ItemIds::STAINED_GLASS, 14, 1);
                    $item->setCustomName(LangUtils::getMessage("editkit-items-lockedname"));
                    $item->setNamedTag(CompoundTag::create()->setTag("immovable", new StringTag("allowed")));
                    $item->setLore([LangUtils::getMessage("editkit-items-boots")]);
                    $menu->getInventory()->setItem($i, $item);
                    break;
                case 51:
                    $menu->getInventory()->setItem($i, $kit->getArmor()[0] ?? ItemFactory::getInstance()->get(ItemIds::AIR));
                    break;
                case 50:
                    $menu->getInventory()->setItem($i, $kit->getArmor()[1] ?? ItemFactory::getInstance()->get(ItemIds::AIR));
                    break;
                case 48:
                    $menu->getInventory()->setItem($i, $kit->getArmor()[2] ?? ItemFactory::getInstance()->get(ItemIds::AIR));
                    break;
                case 47:
                    $menu->getInventory()->setItem($i, $kit->getArmor()[3] ?? ItemFactory::getInstance()->get(Itemids::AIR));
                    break;
                default:
                    $item = ItemFactory::getInstance()->get(ItemIds::STAINED_GLASS, 14, 1);
                    $item->setCustomName(LangUtils::getMessage("editkit-items-lockedname"));
                    $item->setNamedTag(CompoundTag::create()->setTag("immovable", new StringTag("allowed")));
                    $menu->getInventory()->setItem($i, $item);
                    break;
            }
        }

        $menu->send($player);
    }

}