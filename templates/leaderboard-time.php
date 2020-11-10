<div id="leaderboard-<?php the_ID(); ?>" class="slwp-leaderboard leaderboard">

    <div class="slwp">
        <?php the_title( '<h1>', '</h1>' ); ?>

        <div class="container">
            <div class="row">
                <div class="col">
                    <?php the_content(); ?>
                </div>
            </div>
        </div>

        <div class="container">
            <div class="row">
                <div class="col">
                    <h3>Time Leaderboard</h3>
                </div>
            </div>
            <?php // echo $args['name']; ?>
            ACF Name (not set)

            <?php foreach ( $args['activities'] as $activity ) : ?>
                <div class="row">
                    <div class="col">
                        <?php echo slwp_get_athlete_name( $activity->athlete_id ); ?>
                    </div>
                    <div class="col">
                        Time: <?php echo $activity->time; ?>
                    </div>
                    <div class="col">
                        Distance: <?php echo $activity->distance; ?>
                    </div>
                    <div class="col">
                        Activities: <?php echo $activity->activity_count; ?>
                    </div>
                </div>
            <?php endforeach; ?>

        </div>

    </div>

</div>
