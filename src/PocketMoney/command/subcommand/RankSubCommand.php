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
        $all = $this->getPlugin()->getAllMoney();
        arsort($all);
        $page = isset($args[0]) ? (int)$args[0] : 1;
        $banned = [];
        foreach ($this->getPlugin()->getServer()->getNameBans()->getEntries() as $entry) $banned[] = $entry->getName();
        $max_page = (int)ceil((count($all) - count($banned)) / 10);
        $message = str_ireplace(["{PAGE}", "{MAX}"], [$page, $max_page], $this->getPlugin()->getMessage("rank-prefix"));
        $i = 1;
        foreach ($all as $p => $m) {
            if (!in_array($p, $banned) && ((int)ceil($i / 10)) == $page) $message .= "\n" . str_ireplace(["{RANK}", "{PLAYER}", "{MONEY}"], [$i, $p, $m], $this->getPlugin()->getMessage("rank-format"));
            $i++;
        }
        $sender->sendMessage($message);
        return true;
    }
}

