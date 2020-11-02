<div class="slwp">
    <div class="container">
        <div class="row">
            <div class="col">
                <h2>Segment</h2>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <?php echo $data->name; ?>
            </div>
            <div class="col">
                Distance: <?php echo $data->distance; ?><br />
                Avg. Grade: <?php echo $data->avggrade; ?><br />
                Max Grade: <?php echo $data->maxgrade; ?><br />
                Elevation Gain: <?php echo $data->elevgain; ?>
            </div>
            <div class="col">
                Category: <?php echo $data->category; ?>
            </div>
            <div class="col">
                <?php echo $data->location; ?>
            </div>
        </div>
    </div>
</div>
