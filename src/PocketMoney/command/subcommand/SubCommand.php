<?php
namespace PocketMoney\command\subcommand;

use pocketmine\command\CommandSender;
use pocketmine\permission\Permission;
use pocketmine\plugin\Plugin;

abstract class SubCommand {
    /**
     * @var string $name
     * @var string $description
     * @var string $usage
     * @var array $aliases
     * @var Permission $permission
     * @var Plugin $plugin
     */
    private $name, $description, $usage, $aliases, $permission, $plugin;

    /**
     * SubCommand constructor.
     * @param string $name
     * @param string $description
     * @param string $usage
     * @param array $aliases
     * @param Permission|string $permission
     * @param Plugin $owner
     */
    public function __construct(string $name, string $description, string $usage, array $aliases, $permission, Plugin $owner) {
        $this->name = $name;
        $this->description = $description;
        $this->usage = $usage;
        $this->aliases = $aliases;
        $this->permission = is_string($permission) ? new Permission($permission) : $permission;
        $this->plugin = $owner;
    }

    /**
     * @param CommandSender $sender
     * @param array $args
     * @return bool
     */
    public abstract function execute(CommandSender $sender, array $args) :bool;

    /**
     * @return string
     */
    public function getDescription() :string {
        return $this->description;
    }

    /**
     * @return array
     */
    public function getAliases() :array {
        return $this->aliases;
    }

    /**
     * @return string
     */
    public function getName() :string {
        return $this->name;
    }

    /**
     * @return Permission
     */
    public function getPermission() :Permission {
        return $this->permission;
    }

    /**
     * @return string
     */
    public function getUsage() :string {
        return $this->usage;
    }

    /**
     * @param array $aliases
     */
    public function setAliases(array $aliases) {
        $this->aliases = $aliases;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description) {
        $this->description = $description;
    }

    /**
     * @param string $name
     */
    public function setName(string $name) {
        $this->name = $name;
    }

    /**
     * @param Permission|string $permission
     */
    public function setPermission($permission) {
        if (is_string($permission)) $permission = new Permission($permission);
        $this->permission = $permission;
    }

    /**
     * @param string $usage
     */
    public function setUsage(string $usage) {
        $this->usage = $usage;
    }

    /**
     * @return Plugin
     */
    public function getPlugin() :Plugin {
        return $this->plugin;
    }

}

