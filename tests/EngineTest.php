<?php 

require_once 'PHPUnit/Framework.php';

// CSS object
require_once 'lib/CSS.php';

// Engine Modules
require_once 'lib/Engine/Constants.php';
require_once 'lib/Engine/Formatter.php';
require_once 'lib/Engine/Includes.php';
require_once 'lib/Engine/Iteration.php';
require_once 'lib/Engine/Mixins.php';
require_once 'lib/Engine/NestedSelectors.php';

// The Engine
require_once 'lib/Extension/Observable.php';
require_once 'lib/Engine.php';

class Scaffold_EngineTest extends PHPUnit_Framework_TestCase
{
	public $engine;

	public function setUp()
    {
    	$constants 	= $this->getMock('Scaffold_Engine_Constants');
    	$formatter 	= $this->getMock('Scaffold_Engine_Formatter');
    	$includes 	= $this->getMock('Scaffold_Engine_Includes');
    	$iteration 	= $this->getMock('Scaffold_Engine_Iteration');
    	$mixins 	= $this->getMock('Scaffold_Engine_Mixins');
    	$nested 	= $this->getMock('Scaffold_Engine_NestedSelectors');
    	
        $this->engine = new Scaffold_Engine($constants,$formatter,$includes,$iteration,$mixins,$nested);
    }

    public function testCompile()
    {
    	$css = new Scaffold_CSS(__DIR__ . '/files/test.css');
    	$this->engine->compile($css);
    }
}