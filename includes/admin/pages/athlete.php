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
                <th scope="col" id="athlete" class="manage-column column-athlete">Athlete</th>
                <th scope="col" id="leaderboards" class="manage-column column-leaderboards">Leaderboards</th>
                <th scope="col" id="edit" class="manage-column column-edit"></th>
            </tr>
    	</thead>
    
    	<tbody id="the-list">
        	<?php foreach ($athletes as $athlete) : ?>
                <tr id="athlete-<?php echo $athlete->athlete_id; ?>" class="format-standard hentry">
    			    <td class="athlete column-athlete" data-colname="Athlete"><?php slwp_athlete_name($athlete->athlete_id); ?></td>
    			    <td class="athlete column-leaderboards" data-colname="Leaderboards"><?php slwp_athlete_leaderboards_list($athlete->athlete_id); ?></td>
                    <td class="edit column-edit" data-colname="Edit" data-athleteid="<?php echo $athlete->athlete_id; ?>"><a href="#" class="edit-athlete-lb">Edit</a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div id="leaderboards-box" class="slwp-box">
        <form>
            nonce 
            <?php foreach ($leaderboards as $leaderboard): ?>
                <div><label for="lb-cb-<?php echo $leaderboard->ID; ?>"><input type="checkbox" id="lb-cb-<?php echo $leaderboard->ID; ?>" class="lb-cb-<?php echo $leaderboard->ID; ?>" name="lb-cb-<?php echo $leaderboard->ID; ?>" value="1"><?php echo $leaderboard->post_title; ?></label></div>
            <?php endforeach; ?>
            <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Update"></p>
        </form>
    </div>
</div>