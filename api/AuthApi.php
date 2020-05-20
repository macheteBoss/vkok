<?php
require_once 'Api.php';
require_once '../lib/database_class.php';

class AuthApi extends Api
{
    public $apiName = 'auth';

    public function indexAction()
    {
        $token = ($this->requestParams['token']) ? $this->requestParams['token'] : '';
        $db = new DataBase();
        if($userData = $db->select("users", array("*"), "`token`='".$token."'")) {
            $data = array("user" => array("id" => $userData[0]["id"], "name"=>$userData[0]["name"], "lastName"=>$userData[0]["lastName"],
                "email"=>$userData[0]["email"], "phone"=>$userData[0]["phone"], "birthday"=>$userData[0]["birthday"], "avatarUrl"=>$userData[0]["avatarUrl"],
                "country"=>$userData[0]["country"], "city"=>$userData[0]["city"], "work"=>$userData[0]["work"]));
            return $this->response($data, 200);
        }
        return $this->response("Error", 401);
    }

    public function viewAction()
    {
        return $this->response('Data not found', 404);
    }

    public function createAction()
    {
        $postData = file_get_contents('php://input');
        $dataJson = json_decode($postData, true);
        $db = new DataBase();
        if($userData = $db->select("users", array("*"), "`email`='".$dataJson["email"]."'")) {
            if($userData[0]["password"] != md5($dataJson["password"])) {
                return $this->response('Error password', 401);
            }
            $pass = rand(1, 10000);
            $hash=password_hash($pass, PASSWORD_DEFAULT);
            $db->update("users", array("token"=>$hash), "`id`='".$userData[0]["id"]."'");
            $data = array("token" => $hash, "user" => array("id" => $userData[0]["id"], "name"=>$userData[0]["name"], "lastName"=>$userData[0]["lastName"],
                "email"=>$userData[0]["email"], "phone"=>$userData[0]["phone"], "birthday"=>$userData[0]["birthday"], "avatarUrl"=>$userData[0]["avatarUrl"],
                "country"=>$userData[0]["country"], "city"=>$userData[0]["city"], "work"=>$userData[0]["work"]));
            return $this->response($data, 200);
        }
        return $this->response("Auth error", 401);
    }

    public function updateAction()
    {
        return $this->response("Update error", 400);
    }

    public function deleteAction()
    {
        return $this->response("Delete error", 500);
    }

}