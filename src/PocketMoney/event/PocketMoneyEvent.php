<?php
namespace PocketMoney\event;

use pocketmine\event\plugin\PluginEvent;
use pocketmine\Player;
use pocketmine\plugin\Plugin;

class PocketMoneyEvent extends PluginEvent {
    /**
     * @var Player $player
     * @var int $amount
     */
    private $player, $amount, $issuer;

    /**
     * PocketMoneyEvent constructor.
     * @param Plugin $plugin
     * @param Player $player
     * @param int $amount
     * @param $issuer
     */
    public function __construct(Plugin $plugin, Player $player, int $amount, $issuer = null) {
        parent::__construct($plugin);
    }

    /**
     * @return Player
     */
    public function getPlayer() :Player {
        return $this->player;
    }

    /**
     * @param Player $player
     */
    public function setPlayer(Player $player) {
        $this->player = $player;
    }

    /**
     * @return int
     */
    public function getAmount() :int {
        return $this->amount;
    }

    /**
     * @param int $amount
     */
    public function setAmount($amount) {
        $this->amount = $amount;
    }

    /**
     * @return mixed
     */
    public function getIssuer() {
        return $this->issuer;
    }

}