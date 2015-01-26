<?php

abstract class API {
    
    protected $method;
    protected $model;
    protected $mappings = array();
    protected $payload = array();
    protected $headers = array();
    
    function __construct($args) {
        $this->headers['cors'] = "Access-Control-Allow-Methods: *";
        $this->headers['content-type'] = "Content-Type: application/json";
        $this->_getRequestMethod();
        $this->construct($args);
        $this->setCacheControl('private', 30);
        $this->_executeAction($this->method, $args);
    }
    
    protected function setCacheControl($type = 'private', $maxAge = 30){
        $this->headers['cache-control'] = "Cache-Control: $type, max-age=$maxAge";
    }
    
    protected function reject($message, $status = 500){
        $data = array();
        $data['status'] = $status;
        $data['message'] = $message;
        $this->respond($data, $status);
    }
    
    protected function respond($data, $status = 200) {
        $this->headers[] = "HTTP/1.1 " . $status . " " . $this->_requestStatus($status);
        $this->payload = $data;
    }
    
    private function _getRequestMethod (){
        $this->method = $_SERVER['REQUEST_METHOD'];
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
            $this->query();
        } else {
            $this->get($args);
        }
    }
    
    private function _handlePostRequest($args, $data){
        $this->save($args, $data);
    }
    
    private function _handlePutRequest($args, $data){
        $this->update($args, $data);    
    }
    
    private function _handleDeleteRequest($args){
        $this->delete($args);
    }
    
    protected function query() {
        $this->reject('query not implemented', 501);
    }
    
    protected function get($args) {
        $this->reject('get not implemented', 501);
    }
    
    protected function save($data) {
        $this->reject('save not implemented', 501);
    }
    
    protected function update($args, $data) {
        $this->reject('update not implemented', 501);
    }
    
    protected function delete($args, $data) {
        $this->reject('delete not implemented', 501);
    }
    
    private function _requestStatus($code) {
        $status = array(  
            200 => 'OK',
            204 => 'No Content',
            401 => 'Unauthorized',
            404 => 'Not Found',   
            406 => 'Not Acceptable',   
            405 => 'Method Not Allowed',
            500 => 'Internal Server Error',
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