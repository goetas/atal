<?php
namespace goetas\atal;

class ATalXHTML extends ATal {

	protected static $htmlEntities = array ('&uArr;' => '&#8657;', '&icirc;' => '&#238;', '&cong;' => '&#8773;', '&clubs;' => '&#9827;', '&thorn;' => '&#254;', '&rarr;' => '&#8594;', '&prime;' => '&#8242;', '&circ;' => '&#710;', '&Kappa;' => '&#922;', '&perp;' => '&#8869;', '&Uuml;' => '&#220;', '&pound;' => '&#163;', '&middot;' => '&#183;', '&larr;' => '&#8592;', '&acute;' => '&#180;', '&quot;' => '&#34;', '&otimes;' => '&#8855;', '&Mu;' => '&#924;', '&ordm;' => '&#186;', '&Theta;' => '&#920;', '&atilde;' => '&#227;', '&notin;' => '&#8713;', '&Xi;' => '&#926;', '&lfloor;' => '&#8970;', '&nbsp;' => '&#160;', '&ni;' => '&#8715;', '&Nu;' => '&#925;', '&Oslash;' => '&#216;', '&ndash;' => '&#8211;', '&ordf;' => '&#170;', '&thetasym;' => '&#977;', '&ne;' => '&#8800;', '&Rho;' => '&#929;', '&real;' => '&#8476;', '&oplus;' => '&#8853;', '&zwnj;' => '&#8204;', '&hearts;' => '&#9829;', '&alpha;' => '&#945;', '&euml;' => '&#235;', '&bdquo;' => '&#8222;', '&cap;' => '&#8745;', '&uacute;' => '&#250;', '&dagger;' => '&#8224;', '&lrm;' => '&#8206;', '&reg;' => '&#174;', '&Sigma;' => '&#931;', '&Tau;' => '&#932;', '&ensp;' => '&#8194;', '&sbquo;' => '&#8218;', '&Dagger;' => '&#8225;', '&sigma;' => '&#963;', '&plusmn;' => '&#177;', '&iota;' => '&#953;', '&emsp;' => '&#8195;', '&radic;' => '&#8730;', '&mdash;' => '&#8212;', '&Uacute;' => '&#218;', '&ecirc;' => '&#234;', '&ntilde;' => '&#241;', '&image;' => '&#8465;', '&uarr;' => '&#8593;', '&sim;' => '&#8764;', '&acirc;' => '&#226;', '&Beta;' => '&#914;', '&Yuml;' => '&#376;', '&shy;' => '&#173;', '&Acirc;' => '&#194;', '&Ucirc;' => '&#219;', '&Eta;' => '&#919;', '&Iacute;' => '&#205;', '&yacute;' => '&#253;', '&apos;' => '&#39;', '&Eacute;' => '&#201;', '&nsub;' => '&#8836;', '&Aring;' => '&#197;', '&Igrave;' => '&#204;', '&ccedil;' => '&#231;', '&empty;' => '&#8709;', '&aelig;' => '&#230;', '&iuml;' => '&#239;', '&infin;' => '&#8734;', '&le;' => '&#8804;', '&kappa;' => '&#954;', '&OElig;' => '&#338;', '&Omega;' => '&#937;', '&prop;' => '&#8733;', '&lArr;' => '&#8656;', '&oslash;' => '&#248;', '&Agrave;' => '&#192;', '&Psi;' => '&#936;', '&auml;' => '&#228;', '&sdot;' => '&#8901;', '&eacute;' => '&#233;', '&beta;' => '&#946;', '&exist;' => '&#8707;', '&Iuml;' => '&#207;', '&Scaron;' => '&#352;', '&Egrave;' => '&#200;', '&Zeta;' => '&#918;', '&Omicron;' => '&#927;', '&oline;' => '&#8254;', '&int;' => '&#8747;', '&spades;' => '&#9824;', '&fnof;' => '&#402;', '&prod;' => '&#8719;', '&yuml;' => '&#255;', '&rsquo;' => '&#8217;', '&weierp;' => '&#8472;', '&Ograve;' => '&#210;', '&Pi;' => '&#928;', '&there4;' => '&#8756;', '&otilde;' => '&#245;', '&szlig;' => '&#223;', '&darr;' => '&#8595;', '&aacute;' => '&#225;', '&THORN;' => '&#222;', '&ucirc;' => '&#251;', '&iexcl;' => '&#161;', '&ograve;' => '&#242;', '&rArr;' => '&#8658;', '&asymp;' => '&#8776;', '&aring;' => '&#229;', '&rho;' => '&#961;', '&sect;' => '&#167;', '&pi;' => '&#960;', '&macr;' => '&#175;', '&scaron;' => '&#353;', '&copy;' => '&#169;', '&sube;' => '&#8838;', '&diams;' => '&#9830;', '&brvbar;' => '&#166;', '&uuml;' => '&#252;', '&sup3;' => '&#179;', '&minus;' => '&#8722;', '&Otilde;' => '&#213;', '&lang;' => '&#9001;', '&ldquo;' => '&#8220;', '&omega;' => '&#969;', '&ang;' => '&#8736;', '&lowast;' => '&#8727;', '&sup1;' => '&#185;', '&sup2;' => '&#178;', '&Ccedil;' => '&#199;', '&rdquo;' => '&#8221;', '&nabla;' => '&#8711;', '&Chi;' => '&#935;', '&lceil;' => '&#8968;', '&Ntilde;' => '&#209;', '&and;' => '&#8743;', '&mu;' => '&#956;', '&delta;' => '&#948;', '&bull;' => '&#8226;', '&raquo;' => '&#187;', '&part;' => '&#8706;', '&iquest;' => '&#191;', '&psi;' => '&#968;', '&Icirc;' => '&#206;', '&zwj;' => '&#8205;', '&euro;' => '&#8364;', '&Phi;' => '&#934;', '&oelig;' => '&#339;', '&para;' => '&#182;', '&ocirc;' => '&#244;', '&ouml;' => '&#246;', '&omicron;' => '&#959;', '&Ugrave;' => '&#217;', '&Gamma;' => '&#915;', '&sum;' => '&#8721;', '&isin;' => '&#8712;', '&sup;' => '&#8835;', '&AElig;' => '&#198;', '&dArr;' => '&#8659;', '&micro;' => '&#181;', '&agrave;' => '&#224;', '&Delta;' => '&#916;', '&Ecirc;' => '&#202;', '&gamma;' => '&#947;', '&tau;' => '&#964;', '&crarr;' => '&#8629;', '&or;' => '&#8744;', '&epsilon;' => '&#949;', '&rsaquo;' => '&#8250;', '&frac34;' => '&#190;', '&Euml;' => '&#203;', '&rang;' => '&#9002;', '&times;' => '&#215;', '&thinsp;' => '&#8201;', '&tilde;' => '&#732;', '&piv;' => '&#982;', '&Prime;' => '&#8243;', '&curren;' => '&#164;', '&laquo;' => '&#171;', '&cedil;' => '&#184;', '&oacute;' => '&#243;', '&permil;' => '&#8240;', '&Iota;' => '&#921;', '&loz;' => '&#9674;', '&ETH;' => '&#208;', '&trade;' => '&#8482;', '&xi;' => '&#958;', '&cent;' => '&#162;', '&Aacute;' => '&#193;', '&cup;' => '&#8746;', '&yen;' => '&#165;', '&chi;' => '&#967;', '&upsih;' => '&#978;', '&lambda;' => '&#955;', '&Alpha;' => '&#913;', '&sub;' => '&#8834;', '&igrave;' => '&#236;', '&gt;' => '&#62;', '&harr;' => '&#8596;', '&phi;' => '&#966;', '&deg;' => '&#176;', '&not;' => '&#172;', '&Atilde;' => '&#195;', '&lsaquo;' => '&#8249;', '&Oacute;' => '&#211;', '&divide;' => '&#247;', '&Yacute;' => '&#221;', '&nu;' => '&#957;', '&uml;' => '&#168;', '&rceil;' => '&#8969;', '&frac12;' => '&#189;', '&theta;' => '&#952;', '&upsilon;' => '&#965;', '&Auml;' => '&#196;', '&Ocirc;' => '&#212;', '&iacute;' => '&#237;', '&hArr;' => '&#8660;', '&equiv;' => '&#8801;', '&eta;' => '&#951;', '&Upsilon;' => '&#933;', '&frac14;' => '&#188;', '&egrave;' => '&#232;', '&supe;' => '&#8839;', '&ge;' => '&#8805;', '&rfloor;' => '&#8971;', '&Epsilon;' => '&#917;', '&ugrave;' => '&#249;', '&Lambda;' => '&#923;', '&eth;' => '&#240;', '&zeta;' => '&#950;', '&frasl;' => '&#8260;', '&Ouml;' => '&#214;', '&forall;' => '&#8704;', '&rlm;' => '&#8207;', '&hellip;' => '&#8230;', '&sigmaf;' => '&#962;', '&alefsym;' => '&#8501;', '&lsquo;' => '&#8216;' );

	function setup() {
		parent::setup();
		$_this = $this;
		$this->addCompilerSetup(function(Compiler $compiler)use($_this){
			$compiler->getPostLoadFilters()->addFilter(array($_this,'_replaceHtmlEntities'));
			$compiler->getPostFilters()->addFilter(array($_this,'_replaceShortTags'), -10);
		});

	}
	function _replaceShortTags($str) {
		$str = preg_replace_callback( "#<(title|iframe|textarea|div|span|p|h1|h2|h3|h4|h5|h6|label|fieldset|legend|strong|small|cite|script|style|select|em|td|b)([\\s][^\\>]*|[\\s]*)/>#i", function($mch){
			if(strpos($mch[0], '<base ')!==false || strpos($mch[0], '<br')!==false || strpos($mch[0], '<param ')!==false){
				return $mch[0];
			}

			if(strlen(trim($mch[2]))){
				return "<$mch[1] ".trim($mch[2])."></$mch[1]>";
			}else{
				return "<$mch[1]></$mch[1]>";
			}
		}, $str );
		return $str;
	}
	public function _replaceHtmlEntities($str) {
		return str_replace (array_keys(static::$htmlEntities),array_values(static::$htmlEntities), $str );
	}
	public static function replaceHtmlEntities($str) {
		return str_replace (array_keys(static::$htmlEntities),array_values(static::$htmlEntities), $str );
	}
}