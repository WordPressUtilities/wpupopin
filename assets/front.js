
jQuery(document).ready(function() {
    set_wpupopin(jQuery('.wpupopin__wrapper'));
});

function set_wpupopin($popin) {
    'use strict';

    /* HELPERS */

    function hide_popin() {
        $popin.attr('data-visible', '0');
        $popin.trigger('wpupopin_hide');
        /* Mark as viewed */
        if (wpupopin_settings.mark_viewed_when == 'closing') {
            mark_as_viewed();
        }
    }

    function mark_as_viewed() {
        setCookie(wpupopin_settings.cookie_id, 1, wpupopin_settings.cookie_duration);
    }

    function display_popin() {
        /* Disable if already displayed */
        if (hasCookie(wpupopin_settings.cookie_id)) {
            return;
        }

        trigger_display_popin();
    }

    function trigger_display_popin() {
        /* Display popin */
        $popin.attr('data-visible', '1');

        /* Mark as viewed */
        if (wpupopin_settings.mark_viewed_when == 'display') {
            mark_as_viewed();
        }
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

    /* Custom event : Hide popin */
    $popin.on('hide_wpupopin', hide_popin);

    /* Custom event : Show popin */
    $popin.on('show_wpupopin', trigger_display_popin);

    /* Close on overlay */
    $popin.on('click', '.wpupopin__overlay', function(e) {
        e.preventDefault();
        if (wpupopin_settings.close_overlay) {
            hide_popin();
        }
    });

    /* Close on close link or button */
    $popin.on('click', '.wpupopin__close, .wpupopin__button, .close', function(e) {
        var _href = jQuery(this).attr('href');
        if (_href && _href != '#') {
            return;
        }
        e.preventDefault();
        hide_popin();
    });

    /* Close on echap */
    window.addEventListener("keydown", function(e) {
        if (e.key == 'Escape') {
            hide_popin();
        }
    }, true);

    /* ----------------------------------------------------------
      Checks
    ---------------------------------------------------------- */

    /* Count the number of pages viewed */
    var nbPagesWait = parseInt(wpupopin_settings.display_after_n_pages, 10);
    if (!window.sessionStorage.wpupopin_nbPagesViewed) {
        window.sessionStorage.wpupopin_nbPagesViewed = 0;
    }
    window.sessionStorage.wpupopin_nbPagesViewed = parseInt(window.sessionStorage.wpupopin_nbPagesViewed, 10) + 1;

    /* Wait for click */
    var nbClicksWait = parseInt(wpupopin_settings.display_after_n_clicks, 10);
    if (!window.localStorage.getItem('wpupopin_nbClicksCount')) {
        window.localStorage.setItem('wpupopin_nbClicksCount', 0);
    }
    window.addEventListener('click', function() {
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
        /* Do not track visit duration if tab is not visible */
        if (document.visibilityState == 'hidden') {
            return;
        }
        var nbSecsCount = window.localStorage.getItem('wpupopin_nbSecsCount');
        if (isNaN(nbSecsCount)) {
            nbSecsCount = 0;
        }
        nbSecsCount = parseInt(nbSecsCount, 10) + 1;
        window.localStorage.setItem('wpupopin_nbSecsCount', nbSecsCount);
        try_trigger_popin();
    }, 1000);

    /* Wait scroll */
    var nbPixCount = 0,
        nbPixActual = 0,
        scrollDir = 'down',
        nbPixWait = parseInt(wpupopin_settings.display_after_n_pixels, 10);
    var _timeout_scroll;
    window.addEventListener('scroll', function() {
        clearTimeout(_timeout_scroll);
        _timeout_scroll = setTimeout(function() {
            /* Direction */
            scrollDir = nbPixActual > window.pageYOffset ? 'up' : 'down';
            nbPixActual = window.pageYOffset;
            /* Count */
            nbPixCount = Math.max(nbPixCount, nbPixActual);
            try_trigger_popin();
        }, 100);
    });

    /* Check if popin is empty */
    function is_popin_empty() {
        var $popinContent = $popin.find('.wpupopin__content');
        if ($popinContent.length == 0) {
            return false;
        }
        if ($popinContent.html() == '') {
            return true;
        }
        return false;
    }

    if (is_popin_empty()) {
        console.log('[WPUPopin] Popin is empty. Please check your settings.');
        return;
    }

    /* ACTION */
    function try_trigger_popin() {

        /* Check number of pages viewed */
        if (parseInt(window.sessionStorage.wpupopin_nbPagesViewed, 10) < nbPagesWait) {
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

        /* Check scroll direction */
        if (wpupopin_settings.display_on_scroll_top == '1' && scrollDir != 'up') {
            return;
        }

        display_popin();
    }

    /* Initial try */
    try_trigger_popin();

}
