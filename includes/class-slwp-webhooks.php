<?php

class SLWP_Webhooks {

    public function __construct() {
        // nothing yet.
    }

    private function request_subscription() {
slwp_log('POST Webhooks');         
        $url = 'https://www.strava.com/api/v3/push_subscriptions';
        $client_id = get_slwp_client_id();
        $client_secret = get_slwp_client_secret();
        $callback_url = esc_url( home_url( '/slwp/stravaWebhooks' ) );
        //$callback_url = esc_url( home_url( '/slwp/stravaWebhooksJ' ) );
        $verify_token = 'slwplb';
        
        $callback_url = urlencode( $callback_url );
        
        $params = "client_id=$client_id&client_secret=$client_secret&callback_url=$callback_url&verify_token=$verify_token";

        $url = $url . '?' . $params;
        
slwp_log($url);
        
        $curl=curl_init($url);
        
    	curl_setopt($curl, CURLOPT_HEADER, false);
    	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        //curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_HEADER, true);
        
    	$json_response = curl_exec($curl);
    
    	$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
 
slwp_log('Request subscription status: ' . $status); 
  
    	if ($status!=200) :
// error
    	endif;
slwp_log(curl_getinfo($curl));     
    	curl_close($curl);
    
    	$response = json_decode($json_response, true);
   	
//$request_input = file_get_contents('php://input');    	
//slwp_log('Request "input"');
//slwp_log($request_input);
//slwp_log('Request subscription response:');
//slwp_log($json_response);
//slwp_log($response);
//slwp_log($_REQUEST);
//slwp_log('Request subscription $_GET:');
//slwp_log($_GET);
        //return $response;
        //$this->json_validate();
    }
    
    public function validation() {
slwp_log('validation()');

$current_time = date( 'Y-m-d H:i:s A' , time() );

slwp_log($current_time);
       
        // check var.
        //if (isset($_GET['hub_challenge'])) {
           // $this->return_json();     
        //} else {
            $this->request_subscription();
            //$foo = $this->request_subscription();
            //slwp_log($foo);
        //}  
    } 
    
    public function json_validate() {
//echo "abc";        
slwp_log('json_validate()');

$current_time = date( 'Y-m-d H:i:s A' , time() );

slwp_log($current_time); 
//slwp_log($_GET);  
$this->return_json();     
    }
   

private function return_json() {
    slwp_log('return_json()');  
    slwp_log($_GET);        
/*
Example Subscription Creation Response
{
  "id": 1
}    
*/

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
$data = array(
    //'status' => 200,    
    'hub.challenge' => $_GET['hub_challenge'],
    //'hub.challenge' => '15f7d1a91c1f40f8a748fd134752feb3',
);

slwp_log('JSON return');
slwp_log($data);

echo json_encode( $data );

//echo wp_json_encode( $data );

exit();

    }



}
