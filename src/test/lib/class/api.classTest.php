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
 
    function setUp() {
        $this->apiClass = new ConcreteApiClass();
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
        $cors = $this->apiClass->getHeader('cors');
        $contentType = $this->apiClass->getHeader('content-type');
        
        $this->assertEquals($cors, "Access-Control-Allow-Methods: *");
        $this->assertEquals($contentType, "Content-Type: application/json");
    }
    
}
