<?php

require_once dirname(__FILE__) . '/../../../lib/class/api.class.php';


class ConcreteApiClass extends API 
{
    function __destruct(){
       //Stub out 
    }
}

class ApiClassTest extends PHPUnit_Framework_TestCase
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
    
    public function testApiCanBeExtended()
	{
        $this->assertNotNull($this->apiClass); 
	}
    
    public function testClassHasNecessaryAttributes()
    {
        $this->assertObjectHasAttribute('headers', $this->apiClass);
        $this->assertObjectHasAttribute('mappings', $this->apiClass);
        $this->assertObjectHasAttribute('payload', $this->apiClass);
        $this->assertObjectHasAttribute('method', $this->apiClass);
        $this->assertObjectHasAttribute('model', $this->apiClass);
    }
    
    public function testClassConstructorSetsDefaultHeaders(){
        $cors = $this->getHeaderMethod->invoke($this->apiClass, 'cors');
        $contentType = $this->getHeaderMethod->invoke($this->apiClass, 'content-type');
        $cacheControl = $this->getHeaderMethod->invoke($this->apiClass, 'cache-control');
        
        $this->assertEquals("Access-Control-Allow-Methods: *", $cors);
        $this->assertEquals("Content-Type: application/json", $contentType);
        $this->assertEquals("Cache-Control: private, max-age=30", $cacheControl);
    }
    
    public function testSetCacheControlSetsCachingHeader(){
        $setCacheControlMethod = new ReflectionMethod('ConcreteApiClass', 'setCacheControl');
        $setCacheControlMethod->setAccessible(TRUE);
        
        //Defaults
        $setCacheControlMethod->invoke($this->apiClass);
        $cacheControl = $this->getHeaderMethod->invoke($this->apiClass, 'cache-control');
        $this->assertEquals("Cache-Control: private, max-age=30", $cacheControl);
        
        //Specific Values - 1
        $setCacheControlMethod->invoke($this->apiClass, 'public', 3000);
        $cacheControl = $this->getHeaderMethod->invoke($this->apiClass, 'cache-control');
        $this->assertEquals("Cache-Control: public, max-age=3000", $cacheControl);
        
        //Specific Values - 2
        $setCacheControlMethod->invoke($this->apiClass, 'private', 15);
        $cacheControl = $this->getHeaderMethod->invoke($this->apiClass, 'cache-control');
        $this->assertEquals("Cache-Control: private, max-age=15", $cacheControl);
    }
    
    public function testRequestStatusReturnsCorrectHeaderText(){
        $method = new ReflectionMethod('ConcreteApiClass', '_requestStatus');
        $method->setAccessible(TRUE);
        
        // Scenario 1 - 500 Internal Server Error
        $result = $method->invoke(new ConcreteApiClass(), 500);
        $this->assertEquals($result, 'Internal Server Error');
        
        // Scenario 2 - 404 Not Found
        $result = $method->invoke(new ConcreteApiClass(), 404);
        $this->assertEquals($result, 'Not Found');
        
        // Scenario 3 - 400 Bad Request
        $result = $method->invoke(new ConcreteApiClass(), 400);
        $this->assertEquals($result, 'Bad Request');
        
        // Scenario 4 - 501 Not Implemented
        $result = $method->invoke(new ConcreteApiClass(), 501);
        $this->assertEquals($result, 'Not Implemented');
        
        // Scenario 5 - 200 OK
        $result = $method->invoke(new ConcreteApiClass(), 200);
        $this->assertEquals($result, 'OK');
        
        // Scenario 6 - 201 Created
        $result = $method->invoke(new ConcreteApiClass(), 201);
        $this->assertEquals($result, 'Created');
    }
    
    
    
    public function testGetRequestMethodReturnsCorrectValue(){
        $instance = new ConcreteApiClass();
        $method = new ReflectionMethod('ConcreteApiClass', '_getRequestMethod');
        $method->setAccessible(TRUE);
        
        // Scenario 1 - GET Request
        $server = array('REQUEST_METHOD' => 'GET');
        $result = $method->invoke($instance, $server);
        $this->assertEquals($result, 'GET');
        
        // Scenario 2 - GET Request
        $server = array('REQUEST_METHOD' => 'POST');
        $result = $method->invoke($instance, $server);
        $this->assertEquals('POST', $result);
        
        // Scenario 3 - PUT Request
        $server = array('REQUEST_METHOD' => 'POST', 'HTTP_X_HTTP_METHOD' => 'PUT');
        $result = $method->invoke($instance, $server);
        $this->assertEquals('PUT', $result);
        
        // Scenario 4 - DEL Request
        $server = array('REQUEST_METHOD' => 'POST', 'HTTP_X_HTTP_METHOD' => 'DELETE');
        $result = $method->invoke($instance, $server);
        $this->assertEquals('DELETE', $result);
        
        // Scenario 5 - FAKE Request with HTTP_X_HTTP_METHOD
        // TODO
        
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
