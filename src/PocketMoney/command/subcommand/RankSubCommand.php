<?php
namespace PocketMoney\command\subcommand;

use pocketmine\command\CommandSender;
use pocketmine\permission\Permission;
use pocketmine\plugin\Plugin;
use PocketMoney\task\RankSortAsyncTask;

class RankSubCommand extends SubCommand {

    public function __construct(Plugin $owner) {
        parent::__construct("rank", "돈 순위", "rank <page>", ["r"], new Permission("pocketmoney.command.rank"), $owner);
    }

    public  function execute(CommandSender $sender, array $args): bool {
        //TODO: see rank
        $banned = [];
        foreach($this->getPlugin()->getServer()->getNameBans()->getEntries() as $entry){
            if($this->getPlugin()->existsAccount($entry->getName())){
                $banned[] = $entry->getName();
            }
        }
        $ops = [];
        foreach($this->getPlugin()->getServer()->getOps()->getAll() as $op){
            if($this->getPlugin()->existsAccount($op)){
                $ops[] = $op;
            }
        }
        $task = new RankSortAsyncTask($sender->getName(), $this->getPlugin()->getAllMoney(), isset($args[0]) ? (int)$args[0] : 1, $this->getPlugin()->getSetting("rank-add-op"), $ops, $banned);
        $this->getPlugin()->getServer()->getScheduler()->scheduleAsyncTask($task);
        return true;
    }

}