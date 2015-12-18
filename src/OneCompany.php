<?php

namespace OneCompany;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\utils\TextFormat as Color;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\command\PluginCommand;
use pocketmine\utils\Config;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\Event;
use pocketmine\Player;

class OneCompany extends PluginBase implements Listener {
	private static $instance = null;
	public $messages;
	public $economyAPI = null;
	public $CompanyPrice;
	private $config;
	private $CompanyDB;
	private $economy;

	public function onEnable()
	{
		$this->initMessage ();
		$this->CompanyDB = (new Config($this->getDataFolder()."Company.json", Config::JSON))->getAll();
		
		if ($this->getServer ()->getPluginManager ()->getPlugin ( "EconomyAPI" ) != null) {
			$this->economyAPI = \onebone\economyapi\EconomyAPI::getInstance ();
		} else {
			$this->getLogger ()->error ( $this->get ( "there-are-no-economyapi" ) );
			$this->getServer ()->getPluginManager ()->disablePlugin ( $this );
		}
		
		$commandMap = $this->getServer()->getCommandMap();
		$command = new PluginCommand("회사", $this);
		$command->setDescription("회사를 개설합니다.");
		$command->setUsage("사용법 : /회사 생성 | 양도 | 목록 | 회사원추가 | 회사원목록 | 폐쇠");
		$command->setPermission("Company.command.allow");
		$commandMap->register("회사", $command);
		$this->saveResource ( "config.yml", false );
		$this->CompanyPrice = (new Config ( $this->getDataFolder () . "CompanyPrice.yml", Config::YAML ))->getAll ();
		$money = $this->economyAPI->myMoney ( $player );
	}
	public function onDisable()
	{
		$company = new Config($this->getDataFolder()."Company.json", Config::JSON);
		$company->setAll($this->CompanyDB);
		$company->save();
	}
	
	public function get($var) {
		return $this->messages [$this->messages ["default-language"] . "-" . $var];
	}
	public function initMessage() {
		$this->saveResource ( "messages.yml", false );
		$this->messagesUpdate ( "messages.yml" );
		$this->messages = (new Config ( $this->getDataFolder () . "messages.yml", Config::YAML ))->getAll ();
	}
	public function messagesUpdate($targetYmlName) {
		$targetYml = (new Config ( $this->getDataFolder () . $targetYmlName, Config::YAML ))->getAll ();
		if (! isset ( $targetYml ["m_version"] )) {
			$this->saveResource ( $targetYmlName, true );
		} else if ($targetYml ["m_version"] < $this->m_version) {
			$this->saveResource ( $targetYmlName, true );
		}
	}
	
	public function NoticeLisence()
	{
		$this->getLogger()->alert("본 플러그인은 Light-EULA를 사용합니다.");
		$this->getLogger()->alert("이 플러그인을 사용시 Light-EULA라이센스에 동의하는 것으로 간주합니다.");
		$this->getLogger()->alert("라이센스->https://github.com/LightBlue7/Light-EULA/blob/master/LICENSE.md");
	}
	public function onCommand(CommandSender $sender, Command $command, $label, Array $args)
	{
		if(strtolower($command) == "회사"){
			if (!isset($args[0])){
				$sender->sendMessage(Color::RED."[OneCompany] /회사 생성 | 양도 | 목록 | 회사원추가 | 회사원목록 | 폐쇠");
		 		return true;
			}
			switch ($args[0]	){
				case "생성" :
					if(!isset($args[1])) {
						$sender->sendMessage(Color::RED."[OneCompany] /회사 생성 <회사명>");
						break;
					}
					if ($money < $this->CompanyPrice ["create-price"] ) {
				        $this->alert ($player, $this->get ( "not-enough-money-to-purchase" ) . " ( " . $this->get ( "my-money" ) . " : " . $money . " )" );
				        break;
			        } 
					$this->economyAPI->reduceMoney ( $sender, $this->config["create-price"] );  
					$this->CompanyDB[$args[2]]["owner"] = $sender->getName();
					$sender->sendMessage(Color::YELLOW."[OneCompany] 회사가 생성 되었습니다.");
					break;
				case "양도" :
					$this->config;
					$sender->sendMessage(Color::YELLOW."[OneCompany] 회사가 %player%에게 양도 되었습니다.");
					break;
				case "목록" :
					$this->config;
					$sender->sendMessage(Color:: YELLOW. "[OneCompany] 본인이 소유한 회사목록을 보여줍니다.");
				    break;
				case "회사원추가" :
					$this->config;
					$sender->sendMessage(Color:: YELLOW. "[OneCompany] {$sender->getName()}님이 회사에 추가되었습니다.");
				    break;
				case "회사원목록" :
					$this->config;
					$sender->sendMessage(Color:: YELLOW. "[OneCompany] 이 회사를 다니는 유저목록을 보여줍니다.");
				    break;
				case "페쇠" :
					$this->config;
					$sender->sendMessage(Color::YELLOW."[OneCompany] 회사가 사장 {$sender->getName()}님에 의하여 페쇠되었습니다.");
					break;
			}
		}
		return true;
	}
	public function message($player, $text = "", $mark = null) {
	}
	public function alert($player, $text = "", $mark = null) {
	}
}
