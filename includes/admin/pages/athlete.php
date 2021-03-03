<?php $athletes = slwp_get_athletes(); ?>

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
            <?php foreach ( $athletes as $athlete ) : ?>
                <tr id="athlete-<?php echo $athlete->athlete_id; ?>" class="format-standard hentry">
                    <td class="athlete column-athlete" data-colname="Athlete"><?php slwp_athlete_name( $athlete->athlete_id ); ?></td>
                    <td class="athlete column-leaderboards" data-colname="Leaderboards"><?php slwp_athlete_leaderboards_list( $athlete->athlete_id ); ?></td>
                    <td class="edit column-edit" data-colname="Edit" data-athleteid="<?php echo $athlete->athlete_id; ?>"><a href="#" class="edit-athlete-lb">Edit</a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <div id="leaderboards-box" class="slwp-box"></div>
</div>
