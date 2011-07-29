<?php

class BasicHTMLTest extends  PHPUnit_Framework_TestCase{
	/**
	 * @var \goetas\atal\ATalXHTML
	 */
	protected $tal;
	protected function setUp(){
    	$this->tal = new \goetas\atal\ATalXHTML(sys_get_temp_dir());
    	$this->tal->debug = true;
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
    	$this->assertEquals("<div>Test</div>", $str );
    }  
    public function testIf() {
    	$str = $this->tal->get(__DIR__."/templates/if.xml");
    	$this->assertEquals("<div>Test</div>", $str );
    } 
    public function testIf2() {
    	$str = $this->tal->get(__DIR__."/templates/if2.xml");
    	$this->assertEquals("", $str );
    } 
    public function testForeach() {
    	$str = $this->tal->get(__DIR__."/templates/foreach.xml");
    	$this->assertEquals("<div><div>0</div><div>1</div></div>", $str );
    } 
    public function testExtends() {
    	chdir(__DIR__);
    	$str = $this->tal->get("templates/2.xml");
    	$this->assertEquals("<div>Test esteso</div>", $str );
    } 
    public function testExtendsFinder() {
    	//chdir(__DIR__);
 
    	$this->tal->getFinder()->addFinder(new \goetas\atal\finders\Filesystem(realpath("templates")));
    	$this->tal->getFinder()->addFinder(new \goetas\atal\finders\Filesystem(realpath("templates/subdir")));
    	$str = $this->tal->get("2.xml");
    	$this->assertEquals("<div>Test esteso due</div>", $str );
    }     
    public function testExtendsSubdir() {
     	///chdir(__DIR__);
    	$str = $this->tal->get("templates/subdir/2.xml");
    	$this->assertEquals("<div>Test esteso due</div>", $str );
    } 
    public function testExtendsAbsolute() {
    	$this->tal->getFinder()->addFinder(new \goetas\atal\finders\Filesystem(realpath("templates")));
    	$str = $this->tal->get(__DIR__."/templates/subdir/2.xml");
    	$this->assertEquals("<div>Test esteso due</div>", $str );
    } 
    public function testExtendsAbsolute3() {
    	$str = $this->tal->get(__DIR__."/templates/3.xml");
    	$this->assertEquals("<div>Test esteso tre</div>", $str );
    } 
    public function testnoExct() {
    	$str = $this->tal->get(__DIR__."/templates/2-base.xml");
    	$this->assertEquals("<div>Test</div>", $str );
    }

    
    public function testDiff() {
    	//chdir(__DIR__);
 
    	
    	$tal = new \goetas\atal\ATalXHTML(sys_get_temp_dir());
    	$tal->getFinder()->setFinders();
    	$tal->getFinder()->addFinder(new \goetas\atal\finders\Filesystem(realpath("templates/subdir")));
    	//$tal->debug = true;
    	$str = $tal->get("2-base.xml");
    	$this->assertEquals("<div>Test</div>", $str );
    	
    	$tal = new \goetas\atal\ATalXHTML(sys_get_temp_dir());
    	$tal->getFinder()->setFinders();
    	$tal->getFinder()->addFinder(new \goetas\atal\finders\Filesystem(realpath("templates/subdir/subdir")));
    	
    	//$tal->debug = true;
    	$str = $tal->get("2-base.xml");
    	$this->assertEquals("<div>Test diff</div>", $str );
    } 
}