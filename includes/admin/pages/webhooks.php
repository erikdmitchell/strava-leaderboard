<div class="wrap slwp-admin-wrap">
    <h1>Strava Leaderboard</h1>

    <h3>Webhooks</h3>


Next, you need to create a Webhook Subscription by doing a POST request to the Strava Sync webhook controller (http://website.com/strava-sync/webhook/sync) with a Bearer Token and client_id, client_secret, verify_token, callback_url parameters (The callback_url should be the same as the POST request URL)
<br>
check var<br>
send post<br>
<p>
    if get<br>
    return json<br>
    else <br>
    
    fire webhook
</p>

<code>
/*
function slwp_setup_webhooks() {
    slwp()->webhooks->create_subscription();
}

slwp_setup_webhooks();
*/
</code>

<p><a href="http://stapi.test/slwp/stravaWebhooks/" target="_blank">Run</a></p>

</div>
