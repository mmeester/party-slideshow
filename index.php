<?php

require_once 'include/main.config.php';

$smarty->assign('bodyclass', '' );

if($_SERVER['REQUEST_URI'] == '/')
{
	include('models/home.php');
}else{
	
	// remove first slash
	$request_uri = substr($_SERVER['REQUEST_URI'], 1);


	// remove trailing slash
	if(substr($request_uri, -1) == '/')
	{
		$request_uri = substr($request_uri, 0, -1);
	}

	// If the url contains a query, remove it
	if(strstr($request_uri, "?"))
	{
		// URL Query
		$url_query = explode("?", $request_uri);	
		
		//  Make array from url 
		$url_structure = explode("/", $url_query[0]);
	}
	else
	{
		// just folder structure, make array
		$url_structure = explode("/", $request_uri);
	}
	
	$smarty->assign('cms_url', $url_structure);
		
	// backwards compatible
	$sub_pages = $url_structure;
	
	if($sub_pages[0]=='reset')
	{
		if($sub_pages[1]==$GLOBALS['reset_url']){
		session_destroy();
		header('location:/');
		}else{
		header('location:/?no_reset');
		}
	}if($sub_pages[0]=='thankyou')
	{
		$smarty->assign('bodyclass', 'thanks' );
		
		/* include('models/home.php'); */
	}if(isset($sub_pages[1]) && file_exists("models/".$sub_pages[0]."/".$sub_pages[1].".php")){
		include("models/".$sub_pages[0]."/".$sub_pages[1].".php");
	}elseif(file_exists("models/".$sub_pages[0].".php"))
	{	
		include("models/".$sub_pages[0].".php");
	}else{
		include('models/home.php');
	}
	
}

?>
