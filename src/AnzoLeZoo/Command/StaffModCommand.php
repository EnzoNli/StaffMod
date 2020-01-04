<?php


namespace AnzoLeZoo\Command;


use AnzoLeZoo\Main;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\inventory\Inventory;
use pocketmine\utils\TextFormat as TF;


class StaffModCommand extends Command{


    private $plugin;

    function __construct(Main $plugin){
        $this->plugin = $plugin;
        parent::__construct("staffmod", "A utiliser en administration", "/staffmod ou /sm", ["sm"]);
        $this->setPermission("staffmod.perm");
    }


    function execute(CommandSender $sender, string $commandLabel, array $args){
        if($sender instanceof Player){
            if($sender->isOp() or $sender->hasPermission("staffmod.perm")){
                if(!in_array($sender->getName(), $this->plugin->staffMod)){

                    $sender->getInventory()->clearAll();

                    array_push($this->plugin->staffMod, $sender->getName());

                    $item = ItemFactory::get(ItemIds::DYE, 10);
                    $item->setCustomName(TF::BOLD . TF::GREEN . "Activer le Vanish");
                    $sender->getInventory()->addItem($item);

                    $item = ItemFactory::get(381);
                    $item->setCustomName(TF::BOLD . TF::GOLD . "TP Joueur Random");
                    $sender->getInventory()->addItem($item);

                    $item = ItemFactory::get(352);
                    $item->setCustomName(TF::BOLD . TF::AQUA . "Freeze\nTapez le joueur !");
                    $sender->getInventory()->addItem($item);

                    $item = ItemFactory::get(54);
                    $item->setCustomName(TF::BOLD . TF::DARK_PURPLE . "Voir l'inventaire\nTapez le joueur !");
                    $sender->getInventory()->addItem($item);

                    $item = ItemFactory::get(130);
                    $item->setCustomName(TF::BOLD . TF::BLACK . "Voir son EnderChest\nTapez le joueur !");
                    $sender->getInventory()->addItem($item);

                    $item = ItemFactory::get(ItemIds::DYE, 1);
                    $item->setCustomName(TF::BOLD . TF::GREEN . "Quitter le StaffMod");
                    $sender->getInventory()->setItem(8, $item);
            }else{
                $sender->sendMessage(TF::RED . "Vous êtes déjà en StaffMod");
            }
        }
    }
 }
}