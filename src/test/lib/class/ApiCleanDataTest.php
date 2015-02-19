<?php

require_once dirname(__FILE__) . '/../../../lib/class/api.class.php';

class ApiCleanDataTest extends PHPUnit_Framework_TestCase
{
 
    private $apiClass;
    private $cleanDataMethod;
 
    function setUp() {
        $this->apiClass = new ConcreteApiClass();
        $this->cleanDataMethod = new ReflectionMethod('API', '_cleanData');
        $this->cleanDataMethod->setAccessible(TRUE);
    }
    
    public function testCleanDataRemovesHtmlTagsForStrings(){
        $string = "<script>alert('test');</script>";
        $expected = "alert(\'test\');";
        $result = $this->cleanDataMethod->invoke($this->apiClass, $string);
        $this->assertEquals($expected, $result);
    }
    
    public function testCleanDataRemovesHtmlTagsForEachArrayItem(){
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
        $result = $this->cleanDataMethod->invoke($this->apiClass, $array);
        $this->assertArrayHasKey('one', $result);
        $this->assertArrayHasKey('two', $result);
        $this->assertArrayHasKey('three', $result);
        
        $this->assertEquals($expected['one'], $result['one']);
        $this->assertEquals($expected['two'], $result['two']);
        $this->assertEquals($expected['three'], $result['three']);
        
    }
}
