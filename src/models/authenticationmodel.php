<?php

class AuthenticationModel {
    
    function __construct(){
        $this->db = new DB('users');
    }
    
    public function attemptLogin($username, $password){
        $userQuery = $this->db->selectOne(array('username' => $username));
        if($userQuery['status'] == 'success'){
            $result = [];
            $validPass = password_verify($password, $userQuery['data']['password']);
            if($validPass == true){
                $result['status'] = 'success';
                unset($userQuery['data']['password']);
                $result['data'] = $userQuery['data'];
            } else {
                $result['status'] = 'fail'; 
            }
            return $result;
        } else {
            return $userQuery;
        }
    }
    
    
}

?>