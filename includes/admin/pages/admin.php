<div class="wrap slwp-admin-wrap">
    <h1>Strava Leaderboard</h1>

    <h3>Settings</h3>

    <form id="slwp-settings-form" class="slwp-settings-form" action="" method="post">
        <?php wp_nonce_field( 'slwp_update_settings', 'update_settings' ); ?>
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row"><label for="client_id">Client ID</label></th>
                    <td>
                        <input name="slwp[slwp_client_id]" type="text" id="client_id" value="<?php echo get_slwp_client_id(); ?>" class="regular-text">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="client_secret">Client Secret</label></th>
                    <td>
                        <input name="slwp[slwp_client_secret]" type="text" id="client_secret" value="<?php echo get_slwp_client_secret(); ?>" class="regular-text">
                    </td>
                </tr>
            </tbody>
        </table>
        <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes"></p>
    </form>
    
    <p><a href="http://stapi.test/wp-admin/admin.php?page=slwp&subpage=webhooks">Setup Webhooks</a></p>

</div>
