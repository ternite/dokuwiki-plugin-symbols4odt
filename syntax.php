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

class syntax_plugin_symbols4odt extends DokuWiki_Syntax_Plugin
{
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
        $this->Lexer->addSpecialPattern('<SHY>', $mode, 'plugin_symbols4odt');
        $this->Lexer->addSpecialPattern('<shy>', $mode, 'plugin_symbols4odt');
        $this->Lexer->addSpecialPattern('<->', $mode, 'plugin_symbols4odt');
        $this->Lexer->addSpecialPattern('(?<![\x20-\x2F\x5C])\x5C\x2D', $mode, 'plugin_symbols4odt'); // this one is looking for '\-'
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
			
			// insert a soft hyphen for html
			//Ziel: 9746 - ( U+2612 )
			//hex: 0xe2 0x98 0x92 - ( hex: 0x1a 0x0c )
			//chr: chr(242).chr(152).chr(146) ???
			//$renderer->doc .= "<input type='checkbox' id='vehicle1' name='vehicle1' value='Bike'/><label for='vehicle1'> I have a bike</label>";
			$renderer->doc .= '&shy;';
            return true;
			
        } elseif ($mode == 'odt') {
			
			// insert a soft hyphen
			// - [hyphen character:       - (ascii code:  &#45;)]
			// - [soft hyphen charactter: ­ (ascii code: &#173;)]
			$renderer->cdata(chr(194) . chr(173));
			//$renderer->cdata(mb_convert_encoding('&#x2612;', 'UTF-8', 'HTML-ENTITIES'));
			//$renderer->cdata(chr(242).chr(152).chr(146));
			//$renderer->doc.("<draw:control text:anchor-type='as-char' draw:z-index='3' draw:name='Form1' draw:style-name='gr1' draw:text-style-name='P24' svg:width='0.32cm' svg:height='0.32cm' draw:control='control1'/>");
			return true;
			
		}

        return false;
    }
}

