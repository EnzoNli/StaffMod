<?php

namespace AnzoLeZoo;


use pocketmine\event\Listener;
use AnzoLeZoo\Main;
use AnzoLeZoo\Task\VanishScheduler;
use pocketmine\Player;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\inventory\Inventory;
use pocketmine\level\Position;
use pocketmine\utils\TextFormat as TF;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\inventory\InventoryPickupItemEvent;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\event\player\PlayerDeathEvent;

class Events implements Listener{
	
	private $plugin;

	function __construct(Main $plugin){
        $this->plugin = $plugin;
    }

    public function onJoin(PlayerJoinEvent $event){
        $player = $event->getPlayer();
        $this->plugin->vanish[$player->getName()] = false;
        $this->plugin->getScheduler()->scheduleDelayedTask(new VanishScheduler($this->plugin, $player->getPlayer()), 1);
        if(in_array($player->getName(), $this->plugin->freeze)){
        	$player->setImmobile(true);
        	$player->sendMessage(TF::AQUA . "Vous avez été freeze par un staff pour être débloquer, contactez le staff sur discord !");
        }
    }

    public function onQuit(PlayerQuitEvent $event){
        $player = $event->getPlayer();
        $this->plugin->vanish[$player->getName()] = false;
   	 	if(in_array($event->getPlayer()->getName(), $this->plugin->staffMod)){
        	$key = array_search($player->getName(), $this->plugin->staffMod);
        	unset($this->plugin->staffMod[$key]);
   	 		$player->getInventory()->clearAll();
   	 	}        
    }

    public function onInteract(PlayerInteractEvent $event){
        $item = $event->getItem();
        $player = $event->getPlayer();
    	if($item->getCustomName() == TF::BOLD . TF::GREEN . "Activer le Vanish"){
                $this->plugin->getServer()->dispatchCommand($player, "vanishstaffmod");
                $player->getInventory()->remove(ItemFactory::get(ItemIds::DYE, 10));
                $newitem = ItemFactory::get(ItemIds::DYE, 13);
                $newitem->setCustomName(TF::BOLD . TF::RED . "Désactiver le Vanish");
                $player->getInventory()->addItem($newitem);
        }

        if($item->getCustomName() == TF::BOLD . TF::RED . "Désactiver le Vanish"){
                $this->plugin->getServer()->dispatchCommand($player, "vanishstaffmod");
                $player->getInventory()->remove(ItemFactory::get(ItemIds::DYE, 13));
                $newitem2 = ItemFactory::get(ItemIds::DYE, 10);
                $newitem2->setCustomName(TF::BOLD . TF::GREEN . "Activer le Vanish");
                $player->getInventory()->addItem($newitem2);
        }


        if($item->getCustomName() == TF::BOLD . TF::GOLD . "TP Joueur Random"){
        	$allonlinePlayer = [];
        	foreach ($this->plugin->getServer()->getOnlinePlayers() as $onlinePlayer){
        		array_push($allonlinePlayer, $onlinePlayer);
        	}

        	$playerTP = array_rand($allonlinePlayer);
        	$pos = new Position($allonlinePlayer[$playerTP]->getX(), $allonlinePlayer[$playerTP]->getY(), $allonlinePlayer[$playerTP]->getZ());
        	$player->teleport($pos);
        	$player->sendMessage(TF::GOLD . "Teleportation vers " . TF::RED . TF::BOLD . $allonlinePlayer[$playerTP]->getName());
        }


        if($item->getCustomName() == TF::BOLD . TF::GREEN . "Quitter le StaffMod"){
        	$player->getInventory()->clearAll();
        	$key = array_search($player->getName(), $this->plugin->staffMod);
        	unset($this->plugin->staffMod[$key]);
        }
    }

     public function onPickupItem(InventoryPickupItemEvent $inventoryPickupItemEvent) {
     	$player = $inventoryPickupItemEvent->getInventory()->getHolder();
     	if(in_array($player->getName(), $this->plugin->staffMod)){
        	$inventoryPickupItemEvent->setCancelled();
     	}
    }

     public function onDrop(PlayerDropItemEvent $dropItemEvent) {
     	if(in_array($dropItemEvent->getPlayer()->getName(), $this->plugin->staffMod)){
            $dropItemEvent->setCancelled();
     	}
    }

     public function onPlayerDeath(PlayerDeathEvent $event){
        if($event->getEntity() instanceof Player){
        	if(in_array($event->getPlayer()->getName(), $this->plugin->staffMod)){
        		$event->setKeepInventory(true);
            	$event->setDrops([]);
        	}else{
        		$event->setKeepInventory(false);
        	}
        }
    }


    public function onDamage(EntityDamageEvent $event){
    	if($event instanceof EntityDamageByEntityEvent){
    		$victim = $event->getEntity();
    		$attacker = $event->getDamager();
    		if($victim instanceof Player and $attacker instanceof Player){
    			if($attacker->getInventory()->getItemInHand()->getCustomName() == TF::BOLD . TF::AQUA . "Freeze\nTapez le joueur !"){
    				if(!$victim->isImmobile()){
    					$event->setCancelled();
    					$victim->setImmobile(true);
    					$victim->sendMessage(TF::BOLD . TF::RED . "Vous avez été immobilisé par un staff !");
    					$attacker->sendMessage(TF::AQUA . "Vous avez immobilisé " . TF::RED . TF::BOLD . $victim->getName());
    					array_push($this->plugin->freeze, $victim->getName());
    				}else{
    					$event->setCancelled(true);
    					$victim->setImmobile(false);
    					$victim->sendMessage(TF::BOLD . TF::GREEN . "Vous pouvez a nouveau vous déplacer !");
    					$attacker->sendMessage(TF::RED . TF::BOLD . $victim->getName() . TF::RESET . TF::AQUA . " peut a nouveau se déplacer !");
    					$key = array_search($victim->getName(), $this->plugin->freeze);
        				unset($this->plugin->freeze[$key]);

    				}
    			}

    			if($attacker->getInventory()->getItemInHand()->getCustomName() == TF::BOLD . TF::DARK_PURPLE . "Voir l'inventaire\nTapez le joueur !") {
    				$this->plugin->getServer()->dispatchCommand($attacker, "invsee " . $victim->getName());
    				$event->setCancelled(true);
    			}

    			if($attacker->getInventory()->getItemInHand()->getCustomName() == TF::BOLD . TF::BLACK . "Voir son EnderChest\nTapez le joueur !") {
    				$this->plugin->getServer()->dispatchCommand($attacker, "enderinvsee " . $victim->getName());
    				$event->setCancelled(true);
    			}
    		}
    	}
    }
}