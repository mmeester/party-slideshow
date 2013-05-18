<?php


$GLOBALS['document_root'] = getenv("DOCUMENT_ROOT");

require_once($GLOBALS['document_root'] . '/include/libraries/phpthumb/ThumbLib.inc.php');

$dir = "/volume1/downloads/photos/";
$date = date('d-m-y');

$return_array = array();

$files = scandir($dir.$date.'/');

foreach($files as $entry)
{
if ($entry != "." && $entry != ".."  && $entry != "@eaDir") {
  	
  	if(!file_exists($GLOBALS['document_root'].'/img/slideshow/'.$entry)){ 
			$thumb = PhpThumbFactory::create($dir.$date.'/'.$entry);  
			$thumb->resize(3200, 3200)->save($GLOBALS['document_root'].'/img/slideshow/'.$entry);
			
			$thumb = PhpThumbFactory::create($dir.$date.'/'.$entry);  
			$thumb->resize(400, 500)->save($GLOBALS['document_root'].'/img/slideshow/thumbs/'.$entry);  
  	}
  	
  	$return_array[] = array('image'=>'img/slideshow/'.$entry, 'thumb'=>'img/slideshow/thumbs/'.$entry);
  	      	
  }
}


$return_array = array_reverse($return_array);

echo json_encode($return_array);

?>