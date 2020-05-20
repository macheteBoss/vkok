<?php
require_once 'Api.php';
require_once '../lib/database_class.php';

class UsersApi extends Api
{
    public $apiName = 'users';

    /**
     * Метод GET
     * Вывод списка всех записей
     * http://ДОМЕН/users
     * @return string
     */
    public function indexAction()
    {
        $token = ($this->requestParams['token']) ? $this->requestParams['token'] : '';
        $db = new DataBase();
        if($userData = $db->getAll("users", "`token`!='".$token."'","id", false)) {
            $userI = $db->getField("users", "id", "token", $token);
            $data = array();
            for($i = 0; $i < count($userData); $i++) {
                if($friendData = $db->select("friends", array("*"), "(`user_id`='".$userData[$i]["id"]."' AND `friend_id`='".$userI."') OR (`user_id`='".$userI."' AND `friend_id`='".$userData[$i]["id"]."')")) {
                    if($friendData[0]["user_id"] == $userI) {
                        $bufData = array("id" => $userData[$i]["id"], "name"=>$userData[$i]["name"], "lastName"=>$userData[$i]["lastName"],
                            "email"=>$userData[$i]["email"], "phone"=>$userData[$i]["phone"], "birthday"=>$userData[$i]["birthday"], "avatarUrl"=>$userData[$i]["avatarUrl"],
                            "country"=>$userData[$i]["country"], "city"=>$userData[$i]["city"], "work"=>$userData[$i]["work"]);
                        $bufData["status"] = $friendData[0]["flag_sub"];
                        $data[] = $bufData;
                    } else {
                        $bufData = array("id" => $userData[$i]["id"], "name"=>$userData[$i]["name"], "lastName"=>$userData[$i]["lastName"],
                            "email"=>$userData[$i]["email"], "phone"=>$userData[$i]["phone"], "birthday"=>$userData[$i]["birthday"], "avatarUrl"=>$userData[$i]["avatarUrl"],
                            "country"=>$userData[$i]["country"], "city"=>$userData[$i]["city"], "work"=>$userData[$i]["work"]);
                        switch ($friendData[0]["flag_sub"]) {
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
                } else {
                    $buf = array("id" => $userData[$i]["id"],"name"=>$userData[$i]["name"], "lastName"=>$userData[$i]["lastName"],
                        "email"=>$userData[$i]["email"], "phone"=>$userData[$i]["phone"], "birthday"=>$userData[$i]["birthday"], "avatarUrl"=>$userData[$i]["avatarUrl"],
                        "country"=>$userData[$i]["country"], "city"=>$userData[$i]["city"], "work"=>$userData[$i]["work"],
                        "status"=>3);
                    $data[] = $buf;
                }
            }
            return $this->response($data, 200);
        }
        return $this->response("Error", 401);
    }


    /**
     * Метод GET
     * Просмотр отдельной записи (по id)
     * http://ДОМЕН/users/1
     * @return string
     */
    public function viewAction()
    {
        //id должен быть первым параметром после /users/x
        $id = array_shift($this->requestUri);

        if($id){
            $db = new DataBase();
            $userData = $db->getElementOnID("users", $id);
            if($userData){
                $data = array("user" => array("id" => $userData["id"], "name"=>$userData["name"], "lastName"=>$userData["lastName"],
                    "email"=>$userData["email"], "phone"=>$userData["phone"], "birthday"=>$userData["birthday"], "avatarUrl"=>$userData["avatarUrl"],
                    "country"=>$userData["country"], "city"=>$userData["city"], "work"=>$userData["work"]));
                return $this->response($data, 200);
            }
        }
        return $this->response('Data not found', 404);
    }

    public function createAction()
    {
        return $this->response("Saving error", 500);
    }

    /**
     * Метод PUT
     * Обновление отдельной записи (по ее id)
     * http://ДОМЕН/users/1 + параметры запроса name, email
     * @return string
     */
    public function updateAction()
    {
        $postData = file_get_contents('php://input');
        $dataJson = json_decode($postData,true);

        $id = $dataJson["id"];
        $name = $dataJson["name"];
        $lastName = $dataJson["lastName"];
        $email = $dataJson["email"];
        $phone = $dataJson["phone"];
        $birthday = $dataJson["birthday"];
        $avatarUrl = $dataJson["avatarUrl"];
        $country = $dataJson["country"];
        $city = $dataJson["city"];
        $work = $dataJson["work"];

        $db = new DataBase();

        if($name && $email){
            $user = array("name" => $name, "lastName" => $lastName, "email" => $email,
                "phone" => $phone, "birthday" => $birthday, "avatarUrl" => $avatarUrl, "country" => $country, "city" => $city, "work" => $work);
            if($db->update("users", $user, "`id`='".$id."'")){
                return $this->response($user, 200);
            }
        }

        return $this->response("Update error", 400);
    }

    public function deleteAction()
    {
        return $this->response("Delete error", 500);
    }

}