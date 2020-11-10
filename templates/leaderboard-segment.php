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
                    <h3>Segment Efforts</h3>
                </div>
            </div>
            <?php // echo $args['name']; ?>
            ACF Name (not set)
            
            <?php foreach ( $args['segments'] as $segment ) : ?>
                <div class="row">
                    <div class="col">
                        <?php echo slwp_get_athlete_name( $segment->athlete_id ); ?>
                    </div>
                    <div class="col">
                        Time <?php echo $segment->time; ?>
                    </div>
                    <div class="col">
                        <?php echo $segment->date; ?>
                    </div>
                    <div class="col">
                        <?php // echo $effort['activityurl']; ?>
                    </div>
                    <div class="col">
                        <?php // echo $effort['komrank']; ?><br />
                        <?php // echo $effort['prrank']; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

    </div>

</div>
