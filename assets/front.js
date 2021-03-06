/* globals jQuery,wpupopin_settings,document,window */

'use strict';

jQuery(document).ready(function() {
    set_wpupopin(jQuery('.wpupopin__wrapper'));
});

function set_wpupopin($popin) {

    /* HELPERS */

    function hide_popin() {
        $popin.attr('data-visible', '0');
        $popin.trigger('wpupopin_hide');
    }

    function display_popin() {
        /* Disable if already displayed */
        if (hasCookie(wpupopin_settings.cookie_id)) {
            return;
        }

        /* Display popin */
        $popin.attr('data-visible', '1');

        /* Create a cookie */
        setCookie(wpupopin_settings.cookie_id, 1, wpupopin_settings.cookie_duration);
    }

    function setCookie(cookie_name, cookie_value, cookie_days) {
        cookie_days = cookie_days || 30;
        var cookie_date = new Date();
        cookie_date.setTime(cookie_date.getTime() + (cookie_days * 24 * 60 * 60 * 1000));
        var expires = "expires=" + cookie_date.toUTCString();
        document.cookie = cookie_name + "=" + cookie_value + ";" + expires + ";path=/";
    }

    /* Thanks to https://www.quirksmode.org/js/cookies.html */
    function readCookie(cookie_name) {
        var nameEQ = cookie_name + "=";
        var ca = document.cookie.split(';');
        for (var i = 0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) == ' ') c = c.substring(1, c.length);
            if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
        }
        return null;
    }

    function hasCookie(cookie_name) {
        return readCookie(cookie_name) !== null;
    }

    /* EVENTS */

    /* Custom event */
    $popin.on('hide_wpupopin', hide_popin);

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

    window.addEventListener('click', function(e) {
        var cookieID = wpupopin_settings.cookie_id + '_clicks',
            nbClicksWait = parseInt(wpupopin_settings.display_after_n_clicks, 10),
            nbClicks = readCookie(cookieID);
        if (!nbClicks) {
            nbClicks = 0;
        }
        nbClicks = parseInt(nbClicks, 10) + 1;
        setCookie(cookieID, nbClicks, wpupopin_settings.cookie_duration);

        if (nbClicks == nbClicksWait) {
            console.log('a');
            display_popin();
        }
    }, true);

    /* Close on close link or button */
    $popin.on('click', '.wpupopin__close, .wpupopin__button, .close', function(e) {
        e.preventDefault();
        hide_popin();
    });

    /* ACTION */

    /* If cookie does not exist */
    if (wpupopin_settings.display_after_n_clicks == 0) {
        display_popin();
    }

}
