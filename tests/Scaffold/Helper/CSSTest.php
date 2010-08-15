<?php

class Scaffold_Helper_CSSTest extends PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		$this->object = new Scaffold_Helper_CSS();
	}
	
	/**
	 * Remove inline comments
	 * @author Anthony Short
	 * @test
	 */
	public function Remove_inline_comments()
	{
		$string = "\n// Inline comment\n#id { background:blue }";
		$expected = "\n#id { background:blue }";
		$output = $this->object->remove_inline_comments($string);
		$this->assertEquals($output,$expected);
	}

	/**
	 * Remove new lines and tabs
	 * @author Anthony Short
	 * @test
	 */
	public function Remove_new_lines_and_tabs()
	{
		$string = "\n\r#id {\nbackground:\t\tblue;\n}\n\n";
		$expected = "#id {background:blue;}";
		$output = $this->object->remove_newlines($string);
		$this->assertEquals($output,$expected);
	}
	
	/**
	 * Remove css style comments
	 * @author Anthony Short
	 * @test
	 */
	public function Remove_css_style_comments()
	{
		$string = "/* Comment */#id /* Comment */{background/* Comment */:/* Comment */blue;/* Comment */}";
		$expected = "#id {background:blue;}";
		$output = $this->object->remove_comments($string);
		$this->assertEquals($output,$expected);
	}
	
	/**
	 * Find css style functions
	 * @author Anthony Short
	 * @test
	 */
	public function Find_css_style_functions()
	{
		$string = '#id {background:url( http://google.com );border-image:url("images/bullet.png")}';
		$expected = array
		(
			0 => array(
					'string' => 'url( http://google.com )',
					'params' => array('http://google.com ')
				),
			1 => array(
					'string' => 'url("images/bullet.png")',
					'params' => array('"images/bullet.png"')
				)
		);
	
		$output = $this->object->find_functions('url',$string);
		$this->assertEquals($output,$expected);
	}

    public function testFind_atrule()
    {
        $string = '@constants{property:value;property:value;}';
		$expected = array
		(
			0 => array(
					0 => '@constants{property:value;property:value;}',	// string
					1 => '',											// params
					2 => 'property:value;property:value;'				// content
				)
		);
	
		$actual = $this->object->find_atrule('constants',$string);
		$this->assertEquals($expected,$actual);
    }

    public function testRemove_atrule()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    public function testRuleset_to_array()
    {
        $string = 'background:blue;color:red';
		$expected = array
		(
			'background' => 'blue',
			'color' => 'red'
		);
	
		$output = $this->object->ruleset_to_array($string);
		$this->assertEquals($output,$expected);
    }

	/**
	 * Rules to array with odd characters
	 * @author Anthony Short
	 * @test
	 */
	public function Rules_to_array_with_odd_characters()
	{
		$string = 'font: 13px/20px "Helvetica Neue", Helvetica, Arial, sans-serif;color: #333;';
		$expected = array
		(
			'font' => '13px/20px "Helvetica Neue", Helvetica, Arial, sans-serif',
			'color' => '#333'
		);
		$output = $this->object->ruleset_to_array($string);
		$this->assertEquals($output,$expected);
		
		//
		
		$string = 'top: 0;left: 0;';
		$expected = array
		(
			'top' => 0,
			'left' => 0
		);
		$output = $this->object->ruleset_to_array($string);
		$this->assertEquals($output,$expected);
		
		//
		
		$string = 'font-family: /*Monaco, ProFont,*/ "Courier New", monospace;';
		$expected = array
		(
			'font-family' => '/*Monaco, ProFont,*/ "Courier New", monospace',
		);
		$output = $this->object->ruleset_to_array($string);
		$this->assertEquals($output,$expected);
		
		//
		
		$string = 'content: ":";';
		$expected = array
		(
			'content' => '":"',
		);
		$output = $this->object->ruleset_to_array($string);
		$this->assertEquals($output,$expected);
		
		// Duplicate rule keys
		
		$string = 'content: ":";content: \'Foo\'';
		$expected = array
		(
			'content' => "'Foo'",
		);
		$output = $this->object->ruleset_to_array($string);
		$this->assertEquals($output,$expected);
	}
	
	/**
	 * Find selectors with property
	 * @author Anthony Short
	 * @test
	 */
	public function Find_selectors_with_property()
	{
		$string = '#id{background:blue}#id2{color:blue}#id3{background-color:blue}#id{dbackground:blue}#id5{color:red;background:blue}';
		$expected = array
		(
			0 => array(
					'string' => '#id{background:blue}',
					'selector' => '#id' 
				),
			1 => array(
					'string' => '#id5{color:red;background:blue}',
					'selector' => '#id5'
				)
		);

		$output = $this->object->find_selectors_with_property('background',$string);
		$this->assertEquals($expected,$output);
	}
	
	/**
	 * Find all properties with a value
	 * @author Anthony Short
	 * @test
	 */
	public function Find_all_properties_with_a_value()
	{
		$string = '#id{background:blue}#id2{color:blue}#id3{background-color:red}#id{dbackground:blue}#id5{color : red ;background:blue}';
		$expected = array
		(
			0 => array(
					'string' => '#id5{color : red ;background:blue}',
					'selector' => '#id5',
					'property' => 'color : red ;',
					'value' => 'red'
				)
		);
		$output = $this->object->find_properties_with_value('color','red',$string);
		$this->assertEquals($expected,$output);
		
		$expected = array
		(
			0 => array(
					'string' => '#id3{background-color:red}',
					'selector' => '#id3',
					'property' => 'background-color:red',
					'value' => 'red'
				)
		);
		$output = $this->object->find_properties_with_value('background-color','red',$string);
		$this->assertEquals($expected,$output);
		
		$expected = array
		(
			0 => array(
					'string' => '#id{background:blue}',
					'selector' => '#id',
					'property' => 'background:blue',
					'value' => 'blue'
				),
			1 => array(
					'string' => '#id5{color : red ;background:blue}',
					'selector' => '#id5',
					'property' => 'background:blue',
					'value' => 'blue'
				)
		);
		$output = $this->object->find_properties_with_value('background','blue',$string);
		$this->assertEquals($expected,$output);
	}
	
	/**
	 * Encode a selector for regex
	 * @author Anthony Short
	 * @test
	 */
	public function Encode_a_selector_for_regex()
	{
		$selector = '*';
		$expected = '\*';
		$actual = $this->object->escape_regex($selector);
		$this->assertEquals($expected,$actual);
		
		$selector = 'id';
		$expected = 'id';
		$actual = $this->object->escape_regex($selector);
		$this->assertEquals($expected,$actual);
		
		$selector = 'E[foo]';
		$expected = 'E\[foo\]';
		$actual = $this->object->escape_regex($selector);
		$this->assertEquals($expected,$actual);
		
		$selector = 'E[foo="bar"]';
		$expected = 'E\[foo\="bar"\]';
		$actual = $this->object->escape_regex($selector);
		$this->assertEquals($expected,$actual);
		
		$selector = 'E[foo~="bar"]';
		$expected = 'E\[foo~\="bar"\]';
		$actual = $this->object->escape_regex($selector);
		$this->assertEquals($expected,$actual);
		
		$selector = 'E[foo^="bar"]';
		$expected = 'E\[foo\^\="bar"\]';
		$actual = $this->object->escape_regex($selector);
		$this->assertEquals($expected,$actual);
		
		$selector = 'E[foo*="bar"]';
		$expected = 'E\[foo\*\="bar"\]';
		$actual = $this->object->escape_regex($selector);
		$this->assertEquals($expected,$actual);
		
		$selector = 'E[foo|="en"]';
		$expected = 'E\[foo\|\="en"\]';
		$actual = $this->object->escape_regex($selector);
		$this->assertEquals($expected,$actual);
		
		$selector = 'E:root';
		$expected = 'E\:root';
		$actual = $this->object->escape_regex($selector);
		$this->assertEquals($expected,$actual);
		
		$selector = 'E:nth-child(n)';
		$expected = 'E\:nth\-child\(n\)';
		$actual = $this->object->escape_regex($selector);
		$this->assertEquals($expected,$actual);
		
		$selector = 'E:nth-last-child(n)';
		$expected = 'E\:nth\-last\-child\(n\)';
		$actual = $this->object->escape_regex($selector);
		$this->assertEquals($expected,$actual);
		
		$selector = 'E:nth-of-type(n)';
		$expected = 'E\:nth\-of\-type\(n\)';
		$actual = $this->object->escape_regex($selector);
		$this->assertEquals($expected,$actual);
		
		$selector = 'E::first-line';
		$expected = 'E\:\:first\-line';
		$actual = $this->object->escape_regex($selector);
		$this->assertEquals($expected,$actual);
		
		$selector = 'E.warning';
		$expected = 'E\.warning';
		$actual = $this->object->escape_regex($selector);
		$this->assertEquals($expected,$actual);
		
		$selector = 'E#myid';
		$expected = 'E\#myid';
		$actual = $this->object->escape_regex($selector);
		$this->assertEquals($expected,$actual);
		
		$selector = 'E F';
		$expected = 'E\s+F';
		$actual = $this->object->escape_regex($selector);
		$this->assertEquals($expected,$actual);
		
		$selector = 'E > F';
		$expected = 'E\s+\>\s+F';
		$actual = $this->object->escape_regex($selector);
		$this->assertEquals($expected,$actual);
		
		$selector = 'E + F';
		$expected = 'E\s+\+\s+F';
		$actual = $this->object->escape_regex($selector);
		$this->assertEquals($expected,$actual);
		
		$selector = 'E ~ F';
		$expected = 'E\s+~\s+F';
		$actual = $this->object->escape_regex($selector);
		$this->assertEquals($expected,$actual);
		
		$selector = 'E_F';
		$expected = 'E_F';
		$actual = $this->object->escape_regex($selector);
		$this->assertEquals($expected,$actual);
		
		$selector = 'E-F';
		$expected = 'E\-F';
		$actual = $this->object->escape_regex($selector);
		$this->assertEquals($expected,$actual);
	}
	
	/**
	 * Find selectors
	 * @author Anthony Short
	 * @test
	 */
	public function Find_selectors()
	{
		$string = 'id{background:blue}#id2{color:blue}#id3{background-color:red}#id4{dbackground:blue}#id5{color:red;background:blue}';
		$expected = array
		(
			0 => 'id{background:blue}'
		);
		$output = $this->object->find_selectors('id',$string);
		$this->assertEquals($expected,$output);
		
		$expected = array
		(
			0 => '#id2{color:blue}'
		);
		$output = $this->object->find_selectors('#id2',$string);
		$this->assertEquals($expected,$output);
		
		$expected = array
		(
			0 => '#id3{background-color:red}'
		);
		$output = $this->object->find_selectors('#id3',$string);
		$this->assertEquals($expected,$output);
		
		$expected = array
		(
			0 => '#id4{dbackground:blue}'
		);
		$output = $this->object->find_selectors('#id4',$string);
		$this->assertEquals($expected,$output);
		
		$expected = array
		(
			0 => '#id5{color:red;background:blue}'
		);
		$output = $this->object->find_selectors('#id5',$string);
		$this->assertEquals($expected,$output);
	}

    public function testFind_property()
    {
        $string = '
			id{background:blue}
			#id2{color:blue}
			#id3{background-color:red}
			#id4{dbackground:blue}
			#id5{color:red;background:blue}
		';
		$expected = array(
			0 => array(
					'string' => 'id{background:blue}',
					'selector' => 'id',
					'property' => 'background:blue',
					'value' => 'blue'
				),
			1 => array(
					'string' => '#id5{color:red;background:blue}',
					'selector' => '#id5',
					'property' => 'background:blue',
					'value' => 'blue'
				),
		);
		$output = $this->object->find_properties('background',$string);
		
		$this->assertEquals($expected,$output);
    }

    public function testSelector_exists()
    {
        $string = 'id{background:blue}#id2{color:blue}#id3{background-color:red}#id4{dbackground:blue}#id5{color:red;background:blue}';
		$output = $this->object->selector_exists('#id2',$string);
		$this->assertTrue($output);
		
		$string = 'id > blah{color:red;background:blue}';
		$output = $this->object->selector_exists('id > blah',$string);
		$this->assertTrue($output);
		
		$string = 'id > blah, bloo blah{color:red;background:blue}';
		$output = $this->object->selector_exists('id > blah',$string);
		$this->assertFalse($output);
    }

    public function testRemove_properties_with_value()
    {
        $string = 'id{background:blue}#id2{color:blue}#id3{background-color:red}#id4{dbackground:blue}#id5{color:red;background:blue}';
		$expected = 'id{}#id2{color:blue}#id3{background-color:red}#id4{dbackground:blue}#id5{color:red;}';
		$output = $this->object->remove_properties_with_value('background','blue',$string);
		$this->assertEquals($expected,$output);
    }

	public function testRemove_properties()
    {
        $string = 'id{background:blue}#id2{color:blue}#id3{background-color:red}#id4{dbackground:blue}#id5{color:red;background:blue}';
		$expected = 'id{}#id2{color:blue}#id3{background-color:red}#id4{dbackground:blue}#id5{color:red;}';
		$output = $this->object->remove_properties('background',$string);
		$this->assertEquals($expected,$output);
		
		//

		$string = 'id{background:blue}#id2{color:blue}#id3{background-color:red}#id4{dbackground:blue}#id5{color:red;background:blue}';
		$expected = 'id{background:blue}#id2{color:blue}#id3{}#id4{dbackground:blue}#id5{color:red;background:blue}';
		$output = $this->object->remove_properties('background-color',$string);
		$this->assertEquals($expected,$output);
    }
	
}