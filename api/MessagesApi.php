<?php
require_once 'Api.php';
require_once '../lib/database_class.php';

class MessagesApi extends Api
{
    public $apiName = 'messages';

    public function indexAction()
    {
        $token = ($this->requestParams['token']) ? $this->requestParams['token'] : '';
        $db = new DataBase();
        if($userData = $db->select("users", array("id"),"`token`='".$token."'")) {
            $userId = $userData[0]["id"];
            if($partyData = $db->select("party", array("*"),"`user_id`='".$userId."'")) {
                $chats = array();
                $buf = array();
                for($i = 0; $i < count($partyData); $i++) {
                    $bufChat = $db->getElementOnID("chat", $partyData[$i]["chat_id"]);
                    $bufMess = $db->select("messages", array("*"),"`chat_id`='".$partyData[$i]["chat_id"]."'");
                    $users = $db->select("party", array("*"),"`chat_id`='".$partyData[$i]["chat_id"]."'");
                    $count = count($users);
                    $arr = array();
                    for($j = 0; $j < $count; $j++) {
                        $bufUser = $db->getElementOnID("users", $users[$j]["user_id"]);
                        $arr[] = array("id" => $bufUser["id"],"name"=>$bufUser["name"], "lastName"=>$bufUser["lastName"],
                            "email"=>$bufUser["email"], "phone"=>$bufUser["phone"], "birthday"=>$bufUser["birthday"], "avatarUrl"=>$bufUser["avatarUrl"],
                            "country"=>$bufUser["country"], "city"=>$bufUser["city"], "work"=>$bufUser["work"]);
                    }
                    $buf["id"] = $bufChat["id"];
                    $buf["name"] = $bufChat["name"];
                    $buf["messages"] = (count($bufMess) > 0) ? $bufMess : array();
                    $buf["users"] = $arr;
                    $chats[] = $buf;
                }
                return $this->response($chats, 200);
            }
            return $this->response("No Chats", 401);
        }
        return $this->response("Error", 401);
    }

    public function viewAction()
    {
        $id = array_shift($this->requestUri);
        $db = new DataBase();
        $info = array();
        $buf = array();
        if($chat = $db->getElementOnID("chat", $id)) {
            $buf["id"] = $chat["id"];
            $buf["name"] = $chat["name"];
            $bufMess = $db->select("messages", array("*"),"`chat_id`='".$chat["id"]."'");
            $users = $db->select("party", array("*"),"`chat_id`='".$chat["id"]."'");
            $count = count($users);
            $arr = array();
            for($j = 0; $j < $count; $j++) {
                $bufUser = $db->getElementOnID("users", $users[$j]["user_id"]);
                $arr[] = array("id" => $bufUser["id"],"name"=>$bufUser["name"], "lastName"=>$bufUser["lastName"],
                    "email"=>$bufUser["email"], "phone"=>$bufUser["phone"], "birthday"=>$bufUser["birthday"], "avatarUrl"=>$bufUser["avatarUrl"],
                    "country"=>$bufUser["country"], "city"=>$bufUser["city"], "work"=>$bufUser["work"]);
            }
            $buf["messages"] = (count($bufMess) > 0) ? $bufMess : array();
            $buf["users"] = $arr;
            $info[] = $buf;
            return $this->response($info, 200);
        }
        return $this->response("Error", 401);
    }

    public function createAction()
    {
        $postData = file_get_contents('php://input');
        $dataJson = json_decode($postData,true);
        $fromUser = $dataJson["fromUser"];
        $toUser = $dataJson["toUser"];
        $message = $dataJson["message"];
        $db = new DataBase();
        global $flag;
        global $chatId;
        if($chats = $db->select("party", array("*"), "`user_id`='".$fromUser."' OR `user_id`='".$toUser."'")) {
            for($i = 0; $i < count($chats); $i++) {
                $keys = array();
                if($chat = $db->select("party", array("*"), "`chat_id`='".$chats[$i]["chat_id"]."'")) {
                    for($j = 0; $j < count($chat); $j++) {
                        $keys[] = $chat[$j]["user_id"];
                    }
                    if(in_array($fromUser, $keys) && in_array($toUser, $keys)) {
                        $flag = true;
                        $chatId = $chats[$i]["chat_id"];
                        break;
                    } else $flag = false;
                }
            }
            if($flag == true) {
                if($message)
                    $db->insert("messages", array("chat_id"=>$chatId, "user_id"=>$fromUser, "contect"=>$message));
            } else {
                $db->insert("chat", array("name"=>$fromUser."".$toUser));
                $buf = $db->getElementOnField("chat", "name", $fromUser."".$toUser);
                $chatId = $buf["id"];
                if($message) {
                    $db->insert("messages", array("user_d"=>$fromUser, "chat_id"=>$buf["id"], "contect"=>$message));
                }
                $db->insert("party", array("user_id"=>$fromUser, "chat_id"=>$chatId));
                $db->insert("party", array("user_id"=>$toUser, "chat_id"=>$chatId));
            }
            return $this->response($chatId, 200);
        }
        return $this->response("Error", 401);
    }

    public function updateAction()
    {
        $postData = file_get_contents('php://input');
        $dataJson = json_decode($postData, true);
        $userId = $dataJson["userId"];
        $chatId = $dataJson["chatId"];
        $message = $dataJson["message"];
        $db = new DataBase();
        if($db->insert("messages", array("chat_id"=>$chatId, "user_id"=>$userId, "contect"=>$message))) {
            $chats = array();
            $buf = array();
            $bufChat = $db->getElementOnID("chat", $chatId);
            $bufMess = $db->select("messages", array("*"),"`chat_id`='".$chatId."'");
            $users = $db->select("party", array("*"),"`chat_id`='".$chatId."'");
            $count = count($users);
            $arr = array();
            for($j = 0; $j < $count; $j++) {
                $bufUser = $db->getElementOnID("users", $users[$j]["user_id"]);
                $arr[] = array("id" => $bufUser["id"],"name"=>$bufUser["name"], "lastName"=>$bufUser["lastName"],
                    "email"=>$bufUser["email"], "phone"=>$bufUser["phone"], "birthday"=>$bufUser["birthday"], "avatarUrl"=>$bufUser["avatarUrl"],
                    "country"=>$bufUser["country"], "city"=>$bufUser["city"], "work"=>$bufUser["work"]);
            }
            $buf["id"] = $bufChat["id"];
            $buf["name"] = $bufChat["name"];
            $buf["messages"] = $bufMess;
            $buf["users"] = $arr;
            $chats[] = $buf;

            return $this->response($chats, 200);
        }
        return $this->response("Update error", 401);
    }

    public function deleteAction()
    {
        return $this->response("Delete error", 401);
    }

}