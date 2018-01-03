<?php
namespace PocketMoney\task;

use pocketmine\command\ConsoleCommandSender;
use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;

class RankSortAsyncTask extends AsyncTask {
    private $sender, $money, $page, $addOp, $ops, $banned, $rank, $max_page;

    /**
     * RankSortAsyncTask constructor.
     * @param string $sender
     * @param array $money
     * @param int $page
     * @param bool $addOp
     * @param array $ops
     * @param array $banned
     */
    public function __construct(string $sender, array $money, int $page = 1, bool $addOp = false, array $ops, array $banned) {
        $this->sender = $sender;
        $this->money = (array)$money;
        $this->page = $page;
        $this->ops = $ops;
        $this->addOp = $addOp;
        $this->banned = $banned;
    }


    public function onRun() {
        $this->rank = serialize($this->getRank());
    }

    /**
     * @return array
     */
    private function getRank() :array {
        arsort($this->money);
        $result = [];
        $this->max_page = (int)ceil(count($this->money) - count($this->banned) - ($this->addOp ? 0 : count($this->ops)) / 10);
        $this->page = $this->page > $this->max_page || $this->page < 1 ? 1 : $this->page;
        $i = 1;
        foreach ($this->money as $player => $money) {
            if (isset($this->banned[$player])) continue;
            if (in_array($player, (array)$this->ops) && $this->addOp) continue;
            $current = (int)ceil($i / 10);
            if ($current == $this->page)
                $result[$i] = [$player, $money];
            else if ($current > $this->page)
                break;
            $i++;
        }
        return $result;
    }

    public function onCompletion(Server $server) {
        $sender = $this->sender == "CONSOLE" ? new ConsoleCommandSender() : $server->getPlayer($this->sender);
        $plugin = $server->getPluginManager()->getPlugin("PocketMoney");
        if ($this->sender != null) {
            $message = str_ireplace(["{PAGE}", "{MAX}"], [$this->page, $this->max_page], $plugin->getMessage("rank-prefix"));
            foreach (unserialize($this->rank) as $i => $arr) $message .= "\n".str_ireplace(["{RANK}", "{PLAYER}", "{MONEY}"], [$i, $arr[0], $arr[1]], $plugin->getMessage("rank-format"));
            $sender->sendMessage($message);
        }
    }

}