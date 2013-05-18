<?php

// Create our Application instance (replace this with your appId and secret).
$facebook = new Facebook(array(
  'appId'  => $GLOBALS['facebook']['app_id'],
  'secret' => $GLOBALS['facebook']['app_secret'],
));

// Get User ID
$user = $facebook->getUser();

if ($user) {
	$facebook->setExtendedAccessToken();
  try {
    // Proceed knowing you have a logged in user who's authenticated.
    $events = $facebook->api('/me/events');
    $access_token = $facebook->getAccessToken();
  } catch (FacebookApiException $e) {
    error_log($e);
    $user = null;
  }
}

Test::pr($access_token);


foreach($events['data'] as $event)
{
	if($event['id']==466096203466449)
	{
		
		Test::pr( $facebook->api('/'.$event['id'].'/attending' ) );
		
		Test::pr($event);
		Test::pr($events);
	}
}



$smarty->assign('events',$events);

Test::pr($events);
$smarty->display('login.tpl');

?>