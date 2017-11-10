<?php
if(!session_id()){
    session_start();
}

// Include the autoloader provided in the SDK
require_once("vendor/autoload.php");

// Include required libraries
use Facebook\Facebook;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\FacebookSession;
use Facebook\FacebookRequest;
use Facebook\FacebookApp;

/*
 * Configuration and setup Facebook SDK
 */
$appId         = '306202179788207'; //Facebook App ID
$appSecret     = '2d08b3ce8f88702744501ba99a4abee3'; //Facebook App Secret
$redirectURL   = 'http://localhost/Facebook/fb-callback.php'; //Callback URL
$fbPermissions = array('email');  //Optional permissions

$fb = new Facebook(array(
    'app_id' => $appId,
    'app_secret' => $appSecret,
    'default_graph_version' => 'v2.10',
));
$fbApp = new FacebookApp($appId, $appSecret);


// Get redirect login helper
$helper = $fb->getRedirectLoginHelper();

// Try to get access token
try {
    if(isset($_SESSION['facebook_access_token'])){
        $accessToken = $_SESSION['facebook_access_token'];
  //      $session = new FacebookSession( $_SESSION['facebook_access_token'] );
    }else{
          $accessToken = $helper->getAccessToken();
    }
} catch(FacebookResponseException $e) {
     echo 'Graph returned an error: ' . $e->getMessage();
      exit;
} catch(FacebookSDKException $e) {
    echo 'Facebook SDK returned an error: ' . $e->getMessage();
      exit;
}

?>