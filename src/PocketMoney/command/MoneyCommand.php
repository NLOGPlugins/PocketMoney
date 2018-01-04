<?php
namespace PocketMoney\command;

use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\plugin\Plugin;
use PocketMoney\command\subcommand\AddSubCommand;
use PocketMoney\command\subcommand\PaySubCommand;
use PocketMoney\command\subcommand\RankSubCommand;
use PocketMoney\command\subcommand\SeeSubCommand;
use PocketMoney\command\subcommand\SetSubCommand;
use PocketMoney\command\subcommand\SubCommand;
use PocketMoney\command\subcommand\TakeSubCommand;

class MoneyCommand extends PluginCommand {
    private $subCommands = [], $commandObjects = [];

    /**
     * MoneyCommand constructor.
     * @param Plugin $owner
     */
    public function __construct(Plugin $owner) {
        parent::__construct("money", $owner);
        $this->setDescription("돈을 관리합니다.");
        $this->setUsage("/money <set|add|take|see|pay|rank>");
        $this->setPermission("pocketmoney.command");
        $this->loadSubCommand(new SetSubCommand($owner));
        $this->loadSubCommand(new AddSubCommand($owner));
        $this->loadSubCommand(new TakeSubCommand($owner));
        $this->loadSubCommand(new SeeSubCommand($owner));
        $this->loadSubCommand(new PaySubCommand($owner));
        $this->loadSubCommand(new RankSubCommand($owner));
    }

    private function loadSubCommand(SubCommand $command) {
        array_push($this->commandObjects, $command);
        $commandId = count($this->commandObjects) - 1;
        $this->subCommands[$command->getName()] = $commandId;
        foreach ($command->getAliases() as $alias) $this->subCommands[$alias] = $commandId;
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     * @return bool|mixed
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args) {
        //TODO: run sub commands
        if (!isset($args[0]) || !isset($this->subCommands[$args[0]])) {
            $sender->sendMessage(str_ireplace("{USAGE}", $this->getUsage(), $this->getPlugin()->getMessage("command-usage")));
            return false;
        }
        $subCommand = $this->commandObjects[$this->subCommands[array_shift($args)]];
        if ($sender->hasPermission($subCommand->getPermission())) {
            if (!$subCommand->execute($sender, $args)) {
                $sender->sendMessage(str_ireplace("{USAGE}", "/money " . $subCommand->getUsage(), $this->getPlugin()->getMessage("command-usage"))."\n".
                    str_ireplace("{DESCRIPTION}", $subCommand->getDescription(), $this->getPlugin()->getMessage("command-description")));
                return false;
            } else return true;
        } else {
            $sender->sendMessage($this->getPlugin()->getMessage("permission-denied"));
            return false;
        }
    }

}