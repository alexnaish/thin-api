<?php

require_once dirname(__FILE__) . '/../../../lib/class/api.class.php';

class ApiUtilityTest extends PHPUnit_Framework_TestCase
{
    
    private $apiClass;
    private $getHeaderMethod;
 
    function setUp() {
        $this->apiClass = new ConcreteApiClass();
        $this->getHeaderMethod = new ReflectionMethod('API', 'getHeader');
        $this->getHeaderMethod->setAccessible(TRUE);
    }
    
    public function testSetCacheControlSetsCachingHeader(){
        $setCacheControlMethod = new ReflectionMethod('API', 'setCacheControl');
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
        
    }
    
}
