<?php

namespace Abius\PHPSell;

use Abius\PHPSell\commands\SellCommand;
use Abius\PHPSell\provider\EconomyProvider;
use Abius\PHPSell\provider\PluginProvider;
use pocketmine\item\StringToItemParser;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\utils\Config;

class SellAll extends PluginBase {

    public static $instance;
    public $message;
    public $sell;

	public static function getInstance() : self {
		return self::$instance;
	}

    public function onEnable(): void{
        self::$instance = $this;
        $this->saveResource("message.yml");
        $this->message = new Config($this->getDataFolder(). "message.yml", Config::YAML);
        $this->saveResource("sell.yml");
        $this->sell = new Config($this->getDataFolder(). "sell.yml", Config::YAML);
        Server::getInstance()->getCommandMap()->register("sell", new SellCommand($this));
    }

    public function getMessage(): Config{
        return $this->message;
    }

    public function getSellManager(): Config{
        return $this->sell;
    }

    public function sellHand(Player $player): void{
        $item = $player->getInventory()->getItemInHand();
        if($item->hasCustomName()){
            $player->sendMessage($this->getMessage()->get("can.not.sell.this.item"));
            return;
        }
        $data = PluginProvider::getInstance()->nametodata($item->getVanillaName());
        if(!$this->getSellManager()->exists($data)){
            $player->sendMessage($this->getMessage()->get("can.not.sell.this.item"));
            return;
        }
        $money = $this->getSellManager()->get($data);
        EconomyProvider::getInstance()->addProvider($player, $money * $item->getCount());
        $player->sendMessage(PluginProvider::getInstance()->tagtodata($this->getMessage()->get("sell.item.successfully"), $item->getCount(), $item->getVanillaName(), $money * $item->getCount()));
        $player->getInventory()->removeItem($item);
    }

    public function sellAll(Player $player): void{
        $itemList = [];
        foreach($player->getInventory()->getContents() as $item){
            if(!$item->hasCustomName() && !$item->hasEnchantments()){
                $data = PluginProvider::getInstance()->nametodata($item->getVanillaName());
                if($this->getSellManager()->exists($data)){
                    if(!in_array($item->getVanillaName(), $itemList)) $itemList[] = $item->getVanillaName();
                }
            }
        }
        if(count($itemList) < 1){
            $player->sendMessage($this->getMessage()->get("no.have.item.to.sell"));
            return;
        }
        foreach($itemList as $itemName){
            $count = 0;
            $itemData = PluginProvider::getInstance()->nametodata($itemName);
            foreach($player->getInventory()->getContents() as $item){
                if(!$item->hasCustomName() && !$item->hasEnchantments()){
                    $data = PluginProvider::getInstance()->nametodata($item->getVanillaName());
                    if($data == $itemData){
                        $count += $item->getCount();
                    }
                }
            }
            $money = $this->getSellManager()->get($itemData);
            $player->sendMessage(PluginProvider::getInstance()->tagtodata($this->getMessage()->get("sell.item.successfully"), $count, $itemName, $money * $count));
            EconomyProvider::getInstance()->addProvider($player, $count * $money);
            $itemRemove = StringToItemParser::getInstance()->parse($itemData)->setCount($count);
            $player->getInventory()->removeItem($itemRemove);
        }
    }
}