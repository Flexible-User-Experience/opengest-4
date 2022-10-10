import '../css/backend.less';
import Routing from '../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js';
const routes = require('../../public/js/fos_js_routes.json');
import { Chart, registerables } from 'chart.js';

Chart.register(...registerables);

global.Chart = Chart;

// start PDF JS library
import jQuery from 'jquery';
import { getDocument, GlobalWorkerOptions } from 'pdfjs-dist/lib/pdf';
GlobalWorkerOptions.workerSrc = require('../../node_modules/pdfjs-dist/build/pdf.worker.entry.js');

jQuery(document).ready(function() {
  let pdfHolderNodes = jQuery('[data-holder]');
  for (let index = 0; index < pdfHolderNodes.length; index++) {
    let pdfHolderNode = pdfHolderNodes[index];
    let id = pdfHolderNode.id;
    let node = jQuery('#' + id);
    let downloadPath = node.attr('data-download');
    let loadingTask = getDocument(downloadPath);
    loadingTask.promise.then((pdf) => {
      pdf.getPage(1).then((page) => {
        let scale = 1;
        let viewport = page.getViewport({ scale: scale, });
        let canvas = document.getElementById('pdf-' + id);
        let context = canvas.getContext('2d');
        canvas.height = viewport.height;
        canvas.width = viewport.width;
        let renderContext = {
          canvasContext: context,
          viewport: viewport
        };
        page.render(renderContext);
      }, (errorGet) => {
        console.error('Error during ' + downloadPath + ' loading first page:', errorGet);
      });
    }, (errorGet) => {
      console.error('Error during ' + downloadPath + ' loading document:', errorGet);
    });
  }
});

window.Dropzone = require('dropzone/dist/min/dropzone.min');

// start the Stimulus application
import '../stimulus_bootstrap';
