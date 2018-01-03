<?php
namespace PocketMoney\command\subcommand;

use pocketmine\command\CommandSender;
use pocketmine\permission\Permission;
use pocketmine\plugin\Plugin;

class AddSubCommand extends SubCommand {

    public function __construct(Plugin $owner) {
        parent::__construct("add", "플레이어의 돈을 추가합니다.", "add <player> <amount>", ["a"], new Permission("pocketmoney.command.add"), $owner);
    }

    public  function execute(CommandSender $sender, array $args): bool {
        if (!isset($args[0], $args[1]) || !is_numeric($args[1])) return false;
        $player = $this->getPlugin()->getServer()->getPlayer($args[0]);
        if (is_null($player)) $player = $args[0];
        if ($this->getPlugin()->addMoney($player, $args[1], $sender)) {
            $sender->sendMessage(str_ireplace(["{ISSUER}", "{AMOUNT}", "{PLAYER}"], [$sender->getName(), $args[1], $player->getName()], $this->getPlugin()->getMessage("add-command")));
            $player->sendMessage(str_ireplace(["{ISSUER}", "{AMOUNT}", "{PLAYER}"], [$sender->getName(), $args[1], $player->getName()], $this->getPlugin()->getMessage("add-command")));
            return true;
        } else {
            $sender->sendMessage($this->getPlugin()->getMeesage("command-error"));
            return false;
        }
    }
}