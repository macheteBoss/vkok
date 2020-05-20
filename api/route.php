<?php

require_once "AuthApi.php";
require_once "RegistrationApi.php";
require_once "UsersApi.php";
require_once "FriendsApi.php";
require_once "MessagesApi.php";
require_once "MediaApi.php";

class Route {

    public $apiName;

    public function __construct($apiName) {
        $this->apiName = $apiName;
    }

    public function marsh() {
        switch ($this->apiName) {
            case "users":
                $api = new UsersApi();
                break;
            case "auth":
                $api = new AuthApi();
                break;
            case "registration":
                $api = new RegistrationApi();
                break;
            case "friends":
                $api = new FriendsApi();
                break;
            case "messages":
                $api = new MessagesApi();
                break;
            case "media":
                $api = new MediaApi();
                break;
            default: $api = "error";
        }
        return $api;
    }

}

?>