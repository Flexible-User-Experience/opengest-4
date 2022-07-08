import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
  static values = {
    attachment: String,
    path: String,
  };

  clicked() {
    const event = new CustomEvent('app-pdf-preview-button-clicked', {detail: {attachment: this.attachmentValue, path: this.pathValue}});
    window.dispatchEvent(event);
  }
}
