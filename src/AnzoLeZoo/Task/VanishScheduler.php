<?php

namespace AnzoLeZoo\Task;

use AnzoLeZoo\Main;
use pocketmine\Player;
use pocketmine\scheduler\Task;


class VanishScheduler extends Task{

    private $plugin;
    private $player;

    function __construct(Main $plugin, Player $player){
        $this->plugin = $plugin;
        $this->player = $player;
    }


    function onRun($tick){
        $player = $this->player;
        $this->plugin->getScheduler()->scheduleDelayedTask(new VanishScheduler($this->plugin, $player->getPlayer()), 5);
        if($this->plugin->vanish[$player->getName()] === true){
            foreach ($this->plugin->getServer()->getOnlinePlayers() as $onlinePlayer){
                $onlinePlayer->hidePlayer($player);
            }
        } else {
            foreach ($this->plugin->getServer()->getOnlinePlayers() as $onlinePlayer){
                $onlinePlayer->showPlayer($player);
            }
        }
    }
}