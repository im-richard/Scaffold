<?php

/**
 * Formatter
 *
 * This module uses the methods from Minify_Compressor.php from Minify.
 * I've modified it to allow for optional compression in certain parts.
 * 
 * @author Stephen Clay <steve@mrclay.org>
 * @author Anthony Short
 */
class Scaffold_Engine_Formatter
{
	public function format(Scaffold_CSS_Source $css)
	{
		$class = __CLASS__ . '_' . $this->settings['engine'];
		$controller = new $class();
		$controller->format($css);
	}

    /**
     * Minify a CSS string
     * 
     * @param string $css
     * @return string
     */
    public function minify($css)
    {
        $css = str_replace("\r\n", "\n", $css);

        // apply callback to all valid comments (and strip out surrounding ws
        $css = preg_replace_callback('@\\s*/\\*([\\s\\S]*?)\\*/\\s*@',array('Formatter', '_commentCB'), $css);

        // Convert rgb() values to hex
        $css = $this->rgb_to_hex($css);
        
        // Strip out the units on 0 measurements eg 0px
        $css = preg_replace('/([^0-9])0(px|em|\%)/', "\${1}0", $css);
        $css = preg_replace('/([^0-9])0\.([0-9]+)em/', '$1.$2em', $css);
        $css = preg_replace('/\-0([^\.])/',"0\${1}",$css);

        // Convert font-weights to numbers
        $css = $this->font_weights_to_numbers($css);

        // remove ws around { } and last semicolon in declaration block
        $css = preg_replace('/\\s*{\\s*/', '{', $css);
        $css = preg_replace('/;?\\s*}\\s*/', '}', $css);
        
        // remove ws surrounding semicolons
        $css = preg_replace('/\\s*;\\s*/', ';', $css);
        
        // remove ws around urls
        $css = preg_replace('/
                url\\(      # url(
                \\s*
                ([^\\)]+?)  # 1 = the URL (really just a bunch of non right parenthesis)
                \\s*
                \\)         # )
            /x', 'url($1)', $css);
        
        // remove ws between rules and colons
        $css = preg_replace('/
                \\s*
                ([{;])              # 1 = beginning of block or rule separator 
                \\s*
                ([\\*_]?[\\w\\-]+)  # 2 = property (and maybe IE filter)
                \\s*
                :
                \\s*
                (\\b|[#\'"])        # 3 = first character of a value
            /x', '$1$2:$3', $css);
        
        // remove ws in selectors
        $css = preg_replace_callback('/
                (?:              # non-capture
                    \\s*
                    [^~>+,\\s]+  # selector part
                    \\s*
                    [,>+~]       # combinators
                )+
                \\s*
                [^~>+,\\s]+      # selector part
                {                # open declaration block
            /x'
            ,array('Formatter', '_selectorsCB'), $css);
        
        // minimize hex colors
        $css = preg_replace('/([^=])#([a-f\\d])\\2([a-f\\d])\\3([a-f\\d])\\4([\\s;\\}])/i'
            , '$1#$2$3$4$5', $css);
        
        // remove spaces between font families
        $css = preg_replace_callback('/font-family:([^;}]+)([;}])/'
            ,array('Formatter', '_fontFamilyCB'), $css);
        
        $css = preg_replace('/@import\\s+url/', '@import url', $css);
        
        // replace any ws involving newlines with a single newline
        $css = preg_replace('/[ \\t]*\\n+\\s*/', "\n", $css);
        
        // separate common descendent selectors w/ newlines (to limit line lengths)
        $css = preg_replace('/([\\w#\\.\\*]+)\\s+([\\w#\\.\\*]+){/', "$1\n$2{", $css);
        
        // Use newline after 1st numeric value (to limit line lengths).
        $css = preg_replace('/
            ((?:padding|margin|border|outline):\\d+(?:px|em)?) # 1 = prop : 1st numeric value
            \\s+
            /x'
            ,"$1\n", $css);

        return trim($css);
    }
    
    /**
     * Converts font-weights into numbers
     *
     * @param $css
     * @return return type
     */
    private function font_weights_to_numbers($css)
    {
    	if( $found = Scaffold::$css->find_properties_with_value('font-weight','bold|normal') )
    	{	
	    	foreach($found[2] as $key => $value)
	    	{
	    		if($value == 'bold')
	    		{
	    			$css = str_replace($found[0][$key],'font-weight:700',$css);
	    		}
	    		elseif($value == 'normal')
	    		{
	    			$css = str_replace($found[0][$key],'font-weight:400',$css);
	    		}
	    	}
    	}
    	
    	return $css;
    }
    
    /**
     * Takes a CSS string, finds all rgb() values and converts them to hex format
     *
     * @param $css
     * @return string
     */
    private function rgb_to_hex($css)
    {
    	if( $rgbs = Scaffold::$css->find_functions('rgb') )
    	{
	    	foreach( $rgbs[2] as $key => $found )
	    	{
	    		$color = null;
	    		
	    		foreach(explode(',',$found) as $value)
	    		{
	    			$hex = dechex($value);
	    			$color .= ( strlen($hex) == 1 ) ? 0 . $hex : $hex;
	    		}
	
	    		$css = str_replace($rgbs[0][$key],"#" . $color,$css);
	    	}
    	}
    	
    	return $css;
    }
    
    /**
     * Replace what looks like a set of selectors  
     *
     * @param array $m regex matches
     * @return string
     */
    protected function _selectorsCB($m)
    {
        // remove ws around the combinators
        return preg_replace('/\\s*([,>+~])\\s*/', '$1', $m[0]);
    }
    
    /**
     * Process a comment and return a replacement
     * 
     * @param array $m regex matches
     * @return string
     */
    protected function _commentCB($m)
    {
        $m = $m[1];

        // $m is the comment content w/o the surrounding tokens, 
        // but the return value will replace the entire comment.

		if($this->preserve_comments === true)
		{
			return '/*' .$m. '*/';
		}
		else
		{
			return ''; // remove all other comments
		}
    }
    
    /**
     * Process a font-family listing and return a replacement
     * 
     * @param array $m regex matches
     * 
     * @return string   
     */
    protected function _fontFamilyCB($m)
    {
        $m[1] = preg_replace('/
                \\s*
                (
                    "[^"]+"      # 1 = family in double qutoes
                    |\'[^\']+\'  # or 1 = family in single quotes
                    |[\\w\\-]+   # or 1 = unquoted family
                )
                \\s*
            /x', '$1', $m[1]);
        return 'font-family:' . $m[1] . $m[2];
    }
    
    /**
     * Makes a CSS string easier to read by adding line breaks where
     * needed and stripping out unneeded whitespace
     *
     * @param $css
     * @return string
     */
    public function prettify($css)
    {
  		// escape data protocol to prevent processing
    	$css = preg_replace('#(url\(data:[^\)]+\))#e', "'esc('.base64_encode('$1').')'", $css);
  
  		// line break after semi-colons (for @import)
    	$css = str_replace(';', ";\r\r", $css);
 	
    	// normalize comments spacing and lines
    	$css = preg_replace('#\*/#sx',"*/\r",$css);
    	
    	// normalize space around opening brackets
    	$css = preg_replace('#\s*\{\s*#', "\r{\r", $css); 
    	
    	// normalize property name/value space
    	$css = preg_replace('#([-a-z]+):\s*([^;}{]+);\s*#i', "\t$1: $2;\r", $css); 
    	
    	// normalize space around closing brackets
    	$css = preg_replace('#\s*\}\s*#', "\r}\r\r", $css);
    	
    	// new line for each selector in a compound selector
    	$css = preg_replace('#,\s*#', ",\r", $css);
   
    	// remove returns after commas in property values
    	if (preg_match_all('#:[^;]+,[^;]+;#', $css, $m))
    	{
    		foreach($m[0] as $oops)
    		{
    			$css = str_replace($oops, preg_replace('#,\r#', ', ', $oops), $css);
    		}
    	}
    	
    	$css = preg_replace('#esc\(([^\)]+)\)#e', "base64_decode('$1')", $css); // unescape escaped blocks
    	
    	// indent nested @media rules
    	if (preg_match('#@media[^\{]*\{(.*\}\s*)\}#', $css, $m))
    	{
    		$css = str_replace($m[0], str_replace($m[1], "\r\t".preg_replace("#\r#", "\r\t", trim($m[1]))."\r", $m[0]), $css);
    	}

    	return $css;
    }
} 
