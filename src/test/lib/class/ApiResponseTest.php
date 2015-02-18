<?php

require_once dirname(__FILE__) . '/../../../lib/class/api.class.php';

class ApiResponseTest extends PHPUnit_Framework_TestCase
{
 
    private $apiClass;
    private $getPayloadMethod;
    private $getHeaderMethod;
 
    function setUp() {
        $this->apiClass = new ConcreteApiClass();
        $this->getPayloadMethod = new ReflectionMethod('ConcreteApiClass', 'getPayload');
        $this->getPayloadMethod->setAccessible(TRUE);
        $this->getHeaderMethod = new ReflectionMethod('ConcreteApiClass', 'getHeader');
        $this->getHeaderMethod->setAccessible(TRUE);
    }
    
    public function testRejectSetsResponsePayloadAndHeader(){
        $status = 500;
        $message = 'test message';
        $expectedHeader = "HTTP/1.1 500 Internal Server Error";
        
        $this->apiClass->reject($message, $status);

        $actualHeader = $this->getHeaderMethod->invoke($this->apiClass, 'response');
        $actualPayload = $this->getPayloadMethod->invoke($this->apiClass);
        
        $this->assertEquals($expectedHeader, $actualHeader);
        $this->assertArrayHasKey ('status', $actualPayload);
        $this->assertArrayHasKey ('message', $actualPayload);
        $this->assertEquals($status, $actualPayload['status']);
        $this->assertEquals($message, $actualPayload['message']);
    }
    
    public function testRespondSetsPayloadAndHeader(){
        
        //Scenario 1 - 200 Ok
        $status = 200;
        $payloadKey = "test";
        $payloadValue = "This is a value for an array";
        $payload = array($payloadKey => $payloadValue);
        $expectedHeader = "HTTP/1.1 200 OK";
        
        $this->apiClass->respond($payload, $status);
        $actualHeader = $this->getHeaderMethod->invoke($this->apiClass, 'response');
        $actualPayload = $this->getPayloadMethod->invoke($this->apiClass);
        
        $this->assertEquals($expectedHeader, $actualHeader);
        $this->assertArrayHasKey ($payloadKey, $actualPayload);
        $this->assertEquals($payloadValue, $actualPayload[$payloadKey]);
        
        //Scenario 2 - 409 Ok
        $status = 409;
        $payloadKey = "conflict";
        $payloadValue = "This is a test message with key information";
        $payload = array($payloadKey => $payloadValue);
        $expectedHeader = "HTTP/1.1 409 Conflict";
        
        $this->apiClass->respond($payload, $status);
        $actualHeader = $this->getHeaderMethod->invoke($this->apiClass, 'response');
        $actualPayload = $this->getPayloadMethod->invoke($this->apiClass);
        
        $this->assertEquals($expectedHeader, $actualHeader);
        $this->assertArrayHasKey ($payloadKey, $actualPayload);
        $this->assertEquals($payloadValue, $actualPayload[$payloadKey]);
    }
    
}