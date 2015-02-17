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
        $cacheControl = $this->apiClass->getHeader('cache-control');
        
        $this->assertEquals($cors, "Access-Control-Allow-Methods: *");
        $this->assertEquals($contentType, "Content-Type: application/json");
        $this->assertEquals($cacheControl, "Cache-Control: private, max-age=30");
    }
    
    public function testSetCacheControlSetsCachingHeader(){
        //Defaults
        $this->apiClass->setCacheControl();
        $cacheControl = $this->apiClass->getHeader('cache-control');
        $this->assertEquals($cacheControl, "Cache-Control: private, max-age=30");
        
        //Specific Values - 1
        $this->apiClass->setCacheControl('public', 3000);
        $cacheControl = $this->apiClass->getHeader('cache-control');
        $this->assertEquals($cacheControl, "Cache-Control: public, max-age=3000");
        
        //Specific Values - 2
        $this->apiClass->setCacheControl('private', 15);
        $cacheControl = $this->apiClass->getHeader('cache-control');
        $this->assertEquals($cacheControl, "Cache-Control: private, max-age=15");
    }
    
    public function testRejectMethodSetsResponsePayloadAndHeader(){
        $message = 'test message';
        $this->apiClass->reject($message, 500);
        $this->apiClass->getHeader('response');
        
        $this->assertEquals()
        
    }
    
    
    
}
