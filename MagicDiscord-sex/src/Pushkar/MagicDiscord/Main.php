<?php

namespace Pushkar\MagicDiscord;

use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\event\player\PlayerKickEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\utils\TextFormat;
use CortexPE\DiscordWebhookAPI\Message;
use CortexPE\DiscordWebhookAPI\Webhook;
use CortexPE\DiscordWebhookAPI\Embed;
use jojoe77777\FormAPI\SimpleForm;
use jojoe77777\FormAPI\CustomForm;

class Main extends PluginBase implements Listener
{
		
		public function onEnable(): void
    {
      $this->getLogger()->info("MagicDiscord Enabled");
      if ($this->getConfig()->get("enable-startup-alert") === true) {
        $webhook = new Webhook($this->getConfig()->get("startup-webhook-url"));
  			$colorval = hexdec($this->getConfig()->get("startup-embed-color"));
  			
  			$msg = new Message();
  			$msg->setUsername($this->getConfig()->get("webhook-username"));
  			$msg->setAvatarURL($this->getConfig()->get("webhook-avatar-url"));
  
  			$embed = new Embed();
  			$embed->setTitle($this->getConfig()->get("startup-message-title"));
  			$embed->setColor($colorval);
  			$embed->addField($this->getConfig()->get("startup-embed-field-title"), $this->getConfig()->get("startup-embed-field-message"));
  			$embed->setThumbnail($this->getConfig()->get("startup-thumbnail-url"));
  			$embed->setFooter($this->getConfig()->get("startup-footer-message"), $this->getConfig()->get("startup-footer-image-url"));
  			$msg->addEmbed($embed);
  			
  			$webhook->send($msg);
      }
		}
		
		public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args):bool {
        switch($cmd->getName()) {
        case "report": 
            if($sender instanceof Player) {
                $this->mainForm($sender);
            } else {
          $sender->sendMessage("command can extend in-game only");
        }
        break;
        case "playerreport": 
            if($sender instanceof Player) {
                $this->reportForm($sender);
            } else {
          $sender->sendMessage("command can extend in-game only");
        }
        break;
        case "suggest": 
            if($sender instanceof Player) {
                $this->suggestForm($sender);
            } else {
          $sender->sendMessage("command can extend in-game only");
        }
        break;
        case "bug": 
            if($sender instanceof Player) {
                $this->bugForm($sender);
            } else {
          $sender->sendMessage("command can extend in-game only");
        }
        break;
    }
    return true;
    }

		public function OnDisable(): void
    {
      /*if ($this->getConfig()->get("enable-shutdown-alert") === true) {
        $webhook = new Webhook($this->getConfig()->get("webhook-url"));
  			$colorval = hexdec($this->getConfig()->get("shutdown-embed-color"));
  			
  			$msg = new Message();
  			$msg->setUsername($this->getConfig()->get("webhook-username"));
  			$msg->setAvatarURL($this->getConfig()->get("webhook-avatar-url"));
  
  			$embed = new Embed();
  			$embed->setTitle($this->getConfig()->get("shutdown-message-title"));
  			$embed->setColor($colorval);
  			$embed->addField($this->getConfig()->get("shutdown-embed-field-title"), $this->getConfig()->get("shutdown-embed-field-message"));
  			$embed->setThumbnail($this->getConfig()->get("shutdown-thumbnail-url"));
  			$embed->setFooter($this->getConfig()->get("shutdown-footer-message"), $this->getConfig()->get("shutdown-footer-image-url"));
  			$msg->addEmbed($embed);
  			
  			$webhook->send($msg);
      }*/
    }
    
    public function reportForm($player) {
        $list = [];
        foreach($this->getServer()->getOnlinePlayers() as $p) {
            $list[] = $p->getName();
        }
        $this->players[$player->getName()] = $list;
        $form = new CustomForm(function (Player $player, array $data = null){
            if($data === null) {
              $player->sendMessage("Report Failed");
                return true;
            }
            $web = new Webhook($this->getConfig()->get("report-webhook-url"));
            $colorval = hexdec($this->getConfig()->get("report-embed-color"));
            $msg = new Message();
            $msg->setUsername($this->getConfig()->get("report-webhook-username"));
  		    	$msg->setAvatarURL($this->getConfig()->get("report-webhook-avatar-url"));
            $e = new Embed();
            $e->setColor($colorval);
            $index=$data[1];
            $e->setTitle("REPORT ❌");
            $e->addField("Reported player", $this->players[$player->getName()][$index]);
            $e->addField("Reported by", $player->getName());
            $e->addField("Reason:", $data[2]);
            $e->setThumbnail($this->getConfig()->get("report-thumbnail-url"));
            $msg->addEmbed($e);
            $web->send($msg);
            $player->sendMessage("§e§lMAGICGAMES > §r§bReport Has Been Sent");
        });
        $name = $player->getName();
        $form->setTitle("§lREPORTS");
        $form->addLabel("§dHello, §e$name\n\n§dType Your Report Below, It Will Send To Our Moderators");
        $form->addDropdown("Select a player", $this->players[$player->getName()]);
        $form->addInput("§bType Your Reason", "Type a reason", "Hacking");
        $form->sendToPlayer($player);
        return $form;
    }
    public function suggestForm($player){
		$form = new CustomForm(function (Player $player, array $data = null){
				$result = $data;
				if($result === null){
					return true;
				}
				if($result != null){
					$web = new Webhook($this->getConfig()->get("suggest-webhook-url"));
            $colorval = hexdec($this->getConfig()->get("suggest-embed-color"));
            $msg = new Message();
            $msg->setUsername($this->getConfig()->get("suggest-webhook-username"));
  		    	$msg->setAvatarURL($this->getConfig()->get("suggest-webhook-avatar-url"));
            $e = new Embed();
            $e->setColor($colorval);
            $e->setTitle("SUGGESTIONS 🍬");
            $e->addField("Suggested By", $player->getName());
            $e->addField("Suggestions:", $data[0]);
            $e->setThumbnail($this->getConfig()->get("suggest-thumbnail-url"));
            $msg->addEmbed($e);
            $web->send($msg);
            $player->sendMessage("§e§lMAGICGAMES > §r§bSuggestions Was Sent");
					return true;
				}
		});
		$name = $player->getName();
		$form->setTitle("§e§lSUGGESTIONS");
		$form->addInput("§dHello, §e$name\n\n§dType Your Suggestions Below, It Will Send To Our Development Team");
		$form->sendToPlayer($player);
		return $form;
    }
    public function bugForm($player){
		$form = new CustomForm(function (Player $player, array $data = null){
				$result = $data;
				if($result === null){
					return true;
				}
				if($result != null){
					$web = new Webhook($this->getConfig()->get("bug-webhook-url"));
            $colorval = hexdec($this->getConfig()->get("bug-embed-color"));
            $msg = new Message();
            $msg->setUsername($this->getConfig()->get("bug-webhook-username"));
  		    	$msg->setAvatarURL($this->getConfig()->get("bug-webhook-avatar-url"));
            $e = new Embed();
            $e->setColor($colorval);
            $e->setTitle("BUGS 🐞");
            $e->addField("Told By", $player->getName());
            $e->addField("Bug:", $data[0]);
            $e->setThumbnail($this->getConfig()->get("bug-thumbnail-url"));
            $msg->addEmbed($e);
            $web->send($msg);
            $player->sendMessage("§e§lMAGICGAMES > §r§bBug Report Has Been Sent");
					return true;
				}
		});
		$name = $player->getName();
		$form->setTitle("§e§lBUGS");
		$form->addInput("§dHello, §e$name\n\n§dType Your Bug, It Will Send To Our Development Team");
		$form->sendToPlayer($player);
		return $form;
    }
    public function mainForm(Player $sender){
        $form = new SimpleForm(function (Player $sender, $data) {
            $result = $data;
            if ($result == null) {
            }
            switch ($result) {
                case 0:
                $this->reportForm($sender);
                    break;
                case 1:
                $this->suggestForm($sender);
                    break;
                case 2:
                $this->bugForm($sender);
                    break;
                case 3:
                    break;
            }
        });
        $name = $sender->getName();
        $form->setTitle("§l§eREPORTS");
        $form->setContent("§bHello §e$name\n\n§bPlease Select The Report You Want To Give");
        $form->addButton("§l§aPLAYER REPORT\n§l§9»» §r§oTap to open", 1, "https://i.imgur.com/gP9n9zJ.png");
        $form->addButton("§l§aGIVE SUGGESTIONS\n§l§9»» §r§oTap to open", 1, "https://i.imgur.com/EHigDU8.png");
        $form->addButton("§l§aBUG REPORT\n§l§9»» §r§oTap to open", 1, "https://i.imgur.com/Y1wEA7w.png");
        $form->addButton("§l§cEXIT\n§l§9»» §r§oTap To Exit", 1, "https://cdn-icons-png.flaticon.com/128/929/929416.png");
        $sender->sendForm($form);
    }
  }
