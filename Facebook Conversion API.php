// INSTRUCTIONS:
// 1. Go to https://business.facebook.com/
// 2. Navigate to Events Manager
// 3. Then, select the pixel you want to integrate with Facebook Conversion API (do create a Pixel if you don't have one)
// 4. Copy the required values:
// <insert_your_fb_pixel_here>: Overview > Grab the Pixel > Replace the value at the code below according 
// <insert_your_fb_access_token_here>: Implementations > Generate Access Token > Grab the Access Code > Replace the value at the code below according
// <insert_your_fb_conversion_api_test_code_here>: Test events > Grab the TEST code > Replace the value at the code below according
// 5. Paste the code below to header.php on WordPress and save.
// 6. Try to visit your website and Facebook should catch the events under the tab "Test events". (Event Manager > Select the tested pixel > Test Events)
// 7. If everything works well and you are ready to push it for production, remember to remove line 54 below where test_event_code is attached.

$pixel_id = <insert_your_fb_pixel_here>;
$access_token='<insert_your_fb_access_token_here>';

$url = 'https://graph.facebook.com/v9.0/' . $pixel_id . '/events';
$ch = curl_init($url);

$current_user = wp_get_current_user();
$user_data = array(
	'client_ip_address' => $_SERVER['REMOTE_ADDR'],
	'client_user_agent' => $_SERVER['HTTP_USER_AGENT']
);

(0 == $current_user -> ID) ? $user_data = $user_data : $user_data['em'] = hash("sha256", $current_user -> user_email);

// For Facebook Browser Id //
// If cookies named _fbp can be found, attach it to $user_data
if(isset($_COOKIE['_fbp'])){
	$user_data['fbp'] = $_COOKIE['_fbp'];
}

// For Facebook Ads Click Id //
// If cookies named _fbc can be found, attach it to $user_data.
// If not found, create fbc parameter by ownself.
if(isset($_COOKIE['_fbc'])){
	$user_data['fbc'] = $_COOKIE['_fbc'];
} elseif (isset($_GET['fbclid'])) {
	$user_data['fbc'] = 'fb.1' . $_SERVER['REQUEST_TIME'] . $_GET['fbclid'];
}

$payload = json_encode(array(
	'data' => array(
		array(
			'event_name' => 'PageView',
			'event_time' => $_SERVER['REQUEST_TIME'],
			'event_source_url' => $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'],
			'action_source' => 'website',
			'user_data' => $user_data
		)
	),
	'access_token' => $access_token,
	'test_event_code' => '<insert_your_fb_conversion_api_test_code_here>' // Remove for production environment
));

curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$result = curl_exec($ch);
curl_close($ch);