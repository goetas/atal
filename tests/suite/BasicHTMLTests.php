<?php

class BasicHTMLTests extends  PHPUnit_Framework_TestCase{
	/**
	 * @var \goetas\atal\ATalXHTML
	 */
	protected $tal;
	protected function setUp(){
    	$this->tal = new \goetas\atal\ATalXHTML();
    	$this->tal->setCompileDir("../cache");
    }
 
    public function testAssign(){
    	$this->tal->foo = "bar";
    }
 	
    
    
}