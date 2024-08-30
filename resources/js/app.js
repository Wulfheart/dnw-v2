import './bootstrap';
import './webdip/clickhandler';
import {updateTimers} from './webdip/timehandler';
import htmx from 'htmx.org';

document.body.addEventListener('htmx:configRequest', (event) => {
    console.log(document.querySelector('meta[name="csrf-token"]').content);
    event.detail.headers['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').content;
})

document.addEventListener('DOMContentLoaded', () => {
    const config = document.querySelector('meta[name="timer-config"]').content
    updateTimers(JSON.parse(config));
});
