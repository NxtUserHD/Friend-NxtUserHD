<?php

namespace NxtUserHD;

use pocketmine\plugin\PluginBase;

use pocketmine\event\Listener;

use pocketmine\Player;

use pocketmine\utils\Config;

use pocketmine\command\CommandSender;

use pocketmine\command\Command;

use pocketmine\event\player\PlayerJoinEvent;

use pocketmine\event\player\PlayerQuitEvent;

use pocketmine\event\player\PlayerChatEvent;

class friends extends PluginBase implements Listener{

public $prefix = "§7[§4Friends§7]§r ";

public function onEnable(){

	

@mkdir("/root/Server/Freunde");

$this->getServer()->getPluginManager()->registerEvents($this, $this);

$this->getLogger()->info($this->prefix."§awurde aktiviert");

} 

public function onJoin(PlayerJoinEvent $event){

$player = $event->getPlayer();

$name = (string)$player->getName();

if(!file_exists($this->getDataFolder().$name.".yml")){

$playerfile = new Config("/root/Server/Freunde/" . $name . ".yml", Config::YAML);

$playerfile->set("Freunde", array());

$playerfile->set("Einladungen", array());

$playerfile->set("blocked", false);

$playerfile->save();

}else{

$playerfile = new Config("/root/Server/Freunde/" . $name . ".yml", Config::YAML);

if(!empty($playerfile->get("Einladungen"))){

foreach($playerfile->get("Einladungen") as $e){

$player->sendMessage($this->prefix."§e".$e." hat dir eine Freundschaftsanfrage gesendet");

}

}

if(!empty($playerfile->get("Freunde"))){

foreach($playerfile->get("Freunde") as $f){

$v = $this->getServer()->getPlayerExact($f);

if(!$v == null){

$v->sendMessage($this->prefix."§a".$player->getName()." ist momentan online");

}

}

}

}

}

public function onQuit(PlayerQuitEvent $event){

$player = $event->getPlayer();

$name = $player->getName();

$playerfile = new Config("/root/Server/Freunde/" . $name . ".yml", Config::YAML);

if(!empty($playerfile->get("Freunde"))){

foreach($playerfile->get("Freunde") as $f){

$v = $this->getServer()->getPlayerExact($f);

if(!$v == null){

$v->sendMessage($this->prefix."§a".$name." ist jetzt offline");

}

}

}

}

public function onCommand(CommandSender $sender, Command $cmd, $label, array $args): bool {

if($cmd->getName() == "friends"){

if($sender instanceof Player){

$playerfile = new Config("/root/Server/Freunde/" . $sender->getName() . ".yml", Config::YAML);

if(empty($args[0])){

$sender->sendMessage("§cUse: /friends help");

}else{

if($args[0] == "invite"){

if(empty($args[1])){

$sender->sendMessage($this->prefix."/friends invite <player>");

}else{

if(file_exists("/root/Server/Freunde/" . $args[1] . ".yml")) {

$vplayerfile = new Config("/root/Server/Freunde/" . $args[1] . ".yml", Config::YAML);

if($vplayerfile->get("blocked") == false){

$einladungen = $vplayerfile->get("Einladungen");

$einladungen[] = $sender->getName();

$vplayerfile->set("Einladungen", $einladungen);

$vplayerfile->save();

$sender->sendMessage($this->prefix."§aEs wurde eine Freundschaftsanfrage an ".$args[1]." versandt");

$v = $this->getServer()->getPlayerExact($args[1]);

if(!$v == null){

$v->sendMessage("§a".$sender->getName()." hat dir eine Freundschaftsanfrage gesendet\n   §l[/friends accept ".$sender->getName()."]    §c[/friends deny ".$sender->getName()."]");

}

}else{

$sender->sendMessage($this->prefix."§cDieser Spieler nimmt keine Anfragen an!");

}

}else{

$sender->sendMessage($this->prefix."§4Dieser Spieler existiert nicht");

}

}

}

if($args[0] == "accept"){

if(empty($args[1])){

$sender->sendMessage($this->prefix."/friends accept <player>");

}else{

if(file_exists("/root/Server/Freunde/" . $args[1] . ".yml")) {

$vplayerfile = new Config("/root/Server/Freunde/" . (string)$sender->getName() . ".yml", Config::YAML);

if(in_array($args[1], $playerfile->get("Einladungen"))){

$old = $playerfile->get("Einladungen");

unset($old[array_search($args[1], $old)]);

$playerfile->set("Einladungen", $old);

$newfriend = $playerfile->get("Freunde");

$newfriend[] = $args[1];

$playerfile->set("Freunde", $newfriend);

$playerfile->save();

$vplayerfile = new Config($this->getDataFolder().$args[1].".yml", Config::YAML);

$newfriend = $vplayerfile->get("Freunde");

$newfriend[] = $sender->getName();

$vplayerfile->set("Freunde", $newfriend);

$vplayerfile->save();

if(!$this->getServer()->getPlayerExact($args[1]) == null){

$this->getServer()->getPlayerExact($args[1])->sendMessage($this->prefix."§a".$sender->getName()." ist jetzt dein Freund");

}

$sender->sendMessage($this->prefix."§a".$args[1]." ist jetzt dein Freund");

}else{

$sender->sendMessage($this->prefix."§cDieser Spieler hat dir keine Einladung gesendet!");

}

}else{

$sender->sendMessage($this->prefix."§4Dieser Spieler existiert nicht");

}

}

}

if($args[0] == "deny"){

if(empty($args[1])){

$sender->sendMessage($this->prefix."/friends deny <player>");

}else{

if(file_exists("/root/Server/Freunde/" . $args[1] . ".yml")) {

$vplayerfile = new Config("/root/Server/Freunde/" . $args[1] . ".yml", Config::YAML);

if(in_array($args[1], $playerfile->get("Einladungen"))){

$old = $playerfile->get("Einladungen");

unset($old[array_search($args[1], $old)]);

$playerfile->set("Einladungen", $old);

$playerfile->save();

$sender->sendMessage($this->prefix."§aDie Einladung von ".$args[1]." wurde abgelehnt");

}else{

$sender->senMessage($this->prefix."§cDieser Spieler hat dir keine Einladung gesendet!");

}

}else{

$sender->sendMessage($this->prefix."§4Dieser Spieler existiert nicht");

}

}

}

if($args[0] == "remove"){

if(empty($args[1])){

$sender->sendMessage($this->prefix."/friends remove <player>");

}else{

if(file_exists("/root/Server/Freunde/" . $args[1] . ".yml")) {

$vplayerfile = new Config("/root/Server/Freunde/" . $args[1] . ".yml", Config::YAML);

if(in_array($args[1], $playerfile->get("Freunde"))){

$old = $playerfile->get("Freunde");

unset($old[array_search($args[1], $old)]);

$playerfile->set("Freunde", $old);

$playerfile->save();

$vplayerfile = new Config("/root/Server/Freunde/" . $args[1] . ".yml", Config::YAML);

$old = $vplayerfile->get("Freunde");

unset($old[array_search((string)$sender->getName(), $old)]);

$vplayerfile->set("Freunde", $old);

$vplayerfile->save();

$sender->sendMessage($this->prefix."§a".$args[1]." ist jetzt nicht mehr dein Freund");

}else{

$sender->sendMessage($this->prefix."§cDieser Spieler ist nicht dein Freund!");

}

}else{

$sender->sendMessage($this->prefix."§4Dieser Spieler existiert nicht");

}

}

}

if($args[0] == "list"){

if(empty($playerfile->get("Freunde"))){

$sender->sendMessage($this->prefix."§cDu hast keine Freunde");

}else{

$sender->sendMessage("§eAlle Freunde:");

foreach($playerfile->get("Freunde") as $f){

if($this->getServer()->getPlayerExact($f) == null){

$sender->sendMessage("§e".$f."§7(§coffline§7)");

}else{

$sender->sendMessage("§e".$f."§7(§aonline§7)");

}

}

}

}

if($args[0] == "block"){

if($playerfile->get("blocked") === false){

$playerfile->set("blocked", true);

$playerfile->save();

$sender->sendMessage($this->prefix."§aDu bekommst nun keine Freundschaftsanfragen mehr");

}else{

$sender->sendMessage($this->prefix."§aDu bekommst jetzt wieder Freundschaftsanfragen");

$playerfile->set("blocked", false);

$playerfile->save();

}

}

if($args[0] == "help"){

$sender->sendMessage("§7========================== [§4EpicFriends§7] ==========================");

$sender->sendMessage("§7- §e/friends invite {Spieler} §7(§eFüge einen Freund hinzu§7)");

$sender->sendMessage("§7- §e/friends accept {Spieler} §7(§eNimm eine Freundschaftsanfrage an§7)");

$sender->sendMessage("§7- §e/friends deny {Spieler} §7(§eLehne eine Freundschaftsanfrage ab§7)");

$sender->sendMessage("§7- §e/friends remove {Spieler} §7(§eLösche einen Freund§7)");

$sender->sendMessage("§7- §e/friends block §7(§eErhalte keine freundschaftsanfragen mehr§7)");

$sender->sendMessage("§7- @NameDesFreundes §7(§eSchreibe Private narichten an deinen Freund§7)");

$sender->sendMessage("§7========================== [§4EpicFriends§7] ==========================");

}else{

}

}

}

return true;

}

}

public function onChat(PlayerChatEvent $event){

$player = $event->getPlayer();

$msg = $event->getMessage();

$playerfile = new Config("/root/Server/Freunde/" . $player->getName() . ".yml", Config::YAML);

$words = explode(" ", $msg);

if(in_array(str_replace("@", "", $words[0]), $playerfile->get("Freunde"))){

$f = $this->getServer()->getPlayerExact(str_replace("@", "", $words[0]));

if(!$f == null){

$f->sendMessage($this->prefix." §7[§e".str_replace("@", "", $words[0])."§7] §l>>§r ".str_replace($words[0], "", $msg));

$player->sendMessage($this->prefix." §7[§e".str_replace("@", "", $words[0])."§7] §l>>§r ".str_replace($words[0], "", $msg));

}else{

$player->sendMessage($this->prefix."§c".str_replace("@", "", $words[0])." ist nicht online!");

}

$event->setCancelled();

}

}

}

?>
