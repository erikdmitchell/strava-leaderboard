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

jQuery( document ).on(
    'click',
    '#slwp-update-athlete-lbs',
    function(e) {
        e.preventDefault();

        var data = {
            'action': 'slwp_update_athlete_lbs',
            'form_data': jQuery('form#slwp-athlete-lbs').serialize(),
            // nonce
        };

        jQuery.post(
            ajaxurl,
            data,
            function(response) {
console.log(response);
            }
        );

    }
);