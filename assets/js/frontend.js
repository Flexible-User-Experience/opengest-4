/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import '../css/frontend.less';

// Need jQuery? Install it with "yarn add jquery", then uncomment to import it.
// import jQuery from 'jquery/dist/jquery.min.js';
import $ from 'jquery';

const routes = require('../../public/js/fos_js_routes.js');
import Routing from '../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js';

window.Dropzone = require('dropzone/dist/min/dropzone.min');

console.log('Hello Webpack Encore! Edit me in assets/js/frontend.js');

$(document).ready(function() {
    $('[data-toggle="popover"]').popover();
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
    let verticalOffset = typeof(verticalOffset) != 'undefined' ? verticalOffset : 0;
    let element = jQuery('body');
    let offset = element.offset();
    let offsetTop = offset.top;
    jQuery('html, body').animate({scrollTop: offsetTop}, 1000, 'swing');
}
