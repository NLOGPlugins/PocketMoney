<?php
namespace PocketMoney\event;

use pocketmine\event\Cancellable;
use pocketmine\Player;
use pocketmine\plugin\Plugin;

class SetMoneyEvent extends PocketMoneyEvent implements Cancellable {
    public static $handlerList = null;

    /**
     * SetMoneyEvent constructor.
     * @param Plugin $plugin
     * @param string $player
     * @param int $amount
     * @param $issuer
     */
    public function __construct(Plugin $plugin, string $player, int $amount, $issuer = null) {
        parent::__construct($plugin, $player, $amount, $issuer);
    }

}
