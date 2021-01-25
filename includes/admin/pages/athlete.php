<div class="wrap slwp-admin-wrap">
    <h1>Strava Leaderbaord</h1>

    <h3>Athletes</h3>
<ul>
<li>List of athletes w/ option to match to multiple leaderboards</li>
</ul>

<div>
    Athlete: dropdown (value is id) | js based + that will add a leaderboard dd (value is id)
</div>

</div>

<pre>
<?php

$athletes = slwp_get_athletes(); 
$leaderboards = slwp_get_leaderboards();
   
print_r($athletes);
print_r($leaderboards);
?>
</pre>