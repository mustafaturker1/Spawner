<?php

namespace Spawner\event;

use pocketmine\block\BlockLegacyIds;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\item\ItemFactory;
use Spawner\Main;
use Spawner\provider\SQLite;

class EventListener implements Listener
{

    public function place(BlockPlaceEvent $placeEvent)
    {
        $player = $placeEvent->getPlayer();
        $item = $placeEvent->getItem();
        $block = $placeEvent->getBlock();

        if (!$placeEvent->isCancelled()) {
            $database = SQLite::getDatabase();
            if ($item->getId() == BlockLegacyIds::MOB_SPAWNER) {
                if ($type = $item->getNamedTag()->getString("type")) {
                    if (in_array($type, Main::getSpawnerCategories())) {
                        $database->addSpawner($player->getName(), $block->getPosition()->getX(), $block->getPosition()->getY(), $block->getPosition()->getZ(), $block->getPosition()->getWorld()->getFolderName(), $type);
                        $player->sendMessage("§aSpawner placed!");
                    }
                }
            }
        }
    }

    public function break(BlockBreakEvent $breakEvent)
    {
        $player = $breakEvent->getPlayer();
        $block = $breakEvent->getBlock();

        if (!$breakEvent->isCancelled()) {
            if ($block->getId() == BlockLegacyIds::MOB_SPAWNER) {
                $database = SQLite::getDatabase();
                if (!empty($database->getSpawners())) {
                    foreach ($database->getSpawners() as $spawner) {
                        if ($block->getPosition()->getX() == $spawner["x"] and $block->getPosition()->getY() == $spawner["y"] and $block->getPosition()->getZ() == $spawner["z"] and $block->getPosition()->getWorld()->getFolderName() == $spawner["world"]) {
                            if ($player->getName() != $spawner["spawnerOwner"]) {
                                $player->sendMessage("§cYou can't break this spawner!");
                                return;
                            }
                            $item = ItemFactory::getInstance()->get(BlockLegacyIds::MOB_SPAWNER);
                            $item->setCustomName("§b" . $spawner["mobType"] . " Spawner");
                            $item->getNamedTag()->setString("type", $spawner["mobType"]);
                            if (!$player->getInventory()->canAddItem($item)) {
                                $player->sendMessage("§cThere is no empty space in your inventory!");
                                return;
                            }
                            $player->getInventory()->addItem($item);
                            $database->removeSpawner($block->getPosition()->getX(), $block->getPosition()->getY(), $block->getPosition()->getZ(), $block->getPosition()->getWorld()->getFolderName());
                            $player->sendMessage("§aSpawner removed and added to your inventory!");
                        }
                    }
                }
            }
        }
    }

    public function interact(PlayerInteractEvent $interactEvent)
    {
        $player = $interactEvent->getPlayer();
        $block = $interactEvent->getBlock();
        if (!$interactEvent->isCancelled()) {
            if ($block->getId() == BlockLegacyIds::MOB_SPAWNER) {
                $database = SQLite::getDatabase();
                if (!empty($database->getSpawners())) {
                    foreach ($database->getSpawners() as $spawner) {
                        if ($block->getPosition()->getX() == $spawner["x"] and $block->getPosition()->getY() == $spawner["y"] and $block->getPosition()->getZ() == $spawner["z"] and $block->getPosition()->getWorld()->getFolderName() == $spawner["world"]) {
                            if ($player->getName() == $spawner["spawnerOwner"]) {
                                $player->sendPopup("§b" . $spawner["mobType"] . " Spawner");
                            } else {
                                $player->sendPopup("§b" . $spawner["mobType"] . " Spawner\n§bOwner: " . $spawner["spawnerOwner"]);
                            }
                        }
                    }
                }
            }
        }
    }
}