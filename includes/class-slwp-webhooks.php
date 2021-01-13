<?php

class SLWP_Webhooks {

    public function __construct() {
        // nothing yet.
    }

    public function create_subscription() {
        //$this->request_subscription();
    }

    private function request_subscription() {
slwp_log('POST Webhooks');         
        $url = 'https://www.strava.com/api/v3/push_subscriptions';
        $client_id = get_slwp_client_id();
        $client_secret = get_slwp_client_secret();
        //$callback_url = esc_url( home_url( '/slwp/stravaWebhooks' ) );
        $callback_url = esc_url( home_url( '/slwp/stravaWebhooks' ) );
        $verify_token = 'slwplb';
        
        $callback_url = urlencode( $callback_url );
        
        $params = "client_id=$client_id&client_secret=$client_secret&callback_url=$callback_url&verify_token=$verify_token";
//echo $params.'<br>';
		//. "&redirect_uri=" . urlencode(SF_REDIRECT_URI);

        $curl=curl_init($url);
        
    	curl_setopt($curl, CURLOPT_HEADER, false);
    	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    	curl_setopt($curl, CURLOPT_POST, true);
    	curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
    
    	$json_response = curl_exec($curl);
    
    	$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
//echo '<pre>';    	
//print_r($status); 
slwp_log('Request subscription');
slwp_log($status);   
    	if ($status!=200) :
//echo "error";    			
    	endif;
    
    	curl_close($curl);
    
    	$response = json_decode($json_response, true);
slwp_log($response);
    	
//print_r($response);    	
//echo '</pre>';
    }
    
    public function validation() {
        // check var.
        if (isset($_GET['hub_challenge'])) {
            $this->return_json();     
        } else {
            $this->request_subscription();
        }

/*
    
Next, you need to create a Webhook Subscription by doing a POST request to the Strava Sync webhook controller (http://website.com/strava-sync/webhook/sync) with a Bearer Token 
and client_id, client_secret, verify_token, callback_url parameters (The callback_url should be the same as the POST request URL)

<br>
check var<br>
send post<br>
<p>
    if get<br>
    return json<br>
    else <br>
    
    fire webhook
</p>

function slwp_setup_webhooks() {
    slwp()->webhooks->create_subscription();
}

slwp_setup_webhooks();
*/    
   

private function return_json() {
    slwp_log('Validate Webhooks');  
    slwp_log($_GET);        
    
//echo "validate webhooks<br>";        

//?hub.verify_token= slwplb&hub.challenge= $_GET['hub.challenge'] &hub.mode=subscribe
/*
Your callback address must respond within two seconds to the GET request from Strava’s subscription service. The response should indicate status code 200 and should echo the hub.challenge field in the response body as application/json content type: { “hub.challenge”:”15f7d1a91c1f40f8a748fd134752feb3” }

Once you have successfully created a webhook events subscription by responding to the callback validation, you will receive a response to your original subscription creation POST request. This response will include the id of the newly created subscription. If creation of a new subscription fails, this response will instead include error information. The most common cause of subscription creation failure is a failure to respond in a timely manner to the validation GET request, or failure to correctly echo the hub.challenge field.

Example Subscription Creation Response
{
  "id": 1
}    
*/

/*
    // clear the old headers
    header_remove();
    // set the actual code
    http_response_code(200);
    
    header('Access-Control-Allow-Origin: *');
    
    // set the header to make sure cache is forced
    header("Cache-Control: no-transform,public,max-age=300,s-maxage=900");
    // treat this as json
    header('Content-type:application/json;charset=utf-8');

    // ok, validation error, or failure
    //header('Status: '.200);
    header("Status: 200");
    // return the encoded json
*/




/*
header('Access-Control-Allow-Origin: *');
header('Content-type:application/json;charset=utf-8');
http_response_code(200);
    // set the header to make sure cache is forced
    header("Cache-Control: no-transform,public,max-age=300,s-maxage=900");
    header('Content-type:application/json;charset=utf-8');
header('Status: 200');
//print_r($_GET);
$data = array(
    //'status' => 200,    
    'hub.challenge' => $_GET['hub_challenge'],
    //'hub.challenge' => '15f7d1a91c1f40f8a748fd134752feb3',
);

echo json_encode( $data );

// echo wp_json_encode( $data );

exit();
*/

    }



}
