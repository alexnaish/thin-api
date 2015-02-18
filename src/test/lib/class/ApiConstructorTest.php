<?php

require_once dirname(__FILE__) . '/../../../lib/class/api.class.php';


class ConcreteApiClass extends API 
{
    function __destruct(){
       //Stub out 
    }
}

class ApiConstructorTest extends PHPUnit_Framework_TestCase
{
    
    private $apiClass;
    private $getPayloadMethod;
    private $getHeaderMethod;
 
    function setUp() {
        $this->apiClass = new ConcreteApiClass();
        $this->getPayloadMethod = new ReflectionMethod('API', 'getPayload');
        $this->getPayloadMethod->setAccessible(TRUE);
        $this->getHeaderMethod = new ReflectionMethod('API', 'getHeader');
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
    
}
