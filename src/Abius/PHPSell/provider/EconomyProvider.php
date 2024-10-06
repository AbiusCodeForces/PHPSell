<?php

namespace Abius\PHPSell\provider;

use Noob\CoinAPI\CoinAPI;
use pocketmine\player\Player;
use pocketmine\utils\Config;
use pocketmine\utils\SingletonTrait;

class EconomyProvider {

    use SingletonTrait;

    public function getProviderManager(): Config{
        return CoinAPI::getCoinData();
    }

    public function myProvider(Player $player){
        return $this->getProviderManager()->get($player->getName());
    }

    public function addProvider(Player $player, float|int $amount): void{
        $this->getProviderManager()->set($player->getName(), $this->getProviderManager()->get($player->getName()) + $amount);
        $this->getProviderManager()->save();
    }

    public function reduceProvider(Player $player, float|int $amount): void{
        $this->getProviderManager()->set($player->getName(), $this->getProviderManager()->get($player->getName()) - $amount);
        $this->getProviderManager()->save();
    }

    public function compareValue(float|int $value1, float|int $value2): bool{
        if($value1 == $value2){
            return true;
        }
        return false;
    }
}