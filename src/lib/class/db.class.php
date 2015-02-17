<?php

class DB {
    private $db;
    private $err;
    private $table;
        
    function __construct($table) {
        $dsn = 'mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8';
        try {
            $this->db = new PDO($dsn, DB_USER, DB_PASSWORD, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
            $this->table = $table;
        } catch (PDOException $e) {
            echo 'Connection failed: ' . $e->getMessage();
            exit;
        }
    }
    
    function select($where = null, $projection = '*'){
        try{
            $a = array();
            $q = array();
            if(isset($where) && is_array($where) && count($where) > 0){
                foreach ($where as $key => $value) {
                    if(is_numeric($value)){
                        $q[] = $key." = :".$key;
                    }else{
                        $q[] = $key. " like :".$key;    
                    }
                    $a[":".$key] = $value;
                }    
            }
            $sql = "select $projection from $this->table";
            if(count($q) > 0){
                $sql .= " where ". implode(' and ', $q);
            }
            $stmt = $this->db->prepare($sql);
            $stmt->execute($a);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if(count($rows)<=0){
                $response["status"] = "empty";
                $response["message"] = "No records match the criteria."; 
            }else{
                $response["status"] = "success";
            }
                $response["data"] = $rows;
        }catch(PDOException $e){
            $response["status"] = "error";
            $response["message"] = 'Select Failed: ' .$e->getMessage();
        }
        return $response;
    }
    
    function selectOne($where = null, $projection = '*'){
        $result = $this->select($where, $projection);
        if($result['status'] === 'success'){
            $result['data'] = $result['data'][0];    
        }
        
        return $result;
    }
    
    function insert($columnsArray, $requiredColumns = []) {
        $check = $this->verifyRequiredParams($columnsArray, $requiredColumns);
        if($check['errorCount'] > 0){
            return $check;
        }
        
        try{
            $a = array();
            $c = "";
            $v = "";
            foreach ($columnsArray as $key => $value) {
                $c .= $key. ", ";
                $v .= ":".$key. ", ";
                $a[":".$key] = $value;
            }
            $c = rtrim($c,', ');
            $v = rtrim($v,', ');
            $stmt =  $this->db->prepare("INSERT INTO $this->table($c) VALUES($v)");
            $stmt->execute($a);
            $affected_rows = $stmt->rowCount();
            $response["status"] = "success";
//            $response["data"] = $stmt->fetch(PDO::FETCH_ASSOC);
            $response["message"] = $affected_rows." row inserted into database";
        }catch(PDOException $e){
            $response["status"] = "error";
            $response["message"] = 'Insert Failed: ' .$e->getMessage();
        }
        return $response;
    }
    
    function delete($where) {
        if(count($where) == 0){
            $response["status"] = "warning";
            $response["message"] = "Delete Failed: At least one condition is required.";
        } else {
            try{
                $a = array();
                $q = "";
                foreach ($where as $key => $value) {
                    if(is_numeric($value)){
                        $q[] = $key." = :".$key;
                    }else{
                        $q[] = $key. " like :".$key;    
                    }
                    $a[":".$key] = $value;
                }
                $sql = "DELETE FROM $this->table";
                if(count($q) > 0){
                    $sql .= " WHERE ". implode(' AND ', $q);
                }
                $stmt =  $this->db->prepare($sql);
                $stmt->execute($a);
                $affected_rows = $stmt->rowCount();
                if($affected_rows<=0){
                    $response["status"] = "empty";
                    $response["message"] = "No row deleted.";
                }else{
                    $response["status"] = "success";
                    $response["message"] = $affected_rows." row(s) deleted from database.";
                }
            }catch(PDOException $e){
                $response["status"] = "error";
                $response["message"] = 'Delete Failed: ' .$e->getMessage();
            }
        }
        return $response;
    }
    
    function verifyRequiredParams($dataArray, $requiredColumns) {
        $errorColumns = "";
        $errorCount = 0;
        $response = array();
        foreach ($requiredColumns as $field) {
            if (!isset($dataArray[$field]) || strlen(trim($dataArray[$field])) <= 0) {
                $errorCount++;
                $errorColumns .= $field . ', ';
            }
        }
 
        if ($errorCount > 0) {
            $response["status"] = "error";
            $response["message"] = 'Required field(s) ' . rtrim($errorColumns, ', ') . ' ' . ($errorCount === 1 ? 'is' : 'are') .' missing or empty.';
        }
        $response['errorCount'] = $errorCount;
        return $response;
    }
    
}


?>