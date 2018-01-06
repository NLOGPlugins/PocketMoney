<?php
namespace PocketMoney;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\plugin\Plugin;

class EventListener implements Listener {
    private $plugin;

    public function __construct(Plugin $owner) {
        $this->plugin = $owner;
    }

    public function onJoin(PlayerJoinEvent $event) {
        if (!$this->plugin->existsAccount($event->getPlayer())) {
            $this->plugin->createAccount($event->getPlayer());
        }
        $name = $event->getPlayer()->getName();
        $plugin = $this->plugin;
        if (!$plugin->existsAccount($name) && !$plugin->createAccount($name)) {
            $event->getPlayer()->sendMessage($this->plugin->getMessage("login-error"));
        }
        $this->plugin->saveAll();
    }

}
