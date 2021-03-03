<form name="slwp_athlete_lbs" id="slwp-athlete-lbs" class="slwp-athlete-lbs" action="" method="post">
    <?php wp_nonce_field( 'update', 'slwp_update_athlete_lbs' ); ?>
    <?php foreach ( $leaderboards as $leaderboard ) : ?>
        <div><label for="lb-cb-<?php echo $leaderboard->ID; ?>"><input type="checkbox" id="lb-cb-<?php echo $leaderboard->ID; ?>" class="lb-cb-<?php echo $leaderboard->ID; ?>" name="lb-cb-<?php echo $leaderboard->ID; ?>" value="1" <?php slwp_athlete_in_leaderboard_checked( $athlete_id, $leaderboard->ID ); ?>><?php echo $leaderboard->post_title; ?></label></div>
    <?php endforeach; ?>
    <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Update"></p>
</form>
