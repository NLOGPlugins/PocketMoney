<?php
namespace PocketMoney;

use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use PocketMoney\command\MoneyCommand;
use PocketMoney\event\ReduceMoneyEvent;
use PocketMoney\event\SetMoneyEvent;
use PocketMoney\task\AutoSaveTask;

class PocketMoney extends PluginBase {
    /**
     * @var PocketMoney $api
     */
    private static $api;
    /**
     * @var array $message
     * @var array $config
     * @var Config $money
     */
    private $message, $config, $money;

    public function onEnable() {
        self:$api = $this;
        $this->getServer()->getCommandMap()->register("money", new MoneyCommand($this));
        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
        $this->getServer()->getScheduler()->scheduleDelayedRepeatingTask(new AutoSaveTask($this), 20*60*1, 20*60*1);
    }

    public function onDisable() {
        $this->saveAll();
    }

    public function onLoad() {
        @mkdir($this->getDataFolder());
        $this->saveResource("message.yml");
        $this->saveResource("config.yml");
        $this->message = (new Config($this->getDataFolder()."message.yml", Config::YAML))->getAll();
        $this->config = (new Config($this->getDataFolder()."config.yml", Config::YAML))->getAll();
        $this->money = (new Config($this->getDataFolder()."money.yml", Config::YAML))->getAll();
    }

    public function saveAll() {

    }

    /**
     * @return PocketMoney
     */
    public static function getApi() :PocketMoney {
        return self::$api;
    }

    /**
     * @param string $key
     * @return string|null
     */
    public function getMessage(string $key) {
        return $this->message[$key];
    }

    public function  getSetting(string $key) {
        return $this->config[$key];
    }

    public function getAllMoney() :array {
        return $this->money;
    }

    public function getRankMaxPage(int $banned_cnt) :int {
        return (int)ceil((count($this->money) - $banned_cnt) / 10);
    }

    /**
     * @param int $page
     * @return array
     */
    public function getRank(int $page) :array {
        arsort($this->money);
        $addOp = $this->config["rank-add-op"];
        $ops = $this->getServer()->getOps()->getAll();
        $banned = [];
        foreach ($this->getServer()->getNameBans()->getEntries() as $entry) $banned[] = $entry->getName();
        $max = $this->getRankMaxPage(count($banned));
        $page = $page < 1 ? 1 : ($page > $max ? $max : $page);
        $result = [];
        $i = 1;
        foreach ($this->money as $p => $m) {
            if (isset($banned[$p])) continue;
            if (!$addOp && isset($ops[$p])) continue;
            if (((int)ceil($i / 10)) == $page) $result[$i] = [$p, $m];
            $i++;
        };
        return $result;
    }

    /**
     * @param Player|string $player
     * @return int|bool
     */
    public function getMoney($player) {
        $player = $player instanceof Player ? $player->getName() : $player;
        $result = $this->money[$player];
        if ($result === false || $result == null) return false;
        return (int)$result;
    }

    /**
     * @param Player|string $player
     * @param int $amount
     * @param $issuer
     * @return bool
     */
    public function setMoney($player, int $amount, $issuer = null) :bool {
        if ($amount < 0) return false;
        if (!$this->existsAccount($player)) $this->createAccount($player);
        $player = $player instanceof Player ? $player : $this->getServer()->getPlayer($player);
        $event = new SetMoneyEvent($this, $player, $amount, $issuer);
        $this->getServer()->getPluginManager()->callEvent($event);
        if ($event->isCancelled()) return false;
        $amount = $event->getAmount();
        $player = $event->getPlayer();
        $this->money[$player->getName()] = $amount;
        return true;
    }

    /**
     * @param Player|string $player
     * @param int $amount
     * @param $issuer
     * @return bool
     */
    public function addMoney($player, int $amount, $issuer = null) :bool {
        if ($amount < 0) return false;
        if (!$this->existsAccount($player)) $this->createAccount($player);
        $player = $player instanceof Player ? $player : $this->getServer()->getPlayer($player);
        $event = new SetMoneyEvent($this, $this->getServer()->getPlayer($player), $amount, $issuer);
        $this->getServer()->getPluginManager()->callEvent($event);
        if ($event->isCancelled()) return false;
        $amount = $event->getAmount();
        $player = $event->getPlayer();
        $now = $this->money[$player->getName()];
        $this->money[$player->getName()] = $now+$amount;
    }

    /**
     * @param Player|string $player
     * @param int $amount
     * @param $issuer
     * @return bool
     */
    public function reduceMoney($player, int $amount, $issuer = null) :bool {
        if ($amount < 0) return false;
        if (!$this->existsAccount($player)) $this->createAccount($player);
        $player = $player instanceof Player ? $player : $this->getServer()->getPlayer($player);
        $event = new ReduceMoneyEvent($this, $this->getServer()->getPlayer($player), $amount, $issuer);
        $this->getServer()->getPluginManager()->callEvent($event);
        if ($event->isCancelled()) return false;
        $amount = $event->getAmount();
        $player = $event->getPlayer();
        $now = $this->money[$player->getName()];
        $this->money[$player->getName()] = $now-$amount;
        return true;
    }

    /**
     * @param Player|string $player
     * @return bool
     */
    public function createAccount($player) :bool {
        if ($this->existsAccount($player)) return false;
        $player = $player instanceof Player ? $player->getName() : $player;
        $default = $this->config["default-money"];
        $this->money[$player] = $default;
        return true;
    }

    /**
     * @param Player|string $player
     * @return bool
     */
    public function existsAccount($player) :bool {
        $player = $player instanceof Player ? $player->getName() : $player;
        return isset($this->money[$player]);
    }

}