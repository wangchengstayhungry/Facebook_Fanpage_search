<?php
	
	require_once 'fb_config.php';

	///////////////////////	
	if (! isset($accessToken)) {
	  if ($helper->getError()) {
	    header('HTTP/1.0 401 Unauthorized');
	    echo "Error: " . $helper->getError() . "\n";
	    echo "Error Code: " . $helper->getErrorCode() . "\n";
	    echo "Error Reason: " . $helper->getErrorReason() . "\n";
	    echo "Error Description: " . $helper->getErrorDescription() . "\n";
	  } else {
	    header('HTTP/1.0 400 Bad Request');
	    echo 'Bad request';
	  }
	  exit;
	}

	// Logged in
	echo '<h3>Access Token</h3>';
	echo $accessToken;
	var_dump($accessToken->getValue());

	// The OAuth 2.0 client handler helps us manage access tokens
	$oAuth2Client = $fb->getOAuth2Client();

	// Get the access token metadata from /debug_token
	$tokenMetadata = $oAuth2Client->debugToken($accessToken);
	echo '<h3>Metadata</h3>';
	var_dump($tokenMetadata);

	// Validation (these will throw FacebookSDKException's when they fail)
	//$tokenMetadata->validateAppId($config['306202179788207']);
	// If you know the user ID this access token belongs to, you can validate it here
	//$tokenMetadata->validateUserId('123');
	//$tokenMetadata->validateExpiration();

	if (! $accessToken->isLongLived()) {
	  // Exchanges a short-lived access token for a long-lived one
	  try {
	    $accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
	  } catch (Facebook\Exceptions\FacebookSDKException $e) {
	    echo "<p>Error getting long-lived access token: " . $e->getMessage() . "</p>\n\n";
	    exit;
	  }

	  echo '<h3>Long-lived</h3>';

	  var_dump($accessToken->getValue());
	}
	$_SESSION['facebook_access_token'] = (string) $accessToken;
	//$_SESSION['fb_access_token'] = (string) $accessToken;


?>
<!DOCTYPE html>
<html>
<head>
	<title>Fanpage Search with Keywords</title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	<style> 
	input[type=text] {
	    width: 130px;
	    box-sizing: border-box;
	    border: 2px solid #ccc;
	    border-radius: 4px;
	    font-size: 16px;
	    background-color: white;

	    background-position: 10px 10px; 
	    background-repeat: no-repeat;
	    padding: 12px 20px 12px 40px;
	    -webkit-transition: width 0.4s ease-in-out;
	    transition: width 0.4s ease-in-out;
	}

	input[type=text]:focus {
	    width: 100%;
	}
	</style>
</head>
<body>
	<div class="container">
		<div class="col-md-12">
			<form>
				<div class="col-md-8">
					<input type="text" id="search_text" placeholder="Search..">	
				</div>
		  		<div class="col-md-4">
		  			<button type="button" class="btn btn-default" id="search_btn">
			      		<span class="glyphicon glyphicon-search"></span> Search
			   	  	</button>			
		  		</div>
		  	</form>		
		</div>
		<!--display the image and category-->
		<div class="row col-md-12">
			<div class="col-md-6" >
				<div class="list-group" id="fbhtmlImage">
		            	            
		        </div>
			</div>
			<div class="col-md-6">
				
			</div>
		</div>
		<!--end of displaying the image and category-->
	</div>
		
	<!-- jQuery library -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
	<!-- Latest compiled JavaScript -->
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
	<script type="text/javascript">
		$(document).ready(function() {
		    $('#search_btn').click(function(){

				var search_text = document.getElementById("search_text").value;
                var fbhtml = '';
                $.ajax({                    
                     type: "GET",
                     url: "middle.php",
                     data: { mode: 'gen_search',search_text: search_text }

                }).done(function(response) {
					//console.log(response);
					var jsonData = jQuery.parseJSON(response);
					for (var i = 0; i < jsonData.length; i++) {
					    var nameset = jsonData[i];
					    $("#fbhtmlImage").append("<a class=" + "list-group-item " +"id=" + nameset.id +">" + nameset.name + "</a>");
					    console.log(nameset.name);
					}
					
					$(".list-group-item").click(function(){
			        	console.log($(this)[0].id);
			        	var search_id = $(this)[0].id;
			        	$.ajax({
			        		type: "GET",
			        		url: "middle.php",
			        		data: { mode: 'detail_search', search_id: search_id }
			        	}).done(function(response) {
			        		console.log(response);
			        	});
			        });
		    	});
            });
		});
		
	</script>
</body>
</html>