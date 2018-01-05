<?php
namespace PocketMoney\command\subcommand;

use pocketmine\command\CommandSender;
use pocketmine\permission\Permission;
use pocketmine\plugin\Plugin;
use pocketmine\Player;

class SetSubCommand extends SubCommand {

    /**
     * SetSubCommand constructor.
     * @param Plugin $owner
     */
    public function __construct(Plugin $owner) {
        parent::__construct("set", "플레이어의 돈을 설정합니다.", "set <player> <amount>", ["a"], new Permission("pocketmoney.command.set"), $owner);
    }

    /**
     * @param CommandSender $sender
     * @param array $args
     * @return bool
     */
    public  function execute(CommandSender $sender, array $args): bool {
        if (!isset($args[0], $args[1]) || !is_numeric($args[1])) return false;
        $player = $args[0];
        if (is_null($player)) $player = $args[0];
        if ($this->getPlugin()->setMoney($player, $args[1], $sender)) {
            $sender->sendMessage(str_ireplace(["{ISSUER}", "{AMOUNT}", "{PLAYER}"], [$sender->getName(), $args[1], $player], $this->getPlugin()->getMessage("set-command")));
            if ($player = $this->owner->getServer()->getPlayer($player) instanceof Player) {
				$player->sendMessage(str_ireplace(["{ISSUER}", "{AMOUNT}", "{PLAYER}"], [$sender->getName(), $args[1], $player], $this->getPlugin()->getMessage("set-command")));
            }
			return true;
        } else {
            $sender->sendMessage($this->getPlugin()->getMeesage("command-error"));
            return false;
        }
    }
}