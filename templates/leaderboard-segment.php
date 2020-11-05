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
            <?php echo $args['name']; ?>
            
            <?php foreach ($args['athletes'] as $athlete) : ?>
                <?php foreach ( $athlete['efforts'] as $effort ) : ?>
                    <div class="row">
                        <div class="col">
                            <?php echo $athlete['firstname']; ?> <?php echo $athlete['lastname']; ?>
                        </div>
                        <div class="col">
                            Time <?php echo $effort['time']; ?>
                            <?php echo $effort['iskom']; ?>
                        </div>
                        <div class="col">
                            <?php echo $effort['date']; ?>
                        </div>
                        <div class="col">
                            <?php echo $effort['activityurl']; ?>
                        </div>
                        <div class="col">
                            <?php echo $effort['komrank']; ?><br />
                            <?php echo $effort['prrank']; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </div>

    </div>

</div>
