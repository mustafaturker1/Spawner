<?php

namespace Spawner\command;

use pocketmine\block\BlockLegacyIds;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\item\ItemFactory;
use pocketmine\player\Player;
use Spawner\Main;

class SpawnerGiveCommand extends Command
{
    public function __construct()
    {
        parent::__construct("spawner", "Spawner give command!");
        $this->setPermission("spawner.give.command");
        $this->setPermissionMessage("§cYou are not authorized to use this command!");
        $this->setUsage("§fUsage: §7/spawner <player> <spawnerType> <count>");
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     * @return bool
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): bool
    {
        if (!$sender->hasPermission($this->getPermission())) {
            $sender->sendMessage($this->getPermissionMessage());
            return false;
        }
        if (!(isset($args[0]) and isset($args[1]) and isset($args[2]))) {
            $sender->sendMessage($this->getUsage());
            return false;
        }
        $player = Main::getInstance()->getServer()->getPlayerExact($args[0]);
        if (!$player instanceof Player) {
            $sender->sendMessage("§cPlayer is not active!");
            return false;
        }
        if (Main::getSpawnerCategories() !== null) {
            if (!in_array($args[1], Main::getSpawnerCategories())) {
                $sender->sendMessage("§cSpawner type not found!");
                return false;
            }
            if (!is_numeric($args[2])) {
                $sender->sendMessage("§cEnter numeric value!");
                return false;
            }
            if (strstr($args[2], "-") || strstr($args[2], ".") || strstr($args[2], ",")) {
                $sender->sendMessage("§cDo not use invalid characters!");
                return false;
            }
            $item = ItemFactory::getInstance()->get(BlockLegacyIds::MOB_SPAWNER);
            $item->setCount($args[2]);
            if (!$player->getInventory()->canAddItem($item)) {
                $sender->sendMessage("§cPlayer's inventory is full!");
                return false;
            }
            $item->setCustomName("§b" . $args[1] . " Spawner");
            $item->getNamedTag()->setString("type", $args[1]);
            $player->getInventory()->addItem($item);
            $sender->sendMessage("§aPlayer was given a spawner!");
            $player->sendMessage("§aAdded spawner to your inventory!");
        }
        return true;
    }
}