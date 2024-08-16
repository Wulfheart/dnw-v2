import './bootstrap';
import './webdip/clickhandler';
import NProgress from 'nprogress';
import htmx from "htmx.org";

document.body.addEventListener('htmx:beforeRequest', (event) => {
    if (event.detail.boosted) {

        NProgress.configure({showSpinner: false});
        NProgress.start();
    }
});
document.body.addEventListener('htmx:afterOnLoad', (event) => {
    if (event.detail.boosted) {
        NProgress.done();
    }
});
document.body.addEventListener('htmx:historyRestore', (event) => {
    NProgress.remove();
});

document.body.addEventListener('htmx:beforeOnLoad', function (evt) {
    if (evt.detail.xhr.status >= 400) {
        htmx.saveCurrentPageToHistory()
        document.location = evt.detail.xhr.responseURL;
        let iframe = document.createElement('iframe');
        document.getElementById('htmx-error-modal-content').appendChild(iframe);
        iframe.src = 'about:blank';
        iframe.contentWindow.document.open();
        iframe.contentWindow.document.write(evt.detail.xhr.responseText);
        iframe.contentWindow.document.close();
        document.getElementById('htmx-error-modal-backdrop').style.display = 'block';
    }
});
