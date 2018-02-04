/**
 * @file
 * Track article read events.
 */
/* global ga*/

(function ($) {
    'use strict';

    var scrollTimer = false;

    var checkScroll = function () {
        var article = $('.node--type-article.node--view-mode-full');
        var scrollTarget = article.offset().top + article.height() * 9/10;
        var pageBottom = $(window).scrollTop() + $(window).height();

        if (pageBottom > scrollTarget) {
            ga('send', 'event', 'article', 'read');
            $(document).off('scroll.articleread');
        }

        scrollTimer = false;
    };

    $(document).on('scroll.articleread', function () {
        if (!scrollTimer) {
            scrollTimer = setTimeout(checkScroll, 100);
        }
    });

})(jQuery);
