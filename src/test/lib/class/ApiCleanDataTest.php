<?php

require_once dirname(__FILE__) . '/../../../lib/class/api.class.php';

class ApiCleanDataTest extends PHPUnit_Framework_TestCase
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
    
    public function testCleanDataRemovesHtmlTagsForStrings(){
        $cleanDataMethod = new ReflectionMethod('ConcreteApiClass', '_cleanData');
        $cleanDataMethod->setAccessible(TRUE);
        
        //Defaults
        $string = "<script>alert('test');</script>";
        $expected = "alert(\'test\');";
        $result = $cleanDataMethod->invoke($this->apiClass, $string);
        $this->assertEquals($expected, $result);
    }
    
    public function testCleanDataRemovesHtmlTagsForEachArrayItem(){
        $cleanDataMethod = new ReflectionMethod('ConcreteApiClass', '_cleanData');
        $cleanDataMethod->setAccessible(TRUE);
        
        //Defaults
        $array = array(
                    "one" => "<script>alert('test');</script>",
                    "two" => "<html>TestHTML</html>",
                    "three" => '<link rel="shortcut icon" href="http://www.php.net/favicon.ico">',
                );
        $expected = array(
                    "one" => "alert(\'test\');",
                    "two" => "TestHTML",
                    "three" => '',
                );
        $result = $cleanDataMethod->invoke($this->apiClass, $array);
        $this->assertArrayHasKey('one', $result);
        $this->assertArrayHasKey('two', $result);
        $this->assertArrayHasKey('three', $result);
        
        $this->assertEquals($expected['one'], $result['one']);
        $this->assertEquals($expected['two'], $result['two']);
        $this->assertEquals($expected['three'], $result['three']);
        
    }
}