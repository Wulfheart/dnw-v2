import './bootstrap';
import './webdip/clickhandler';
import 'htmx.org';
import NProgress from 'nprogress';

window.addEventListener('htmx:beforeRequest', (event) => {
    if (event.detail.boosted) {
        NProgress.configure({showSpinner: false});
        NProgress.start();
    }
});
window.addEventListener('htmx:afterOnLoad', (event) => {
    if (event.detail.boosted) {
        NProgress.done();
    }
});
window.addEventListener('htmx:historyRestore', (event) => {
    NProgress.remove();
});

