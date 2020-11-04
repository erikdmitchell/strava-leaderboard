<div class="slwp">
    <div class="container">
        <div class="row">
            <div class="col">
                <h2>Athlete</h2>
            </div>
        </div>    
        <div class="row">
            <div class="col">
                <img src="<?php echo $data->profile; ?>" />
            </div>
            <div class="col">
                <?php echo $data->firstname; ?>
                <?php echo $data->lastname; ?>
            </div>
            <div class="col">
                <?php echo $data->location; ?>
            </div>
            <div class="col">
                <?php echo $data->gender; ?>
            </div>
        </div>
    </div>
</div>
