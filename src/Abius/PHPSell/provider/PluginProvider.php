<?php

namespace Abius\PHPSell\provider;

use Noob\CoinAPI\CoinAPI;
use pocketmine\player\Player;
use pocketmine\utils\Config;
use pocketmine\utils\SingletonTrait;

class PluginProvider {

    use SingletonTrait;

    public function nametodata(string $itemName): string{
        $name = strtolower($itemName);
        for($i = 0; $i < strlen($name); $i++){
            if($name[$i] == ' ') $name[$i] = '_';
        }
        return $name;
    }

    public function tagtodata(string $stringTag, int $count, string $dataName, float|int $money): string{
        $tags = [
            "{count}" => $count,
            "{item_name}" => $dataName,
            "{money}" => $money
        ];

        foreach($tags as $data => $value){
            $stringTag = str_replace($data, $value, $stringTag);
        }
        return $stringTag;
    }
}