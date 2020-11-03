<?php $data = check_acf(get_the_ID()); ?>

<div id="leaderboard-<?php the_ID(); ?>" class="slwp-leaderboard leaderboard">

	<div class="summary entry-summary">


<div class="slwp">
    <div class="container">
        <div class="row">
            <div class="col">
                <h3>Segment Efforts</h3>
            </div>
        </div>
        <?php echo $data['name']; ?>
        <?php foreach($data['efforts'] as $effort) : ?>
            <div class="row">
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
    </div>
</div>

	
	
	</div>

</div>