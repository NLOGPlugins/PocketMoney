<?php

namespace PocketMoney\command\subcommand;

use pocketmine\command\CommandSender;
use pocketmine\permission\Permission;
use pocketmine\plugin\Plugin;

class PaySubCommand extends SubCommand {

    public function __construct(Plugin $owner) {
        parent::__construct("pay", "돈을 지불합니다.", "pay <player> <amount>", ["p"], new Permission("pocketmoney.command.pay"), $owner);
    }

    public  function execute(CommandSender $sender, array $args): bool {
        if (!isset($args[0], $args[1]) || !is_numeric($args[1])) return false;
        $player = $this->getPlugin()->getServer()->getPlayer($args[0]);
        if (is_null($player)) $player = $args[0];
        if ($this->getPlugin()->addMoney($player, $args[1], $sender) && $this->getPlugin()->reduceMoney($sender, $args[1], $sender)) {
            $sender->sendMessage(str_ireplace(["{ISSUER}", "{AMOUNT}", "{PLAYER}"], [$sender->getName(), $args[1], $player->getName()], $this->getPlugin()->getMessage("pay-command")));
            $player->sendMessage(str_ireplace(["{ISSUER}", "{AMOUNT}", "{PLAYER}"], [$sender->getName(), $args[1], $player->getName()], $this->getPlugin()->getMessage("pay-command")));
            return true;
        } else {
            $sender->sendMessage($this->getPlugin()->getMeesage("command-error"));
            return false;
        }
    }

}