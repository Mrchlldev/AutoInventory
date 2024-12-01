<?php

namespace Mrchlldev\AutoInventory;

use onebone\economyland\EconomyLand;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\event\Listener;
use pocketmine\player\GameMode;

class Loader extends PluginBase implements Listener {

    public function onEnable(): void {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getServer()->getLogger()->info("AutoInventory by Mrchlldev has Enabled!");
    }

    public function onDeath(PlayerDeathEvent $event): void {
        $player = $event->getPlayer();
        $drops = $event->getDrops();
        if ($event instanceof EntityDamageByEntityEvent) {
            $damager = $event->getDamager();
            if (!$damager instanceof Player) return;
            foreach ($drops as $item) {
                if ($damager->getInventory()->canAddItem($drop)) {
                    $damager->getInventory()->addItem($item);
                    $event->setDrops([]);
                    $player->sendTip("§aThe item drops automatic\nadded on your inventory");
                } else {
                    $player->sendMessage("§cYour inventory is full! can't automatic add item on your inventory!");
                }
            }
        }
    }

    public function onBreak(BlockBreakEvent $event): void {
        $player = $event->getPlayer();
        $drops = $event->getDrops();
        EconomyLand::getInstance()->permissionCheck($event);
        if ($player->getGamemode() === GameMode::CREATIVE) return;
        if ($event->isCancelled()) return;
        foreach ($drops as $item) {
            if ($player->getInventory()->canAddItem($item)) {
                $player->getInventory()->addItem($item);
                $event->setDrops([]);
                $player->sendTip("§aThe item drops automatic\nadded on your inventory");
            } else {
                $player->sendMessage("§cYour inventory is full!\nplease leave some inventory space to accommodate drops!");
                $event->cancel();
            }
        }
    }
}