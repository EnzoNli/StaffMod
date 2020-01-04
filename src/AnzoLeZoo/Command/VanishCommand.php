<?php


namespace AnzoLeZoo\Command;


use AnzoLeZoo\Main;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat as TF;


class VanishCommand extends Command{

    private $plugin;

    function __construct(Main $plugin){
        $this->plugin = $plugin;
        $cfg = $this->plugin->getConfig();
        parent::__construct("vanishstaffmod", "A n'utiliser qu'en StaffMod actif", "Pas d'usage", ["vanishsm"]);
        $this->setPermission("staffmod.perm");
    }


    function execute(CommandSender $sender, string $commandLabel, array $args){
        if($sender instanceof Player){
            if($sender->hasPermission("staffmod.perm")){
                if($this->plugin->vanish[$sender->getName()] === false){
                    $sender->sendMessage(TF::BOLD . TF::GREEN . "Vanish activé !");
                    foreach ($this->plugin->getServer()->getOnlinePlayers() as $onlinePlayer){
                        $onlinePlayer->hidePlayer($sender);
                    }
                    $this->plugin->vanish[$sender->getName()] = true;
                } else {
                    $sender->sendMessage(TF::BOLD . TF::RED . "Vanish désactivé !");
                    foreach ($this->plugin->getServer()->getOnlinePlayers() as $onlinePlayer){
                        $onlinePlayer->showPlayer($sender);
                    }
                    $this->plugin->vanish[$sender->getName()] = false;
                }
            }
        }
    }
}