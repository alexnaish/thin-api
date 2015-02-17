<?php

class AuthController extends API {

    protected function construct(){
        $this->model = new AuthenticationModel();
    }
    
    function save($args, $data){
        $result = $this->model->attemptLogin($data['username'], $data['password']);
        switch($result['status']){
            case 'success':
                $this->respond($result, 200);
                break;
            default:
                $this->reject('Unable to login.', 401);
                break;
        }
    }
}

?>