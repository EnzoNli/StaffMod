<?php

namespace AnzoLeZoo;

use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use AnzoLeZoo\Command\StaffModCommand;
use AnzoLeZoo\Command\VanishCommand;
use AnzoLeZoo\Task\VanishScheduler;


class Main extends PluginBase implements Listener{

    public $vanish = [];
    public $staffMod = [];
    public $freeze = [];

    function onEnable(){
    	$this->getServer()->getPluginManager()->registerEvents($this, $this);
    	$this->getServer()->getCommandMap()->register("staffmod", new StaffModCommand($this));
    	$this->getServer()->getCommandMap()->register("vanishstaffmod", new VanishCommand($this));
        $this->getServer()->getPluginManager()->registerEvents(new Events($this), $this);
    }
}