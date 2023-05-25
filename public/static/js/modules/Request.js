//Constantes
import { CSRF_TOKEN } from "./../constants/Constants.js";
import Collection from "./../modules/Collection.js";

export default class Request {

    sync = false;
    domain = window.location.protocol + "//" + window.location.hostname;
    headers = {
        'X-CSRF-Token': CSRF_TOKEN,
        'Vary': 'Origin',
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'Access-Control-Allow-Origin': URL,
    };
    credentials = "same-origin";

    url(url) {
        let self = this;
        self.domain = url;
        return self;
    }

    sync(sync = true) {
        let self = this;
        self.sync = sync;
        return self;
    }

    get(uri, params = {}) {
        let self = this;
        if (self.sync) {
            return self.getSync(uri, params);
        }
        return self.getAsync(uri, params);
    }

    header(headers = {}) {
        let self = this;
        self.headers = { ...self.headers, ...headers };
        return self;
    }

    post(uri, params = {}) {
        let self = this;
        if (self.sync) {
            return self.postSync(uri, params);
        }
        return self.postAsync(uri, params);
    }

    put(uri, params = {}) {
        let self = this;
        if (self.sync) {
            return self.putSync(uri, params);
        }
        return self.putAsync(uri, params);
    }

    delete(uri, params = {}) {
        let self = this;
        if (self.sync) {
            return self.deleteSync(uri, params);
        }
        return self.deleteAsync(uri, params);
    }

    getSync(uri, params = {}) {
        let self = this;
        const queryString = self.objToQueryString(params);
        let url = self.domain + uri + '?' + queryString;
        return self.fetchSync('GET', url, params);
    }

    postSync(uri, params = {}) {
        let self = this;
        let url = self.domain + uri;
        return self.fetchSync('POST', url, params);
    }

    putSync(uri, params = {}) {
        let self = this;
        let url = self.domain + uri;
        return self.fetchSync('PUT', url, params);
    }

    deleteSync(uri, params = {}) {
        let self = this;
        const queryString = self.objToQueryString(params);
        let url = self.domain + uri + '?' + queryString;
        return self.fetchSync('DELETE', url, params);
    }

    getAsync(uri, params = {}) {
        let self = this;
        const queryString = self.objToQueryString(params);
        let url = self.domain + uri + '?' + queryString;
        return self.fetchAsync('GET', url, params);
    }

    postAsync(uri, params = {}) {
        let self = this;
        let url = self.domain + uri;
        return self.fetchAsync('POST', url, params);
    }

    putAsync(uri, params = {}) {
        let self = this;
        let url = self.domain + uri;
        return self.fetchAsync('PUT', url, params);
    }

    deleteAsync(uri, params = {}) {
        let self = this;
        const queryString = self.objToQueryString(params);
        let url = self.domain + uri + '?' + queryString;
        return self.fetchAsync('DELETE', url, params);
    }

    objToQueryString(obj) {
        let keyValuePairs = [];
        for (const key in obj) {
            keyValuePairs.push(encodeURIComponent(key) + '=' + encodeURIComponent(obj[key]));
        }
        return keyValuePairs.join('&');
    }

    serialize(form) {
        let self = this;
        let help = self.help();
        if (form && (form instanceof Object) && ((form instanceof FormData) === false)) {
            return help.serializeFile(help.associative(form));
        }
        if (form && (form instanceof Object) && ((form instanceof FormData) === true)) {
            return help.serializeFile(help.associative(help.serialize(form)));
        }
        return {};
    }

    async fetchAsync(method = 'GET', url, params = {}) {
        let self = this;
        let response = fetch(
            url,
            {
                headers: self.headers,
                method: method,
                credentials: self.credentials,
                body: JSON.stringify(self.serialize(params))
            })
            .then(http => {
                let response = http.json()
                    .then((json) => { http.decode = () => { return json; }; return http; })
                    .catch(() => { http.decode = () => { return {}; }; return http; });
                return Promise.resolve(response);
            })
            .then(http => {
                http.collection = () => { return new Collection(http.decode()); };
                return Promise.resolve(http);
            })
            .then(http => {
                if (http && (http.ok || (http.status >= 200 && http.status <= 299))) {
                    return Promise.resolve(http);
                }
                return Promise.reject(http);
            });
        return response;
    }

    fetchSync(method = 'GET', url, params = {}) {
        let self = this;
        let request = new XMLHttpRequest();
        let response = {
            'request': () => request,
            'status': () => request.status,
            'ok': () => parseInt(request.status) === 200,
            'data': () => response.request().responseText,
            'body': () => response.request().responseText,
            'header': (header) => response.request()
                .getResponseHeader(header),
            'headers': () => response.request()
                .getAllResponseHeaders().split(/\n|\r/).filter(h => h.length > 0),
            'json': () => JSON
                .parse(response.request().responseText.length ? response.request().responseText : '{}'),
            'error': (callback) => (response.request().status < 200 || response.request().status > 299) ?
                callback(response.request()) : null,
            'success': (callback) => (response.request().status >= 200 && response.request().status <= 299) ?
                callback(response.request()) : null,
            'message': () => response.request().getResponseHeader('app-message')
                ? response.request().getResponseHeader('app-message') : request.statusText,
            'messages': () => response.request().getResponseHeader('app-messages')
                ? response.request().getResponseHeader('app-messages').split(/\n|\r|\,/) : [request.statusText],
        };
        request.open(method, url, false);
        request.withCredentials = true;
        for (const headerKey in self.headers) {
            request.setRequestHeader(headerKey, self.headers[headerKey]);
        }
        request.send(JSON.stringify(self.serialize(params)));
        response.request = () => request;
        return response;
    }

    help() {
        let help = {
            set: function (object = {}, key, value) {
                let self = this;
                let keys = key.split('.').filter(n => n);
                let first = keys.shift();
                let next = null;
                object = (object && (object instanceof Object)) ? object : {};
                if (first.length && (keys.length == 0)) {
                    object[first] = value;
                    return object;
                }
                if (first.length && (keys.length > 0)) {
                    next = Object.keys(object).includes(first) ? object[first] : {};
                    next = (next && (next instanceof Object)) ? next : {};
                    object[first] = self.set(next, keys.join('.'), value);
                    return object;
                }
                return object;
            },
            serialize: function (formData) {
                let form = {};
                let keys = [];
                formData.forEach(function (value, key) {
                    keys = Object.keys(form);
                    if (keys.includes(key)) {
                        form[key] = Array.isArray(form[key]) ? form[key] : [form[key]];
                        form[key].push(value);
                        return form;
                    }
                    form[key] = value;
                    return form;
                });
                return form;
            },
            associative: function (data) {
                let self = this;
                let json = {};
                let index = null;
                for (let key in data) {
                    index = (key.split(/\[|\]/g).filter(n => n).join('.'));
                    json = self.set(json, index, data[key])
                }
                return json;
            },
            serializeFile: function (data) {
                let self = this;
                let file = {};
                let exists = false;
                if (data && (data instanceof Object) && ((data instanceof File) === false)) {
                    for (const key in data) {
                        if (data[key] && (data[key] instanceof Object)) {
                            data[key] = self.serializeFile(data[key]);
                        }
                    }
                    return data;
                }
                if (data && (data instanceof Object) && ((data instanceof File) === true)) {
                    file = {
                        name: data.name,
                        size: data.size,
                        type: data.type,
                        mime: data.type,
                        date: data.lastModifiedDate,
                        timestamp: data.lastModified,
                        extension: data.type.split(/\\|\//).pop(),
                        base64: self.serializeFileBase64(data)
                    };
                    exists = file.name.toString().trim().length;
                    return exists ? file : null;
                }
                return data;
            },
            serializeFileBase64: function (file) {
                let returnText = '';
                let url = URL.createObjectURL(file);
                let xhr = new XMLHttpRequest();
                xhr.open("GET", url, false);
                xhr.overrideMimeType("text/plain; charset=x-user-defined");
                xhr.send();
                URL.revokeObjectURL(url);
                for (let i = 0; i < xhr.responseText.length; i++) {
                    returnText += String.fromCharCode(xhr.responseText.charCodeAt(i) & 0xff);
                };
                if (returnText && returnText.length) {
                    return "data:" + file.type + ";base64," + btoa(returnText);
                }
                return null;
            }
        };
        return help;
    }
}
