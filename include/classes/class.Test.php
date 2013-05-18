<?php

/**
 * Detailed description will follow later on.
 *
 * @author     seb
 */

class Test
{

	static function dump($data)
	{
		echo '<pre style="padding:5px;margin:60px 5px 5px 5px;background-color:rgb(255,242,179);border:1px solid black;"><strong>Debug information:</strong><br />';
		var_dump($data);
		echo "</pre>";
		
	}
	
	static function pr($data)
	{
		echo '<pre style="padding:5px;margin:60px 5px 5px 5px;background-color:rgb(255,242,179);border:1px solid black;"><strong>Debug information:</strong><br />';
		print_r($data);
		echo "</pre>";
		
	}

}

?>