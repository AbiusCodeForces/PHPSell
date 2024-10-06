<?php

namespace Abius\PHPSell\commands;

use pocketmine\player\Player;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginOwned;
use pocketmine\Server;

use Abius\PHPSell\SellAll;

class SellCommand extends Command implements PluginOwned
{
    private SellAll $plugin;

    public function __construct(SellAll $plugin)
    {
        $this->plugin = $plugin;
        parent::__construct("sell", "Bán Đồ", null, ["bando"]);
        $this->setPermission("sell.cmd");
    }

    public function execute(CommandSender $player, string $label, array $args)
    {
        if (!$player instanceof Player) {
            $this->getOwningPlugin()->getLogger()->notice("Xin hãy sử dụng lệnh trong trò chơi");
            return 1;
        }
        
        if(!isset($args[0])){
            $player->sendMessage(SellAll::getInstance()->getMessage()->get("usage"));
            return 1;
        }
        switch($args[0]){
            case "hand":
                SellAll::getInstance()->sellHand($player);
                break;
            case "all":
                SellAll::getInstance()->sellAll($player);
                break;
            default:
                $player->sendMessage(SellAll::getInstance()->getMessage()->get("usage"));
                break;
        }  
        
    }

    public function getOwningPlugin(): SellAll
    {
        return $this->plugin;
    }
}