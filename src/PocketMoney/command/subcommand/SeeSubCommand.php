<?php
namespace PocketMoney\command\subcommand;

use pocketmine\command\CommandSender;
use pocketmine\permission\Permission;
use pocketmine\plugin\Plugin;

class SeeSubCommand extends SubCommand {

    /**
     * SeeSubCommand constructor.
     * @param Plugin $owner
     */
    public function __construct(Plugin $owner) {
        parent::__construct("see", "플레이어의 돈을 확인합니다.", "see <player>", [], new Permission("pocketmoney.command.see"), $owner);
    }

    /**
     * @param CommandSender $sender
     * @param array $args
     * @return bool
     */
    public  function execute(CommandSender $sender, array $args): bool {
        if (!isset($args[0])) return false;
        $player = $this->getPlugin()->getServer()->getPlayer($args[0]);
        $amount = $this->getPlugin()->getMoney($player->getName());
        if (is_null($player)) $player = $args[0];
        $sender->sendMessage(str_ireplace(["{AMOUNT}", "{PLAYER}"], [$amount, $player->getName()], $this->getPlugin()->getMessage("see-command")));
        return true;
    }

}