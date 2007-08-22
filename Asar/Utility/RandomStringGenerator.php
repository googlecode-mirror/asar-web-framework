<?php
/**
 * Created on Jun 21, 2007
 * 
 * @author     Wayne Duran
 */

class Asar_Utility_RandomStringGenerator {
	
	private static $instance;
	private static $characters = array();
	private static $lower_a_start;
	private static $lower_a_end;
	private static $upper_a_start;
	private static $upper_a_end;
	private static $n_start;
	private static $n_end;
	private static $uscore; 
	
	
	public static function instance()
 	{
 		if (self::$instance == null ) {
 			self::$instance = new Asar_Utility_RandomStringGenerator();
 			
	 		//self::$characters    = str_split('_abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890');
	 		self::$characters    = str_split('_abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890');
	 		self::$lower_a_start = 1;
	 		self::$lower_a_end   = 26;
	 		self::$upper_a_start = 27;
	 		self::$upper_a_end   = 52;
	 		self::$n_start       = 53;
	 		self::$n_end         = 62;
	 		self::$uscore        = 0;
 		}
 		
 		return self::$instance;	
 	}
 	
 	private function __contstruct() {
 	}
 	
 	private function __clone() {}
 	
 	
 	public function getValue($pos) {
 		
 		if (isset(self::$characters[$pos])) {
 			return self::$characters[$pos];
 		} else {
 			false;
 		}
 	}
 	
 	
 	private function getRandomString($length, $min, $max) {
 		$str = '';
 		for ($i = 0; $i < $length; $i++) {
 			$j = mt_rand($min, $max);
 			$str = $str . $this->getValue($j);
 		}
		return $str;
 	}
 	
 	
 	public function getAlphaNumeric($length) {
 		return $this->getRandomString($length, self::$lower_a_start, self::$n_end);
 	}
 	
 	
 	public function getAlpha($length) {
 		return $this->getRandomString($length, self::$lower_a_start, self::$upper_a_end);
 	}
 	
 	
 	public function getNumeric($length) {
 		return $this->getRandomString($length, self::$n_start, self::$n_end);
 	}
 	
 	
 	public function getUppercaseAlpha($length) {
 		return $this->getRandomString($length, self::$upper_a_start, self::$upper_a_end);
 	}
 	
 	
 	public function getLowercaseAlpha($length) {
 		return $this->getRandomString($length, self::$lower_a_start, self::$lower_a_end);
 	}
 	
 	
 	public function getPhpLabel($length) {
 		$str = $this->getRandomString(1, self::$uscore, self::$upper_a_end);
 		return $str.($this->getRandomString($length - 1, self::$uscore, self::$n_end));
 	}
 	
}
?>
