import '../css/backend.less';
import '../../node_modules/bootstrap-less/js/bootstrap.min';
import Routing from '../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js';
const routes = require('../../public/js/fos_js_routes.json');

window.Dropzone = require('dropzone/dist/min/dropzone.min');

jQuery(document).ready(function() {
    jQuery('[data-toggle="popover"]').popover();
});
