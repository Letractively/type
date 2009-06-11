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

/**
 * NOTE TO OTHER DEVS
 **
 * The code on line '103' revision 'r9'
 * needs expansion to support words like
 * 'erbal etc. These tend to be slag, but
 * need supporting.
 **
 * - R4000
 **/
 
class Typography {
  // a simple approach to kerning: just kern these pairs of letters.
  public $kern_pairs = array("Fa", "Fc", "Fe", "Fg", "Fm", "Fn", "Fo", "Fp", "Fq", "Fr", "Fs", "Fu", "Fv", "Fw", "Fx", "Fy", "Fz", "F.", "F,",
                             "Ku", "Kv", "Kw", "Ky",
                             "Pa", "Pc", "Pe", "Pg", "Pm", "Pn", "Po", "Pp", "Pq", "Pr", "Ps", "Pu", "F.", "F,",
                             "Ta", "Tc", "Te", "Tg", "Tm", "Tn", "To", "Tp", "Tq", "Tr", "Ts", "Tu", "Tv", "Tw", "Tx", "Ty", "Tz", "T.", "T,",
                             "Va", "Vc", "Ve", "Vg", "Vm", "Vn", "Vo", "Vp", "Vq", "Vr", "Vs", "Vu", "Vv", "Vw", "Vx", "Vy", "Vz", "V.", "V,",
                             "Wa", "Wc", "We", "Wg", "Wm", "Wn", "Wo", "Wp", "Wq", "Wr", "Ws", "Wu", "Wv", "Ww", "Wx", "Wy", "Wz", "W.", "W,",
                             "Ya", "Yc", "Ye", "Yg", "Ym", "Yn", "Yo", "Yp", "Yq", "Yr", "Ys", "Yu", "Yv", "Yw", "Yx", "Yy", "Yz", "Y.", "Y,",
                             "WA", "AW", "VA", "AV"
                             );
  // ligatures we want replacing.
  public $ligature_pairs = array("ffi", "ffl", "fi", "ff", "fl");
  public $ligatures = array("&#xfb03;", "&#xfb04;", "&#xfb01;", "&#xfb00;", "&#xfb02;");
  // magic quote regexps &c.
  public $quote_match = array('/"(.*)"/U', "/'(.*)'/U");
  public $quotes = array('&#8220;$1&#8221;', '&#8216;$1&#8217;');
  
  // internal variable to help with magic quotes.
  public $html_tag_replacements = array();
  
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
    // it's MUCH easier to do it with arrays and implode() than strings and substr()
    $charlist = str_split($text);
    $html_open = false;
    $squote_open = false;
    $dquote_open = false;
    // for? are you kidding, pc? foreach is the PHP loop-through construct :-)
    foreach ($charlist as $i=>&$char) { // notice the ampersand. $i is used to find previous letter.
      switch ($char) {
        case "<" :
          $html_open = true;
        break;
        case ">" :
          $html_open = false;
        break;
        case '"' :
          if (!$html_open) {
            if (!$dquote_open) {
              $char = "&#8220;";
              $dquote_open = true;
            } else {
              $char = "&#8221;";
              $dquote_open = false;
            }
          }
        break;
        case "'" :
          if (!$html_open) {
            if (!$squote_open) {
              $char = "&#8216;";
              if($charlist[$i-1] == " ") $squote_open = true; // If the char before ' isn't a space then don't open.
            } else {
              $char = "&#8217;";
              $squote_open = false;
            }
          }
        break;
      }
    }
    return implode($charlist);
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