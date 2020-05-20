<?php
require_once 'Api.php';
require_once '../lib/database_class.php';

class FriendsApi extends Api
{
    public $apiName = 'friends';

    public function indexAction()
    {
        return $this->response("Data not found", 401);
    }

    public function viewAction()
    {
        $id = array_shift($this->requestUri);
        $db = new DataBase();
        if($friendData = $db->select("friends", array("*"), "`user_id`='".$id."' OR `friend_id`='".$id."'")) {
            $data = array();
            for($i = 0; $i < count($friendData); $i++) {
                if($friendData[$i]["user_id"] == $id) {
                    $buf = $db->select("users", array("*"), "`id`='".$friendData[$i]["friend_id"]."'");
                    $bufData = array("id" => $buf[0]["id"], "name"=>$buf[0]["name"], "lastName"=>$buf[0]["lastName"],
                        "email"=>$buf[0]["email"], "phone"=>$buf[0]["phone"], "birthday"=>$buf[0]["birthday"], "avatarUrl"=>$buf[0]["avatarUrl"],
                        "country"=>$buf[0]["country"], "city"=>$buf[0]["city"], "work"=>$buf[0]["work"]);
                    $bufData["status"] = $friendData[$i]["flag_sub"];
                    $data[] = $bufData;
                } else {
                    $buf = $db->select("users", array("*"), "`id`='".$friendData[$i]["user_id"]."'");
                    $bufData = array("id" => $buf[0]["id"], "name"=>$buf[0]["name"], "lastName"=>$buf[0]["lastName"],
                        "email"=>$buf[0]["email"], "phone"=>$buf[0]["phone"], "birthday"=>$buf[0]["birthday"], "avatarUrl"=>$buf[0]["avatarUrl"],
                        "country"=>$buf[0]["country"], "city"=>$buf[0]["city"], "work"=>$buf[0]["work"]);
                    switch ($friendData[$i]["flag_sub"]) {
                        case "0":
                            $bufData["status"] = 0;
                            break;
                        case "1":
                            $bufData["status"] = 2;
                            break;
                        default: $bufData["status"] = 1;
                    }
                    $data[] = $bufData;
                }
            }
            return $this->response($data, 200);
        }
        return $this->response("Error", 401);
    }

    public function createAction()
    {
        $postData = file_get_contents('php://input');
        $dataJson = json_decode($postData, true);
        $userId = $dataJson["userId"];
        $friendId = $dataJson["friendId"];
        $db = new DataBase();
        if($friendData = $db->select("friends", array("*"), "(`user_id`='".$userId."' AND `friend_id`='".$friendId."') OR (`user_id`='".$friendId."' AND `friend_id`='".$userId."')")) {
            if($db->update("friends", array("flag_sub"=>0), "`id`='".$friendData[0]["id"]."'")) {
                if($friend = $db->select("users", array("*"), "`id`='".$friendId."'")) {
                    $data = array("id" => $friend[0]["id"], "name"=>$friend[0]["name"], "lastName"=>$friend[0]["lastName"],
                        "email"=>$friend[0]["email"], "phone"=>$friend[0]["phone"], "birthday"=>$friend[0]["birthday"], "avatarUrl"=>$friend[0]["avatarUrl"],
                        "country"=>$friend[0]["country"], "city"=>$friend[0]["city"], "work"=>$friend[0]["work"], "status"=>0);
                    return $this->response($data, 200);
                }
                return $this->response("Error update", 401);
            }
        } else {
            if($newData = $db->insert("friends", array("user_id"=>$userId, "friend_id"=>$friendId, "flag_sub"=>1))) {
                if($friendData = $db->select("users", array("*"), "`id`='".$friendId."'")) {
                    $data = array("id" => $friendData[0]["id"], "name"=>$friendData[0]["name"], "lastName"=>$friendData[0]["lastName"],
                        "email"=>$friendData[0]["email"], "phone"=>$friendData[0]["phone"], "birthday"=>$friendData[0]["birthday"], "avatarUrl"=>$friendData[0]["avatarUrl"],
                        "country"=>$friendData[0]["country"], "city"=>$friendData[0]["city"], "work"=>$friendData[0]["work"], "status"=>1);
                    return $this->response($data, 200);
                }
            }
        }
        return $this->response("Error", 401);
    }

    public function updateAction()
    {
        return $this->response("Update error", 400);
    }

    public function deleteAction()
    {
        return $this->response("Delete error", 401);
    }

}