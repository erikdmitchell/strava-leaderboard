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
            <?php echo $args['name']; ?>

            <?php foreach ($args['athletes'] as $athlete) : ?>
                <div class="row">
                    <div class="col">
                        <?php echo $athlete['firstname']; ?> <?php echo $athlete['lastname']; ?>
                    </div>
                    <div class="col">
                        Time: <?php echo $athlete['total_time']; ?>
                    </div>
                    <div class="col">
                        Distance: <?php echo $athlete['total_distance']; ?>
                    </div>
                    <div class="col">
                        Activities: <?php echo $athlete['activities_count']; ?>
                    </div>
                </div>
            <?php endforeach; ?>

        </div>

    </div>

</div>