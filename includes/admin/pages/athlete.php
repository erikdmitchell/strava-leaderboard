<?php
$athletes = slwp_get_athletes(); 
$leaderboards = slwp_get_leaderboards();
?>

<div class="wrap slwp-admin-wrap">
    <h1>Strava Leaderbaord</h1>

    <h3>Athletes</h3>

    <table class="wp-list-table widefat fixed striped table-view-list slwp-athletes">
    	<thead>
    	    <tr>
                <th scope="col" id="title" class="manage-column column-athlete">Athlete</th>
                <th scope="col" id="author" class="manage-column column-leaderboards">Leaderboards</th>
            </tr>
    	</thead>
    
    	<tbody id="the-list">
        	<?php foreach ($athletes as $athlete) : ?>
                <tr id="athlete-<?php echo $athlete->athlete_id; ?>" class="format-standard hentry">
    			    <td class="athlete column-athlete" data-colname="Athlete"><?php slwp_athlete_name($athlete->athlete_id); ?></td>
    			    <td class="athlete column-leaderboards" data-colname="Leaderboards">#####</td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</div>