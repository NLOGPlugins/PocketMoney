<?php
namespace PocketMoney\command\subcommand;

use pocketmine\command\CommandSender;
use pocketmine\permission\Permission;
use pocketmine\plugin\Plugin;

class RankSubCommand extends SubCommand {

    public function __construct(Plugin $owner) {
        parent::__construct("rank", "돈 순위", "rank <page>", ["r"], new Permission("pocketmoney.command.rank"), $owner);
    }

    public function execute(CommandSender $sender, array $args): bool {
        //TODO: see rank
        $page = isset($args[0]) ? (int) $args[0] : 1;
        $max = count($this->getPlugin()->getServer()->getNameBans()->getEntries());
        $message = str_ireplace(["{PAGE}", "{MAX}"], [$page, $this->getPlugin()->getRankMaxPage($max)], $this->getPlugin()->getMessage("rank-prefix"));
        $rank = $this->getPlugin()->getRankPage($page);
        if ($rank === null) return false;
        foreach ($rank as $rank => $name) {
            $message .= "\n".str_ireplace(["{RANK}", "{PLAYER}", "{MONEY}"], [$rank, $name, $this->getPlugin()->getMoney($name)], $this->getPlugin()->getMessage("rank-format"));
		}
        $sender->sendMessage($message);
        return true;
    }
}

