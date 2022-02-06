<?php

namespace Spawner\task;

use pocketmine\scheduler\Task;
use Spawner\Entity;

class SpawnerTask extends Task
{

    public function onRun(): void
    {
        Entity::init();
    }
}