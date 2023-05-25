const URL = window.location.protocol + "//" + window.location.hostname;
const URI = window.location.pathname;
const SEARCH = window.location.search;
const DOMAIN = window.location.hostname;
const CSRF_TOKEN = $('meta[name="csrf-token"]').attr("content");


export {
    URL,
    URI,
    SEARCH,
    DOMAIN,
    CSRF_TOKEN,
};

export const CONSTANTS = {
    URL: URL,
    URI: URI,
    SEARCH: SEARCH,
    DOMAIN: DOMAIN,
    CSRF_TOKEN: CSRF_TOKEN
};
