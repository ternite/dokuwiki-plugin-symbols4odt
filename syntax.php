<?php
/**
 * DokuWiki Plugin symbols4odt (Syntax Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Thomas Schäfer <thomas.schaefer@itschert.net>
 */

// must be run within Dokuwiki
if (!defined('DOKU_INC')) {
    die();
}

abstract class Symbols_Syntax_Plugin extends DokuWiki_Syntax_Plugin {
	
	protected array $patternMap;
	
	protected abstract function getPatterns();
	
	/**
     * @return string Syntax mode type
     */
    public function getType()
    {
        return 'substition';
    }

    /**
     * @return string Paragraph type
     */
    public function getPType()
    {
        return 'normal';
    }

    /**
     * @return int Sort order - Low numbers go before high numbers
     */
    public function getSort()
    {
        return 1;
    }
	
    /**
     * Connect lookup pattern to lexer.
     *
     * @param string $mode Parser mode
     */
    public function connectTo($mode)
    {
		foreach($this->getPatterns() as $pluginMode => $substitutionSet) {
			foreach($substitutionSet["pattern"] as $substitionString) {
				$this->Lexer->addSpecialPattern($substitionString, $mode, substr(get_class($this), 7));
			}
		}
	}

    /**
     * Handle matches of the symbols4odt syntax
     *
     * @param string       $match   The match of the syntax
     * @param int          $state   The state of the handler
     * @param int          $pos     The position in the document
     * @param Doku_Handler $handler The handler
     *
     * @return array Data for the renderer
     */
    public function handle($match, $state, $pos, Doku_Handler $handler)
    {
		foreach($this->getPatterns() as $pluginMode => $substitutionSet) {
			foreach($substitutionSet["pattern"] as $patternString) {
				if (strcasecmp($patternString,$match) === 0)
					return array(
						"substitute4XHTML" => $substitutionSet["substitute4XHTML"],
						"substitute4ODT" => $substitutionSet["substitute4ODT"],
					);
			}
		}
        return array();
    }

    /**
     * Render xhtml output or metadata
     *
     * @param string        $mode     Renderer mode (supported modes: xhtml)
     * @param Doku_Renderer $renderer The renderer
     * @param array         $data     The data from the handler() function
     *
     * @return bool If rendering was successful.
     */
    public function render($mode, Doku_Renderer $renderer, $data)
    {
        if ($mode == 'xhtml') {
			
			$renderer->doc .= $data["substitute4XHTML"];
            return true;
			
        } elseif ($mode == 'odt') {
			
			$renderer->cdata($data["substitute4ODT"]);
			return true;
			
		}

        return false;
    }
	

    /**
     * Returns a unicode character for the given unicode number.
     *
     * @param string    $unicodenumber    Unicode number of the character to be returned - only the number!
	                                      For example, in order to get an 'A' character (unicode number: U+0041), provide a parameter $unicodenumber with a value of '0041'.
     *
     * @return The encoded String or Array on success, false in case of an error. (see return value of mb_convert_encoding, https://www.php.net/manual/de/function.mb-convert-encoding.php)
     */
	public function getUTF8forHexadecimal(string $unicodenumber){
		return mb_convert_encoding('&#x'.$unicodenumber.';', 'UTF-8', 'HTML-ENTITIES');
	}
}

class syntax_plugin_symbols4odt extends Symbols_Syntax_Plugin
{
	private $patterns;
	
	protected function getPatterns() {
		if (!isset($this->patterns)) {
			$this->patterns = array(
				"shy" => array( 
					"pattern" 			=> array('<SHY>','<shy>','<->'), //array('(?<![\x20-\x2F\x5C])\x5C\x2D','<SHY>','<shy>','<->'), // the first (the complex) pattern is looking for '\-', but I don't get that to compare to the $match in handle()
					"substitute4XHTML"	=> '&shy;',
					"substitute4ODT" 	=>$this->getUTF8forHexadecimal('00AD'), // alternative: chr(194).chr(173),
				),
				"checkbox_empty" => array( 
					"pattern" 			=> array('<checkbox>','<CHECKBOX>'),
					"substitute4XHTML"	=>  "<input type='checkbox'/>",
					"substitute4ODT" 	=> $this->getUTF8forHexadecimal('2610'),
				),
				"checkbox_filled" => array( 
					"pattern" 			=> array('<checkbox_checked>','<CHECKBOX_CHECKED>'),
					"substitute4XHTML"	=>  "<input type='checkbox' checked/>",
					"substitute4ODT" 	=> $this->getUTF8forHexadecimal('2612'),
				),
			);
		}
		
		return $this->patterns;
	}
}

