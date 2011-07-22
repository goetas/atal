<?php

class BasicHTMLTest extends  PHPUnit_Framework_TestCase{
	/**
	 * @var \goetas\atal\ATalXHTML
	 */
	protected $tal;
	protected function setUp(){
    	$this->tal = new \goetas\atal\ATalXHTML(sys_get_temp_dir());
    }
 
    public function testAssign(){
    	$this->tal->foo = "bar";
    	$this->assertEquals("bar", $this->tal->foo );
    }
	public function testAssignArray(){
    	$this->tal->foo[2] = "bar";
    	$this->assertEquals("bar", $this->tal->foo[2] );
    }
    public function testGet() {
    	$str = $this->tal->get(__DIR__."/templates/1.xml");
    	var_dump($str);
    	$this->assertEquals("<div>Test</div>", $str );
    }    
}