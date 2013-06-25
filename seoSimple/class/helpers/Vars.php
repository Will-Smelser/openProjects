<?php

class Vars{
	
	/**
	 * Get a URL var
	 * @param String $varname The url variable name
	 * @return String or Null
	 */
	static function GET($varname){
		return (isset($_GET[$varname])) ? $_GET[$varname] : null;
	}
}