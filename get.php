<?php	

	/* 


	by isaacrulz23.
	
	Скрипт для группы, который кикает из беседы при @all или @everyone
	VK API - 5.80
	Все настройки находятся в файле settings
	
	
	*/

	include("settings.php");
	
	$keyGet = $_REQUEST["akey"];
	$userdata = json_decode(file_get_contents('php://input'));
	
	function kick($usr_id, $idconserv) {
		$idconserv = $idconserv - 2000000000;
		$eeee = file_get_contents("https://api.vk.com/method/messages.removeChatUser?access_token={$GLOBALS['token']}&chat_id={$idconserv}&user_id={$usr_id}&v=5.88", true);
	}
	
	function snd_msg($idconserv,$msg) {
		$nernd_id = $GLOBALS['userdata']->object->date;
		$nernd_id .= $GLOBALS['userdata']->object->from_id;
		$request_params = array( 
			'message' => $msg,
			'random_id' => $nernd_id,
			'peer_id' => $idconserv, 
			'access_token' => $GLOBALS['token'], 
			'v' => '5.103' 
		);
		$get_params = http_build_query($request_params); 
		$otvet = file_get_contents('https://api.vk.com/method/messages.send?'. $get_params);
	}
	
	if($keyGet == $secretKey)
	{
		switch ($userdata->type) 
		{
			case 'confirmation':
				die($confirmationKey);
				break;
			case 'message_new':
				$message = $userdata->object->text;
				$message = mb_strtolower($message, 'UTF-8');
				$id = $userdata->object->from_id;
				$beseda = $userdata->object->peer_id;
				if ($id == $beseda) {
					snd_msg($beseda, $GLOBALS['privatemessage']);
					die("ok");
					break;
				} else {
					if (strpos($message, $GLOBALS['testcmd']) !== false) {
						snd_msg($beseda, $GLOBALS['testanswer']);
					}
					
					if ($message == "") {
						$json = json_decode(file_get_contents('php://input'),1);
						$action = $json[object]['action'];
						if (isset($action['type'])) {
							$member_id = $action['member_id'];
							if ($action['type'] == "chat_invite_user") {
								if ($id == $member_id) {
									snd_msg($beseda, $GLOBALS['returnconserv']);
									die("ok");
									break;
								} else {
									snd_msg($beseda, $GLOBALS['greeting']);
									die("ok");
									break;
								}
							} elseif ($action['type'] == "chat_invite_user_by_link") {
								snd_msg($beseda, $GLOBALS['greeting']);
								die("ok");
								break;
							} elseif ($action['type'] == "chat_kick_user") {
								if ($id == $member_id) {
									snd_msg($beseda, $GLOBALS['userleave']);
								} else {
									snd_msg($beseda, $GLOBALS['kickadminpanel']);
								}
								die("ok");
								break;
							} else {
								die("ok");
								break;
							}
						}
					}
					
					if (strpos($message, '!kick') !== false) {
						json_decode(file_get_contents('php://input'));
						$request_params = array( 
						'peer_ids' => $beseda, 
						'access_token' => $GLOBALS['token'], 
						'v' => '5.103' 
						);
						$get_params = http_build_query($request_params); 
						$otvet = file_get_contents('https://api.vk.com/method/messages.getConversationsById?'. $get_params);
						$otvet = json_decode($otvet,1);
						$admins[] = $otvet['response']['items']['0']['chat_settings']['owner_id'];
						$admins = array_merge($admins,$otvet['response']['items']['0']['chat_settings']['admin_ids']);
						if (in_array($id, $admins)) {
							$json = json_decode(file_get_contents('php://input'),1);
							if (isset($json[object]['fwd_messages']['0']['from_id'])) {
								if (in_array($json[object]['fwd_messages']['0']['from_id'], $admins)) { 
									$kostblLy = snd_msg($beseda, $GLOBALS['dontkickadmins']);
								} else {
									$kostblLy = snd_msg($beseda, $GLOBALS['byebye']);
									kick($json[object]['fwd_messages']['0']['from_id'], $beseda);
								}
							} else {
								$kostblLy = snd_msg($beseda, $GLOBALS['faggot']);
							}
						} else {
							$kostblLy = snd_msg($beseda, $GLOBALS['youshallnotpass']);
						}
					}
					
					if (preg_match($GLOBALS['regular'], $message)) {
						json_decode(file_get_contents('php://input'));
						$request_params = array( 
							'peer_ids' => $beseda, 
							'access_token' => $GLOBALS['token'], 
							'v' => '5.103' 
						);
						$get_params = http_build_query($request_params); 
						$otvet = file_get_contents('https://api.vk.com/method/messages.getConversationsById?'. $get_params);
						$otvet = json_decode($otvet,1);
						$admins[] = $otvet['response']['items']['0']['chat_settings']['owner_id'];
						$admins = array_merge($admins,$otvet['response']['items']['0']['chat_settings']['admin_ids']);
						if (in_array($id, $admins)) {
							$kostblLy = snd_msg($beseda, $GLOBALS['dontautokickadmins']);
						} else {
							$kostblLy = snd_msg($beseda, $GLOBALS['lastword']);
							kick($id, $beseda);
						}	
					}
					die("ok");
				}
		}
	}
	else
	{
		die(json_encode(array("response" => 0, "error" => array("error_id" => 1, "error_message" => "Bad secret key"))));
	}
?>