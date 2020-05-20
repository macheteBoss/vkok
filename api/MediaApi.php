<?php
require_once 'Api.php';
require_once '../lib/database_class.php';

class MediaApi extends Api
{
    public $apiName = 'media';

    public function indexAction()
    {
        return $this->response("Error", 401);
    }

    public function viewAction()
    {
        $rash = array_shift($this->requestUri);
        $id = array_shift($this->requestUri);

        $content = file_get_contents('php://input');

        $pass = rand(1, 10000);
        $hash = password_hash($pass, PASSWORD_DEFAULT);

        $file = fopen($hash.".".$rash, 'w+');

        fwrite($file, $content);
        fclose($file);

        $dir = '/image/'.$hash.".".$rash;
        if(move_uploaded_file($hash.".".$rash, $dir )) {
            $db = new DataBase();
            $url = $_SERVER['SERVER_NAME']."/image".$hash.".".$rash;
            $db->setFieldOnID("users", $id, "avatarUrl", $url);
            return $this->response($url, 200);
        }

        return $this->response('Data not found', 404);
    }

    public function createAction()
    {
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