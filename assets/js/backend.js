import '../css/backend.less';
import Routing from '../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js';
const routes = require('../../public/js/fos_js_routes.json');
import { Chart, registerables } from 'chart.js';
import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';

Chart.register(...registerables);

global.Chart = Chart;

global.Calendar = Calendar
global.dayGridPlugin = dayGridPlugin

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

// Draw charts functions

global.drawBarChart = (context, title, labels, data1, labelData1, data2, labelData2) => {
  const config = {
    type: 'bar',
    data: {
      labels: labels,
      datasets: [
        {
          label: labelData1,
          data: data1,
          fill: false,
          borderColor:'#eded5b',
          backgroundColor:'#eded5b',
          tension: 0.1
        },
        {
          label: labelData2,
          data: data2,
          fill: false,
          borderColor:  '#747c08',
          backgroundColor: '#747c08',
          tension: 0.1
        },
      ]
    },
    options: {
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: 'right',
        },
        title: {
          display: true,
          text: title
        }
      }
    }
  };

  return new Chart(context, config)
}

global.drawLineChart = (context, title, labels, data1, labelData1, data2, labelData2) => {
  const config = {
    type: 'line',
    data: {
      labels: labels,
      datasets: [
        {
          label: labelData1,
          data: data1,
          fill: false,
          borderColor:  '#eded5b',
          tension: 0.1,
        },
        {
          label: labelData2,
          data: data2,
          fill: false,
          borderColor:  '#747c08',
          tension: 0.1,
        },
      ]
    },
    options: {
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: 'right',
        },
        title: {
          display: true,
          text: title
        }
      },
    }
  };

  return new Chart(context, config)
}

