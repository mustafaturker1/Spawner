<?php

namespace Spawner;

use CLADevs\VanillaX\entities\monster\SkeletonEntity;
use CLADevs\VanillaX\entities\neutral\SpiderEntity;
use CLADevs\VanillaX\entities\passive\CowEntity;
use CLADevs\VanillaX\entities\passive\PigEntity;
use CLADevs\VanillaX\entities\passive\SheepEntity;
use pocketmine\entity\Location;
use Spawner\provider\SQLite;

class Entity
{

    /**
     * @var CowEntity|SkeletonEntity|SheepEntity|PigEntity|SpiderEntity
     */
    private static CowEntity|SkeletonEntity|SheepEntity|PigEntity|SpiderEntity $entity;

    public static function init(): void
    {
        foreach (SQLite::getDatabase()->getSpawners() as $spawner) {
            if (Main::getInstance()->getServer()->getPlayerExact($spawner["spawnerOwner"])) {
                if ($level = Main::getInstance()->getServer()->getWorldManager()->getWorldByName($spawner["world"])) {
                    $location = new Location($spawner["x"], $spawner["y"], $spawner["z"], $level, 0, 0);
                    if (in_array($spawner["mobType"], Main::getSpawnerCategories())) {
                        self::$entity = match ($spawner["mobType"]) {
                            "Cow" => new CowEntity($location),
                            "Sheep" => new SheepEntity($location),
                            "Pig" => new PigEntity($location),
                            "Spider" => new SpiderEntity($location),
                            "Skeleton" => new SkeletonEntity($location),
                        };
                        self::$entity->setNameTag("§7x1 " . $spawner["mobType"]);
                        self::$entity->setNameTagVisible();
                        self::$entity->spawnToAll();
                    }
                }
            }
        }
    }
}