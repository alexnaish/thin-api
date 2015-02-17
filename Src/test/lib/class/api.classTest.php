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
    
    public function testClassHasNecessaryMethods(){
         $this->assertClassHasAttribute('foo', 'API');
    }
    
}
