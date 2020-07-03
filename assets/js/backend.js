/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import '../css/backend.less';

// Need jQuery? Install it with "yarn add jquery", then uncomment to import it.
// import $ from 'jquery';
import 'ckeditor4/ckeditor.js';

const routes = require('../../public/js/fos_js_routes.json');
import Routing from '../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js';

window.Dropzone = require('dropzone/dist/min/dropzone.min');

console.log('Hello Webpack Encore! Edit me in assets/js/backend.js');

$(document).ready(function() {
    $('[data-toggle="popover"]').popover();
    console.log('assets/js/backend.js on Ready trigger');
});
