<?php

require_once dirname(__FILE__) . '/../../../lib/class/api.class.php';

class GetEmptyApiClass extends API 
{
    function __destruct(){
       //Stub out 
    }
}

class GetUnMappedApiClass extends API 
{
    
    public $method;
    public $args = null;
    
    function query() {
        $this->method = 'QUERY';
    }
    
    function get($args) {
        $this->method = 'GET';
        $this->args = $args;
    }
    
    function __destruct(){
       //Stub out 
    }
}

class GetMappedApiClass extends API 
{
    
    public $method;
    public $args = null;
    
    function construct(){
        $this->mapping['QUERY'] = 'testQuery';
        $this->mapping['GET'] = 'testGet';
    }
    
    function testQuery(){
        $this->method = 'QUERY_MAPPED';
    }
    
    function testGet($args){
        $this->method = 'GET_MAPPED';
        $this->args = $args;
    }
    
    function __destruct(){
       //Stub out 
    }
}

class ApiGetMappingTest extends PHPUnit_Framework_TestCase
{
    private $blankClass;
    private $mappedClass;
    private $unmappedClass;
    private $getMethod;
    private $getPayloadMethod;
    private $getHeaderMethod;
 
    function setUp() {
        $this->blankClass = new GetEmptyApiClass();
        $this->mappedClass = new GetMappedApiClass();
        $this->unmappedClass = new GetUnMappedApiClass();
        $this->getMethod = new ReflectionMethod('API', '_handleGetRequest');
        $this->getMethod->setAccessible(TRUE);
        $this->getPayloadMethod = new ReflectionMethod('API', 'getPayload');
        $this->getPayloadMethod->setAccessible(TRUE);
        $this->getHeaderMethod = new ReflectionMethod('ConcreteApiClass', 'getHeader');
        $this->getHeaderMethod->setAccessible(TRUE);
    }
    
    public function testUnmappedApiUsesQueryRouteWhenNoArgs()
	{
        $this->getMethod->invoke($this->unmappedClass);
        $this->assertEquals('QUERY', $this->unmappedClass->method);
        $this->assertNull($this->unmappedClass->args);
	}
    
    public function testUnmappedApiUsesGetRouteWhenArgsAndPassesArgsIn()
	{
        $this->getMethod->invoke($this->unmappedClass, 'test');
        $this->assertEquals('GET', $this->unmappedClass->method);
        $this->assertEquals('test', $this->unmappedClass->args);
	}
    
    public function testUnmappedApiGetRoutePassesInAnyArgs()
	{
        $args = array('test', 'test2');
        
        $this->getMethod->invoke($this->unmappedClass, $args);
        $this->assertEquals('GET', $this->unmappedClass->method);
        $this->assertEquals($args, $this->unmappedClass->args);
	}
    
    public function testMappedApiUsesQueryRouteWhenNoArgs()
	{
        $this->getMethod->invoke($this->mappedClass);
        $this->assertEquals('QUERY_MAPPED', $this->mappedClass->method);
        $this->assertNull($this->mappedClass->args);
	}
    
    public function testMappedApiUsesGetRouteWhenArgsAndPassesArgsIn()
	{
        $this->getMethod->invoke($this->mappedClass, 'test');
        $this->assertEquals('GET_MAPPED', $this->mappedClass->method);
        $this->assertEquals('test', $this->mappedClass->args);
	}
    
    public function testMappedApiGetRoutePassesInAnyArgs()
	{
        $args = array('test', 'test2');
        
        $this->getMethod->invoke($this->mappedClass, $args);
        $this->assertEquals('GET_MAPPED', $this->mappedClass->method);
        $this->assertEquals($args, $this->mappedClass->args);
	}
    
    public function testQueryRouteProvidesNotImplementedIfNotDefined()
    {
        $this->getMethod->invoke($this->blankClass);
        
        $payload = $this->getPayloadMethod->invoke($this->blankClass);
        $header = $this->getHeaderMethod->invoke($this->blankClass, 'response');
        $this->assertEquals('query not implemented', $payload['message']);
        $this->assertEquals(501, $payload['status']);
        $this->assertEquals('HTTP/1.1 501 Not Implemented', $header);
    }
    
    public function testGetRouteProvidesNotImplementedIfNotDefined()
    {
        $this->getMethod->invoke($this->blankClass);
        
        $payload = $this->getPayloadMethod->invoke($this->blankClass, 'test');
        $header = $this->getHeaderMethod->invoke($this->blankClass, 'response');
        $this->assertEquals('query not implemented', $payload['message']);
        $this->assertEquals(501, $payload['status']);
        $this->assertEquals('HTTP/1.1 501 Not Implemented', $header);
    }
    
}
