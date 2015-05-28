jQuery( document ).ready( function($) {

    /**
     * Example 1
     */
    var original = $( '#dogs' ).attr( 'src' );
    $( "#show_me_hobbes" ).change(function() {
        if ( $( '#show_me_hobbes' ).prop( "checked" ) ) {
            $( '#spinner' ).fadeIn();
            $.get(
                wc_miami_ajax.ajaxURL, {
                    action: 'show_me_hobbes'
                }
            ).done( function( response ) {
                $( '#dogs' ).attr( 'src', response );
                    $( '#spinner' ).fadeOut();
            });

        }else{
            if ( original != $( '#dogs' ).attr( 'src') ) {
                $( '#dogs' ).attr( 'src', original );
            }

        }
    });

    /**
     * Example 2
     */


    //get example
    $.get( wc_miami_ajax.ajaxURL, {
        action: 'dog_check',
        nonce : wc_miami_ajax.nonce
    } ).done( function( response ) {
            if (  0 != response && 'undefined' != response ) {
                $( '#dogs' ).attr( 'src', response );
            }
    });

    //post example
    $( "#dog-selector" ).change(function() {
        var dog = $( '#dog-selector' ).val();
        if ( 'none' != dog ) {
            $( '#spinner' ).fadeIn();
            var data = {
                action: 'choose_dog',
                dog: dog,
                nonce: wc_miami_ajax.nonce
            };
            $.post(
                wc_miami_ajax.ajaxURL,
                data
            ).complete( function () {
                    $( '#spinner' ).fadeOut();
            } ).success( function ( response ) {
                    if ( 'undefined' != response.data.dog ) {
                        $( '#dogs' ).attr( 'src', response.data.dog );
                    }
                } ).fail( function ( xhr ) {
                    alert( wc_miami_ajax.failMessage + xhr.status );
                } );
        }

    });


});
