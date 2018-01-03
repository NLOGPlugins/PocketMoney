<?php
namespace PocketMoney\command\subcommand;

use pocketmine\command\CommandSender;
use pocketmine\permission\Permission;
use pocketmine\plugin\Plugin;

class TakeSubCommand extends SubCommand {

    /**
     * TakeSubCommand constructor.
     * @param Plugin $owner
     */
    public function __construct(Plugin $owner) {
        parent::__construct("take", "플레이어의 돈을 빼앗습니다.", "take <player> <amount>", ["t"], new Permission("pocketmoney.command.take"), $owner);
    }

    /**
     * @param CommandSender $sender
     * @param array $args
     * @return bool
     */
    public  function execute(CommandSender $sender, array $args): bool {
        if (!isset($args[0], $args[1]) || !is_numeric($args[1])) return false;
        $player = $this->getPlugin()->getServer()->getPlayer($args[0]);
        if (is_null($player)) $player = $args[0];
        if ($this->getPlugin()->reduceMoney($player, $args[1], $sender)) {
            $sender->sendMessage(str_ireplace(["{ISSUER}", "{AMOUNT}", "{PLAYER}"], [$sender->getName(), $args[1], $player->getName()], $this->getPlugin()->getMessage("take-command")));
            $player->sendMessage(str_ireplace(["{ISSUER}", "{AMOUNT}", "{PLAYER}"], [$sender->getName(), $args[1], $player->getName()], $this->getPlugin()->getMessage("take-command")));
            return true;
        } else {
            $sender->sendMessage($this->getPlugin()->getMeesage("command-error"));
            return false;
        }
    }
}