<?php
namespace PocketMoney\task;

use pocketmine\plugin\Plugin;
use pocketmine\scheduler\PluginTask;

class AutoSaveTask extends PluginTask {

    public function __construct(Plugin $owner) {
        parent::__construct($owner);
    }

    public function onRun(int $currentTick) {
        $this->owner->saveAll();
    }
}