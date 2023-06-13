/* globals jQuery,wpupopin_settings,document,window */

jQuery(document).ready(function() {
    set_wpupopin(jQuery('.wpupopin__wrapper'));
});

function set_wpupopin($popin) {
    'use strict';

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

    /* Close on close link or button */
    $popin.on('click', '.wpupopin__close, .wpupopin__button, .close', function(e) {
        e.preventDefault();
        hide_popin();
    });

    /* Close on echap */
    window.addEventListener("keydown", function(e) {
        if (e.keyCode == 27) {
            hide_popin();
        }
    }, true);

    /* ----------------------------------------------------------
      Checks
    ---------------------------------------------------------- */

    /* Count the number of pages viewed */
    var nbPagesWait = parseInt(wpupopin_settings.display_after_n_pages, 10);
    if (!sessionStorage.wpupopin_nbPagesViewed) {
        sessionStorage.wpupopin_nbPagesViewed = 0;
    }
    sessionStorage.wpupopin_nbPagesViewed = parseInt(sessionStorage.wpupopin_nbPagesViewed, 10) + 1;

    /* Wait for click */
    var nbClicksWait = parseInt(wpupopin_settings.display_after_n_clicks, 10);
    if (!window.localStorage.getItem('wpupopin_nbClicksCount')) {
        window.localStorage.setItem('wpupopin_nbClicksCount', 0);
    }
    window.addEventListener('click', function(e) {
        var nbClicksCount = window.localStorage.getItem('wpupopin_nbClicksCount');
        nbClicksCount = parseInt(nbClicksCount, 10) + 1;
        window.localStorage.setItem('wpupopin_nbClicksCount', nbClicksCount);
        try_trigger_popin();
    }, true);

    /* Wait time */
    var nbSecsWait = parseInt(wpupopin_settings.display_after_n_seconds, 10);
    if (!window.localStorage.getItem('wpupopin_nbSecsCount')) {
        window.localStorage.setItem('wpupopin_nbSecsCount', 0);
    }
    setInterval(function() {
        var nbSecsCount = window.localStorage.getItem('wpupopin_nbSecsCount');
        nbSecsCount = parseInt(nbSecsCount, 10) + 1;
        window.localStorage.setItem('wpupopin_nbSecsCount', nbSecsCount);
        try_trigger_popin();
    }, 1000);

    /* Wait scroll */
    var nbPixCount = 0,
        nbPixWait = parseInt(wpupopin_settings.display_after_n_pixels, 10);
    var _timeout_scroll;
    window.addEventListener('scroll', function() {
        clearTimeout(_timeout_scroll);
        _timeout_scroll = setTimeout(function() {
            nbPixCount = Math.max(nbPixCount, window.pageYOffset);
            try_trigger_popin();
        }, 100);
    });

    /* ACTION */
    function try_trigger_popin() {

        /* Check number of pages viewed */
        if (parseInt(sessionStorage.wpupopin_nbPagesViewed, 10) < nbPagesWait) {
            return;
        }

        /* Check number of clicks */
        if (parseInt(window.localStorage.getItem('wpupopin_nbClicksCount'), 10) < nbClicksWait) {
            return;
        }

        /* Check number of seconds */
        if (parseInt(window.localStorage.getItem('wpupopin_nbSecsCount'), 10) < nbSecsWait) {
            return;
        }

        /* Check number of pixels */
        if (nbPixCount < nbPixWait) {
            return;
        }

        display_popin();
    }

    /* Initial try */
    try_trigger_popin();

}
