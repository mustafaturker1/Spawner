<?php

namespace Spawner\provider;

use Spawner\Main;
use SQLite3;

class SQLite
{

    /**
     * @var SQLite
     */
    private static SQLite $database;

    /**
     * @var SQLite3
     */
    private SQLite3 $spawner;

    public function __construct()
    {
        self::$database = $this;
        $this->spawner = new SQLite3(Main::getInstance()->getDataFolder() . "Spawner.db");
        $this->spawner->exec("CREATE TABLE IF NOT EXISTS Spawner(spawnerOwner String, x INT, y INT, z INT, world String, mobType String)");
    }

    /**
     * @param string $playerName
     * @param int $x
     * @param int $y
     * @param int $z
     * @param string $world
     * @param string $mobType
     */
    public function addSpawner(string $playerName, int $x, int $y, int $z, string $world, string $mobType)
    {
        $data = $this->spawner->prepare("INSERT INTO Spawner(spawnerOwner, x, y, z, world, mobType) VALUES(:spawnerOwner, :x, :y, :z, :world, :mobType)");
        $data->bindValue(":spawnerOwner", $playerName);
        $data->bindValue(":x", $x);
        $data->bindValue(":y", $y);
        $data->bindValue(":z", $z);
        $data->bindValue(":world", $world);
        $data->bindValue(":mobType", $mobType);
        $data->execute();
    }

    /**
     * @param int $x
     * @param int $y
     * @param int $z
     * @param string $world
     */
    public function removeSpawner(int $x, int $y, int $z, string $world)
    {
        $data = $this->spawner->prepare("DELETE FROM Spawner WHERE x = :x AND y = :y AND z = :z AND world = :world");
        $data->bindValue(":x", $x);
        $data->bindValue(":y", $y);
        $data->bindValue(":z", $z);
        $data->bindValue(":world", $world);
        $data->execute();
    }

    /**
     * @return array
     */
    public function getSpawners(): array
    {
        $data = $this->spawner->prepare("SELECT * FROM Spawner");
        $control = $data->execute();
        $array = [];

        while ($rows = $control->fetchArray()) {
            $array[] = $rows;
        }
        return $array;
    }

    /**
     * @return SQLite
     */
    public static function getDatabase(): SQLite
    {
        return self::$database;
    }
}