<?php

namespace OneCompany;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\utils\TextFormat as Color;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\command\PluginCommand;
use pocketmine\utils\Config;
use onebone\economyapi\EconomyAPI;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\Event;

class OneCompany extends PluginBase implements Listener
{
	
	private $config;
	private $CompanyDB;

	public function onEnable()
	{
		
		$this->Company = (new Config($this->getDataFolder()."Company.json", Config::JSON))->getAll();
		@mkdir ( $this->getDataFolder() );
		if($this->getServer()->getPluginManager()->getPlugin("EconomyAPI") == null){
		    $this->getLogger()->error($this->get("cant-find-economyapi"));
			$this->getServer()->getPluginManager()->disablePlugin($this);
	}
	$this->NoticeVersionLisence();
	$this->Loadconfig();
	$this->CompanyDB = $this->Loadplugindata("CompanyDB.json");
	$this->getServer()->getPluginManager()->registerEvent($this, $this);
	$commandmap = $this->getServer()->getCommandMap();
	$command = new PluginCommand("회사", $this);
	$command->setDescription("회사를 개설합니다.");
	$command->setUsage("사용법 : /회사 생성 | 양도 | 폐쇠 |");
	$command->setPermission("Company.command.allow");
	$command->register("회사", $command);
	}
	public function onDisable()
	{
		$this->save("CompanyDB.json", $this->CompanyDB);
		$config = new Config($this->getDataFolder()."config.yml", Config::YAML);
		$config->setAll($this->config);
		$config->save();
	$company = new Config($this->getDataFolder()."company.json", Config::JSON);
	$company->setAll($this->company);
	$company->save();
	}
	public function Loadconfig()
	{
		$this->saveResource("config.yml");
		$this->config = (new Config($this->getDataFolder()."config.yml", Config::YAML))->getAll();
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
					$sender->sendMessage(Color::RED."[OneCompany] /회사 생성 <이름> | 양도 | 폐쇠 | ");
			 	return true;
				}
				switch ($args[0]	){
					case "생성" :
						$this->economy->reduceMoney ( $player, $price );
						$this->config;
						$sender->sendMessage(Color::YELLOW."[OneCompany] 회사가 생성 되었습니다.");
						break;
					case "양도" :
						$this->config;
						$sender->sendMessage(Color::YELLOW."[OneCompany] 회사가 %player%에게 양도 되었습니다.");
						break;
					case "목록" :
						$this->config;
						$sender->sendMessage(Color:: YELLOW. "[OneCompany] 본인이 소유한 회사목록을 보여줍니다.");
					case "회사원목록" :
						$this->config;
						$sender->sendMessage(Color:: YELLOW. "[OneCompany] 이 회사를 다니는 유저목록을 보여줍니다.")
					case "페쇠" :
						$this->config;
						$sender->sendMessage(Color::YELLOW."[OneCompany] 회사가 사장 {$sender->getName()}님에 의하여 페쇠되었습니다.");
						break;
				}
			}
		}
	}
}
