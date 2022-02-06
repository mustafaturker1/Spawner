<?php

namespace Spawner;

use pocketmine\permission\Permission;
use pocketmine\permission\PermissionManager;
use pocketmine\plugin\PluginBase;
use Spawner\command\SpawnerGiveCommand;
use Spawner\event\EventListener;
use Spawner\provider\SQLite;
use Spawner\task\SpawnerTask;

class Main extends PluginBase
{

    /**
     * @var int
     */
    private static int $time = 12;

    /**
     * @var array|string[]
     */
    private static array $spawner_categories = ["Cow", "Sheep", "Pig", "Spider", "Skeleton"];

    /**
     * @var Main
     */
    private static Main $instance;

    /**
     * @return Main
     */
    public static function getInstance(): Main
    {
        return self::$instance;
    }

    /**
     * @return array
     */
    public static function getSpawnerCategories(): array
    {
        return self::$spawner_categories;
    }

    protected function onEnable(): void
    {
        self::$instance = $this;
        new SQLite();
        PermissionManager::getInstance()->addPermission(new Permission("spawner.give.command", "Spawner give permission."));
        $this->getServer()->getPluginManager()->registerEvents(new EventListener(), $this);
        $this->getServer()->getCommandMap()->register("spawner", new SpawnerGiveCommand());
        $this->getScheduler()->scheduleRepeatingTask(new SpawnerTask(), 20 * self::$time);
    }
}