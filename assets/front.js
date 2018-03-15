/* globals jQuery,wpupopin_settings,document,window */

'use strict';

jQuery(document).ready(function() {
    set_wpupopin(jQuery('.wpupopin__wrapper'));
});

function set_wpupopin($popin) {

    /* HELPERS */

    function hide_popin() {
        $popin.attr('data-visible', '0');
    }

    function display_popin() {
        /* Display popin */
        $popin.attr('data-visible', '1');

        /* Create a cookie */
        setCookie(wpupopin_settings.cookie_id, 1, wpupopin_settings.cookie_duration);
    }

    function setCookie(cookie_name, cookie_value, cookie_days) {
        if (!cookie_days) {
            cookie_days = 30;
        }
        var cookie_date = new Date();
        cookie_date.setTime(cookie_date.getTime() + (cookie_days * 24 * 60 * 60 * 1000));
        var expires = "expires=" + cookie_date.toUTCString();
        document.cookie = cookie_name + "=" + cookie_value + ";" + expires + ";path=/";
    }

    function hasCookie(cookie_name) {
        return document.cookie.indexOf(cookie_name) > 1;
    }

    /* EVENTS */

    /* Close on overlay */
    $popin.on('click', '.wpupopin__overlay', function(e) {
        e.preventDefault();
        if (wpupopin_settings.close_overlay) {
            hide_popin();
        }
    });

    /* Close on echap */
    window.addEventListener("keydown", function(e) {
        if (e.keyCode == 27) {
            hide_popin();
        }
    }, true);

    /* Close on close link or button */
    $popin.on('click', '.wpupopin__close, .wpupopin__button, .close', function(e) {
        e.preventDefault();
        hide_popin();
    });

    /* ACTION */

    /* If cookie does not exist */
    if (!hasCookie(wpupopin_settings.cookie_id)) {
        display_popin();
    }

}
