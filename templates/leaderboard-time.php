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
                    <h3>Time</h3>
                </div>
            </div>
            <?php echo $args['name']; ?>

            <div class="row">
                <div class="col">
                    Athlete Name
                </div>
                <div class="col">
                    Time: <?php echo $args['total_time']; ?>
                </div>
                <div class="col">
                    Distance: <?php echo $args['total_distance']; ?>
                </div>
                <div class="col">
                    Activities: <?php echo $args['activities_count']; ?>
                </div>
            </div>

        </div>

    </div>

</div>
