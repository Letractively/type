<?php
/**
 * Advanced Web Typography
 **
 * This is the main class file for the Advanced Web Typography library.
 * David Kendal <thinglie@gmail.com>
 * Peter Corcoran <drewmoo@gmail.com>
 **
 * This work 'as-is' we provide.
 * No warranty express or implied.
 * We've done our best,
 * to debug and test.
 * Liability for damages denied.
 **
 * Permission is granted hereby,
 * to copy, share, and modify.
 * Use as is fit,
 * free or for profit.
 * These rights, on this notice, rely.
 */
 
/** developer's notes
 *   - apostrophes not correctly handled for words like 'cello. -pc
 *   - numbers support currently experimental and should NOT yet be integrated into process() -dpk
 *       - Maybe it should now as we have yet to figure out any enhancements to it, and it seems stable -pc
 **/

class Typography
{
    /**
     * Ligatures we want replacing.
     **/
    public $ligature_pairs = array();
    
    /**
     * Regular Expressions for matching numbers.
     **/
    public $numbers_match = array();

    /**
     * Kern these pairs of letters, a simple approach.
     **/
    public $kern_pairs = array();

	/**
	 * Magic quote pairs
	 **/
	public $magicquote_pairs = array(	"<\"\">" => array("&#8221;", "&#8220"),
										"<''>" => array("&#8217;", "&#8216;"),
										);

    /**
     * This variable contains the options for processing.
     **/
    private $options = array();
    
    /**
     * Constructor sets up {@link $kern_pairs} {@link $ligature_pairs} {@link $numbers_match}
     **/
    public function __construct($ligatures = true, $magicquote = true, $kern = true, $numbers = true)
    {
        $this->kern_pairs = array(  "Fa", "Fc", "Fe", "Fg", "Fm", "Fn", "Fo", "Fp", "Fq", "Fr", "Fs", "Fu", "Fv", "Fw", "Fx", "Fy", "Fz", "F.", "F,",
                                    "Ku", "Kv", "Kw", "Ky", "Pa", "Pc", "Pe", "Pg", "Pm", "Pn", "Po", "Pp", "Pq", "Pr", "Ps", "Pu", "F.", "F,", "AV",
                                    "Ta", "Tc", "Te", "Tg", "Tm", "Tn", "To", "Tp", "Tq", "Tr", "Ts", "Tu", "Tv", "Tw", "Tx", "Ty", "Tz", "T.", "T,",
                                    "Va", "Vc", "Ve", "Vg", "Vm", "Vn", "Vo", "Vp", "Vq", "Vr", "Vs", "Vu", "Vv", "Vw", "Vx", "Vy", "Vz", "V.", "V,",
                                    "Wa", "Wc", "We", "Wg", "Wm", "Wn", "Wo", "Wp", "Wq", "Wr", "Ws", "Wu", "Wv", "Ww", "Wx", "Wy", "Wz", "W.", "W,",
                                    "Ya", "Yc", "Ye", "Yg", "Ym", "Yn", "Yo", "Yp", "Yq", "Yr", "Ys", "Yu", "Yv", "Yw", "Yx", "Yy", "Yz", "Y.", "Y,",
                                    "WA", "AW", "VA");
                                    
        $this->ligature_pairs = array(  "ffi" => "&#xfb03;", "ffl" => "&#xfb04;", "fi" => "&#xfb01;", "ff" => "&#xfb00;",
                                        "fl" => "&#xfb02;",  "..." => "&hellip;");
                                        
        $this->numbers_match = array('/([0-9]+)(st|nd|rd|th)/' => '$1<sup>$2</sup>', '/No\. ([0-9]+)/' => '&#8470; $1');
        
        $this->options = array("ligatures" => $ligatures, "magicquote" => $magicquote, "kern" => $kern, "numbers" => $numbers);
    }

    /**
     * Process any given text ($text)
     **/
    public function process($text) {
        if($this->options["ligatures"])  $text = $this->add_ligatures($text);
        if($this->options["magicquote"]) $text = $this->magicquote($text);
        if($this->options["kern"])       $text = $this->kern($text);
        //if($this->options["numbers"])    $text = $this->numbers($text); //EXPERIMENTAL
        return $text;
    }

    /**
     * Adds ligatures to given text ($text)
     **/
    public function add_ligatures($text) {
        return str_replace(array_keys($this->ligature_pairs), array_values($this->ligature_pairs), $text);
    }
    
    /**
     * Handles latin kern pairs to given text ($text)
     **/
    public function kern($text) {
        $kerns = array();
        foreach ($this->kern_pairs as $pair)
            $kerns[$pair] = "<span style=\"letter-spacing: -0.1em\">" . $pair[0] . "</span>" . $pair[1];
        return str_replace(array_keys($kerns), array_values($kerns), $text);
    }
    
    /**
     * Handles numbers in given text ($text)
     **/
    public function numbers($text) {
        return preg_replace(array_keys($this->numbers_match), array_values($this->numbers_match), $text);
    }
	
    /**
     * Handles quotes in given text ($text)
     **/
    public function magicquote($text) {
		$charlist = str_split($text);
		$inneropen = array();
		$outeropen = array();
		for($i = 0; $i < count($charlist); $i++) { // These a speed up here with a line increase :) -pc
			$char = &$charlist[$i];
			foreach($this->magicquote_pairs as $find => $replace) {
				switch(strlen($find)) {
					case 4: // Has outer1, inner1, inner2 and outer2 tag.
						list($outer1, $inner1, $inner2, $outer2) = str_split($find);
						if($char == $inner1 || $char == $inner2) {
							if($inneropen[$inner1] == false || $outeropen[$outer1] != true && $charlist[$i-1] == " ") {
								$char = $replace[0];
								$inneropen[$inner1] = true;
							} else if($inneropen[$inner1] == true || $outeropen[$outer1] != true) {
								$char = $replace[1];
								$inneropen[$inner1] = false;
							}
						}
						if($char == $outer1 || $char == $outer2) $outeropen[$outer1] == !$outeropen[$outer1]; // Switch it on/off.
						break;
					case 3: // Has outer1, inner1 and outer2 tag.
						list($outer1, $inner1, $inner2) = str_split($find);
						if($char == $inner1 || $char == $inner2) {
							if($inneropen[$inner1] == false || $outeropen[$outer1] != true && $charlist[$i-1] == " ") {
								$char = $replace[0];
								$inneropen[$inner1] = true;
							} else if($inneropen[$inner1] == true || $outeropen[$outer1] != true) {
								$char = $replace[1];
								$inneropen[$inner1] = false;
							}
						}
						if($char == $outer1) $outeropen[$outer1] == !$outeropen[$outer1]; // Switch it on/off.
						break;
					case 2: // Has inner1 and inner2 tag.
						// This can work like so because it is only a simple
						// two char search, therefore its just on or off :).
						list($inner1, $inner2) = str_split($find);
						if($char == $inner1 || $char == $inner2) {
							if($inneropen[$inner1] == false && $charlist[$i-1] == " ") {
								$char = $replace[0];
								$inneropen[$inner1] = true;
							} else if($inneropen[$inner1] == true) {
								$char = $replace[1];
								$inneropen[$inner1] = false;
							}
						}
						break;
					case 1: // Has inner1 tag.
						// Yay for cloning str_replace :(.
						// If the user only supplys one 'magicquote' char.
						// This will essentialy str_replace it.
						if($char == $find[0]) $char = $replace[0];
						break;
				}
			}
		}
        return implode($charlist);
    }
}
?>