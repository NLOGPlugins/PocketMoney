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
     * @var null|PocketMoney $api
     */
    private static $api = null;
    /**
     * @var array
     */
    private $message, $config, $money;

    public function onLoad() {
        @mkdir($this->getDataFolder());
        $this->saveResource("message.yml");
        $this->saveResource("config.yml");
        $this->message = (new Config($this->getDataFolder()."message.yml", Config::YAML))->getAll();
        $this->config = (new Config($this->getDataFolder()."config.yml", Config::YAML))->getAll();
        $this->money = (new Config($this->getDataFolder()."money.yml", Config::YAML))->getAll();
    }

    public function onEnable() {
        self:$api = $this;
        $this->getServer()->getCommandMap()->register("money", new MoneyCommand($this));
        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
        $this->getServer()->getScheduler()->scheduleDelayedRepeatingTask(new AutoSaveTask($this), 20*60*1, 20*60*1);
    }

    public function onDisable() {
        $this->saveAll();
    }

    public function saveAll() {
        $money_config = new Config($this->getDataFolder()."money.yml", Config::YAML);
        $money_config->setAll($this->money);
        $money_config->save();
    }

    /**
     * @return null|PocketMoney
     */
    public static function getApi() :?PocketMoney {
        return self::$api;
    }
    
    /**
     * @param string $key
     * @return string|null
     */
    public function getMessage(string $key) :?string {
        return $this->message[$key] ?? null;
    }
    
    /**
     * @param string $key
     * @return string|null
     */
    public function getSetting(string $key) :?string  {
        return $this->config[$key] ?? null;
    }
    
    /**
     * 
     * @return array
     */
    public function getRanks() :array{
    	arsort($this->money);
    	$addOp = $this->config["rank-add-op"];
    	$ops = $this->getServer()->getOps()->getAll();
    	$banned = [];
    	foreach ($this->getServer()->getNameBans()->getEntries() as $entry) {
    		$banned[] = $entry->getName();
    	}
    	$temp = [];
    	foreach ($this->money as $p => $m) {
    		if (isset($banned[$p])) {
    			continue;
    		}
    		if (!$addOp && isset($ops[$p])) {
    			continue;
    		}
    		$temp[] = $p;
    	};
    	$result = [];
    	$i = count($temp);
    	foreach ($temp as $p) {
    		$result[$i--] = $p;
    	}
    	return $result;
    }
    
    /**
     * 
     * @param string|Player $player
     * @return int|null
     */
    public function getRank($player) :?int {
    	$result = $this->getRanks();
    	$player = $player instanceof Player ? $player->getName() : $player;
    	if ($this->existsAccount($player)) {
    		foreach ($result as $rank => $name) {
    			if ($name === $player) {
    				return $rank;
    			}
    		}
    	}
    	return null;
    }
    
    public function getRankMaxPage(int $banned_cnt) :int {
        $result = (int)ceil((count($this->money) - $banned_cnt) / 10);
        return $result < 1 ? 1 : $result;
    }
    
    /**
     * @param int $page
     * @return array
     */
    public function getRankPage(int $page) :array {
    	$result = [];
    	$ranks = $this->getRanks();
    	$max = $this->getRankMaxPage(count($banned));
    	$i = 1;
    	$page = $page < 1 ? 1 : ($page > $max ? $max : $page);
    	foreach ($ranks as $rank => $player) {
    		if (((int) ceil($i / 10)) == $page) {
    			$result[$i] = $player;
    		}
    	}
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
        return (int) $result;
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
     * @return array
     */
    public function getAllMoney() :array {
        return $this->money;
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
