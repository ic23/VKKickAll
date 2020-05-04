<?php	

	/* 


	by isaacrulz23.
	
	Скрипт для группы, который кикает из беседы при @all или @everyone
	VK API - 5.80
	Все настройки находятся в файле settings
	
	
	*/

	include("settings.php");
	
	function kick($usr_id, $idconserv) {
		global $token;
		$idconserv = $idconserv - 2000000000;
		$eeee = file_get_contents("https://api.vk.com/method/messages.removeChatUser?access_token={$token}&chat_id={$idconserv}&user_id={$usr_id}&v=5.88", true);
	}
	
	$keyGet = $_REQUEST["akey"];
	$userdata = json_decode(file_get_contents('php://input'));
	
	if($keyGet == $secretKey)
	{
		switch ($userdata->type) {
			case 'confirmation':
				die($confirmationKey);
				break;
			case 'message_new':
				$message = $userdata->object->text;
				$id = $userdata->object->from_id;
				$beseda = $userdata->object->peer_id;
				if (strpos($message, '@all') !== false) 
				{
					kick($id, $beseda);
					die("ok");
					break;
				}
				elseif (strpos($message, '@everyone') !== false)
				{
					kick($id, $beseda);
					die("ok");
					break;
				}
				elseif (strpos($message, '@online') !== false)
				{
					kick($id, $beseda);
					die("ok");
					break;
				}
				die("ok");
				break;
		}
	}
	else
	{
		die(json_encode(array("response" => 0, "error" => array("error_id" => 1, "error_message" => "Bad secret key"))));
	}
?>