<?php
/* Advanced Web Typography
 * by David Kendal
 *****************
 * This work 'as-is' we provide.
 * No warranty express or implied.
 * We've done our best,
 * to debug and test.
 * Liability for damages denied.
 *
 * Permission is granted hereby,
 * to copy, share, and modify.
 * Use as is fit,
 * free or for profit.
 * These rights, on this notice, rely.
 **/

class Typography {
  // a simple approach to kerning: just kern these pairs of letters.
  // TODO: get a typography expert to review these
  public $kern_pairs = array("Wa", "To", "LA", "P.", "Tr", "Ta", "Tu", "Ty", "WA", "We", "Wo", "Ya", "Yo");
  // ligatures we want replacing.
  public $ligature_pairs = array("ffi", "ffl", "fi", "ff", "fl");
  public $ligatures = array("&#xfb03;", "&#xfb04;", "&#xfb01;", "&#xfb00;", "&#xfb02;");
  // magic quote regexps &c.
  public $quote_match = array('/"(.*)"/U', "/'(.*)'/U");
  public $quotes = array('&#8220;$1&#8221;', '&#8216;$1&#8217;');
  
  // autoprocess options
  protected $kern;
  protected $ligature;
  protected $magicquote;
  
  public function __construct($kern=TRUE, $lig=TRUE, $magicquote=TRUE) {
    $this->kern = $kern; $this->ligature = $lig; $this->magicquote = $magicquote;
  }
  
  // automatically process the text
  public function process($text) {
    $text = $this->add_ligatures($text);
    $text = $this->magicquote($text);
    $text = $this->kern($text);
    return $text;
  }
  
  // add ligatures
  public function add_ligatures($text) {
    return str_replace($this->ligature_pairs, $this->ligatures, $text);
  }
  
  // add in all the magic quotes
  public function magicquote($text) {
    $html_open = false;
    $double_quote_open = false;
    $single_quote_open = false;
    $org_text = $text;
    for($offset=0,$i=0;$i<strlen($org_text);$offset++,$i++) {
      switch($org_text[$i]) {
        case "<": $html_open = true;  break;
        case ">": $html_open = false; break;
        case '"':
          if($html_open == false) {
            if($double_quote_open == true) {
              $double_quote_open = false;
              $text = substr($text, 0, $offset-1) . "&#8221;" . substr($text, $offset);
              $offset += 6;
            } else {
              $double_quote_open = true;
              $text = substr($text, 0, $offset) . "&#8220;" . substr($text, $offset+1);
              $offset += 6;
            }
          }
          break;
        case "'":
          if($html_open == false) {
            if($single_quote_open == true) {
              $single_quote_open = false;
              $text = substr($text, 0, $offset-1) . "&#8217;" . substr($text, $offset);
              $offset += 6;
            } else {
              $single_quote_open = true;
              $text = substr($text, 0, $offset) . "&#8216;" . substr($text, $offset+1);
              $offset += 6;
            }
          }
          break;
        }
      }
    return $text;
  }
  
  // add in the <span> kerning
	public function kern($text) {
    $kerns = array();
		foreach ($this->kern_pairs as $pair){
      $kerns[$pair] = "<span style=\"letter-spacing: -0.1em\">" . $pair[0] . "</span>" . $pair[1];
    }
    return str_replace(array_keys($kerns), array_values($kerns), $text);
	}
}
?>