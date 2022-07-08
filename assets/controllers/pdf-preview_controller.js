import { Controller } from '@hotwired/stimulus';
import { getDocument, GlobalWorkerOptions } from 'pdfjs-dist/lib/pdf';
GlobalWorkerOptions.workerSrc = require('pdfjs-dist/build/pdf.worker.entry.js');

export default class extends Controller {
  static classes = [ 'hidden' ];
  static targets = [
    'current',
    'total',
    'downloader',
    'canvas',
    'loader'
  ];

  initialize() {
    this.pdfDoc = null;
    this.pdfPageNum = 1;
    this.pdfPageRendering = false;
    this.pdfPageNumPending = null;
    this.pdfScale = 1.0;
    this.pdfCtx = this.canvasTarget.getContext('2d');
  }

  connect() {
    this.loaderTarget.classList.remove(this.hiddenClass);
    this.canvasTarget.classList.add(this.hiddenClass);
    this.currentTarget.textContent = 1;
  }

  renderPage(num) {
    this.pdfPageRendering = true;
    let self = this;
    this.pdfDoc.getPage(num).then(function(page) {
      let viewport = page.getViewport({ scale: self.pdfScale });
      self.canvasTarget.height = viewport.height;
      self.canvasTarget.width = viewport.width;
      let renderContext = {
        canvasContext: self.pdfCtx,
        viewport: viewport,
      };
      let renderTask = page.render(renderContext);
      renderTask.promise.then(function () {
        self.pdfPageRendering = false;
        if (self.pdfPageNumPending !== null) {
          renderPage(self.pdfPageNumPending);
          self.pdfPageNumPending = null;
        }
      });
    });
    this.currentTarget.textContent = num;
    this.totalTarget.textContent = this.pdfDoc.numPages;
  }

  queueRenderPage(num) {
    if (this.pdfPageRendering) {
      this.pdfPageNumPending = num;
    } else {
      this.renderPage(num);
    }
  }

  onPrevPage() {
    if (this.pdfPageNum <= 1) {
      return;
    }
    this.pdfPageNum--;
    this.queueRenderPage(this.pdfPageNum);
  }

  onNextPage() {
    if (this.pdfPageNum >= this.pdfDoc.numPages) {
      return;
    }
    this.pdfPageNum++;
    this.queueRenderPage(this.pdfPageNum);
  }

  update(event) {
    this.downloaderTarget.href = event.detail.path;
    this.loaderTarget.classList.add(this.hiddenClass);
    this.canvasTarget.classList.remove(this.hiddenClass);
    let loadingTask = getDocument(event.detail.path);
    loadingTask.promise.then((pdf) => {
      this.pdfDoc = pdf;
      this.renderPage(this.pdfPageNum);
    }, (errorGet) => {
      console.error('Error during ' + event.detail.path + ' loading document:', errorGet);
    });
  }
}
