import '../css/frontend.less';
import jQuery from 'jquery';
import '../../node_modules/bootstrap-less/js/bootstrap.min';

jQuery(document).ready(function() {
    jQuery('[data-toggle="popover"]').popover();
});

jQuery(document).on('scroll', function() {
    if (jQuery(window).scrollTop() > 100) {
        jQuery('.scroll-top-wrapper').addClass('show');
    } else {
        jQuery('.scroll-top-wrapper').removeClass('show');
    }
});

jQuery('.scroll-top-wrapper').on('click', scrollToTop);

function scrollToTop() {
    let element = jQuery('body');
    let offset = element.offset();
    let offsetTop = offset.top;
    jQuery('html, body').animate({scrollTop: offsetTop}, 1000, 'swing');
}
