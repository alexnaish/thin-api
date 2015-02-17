<?php

abstract class API {
    
    protected $method;
    protected $model;
    protected $mappings = array();
    protected $payload = array();
    protected $headers = array();
    
    function __construct($args = array()) {
        $this->setHeader('cors', 'Access-Control-Allow-Methods: *');
        $this->setHeader('content-type', 'Content-Type: application/json');
        $this->_getRequestMethod($_SERVER['REQUEST_METHOD']);
        $this->construct($args);
        $this->setCacheControl('private', 30);
        $this->_executeAction($this->method, $args);
    }
    
    
    protected function construct(){
        //Stubbed
    }
    
    function setHeader($key, $value){
        $this->headers[$key] = $value;
    }
    
    function getHeader($key){
        return $this->headers[$key];
    }
    
    function getPayload(){
        return $this->payload;
    }
    
    function setCacheControl($type = 'private', $maxAge = 30){
        $this->setHeader('cache-control', "Cache-Control: $type, max-age=$maxAge");
    }
    
    function reject($message, $status = 500){
        $data = array();
        $data['status'] = $status;
        $data['message'] = $message;
        $this->respond($data, $status);
    }
    
    function respond($data, $status = 200) {
        $this->setHeader('response', "HTTP/1.1 " . $status . " " . $this->_requestStatus($status));
        $this->payload = $data;
    }
    
    private function _getRequestMethod ($serverRequest){
        $this->method = $serverRequest;
        if ($this->method == 'POST' && array_key_exists('HTTP_X_HTTP_METHOD', $_SERVER)) {
            if ($_SERVER['HTTP_X_HTTP_METHOD'] == 'DELETE') {
                $this->method = 'DELETE';
            } else if ($_SERVER['HTTP_X_HTTP_METHOD'] == 'PUT') {
                $this->method = 'PUT';
            } else {
                $this->reject('Unknown Request Method', 500);
            }
        }
    }
    
    private function _executeAction($method, $args) {
        $args = $this->_cleanData($args);
        switch ($method){
            case 'GET':
                $this->_handleGetRequest($args);
                break;
            case 'PUT':
                $json = utf8_encode(file_get_contents('php://input'));
                $request = json_decode($json, true);
                $this->_handlePutRequest($args, $this->_cleanData($request));
                break;
            case 'POST':
                $json = utf8_encode(file_get_contents('php://input'));
                $request = json_decode($json, true);
                $this->_handlePostRequest($args, $this->_cleanData($request));
                break;
            case 'DELETE':
                $this->_handleDeleteRequest($args);
                break;
            default:
                $this->reject('Method Not Allowed', 405);
        }
    }
    
    private function _cleanData ($data) {
        $clean_input = Array();
        if (is_array($data)) {
            foreach ($data as $k => $v) {
                $clean_input[$k] = $this->_cleanData($v);
            }
        } else {
            $clean_input = trim(strip_tags($data));
        }
        return $clean_input;
    }
    
    private function _handleGetRequest($args){
        if(count($args) == 0 || $args[0] == '') {
            if(isset($this->mapping['QUERY'])){
                $this->$this->mapping['QUERY']();
            } else {
                $this->query();    
            }
        } else {
            
            if(isset($this->mapping['GET'])){
                $this->$this->mapping['GET']($args);
            } else {
                $this->get($args);   
            }
        }
    }
    
    private function _handlePostRequest($args, $data){
        if(isset($this->mapping['POST'])){
            $this->$this->mapping['POST']($args, $data);
        } else {
            $this->save($args, $data);
        }
    }
    
    private function _handlePutRequest($args, $data){
        if(isset($this->mapping['POST'])){
            $this->$this->mapping['POST']($args, $data);
        } else {
            $this->update($args, $data);    
        }
    }
    
    private function _handleDeleteRequest($args){
        if(isset($this->mapping['DELETE'])){
            $this->$this->mapping['DELETE']($args);
        } else {
            $this->delete($args);
        }
    }
    
    protected function query() {
        $this->reject('query not implemented', 501);
    }
    
    protected function get($args) {
        $this->reject('get not implemented', 501);
    }
    
    protected function save($args, $data) {
        $this->reject('save not implemented', 501);
    }
    
    protected function update($args, $data) {
        $this->reject('update not implemented', 501);
    }
    
    protected function delete($args) {
        $this->reject('delete not implemented', 501);
    }
    
    private function _requestStatus($code) {
        $status = array(  
            200 => 'OK',
            201 => 'Created',
            204 => 'No Content',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            403 => 'Forbidden',
            404 => 'Not Found',   
            405 => 'Method Not Allowed',
            406 => 'Not Acceptable',
            409 => 'Conflict',
            500 => 'Internal Server Error',
            501 => 'Not Implemented'
        ); 
        return ($status[$code])?$status[$code]:$status[500]; 
    }
    
    function __destruct(){
        foreach($this->headers as $header){
            header($header);
        }
        echo json_encode($this->payload);
    }
    
}


?>
