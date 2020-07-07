// any CSS you import will output into a single css file (app.css in this case)
import '../css/frontend.less';
const $ = require('jquery');

global.$ = global.jQuery = $;

const routes = require('../../public/js/fos_js_routes.json');
import Routing from '../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js';

jQuery(document).ready(function() {
    // jQuery('[data-toggle="popover"]').popover();
    console.log('assets/js/frontend.js on Ready trigger');
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
