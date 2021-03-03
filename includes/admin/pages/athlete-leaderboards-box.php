<form name="slwp_athlete_lbs" id="slwp-athlete-lbs" class="slwp-athlete-lbs">
    <?php wp_nonce_field( 'update', 'slwp_update_athlete_lbs' ); ?>
    <?php foreach ( $leaderboards as $leaderboard ) : ?>
        <div><label for="lb-cb-<?php echo $leaderboard->ID; ?>"><input type="checkbox" id="lb-cb-<?php echo $leaderboard->ID; ?>" class="lb-cb-<?php echo $leaderboard->ID; ?>" name="lb[<?php echo $leaderboard->ID; ?>]" value="1" <?php slwp_athlete_in_leaderboard_checked( $athlete_id, $leaderboard->ID ); ?>><?php echo $leaderboard->post_title; ?></label></div>
    <?php endforeach; ?>
        <input type="hidden" name="athlete_id" value="<?php echo $athlete_id; ?>" />
    <p class="submit"><button name="slwp_update_athlete_lbs" id="slwp-update-athlete-lbs" class="button button-primary">Update</button></p>
</form>
