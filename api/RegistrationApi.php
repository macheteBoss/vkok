<?php
require_once 'Api.php';
require_once '../lib/database_class.php';

class RegistrationApi extends Api
{
    public $apiName = 'registration';

    public function indexAction()
    {
        return $this->response('Data not found', 200);
    }

    public function viewAction()
    {
        return $this->response('Data not found', 200);
    }

    public function createAction()
    {
        $postData = file_get_contents('php://input');
        $data = json_decode($postData, true);
        $name = $data["name"];
        $lastName = $data["lastName"];
        $email = $data["email"];
        $password = $data["password"];
        if($name && $email && $password){
            $db = new DataBase();
            $pass = rand(1, 10000);
            $hash=password_hash($pass, PASSWORD_DEFAULT);
            $user = array("name" => $name, "lastName" => $lastName, "email" => $email, "password" => md5($password), "token" => $hash);
            if($db->isExists("users", "email", $email)){
                return $this->response('Error: User exists.', 401);
            }
            if($db->insert("users", $user)){
                $data = array("token" => $hash);
                return $this->response($data, 200);
            }
        }
        return $this->response("Saving error", 500);
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