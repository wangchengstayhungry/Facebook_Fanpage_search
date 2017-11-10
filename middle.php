<?php

session_start();
require_once("vendor/autoload.php");
require_once("fb_config.php");

use Facebook\FacebookRequest;
// app directory could be anything but website URL must match the URL given in the developers.facebook.com/apps
if (isset($_SESSION['facebook_access_token'])) {
	$accessToken = $_SESSION['facebook_access_token'];	

}
// var_dump($accessToken);
// exit;
// $accessToken = "EAAEWfUAn3a8BANkcEAkxRZAEY6UF1jnCsq9Tw5m455Hgw82BrlaPtpQVl83aOpSx3ZAsccZBv1wMBHLTwRTLJg7y3xIkdxwzDQvcdvWnzBZApznC1iiZAolgZAtw5KxZBR1V4xZCMIAZAqP4f5jgU5BkZBrLxJrbWSdqQX88FkolLmCAZDZD";
//echo $accessToken;
$permissions = []; // optional


if (isset($accessToken)) {

	$mode = $_GET['mode'];
	//$mode = 'detail_search';
	if ($mode == 'gen_search') {
		$search = $_GET['search_text'];
		$search_result = $fb->get('/search?q='.$search.'&type=page&limit=5', $accessToken);
		$search_result = $search_result->getGraphEdge()->asArray();	
		echo json_encode($search_result);
	}
	else if($mode == 'detail_search') {

		$search_id = $_GET['search_id'];
		//$search_id = '569610606446713';
		try{
			$photo = $fb->get('/'.$search_id.'/photos?limit=5', $accessToken);			
		} catch(Facebook\Exceptions\FacebookResponseException $e) {
			echo 'Graph returned an error: '.$e->getMessage();
			exit;
		} catch (Facebook\Exceptions\FacebookSDKException $e) {
			echo 'Facebook SDK returned an error: '.$e->getMessage();
 			exit;
		}
		$graphNode = $photo->getGraphEdge()->asArray();
		
		echo json_encode($graphNode);
		//exit;
		
	}

	if (isset($_SESSION['facebook_access_token'])) {
		$fb->setDefaultAccessToken($_SESSION['facebook_access_token']);
	} else {
		// getting short-lived access token
		$_SESSION['facebook_access_token'] = (string) $accessToken;
	  	// OAuth 2.0 client handler
		$oAuth2Client = $fb->getOAuth2Client();
		// Exchanges a short-lived access token for a long-lived one
		$longLivedAccessToken = $oAuth2Client->getLongLivedAccessToken($_SESSION['facebook_access_token']);
		$_SESSION['facebook_access_token'] = (string) $longLivedAccessToken;
		// setting default access token to be used in script
		$fb->setDefaultAccessToken($_SESSION['facebook_access_token']);
	}
	// redirect the user back to the same page if it has "code" GET variable
	if (isset($_GET['code'])) {
		header('Location: ./');
	}
	// validating user access token
	try {
		$user = $fb->get('/me');
		$user = $user->getGraphNode()->asArray();
	} catch(Facebook\Exceptions\FacebookResponseException $e) {
		// When Graph returns an error
		echo 'Graph returned an error: ' . $e->getMessage();
		session_destroy();
		// if access token is invalid or expired you can simply redirect to login page using header() function
		exit;
	} catch(Facebook\Exceptions\FacebookSDKException $e) {
		// When validation fails or other local issues
		echo 'Facebook SDK returned an error: ' . $e->getMessage();
		exit;
	}
	
	// type can be user, group, page or event
	
  	// Now you can redirect to another page and use the access token from $_SESSION['facebook_access_token']
} else {
	// replace your website URL same as added in the developers.facebook.com/apps e.g. if you used http instead of https and you used non-www version or www version of your website then you must add the same here
	// $loginUrl = $helper->getLoginUrl(APP_URL, $permissions);
	// echo '<a href="' . $loginUrl . '">Log in with Facebook!</a>';

	
}

?>


