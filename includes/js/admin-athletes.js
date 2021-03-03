jQuery( '.edit-athlete-lb' ).on(
    'click',
    function() {
        var data = {
            'action': 'edit_athlete_lb',
            'athlete_id': jQuery( this ).parent().data( 'athleteid' ),
        };

        jQuery.post(
            ajaxurl,
            data,
            function(pageHTML) {
                jQuery( '#leaderboards-box' ).html( '' ).html( pageHTML );
            }
        );

    }
);
