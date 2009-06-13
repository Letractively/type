<?php
/**
 * Advanced Web Typography
 * 
 * This is the main class file for the Advanced Web Typography library.
 * @author David Kendal <thinglie@gmail.com>, Peter Corcoran <drewmoo@gmail.com>
 * @version 1.0
 * @package advanced.web.typography.beta
 */

/**
 * This is the main typography class.
 * @package advanced.web.typography.beta
 * @subpackage advanced.web.typography.beta.classes
 */
class Typography
{
    /**
     * Ligatures we want replacing.
     * @access public
     * @var array
     */
	public $ligature_pairs = array();
	
    /**
     * Regular Expressions for matching numbers.
     * @access public
     * @var array
     */
	public $numbers_match = array();

    /**
     * Kern these pairs of letters, a simple approach.
     * @access public
     * @var array
     */
	public $kern_pairs = array();

    /**
     * This variable contains the options for processing.
     * @access private
     * @var array
     */
	private $options = array();
	
    /**
     * Constructor sets up {@link $kern_pairs} {@link $ligature_pairs} {@link $numbers_match}
     * @param string $ligatures Process Ligatures?
     * @param string $magicquote Process Magic Quotes?
     * @param string $kern Process Kerns?
     * @param string $numbers Process Numbers?
     */
	public function __construct($ligatures = true, $magicquote = true, $kern = true, $numbers = true)
	{
		$this->kern_pairs = array(	"Fa", "Fc", "Fe", "Fg", "Fm", "Fn", "Fo", "Fp", "Fq", "Fr", "Fs", "Fu", "Fv", "Fw", "Fx", "Fy", "Fz", "F.", "F,",
									"Ku", "Kv", "Kw", "Ky", "Pa", "Pc", "Pe", "Pg", "Pm", "Pn", "Po", "Pp", "Pq", "Pr", "Ps", "Pu", "F.", "F,", "AV",
									"Ta", "Tc", "Te", "Tg", "Tm", "Tn", "To", "Tp", "Tq", "Tr", "Ts", "Tu", "Tv", "Tw", "Tx", "Ty", "Tz", "T.", "T,",
									"Va", "Vc", "Ve", "Vg", "Vm", "Vn", "Vo", "Vp", "Vq", "Vr", "Vs", "Vu", "Vv", "Vw", "Vx", "Vy", "Vz", "V.", "V,",
									"Wa", "Wc", "We", "Wg", "Wm", "Wn", "Wo", "Wp", "Wq", "Wr", "Ws", "Wu", "Wv", "Ww", "Wx", "Wy", "Wz", "W.", "W,",
									"Ya", "Yc", "Ye", "Yg", "Ym", "Yn", "Yo", "Yp", "Yq", "Yr", "Ys", "Yu", "Yv", "Yw", "Yx", "Yy", "Yz", "Y.", "Y,",
									"WA", "AW", "VA");
									
		$this->ligature_pairs = array(	"ffi" => "&#xfb03;", "ffl" => "&#xfb04;", "fi" => "&#xfb01;", "ff" => "&#xfb00;",
										"fl" => "&#xfb02;",  "..." => "&hellip;");
										
		$this->numbers_match = array('/([0-9]+)(st|nd|rd|th)/' => '$1<sup>$2</sup>', '/No\. ([0-9]+)/' => '&#8470; $1');
		
		$this->options = array("ligatures" => $ligatures, "magicquote" => $magicquote, "kern" => $kern, "numbers" => $numbers);
	}

    /**
     * Process any given text ($text)
     * @param string $text
     * @return string processed text
     */
	public function process($text) {
		if($this->options["ligatures"])  $text = $this->add_ligatures($text);
		if($this->options["magicquote"]) $text = $this->magicquote($text);
		if($this->options["kern"])       $text = $this->kern($text);
		if($this->options["numbers"])    $text = $this->numbers($text);
		return $text;
	}

    /**
     * Adds ligatures to given text ($text)
     * @param string $text
     * @return string processed text
     */
	public function add_ligatures($text) {
		return str_replace(array_keys($this->ligature_pairs), array_values($this->ligature_pairs), $text);
	}
	
    /**
     * Handles latin kern pairs to given text ($text)
     * @param string $text
     * @return string processed text
     */
	public function kern($text) {
		$kerns = array();
		foreach ($this->kern_pairs as $pair)
			$kerns[$pair] = "<span style=\"letter-spacing: -0.1em\">" . $pair[0] . "</span>" . $pair[1];
		return str_replace(array_keys($kerns), array_values($kerns), $text);
	}
	
	/**
     * Handles numbers in given text ($text)
     * @param string $text
     * @return string processed text
     */
	public function numbers($text) {
		return preg_replace(array_keys($this->numbers_match), array_values($this->numbers_match), $text);
	}
	/**
     * Handles quotes in given text ($text)
     * @param string $text
     * @return string processed text
     */
	public function magicquote($text) {
		$charlist = str_split($text);
		$dquote_open = "&#8221;";
		$squote_open = "&#8217;";
		$html_open = false;
		for($i=0;$i<count($charlist);$i++) {
			$char = &$charlist[$i];
			switch ($char) {
				case "<" :
					$html_open = true;
					break;
				case ">" :
					$html_open = false;
					break;
				case "\"" :
					if (!$html_open) {
						if($dquote_open == "&#8220;")
							$dquote_open = $char = "&#8221;";
						elseif($dquote_open == "&#8221;")
							$dquote_open = $char = "&#8220;";
					}
					break;
				case "'" :
					if (!$html_open) {
						if($squote_open == "&#8217;" || $charlist[$i-1] == " ")
							$squote_open = $char = "&#8216;";
						elseif($squote_open == "&#8216;")
							$squote_open = $char = "&#8217;";
						break;
					}
			}
		}
		return implode($charlist);
	}
}
?>