
export default class CollectionFormatter {

    recurssive = false;
    attribute = null;
    collection = null;
    mask = {
        mask_cep: "#####-###",
        mask_rg: '##.###.###-#',
        mask_cpf: '###.###.###-##',
        mask_cnpj: '##.###.###/####-##',
        mask_celular: '#####-####',
        mask_telefone: '####-####',
        mask_ddd_celular: '(##)#####-####',
        mask_ddd_telefone: '(##)####-####'
    };
    constructor(collection, attribute, recurssive) {
        let self = this;
        self.attribute = attribute;
        self.recurssive = recurssive;
        self.collection = collection;
    }
    attr() {
        let self = this;
        return self.attribute;
    }
    exists() {
        let self = this;
        return self.collection.exists(self.attribute);
    }
    collect() {
        let self = this;
        return self.collection;
    }
    value() {
        let self = this;
        return self.collection.get(self.attribute);
    }

    date(format = 'd/m/y') {
        let self = this;
        return self.timestamp(format);
    }

    numeric() {
        let self = this;
        let help = self.help();
        let value = self.value();
        let exists = self.exists();
        let attribute = self.attr();
        let collection = self.collect();
        if (exists && value) {
            collection.set(attribute, help.onlyNumbers(value));
        }
        return self.callRecurssive(self.numeric.name);
    }

    integer() {
        let self = this;
        let help = self.help();
        let value = self.value();
        let exists = self.exists();
        let attribute = self.attr();
        let collection = self.collect();
        let newValue = value;
        if (exists && value) {
            newValue = help.onlyNumbers(value);
            newValue = help.isNumeric(newValue) ? parseInt(newValue) : newValue;
            collection.set(attribute, newValue);
        }
        return self.callRecurssive(self.integer.name);
    }

    float(decimal = 2) {
        let self = this;
        let help = self.help();
        let value = self.value();
        let exists = self.exists();
        let attribute = self.attr();
        let collection = self.collect();
        let newValue = value;
        if (exists && value) {
            newValue = help.toFloat(value);
            newValue = parseFloat(newValue).toFixed(decimal);
            collection.set(attribute, newValue);
        }
        return self.callRecurssive(self.float.name, decimal);
    }

    floatString(decimal = 2) {
        let self = this;
        let help = self.help();
        let value = self.value();
        let exists = self.exists();
        let attribute = self.attr();
        let collection = self.collect();
        let newValue = value;
        if (exists && value) {
            newValue = help.toFloat(value);
            newValue = help.getToString(newValue.toFixed(decimal));
            collection.set(attribute, newValue);
        }
        return self.callRecurssive(self.floatString.name, decimal);
    }

    real(decimal = 2) {
        let self = this;
        let help = self.help();
        let value = self.value();
        let exists = self.exists();
        let attribute = self.attr();
        let collection = self.collect();
        let newValue = value;
        if (exists && value) {
            newValue = help.floatToMoneyBrasil(value, decimal);
            collection.set(attribute, newValue);
        }
        return self.callRecurssive(self.real.name, decimal);
    }

    array() {
        let self = this;
        let help = self.help();
        let value = self.value();
        let exists = self.exists();
        let attribute = self.attr();
        let collection = self.collect();
        let newValue = value;
        if (exists && value) {
            newValue = help.toArray(value, decimal);
            collection.set(attribute, newValue);
        }
        return self.callRecurssive(self.array.name);
    }

    boolean() {
        let self = this;
        let value = self.value();
        let exists = self.exists();
        let attribute = self.attr();
        let collection = self.collect();
        if (exists && value) {
            collection.set(attribute, ['S', '1', 1, true].includes(value));
        }
        return self.callRecurssive(self.boolean.name);
    }

    rg() {
        let self = this;
        let help = self.help();
        let value = self.value();
        let exists = self.exists();
        let attribute = self.attr();
        let collection = self.collect();
        if (exists && value) {
            value = help.onlyNumbers(value);
            value = help.format(self.mask.mask_rg, value);
            collection.set(attribute, value);
        }
        return self.callRecurssive(self.rg.name);
    }

    cpf() {
        let self = this;
        let help = self.help();
        let value = self.value();
        let exists = self.exists();
        let attribute = self.attr();
        let collection = self.collect();
        if (exists && value) {
            value = help.onlyNumbers(value);
            if (help.getLength(value) === 11) {
                value = help.format(self.mask.mask_cpf, value);
                collection.set(attribute, value);
                return self.callRecurssive(self.cpf.name);
            }
            if (help.getLength(value) === 14) {
                value = help.format(self.mask.mask_cnpj, value);
                collection.set(attribute, value);
                return self.callRecurssive(self.cpf.name);
            }
            collection.set(attribute, value);
        }
        return self.callRecurssive(self.cpf.name);
    }

    cnpj() {
        let self = this;
        let help = self.help();
        let value = self.value();
        let exists = self.exists();
        let attribute = self.attr();
        let collection = self.collect();
        if (exists && value) {
            value = help.onlyNumbers(value);
            if (help.getLength(value) === 11) {
                value = help.format(self.mask.mask_cpf, value);
                collection.set(attribute, value);
                return self.callRecurssive(self.cnpj.name);
            }
            if (help.getLength(value) === 14) {
                value = help.format(self.mask.mask_cnpj, value);
                collection.set(attribute, value);
                return self.callRecurssive(self.cnpj.name);
            }
        }
        return self.callRecurssive(self.cnpj.name);
    }

    telefone() {
        let self = this;
        let help = self.help();
        let value = self.value();
        let exists = self.exists();
        let attribute = self.attr();
        let collection = self.collect();
        if (exists && value) {
            value = help.onlyNumbers(value);
            if (help.getLength(value) === 11) {
                value = help.format(self.mask.mask_ddd_celular, value);
                collection.set(attribute, value);
                return self.callRecurssive(self.telefone.name);
            }
            if (help.getLength(value) === 10) {
                value = help.format(self.mask.mask_ddd_telefone, value);
                collection.set(attribute, value);
                return self.callRecurssive(self.telefone.name);
            }
            if (help.getLength(value) === 9) {
                value = help.format(self.mask.mask_celular, value);
                collection.set(attribute, value);
                return self.callRecurssive(self.telefone.name);
            }
            if (help.getLength(value) === 8) {
                value = help.format(self.mask.mask_telefone, value);
                collection.set(attribute, value);
                return self.callRecurssive(self.telefone.name);
            }
        }
        return self.callRecurssive(self.telefone.name);
    }

    celular() {
        let self = this;
        let help = self.help();
        let value = self.value();
        let exists = self.exists();
        let attribute = self.attr();
        let collection = self.collect();
        if (exists && value) {
            value = help.onlyNumbers(value);
            if (help.getLength(value) === 11) {
                value = help.format(self.mask.mask_ddd_celular, value);
                collection.set(attribute, value);
                return self.callRecurssive(self.celular.name);
            }
            if (help.getLength(value) === 10) {
                value = help.format(self.mask.mask_ddd_telefone, value);
                collection.set(attribute, value);
                return self.callRecurssive(self.celular.name);
            }
            if (help.getLength(value) === 9) {
                value = help.format(self.mask.mask_celular, value);
                collection.set(attribute, value);
                return self.callRecurssive(self.celular.name);
            }
            if (help.getLength(value) === 8) {
                value = help.format(self.mask.mask_telefone, value);
                collection.set(attribute, value);
                return self.callRecurssive(self.celular.name);
            }
        }
        return self.callRecurssive(self.celular.name);
    }

    cep() {
        let self = this;
        let help = self.help();
        let value = self.value();
        let exists = self.exists();
        let attribute = self.attr();
        let collection = self.collect();
        if (exists && value) {
            value = help.onlyNumbers(value);
            if (help.getLength(value) === 8) {
                value = help.format(self.mask.mask_cep, value);
                collection.set(attribute, value);
                return self.callRecurssive(self.cep.name);
            }
        }
        return self.callRecurssive(self.cep.name);
    }

    timestamp(format = 'd/m/y h:i:s') {
        let self = this;
        let help = self.help();
        let value = self.value();
        let exists = self.exists();
        let attribute = self.attr();
        let collection = self.collect();
        let formats = {
            "dd-mm-yyyy": /^(\d{1,2})\/(\d{1,2})\/(\d{4})$/,
            "yyyy-mm-dd": /^(\d{4})\/(\d{1,2})\/(\d{1,2})$/,
            "dd-mm-yyyy_hh": /^(\d{1,2})\-(\d{1,2})\-(\d{4})\s(\d{1,2})$/,
            "yyyy-mm-dd_hh": /^(\d{4})\-(\d{1,2})\-(\d{1,2})\s(\d{1,2})$/,
            "dd-mm-yyyy_hh:mm": /^(\d{1,2})\-(\d{1,2})\-(\d{4})\s(\d{1,2})\:(\d{1,2})$/,
            "yyyy-mm-dd_hh:mm": /^(\d{4})\-(\d{1,2})\-(\d{1,2})\s(\d{1,2})\:(\d{1,2})$/,
            "dd-mm-yyyy_hh:mm:ss": /^(\d{1,2})\-(\d{1,2})\-(\d{4})\s(\d{1,2})\:(\d{1,2})\:(\d{1,2})$/,
            "yyyy-mm-dd_hh:mm:ss": /^(\d{4})\-(\d{1,2})\-(\d{1,2})\s(\d{1,2})\:(\d{1,2})\:(\d{1,2})$/,
        };
        if (exists && value && help.isString(value) && formats && help.isString(format)) {
            let names = [];
            let match = [];
            let assoc = {};
            for (let prop in formats) {
                names = prop.split(/-|\/|\\|\:|\s|\.|\_/);
                match = value.replace(/[\\\/]/g, '-').match(formats[prop]);
                if (names && match) {
                    names = names.filter((v, i) => (typeof i === 'number') && v.match(/[^0-9]/g));
                    match = match.filter((v, i) => (typeof i === 'number') && !v.match(/[^0-9]/g));
                    break;
                }
            }
            if (names && match && (names.length === match.length)) {
                for (let index in names) {
                    assoc[names[index]] = match[index];
                }
            }
            if (assoc && Object.keys(assoc).length > 0) {
                let keys = Object.keys(assoc);
                let year = keys.includes('yyyy') ? assoc['yyyy'] : '1800';
                let month = keys.includes('mm') ? assoc['mm'] : '00';
                let day = keys.includes('dd') ? assoc['dd'] : '01';
                let hours = keys.includes('hh') ? assoc['hh'] : '00';
                let minutes = keys.includes('mm') ? assoc['mm'] : '00';
                let seconds = keys.includes('ss') ? assoc['ss'] : '00';
                let miliSeconds = keys.includes('uu') ? assoc['uu'] : '00';
                let date = new Date(year, (month - 1), day, hours, minutes, seconds, miliSeconds);
                year = date.getFullYear();
                hours = date.getHours().toString().padStart(2, '0');
                minutes = date.getMinutes().toString().padStart(2, '0');
                seconds = date.getSeconds().toString().padStart(2, '0');
                miliSeconds = date.getMilliseconds().toString().padStart(2, '0');
                month = (date.getMonth() + 1).toString().padStart(2, '0');
                day = (date.getDate()).toString().padStart(2, '0');
                format = format.toString().toUpperCase().trim();
                format = format.replace('Y', year);
                format = format.replace('M', month);
                format = format.replace('D', day);
                format = format.replace('H', hours);
                format = format.replace('I', minutes);
                format = format.replace('S', seconds);
                format = format.replace('U', miliSeconds);
                collection.set(attribute, format);
            }
        }
        return self.callRecurssive(self.timestamp.name, format);
    }
    callRecurssive(method, ...args) {
        let self = this;
        let recursive = self.recurssive;
        let collection = self.collect();
        let attribute = self.attr();
        if (recursive && collection && attribute) {
            collection.forInCollections(function (collect) {
                let formatter = collect
                    .formatter(attribute, recursive);
                formatter[method].apply(formatter, args);
            });
        }
        return self;
    }
    help() {
        let help = {
            isNull: function (value) {
                try {
                    return value === null;
                } catch (e) {
                    return false;
                }
            },
            isNotNull: function (value) {
                try {
                    return value !== null;
                } catch (e) {
                    return false;
                }
            },
            isNotEmpty: function (value = null) {
                try {
                    let self = this;
                    return self.isEmpty(value) === false;
                } catch (e) {
                    return false;
                }
            },
            isString: function (value = null) {
                try {
                    let self = this;
                    return self.isTypeOf(value, 'string');
                } catch (e) {
                    return false;
                }
            },
            isNotString: function (value = null) {
                try {
                    let self = this;
                    return self.isTypeOf(value, 'string') === false;
                } catch (e) {
                    return false;
                }
            },
            isBoolean: function (value = null) {
                try {
                    let self = this;
                    return self.isTypeOf(value, 'boolean');
                } catch (e) {
                    return false;
                }
            },
            isNumber: function (value = null) {
                try {
                    let self = this;
                    return self.isTypeOf(value, 'number');
                } catch (e) {
                    return false;
                }
            },
            isNotNumber: function (value = null) {
                try {
                    let self = this;
                    return self.isTypeOf(value, 'number') === false;
                } catch (e) {
                    return false;
                }
            },
            isNumeric: function (value = null) {
                try {
                    let self = this;
                    if (self.isTypeOf(value, 'number')) {
                        return true;
                    }
                    if (!isNaN(str) && !isNaN(parseFloat(str))) {
                        return true;
                    }
                    return true;
                } catch (e) {
                    return false;
                }
            },
            isNotNumeric: function (value = null) {
                try {
                    let self = this;
                    return self.isNumeric(value) === false;
                } catch (e) {
                    return false;
                }
            },
            isInstanceOf: function (value = null, type = null) {
                try {
                    let self = this;
                    return self.isTypeOf(value, type);
                } catch (e) {
                    return false;
                }
            },
            isNotInstanceOf: function (value = null, type = null) {
                try {
                    let self = this;
                    return self.isTypeOf(value, type) === false;
                } catch (e) {
                    return false;
                }
            },
            isNotTypeOf: function (value = null, type = null) {
                try {
                    let self = this;
                    return self.isTypeOf(value, type) === false;
                } catch (e) {
                    return false;
                }
            },
            getFirstJsonDecodeInText: function (value = null) {
                try {
                    let self = this;
                    let jsons = self.getJsonsDecodeInText(value);
                    let keys = Object.keys(jsons);
                    return ((keys.length > 0) ? jsons[keys.shift()] : null);
                } catch (e) {
                    return false;
                }
            },
            getJsonsDecodeInText: function (value = null) {
                try {
                    let self = this;
                    let jsons = self.getStringsJsonsInText(value);
                    jsons.forEach(function (value, index) {
                        return jsons[index] = value = JSON.parse(value);
                    });
                    return jsons;
                } catch (e) {
                    return false;
                }
            },
            getFirstStringJsonInText: function (value = null) {
                try {
                    let self = this;
                    let jsons = self.getStringsJsonsInText(value);
                    let keys = Object.keys(jsons);
                    return ((keys.length > 0) ? jsons[keys.shift()] : null);
                } catch (e) {
                    return false;
                }
            },
            isEmpty: function (value = null) {
                try {
                    let self = this;
                    return self.getLength(value) === 0;
                } catch (e) {
                    return false;
                }
            },
            isArray: function (value = null, implicit = true) {
                try {
                    let self = this;
                    if (implicit) {
                        return (self.isObject(value, !implicit) === true)
                            &&
                            (self.isObject(value, implicit) === false);
                    }
                    return (self.isObject(value, !implicit) === true);
                } catch (e) {
                    return false;
                }
            },
            isNotObject: function (value = null, implicit = true) {
                try {
                    let self = this;
                    return self.isObject(value, implicit) === false;
                } catch (e) {
                    return false;
                }
            },
            isNotArray: function (value = null, implicit = true) {
                try {
                    let self = this;
                    return self.isArray(value, implicit) === false;
                } catch (e) {
                    return false;
                }
            },
            isAttribute: function (obj = {}, value = null) {
                try {
                    let self = this;
                    return self.inIndex(obj, value);
                } catch (e) {
                    return false;
                }
            },
            inAttribute: function (obj = {}, value = null) {
                try {
                    let self = this;
                    return self.inIndex(obj, value);
                } catch (e) {
                    return false;
                }
            },
            isIndex: function (obj = {}, value = null) {
                try {
                    let self = this;
                    return self.inIndex(obj, value);
                } catch (e) {
                    return false;
                }
            },
            inIndex: function (obj = {}, value = null) {
                try {
                    let self = this;
                    let keysInObject = [];
                    if (self.isObject(obj, false)) {
                        keysInObject = self.getKeys(obj);
                        return keysInObject.includes(value);
                    }
                    return false;
                } catch (e) {
                    return false;
                }
            },
            inArray: function (obj = {}, value = null) {
                try {
                    let self = this;
                    return self.inObject(obj, value);
                } catch (e) {
                    return false;
                }
            },
            inObject: function (obj = {}, value = null) {
                try {
                    let self = this;
                    let vauesInObject = [];
                    if (self.isObject(obj, false)) {
                        vauesInObject = self.getValues(obj);
                        return vauesInObject.includes(value);
                    }
                    return false;
                } catch (e) {
                    return false;
                }
            },
            isObject: function (value = null, implicit = true) {
                try {
                    let self = this;
                    let isObjectImplict = false;
                    let isObject = (value && self.isTypeOf(value, 'object'));
                    if (isObject && implicit) {
                        let objectKeys = Object.keys(value);
                        let objectKeysString = objectKeys.filter(
                            v => v && v.toString().replace(/[0-9]/g, '').length > 0
                        );
                        isObjectImplict = (isObject && (objectKeysString.length > 0))
                        return ((isObject === true) && (isObjectImplict === true));
                    }
                    return ((isObject === true) && (isObjectImplict === false));
                } catch (e) {
                    return false;
                }
            },
            getFirst: function (obj = {}) {
                try {
                    let self = this;
                    if (obj && self.isObject(obj, false)) {
                        let keys = self.getKeys(obj);
                        let firstKey = (keys.length > 0 ? keys[0] : null)
                        return (self.isNotEmpty(firstKey) ? obj[firstKey] : null);
                    }
                    return obj;
                } catch (e) {
                    return null;
                }
            },
            getKeys: function (obj = {}) {
                try {
                    let self = this;
                    if (obj && self.isObject(obj, false)) {
                        return Object.keys(obj);
                    }
                    return [];
                } catch (e) {
                    return []
                }
            },
            getValues: function (obj = {}) {
                try {
                    let self = this;
                    if (obj && self.isObject(obj, false)) {
                        return Object.values(obj);
                    }
                    return [];
                } catch (e) {
                    return []
                }
            },
            getLength: function (value = null) {
                try {
                    let self = this;
                    if (self.isNull(value)) {
                        return 0;
                    }
                    if (self.isBoolean(value)) {
                        return 1;
                    }
                    if (self.isObject(value, false)) {
                        return Object.keys(value).length;
                    }
                    return self.getToString(value).length;
                } catch (e) {
                    return 0;
                }
            },
            getToString: function (value = null) {
                try {
                    let self = this;
                    let empty = '';
                    if (self.isNull(value)) {
                        return ('').toString();
                    }
                    if (self.isNotObject(value, false)) {
                        return value.toString();
                    }
                    if (self.isObject(value, false)) {
                        return JSON.stringify(value).toString();
                    }
                    return empty;
                } catch (e) {
                    return '';
                }
            },
            getToArray: function (value = null) {
                try {
                    let self = this;
                    return self.toArray(value);
                } catch (e) {
                    return {};
                }
            },
            getToObject: function (value = null) {
                try {
                    let self = this;
                    return self.toObject(value);
                } catch (e) {
                    return {};
                }
            },
            getSplit: function (value = null, separator = '.') {
                try {
                    let self = this;
                    return self.getToString(value).split(separator);
                } catch (e) {
                    return [];
                }
            },
            getSplitNotEmpty: function (value = null, separator = '.') {
                try {
                    let self = this;
                    return self.getSplit(value, separator)
                        .filter(n => self.getLength(n) > 0);
                } catch (e) {
                    return [];
                }
            },
            toObject: function (value = null) {
                try {
                    let self = this;
                    let json = null;
                    let array = null;
                    if (self.isEmpty(value)) {
                        return {};
                    }
                    if (self.isObject(value)) {
                        return value;
                    }
                    if (self.isArray(value)) {
                        for (let prop in value) {
                            value[self.getToString(prop)]
                                = value[prop];
                        }
                        return value;
                    }
                    if (self.isString(value.toString())) {
                        json = self
                            .getFirstJsonDecodeInText(value);
                        if (json && self.isObject(json, false)) {
                            for (let prop in json) {
                                json[self.getToString(prop)]
                                    = json[prop];
                            }
                            return json;
                        }
                    }
                    if (self.isString(value.toString())) {
                        array = self.getSplit(value, '');
                        if (array && self.isObject(array, false)) {
                            for (let prop in array) {
                                array[self.getToString(prop)]
                                    = array[prop];
                            }
                            return array;
                        }
                    }
                    return { 0: value }
                } catch (e) {
                    return { 0: value }
                }
            },
            toArray: function (value = null) {
                try {
                    let self = this;
                    let json = null;
                    let array = null;
                    if (self.isEmpty(value)) {
                        return [];
                    }
                    if (self.isArray(value)) {
                        return value;
                    }
                    if (self.isObject(value)) {
                        return Object.values(value);
                    }
                    if (self.isString(value.toString())) {
                        json = self
                            .getFirstJsonDecodeInText(value);
                        if (json && self.isObject(json, false)) {
                            return Object.values(json);
                        }
                    }
                    if (self.isString(value.toString())) {
                        array = self.getSplit(value, '');
                        if (array && self.isObject(array, false)) {
                            return Object.values(array);
                        }
                    }
                    return [value];
                } catch (e) {
                    return [value];
                }
            },
            isTypeOf: function (value = null, type = null) {
                try {
                    let typeString = null;
                    let typeType = (typeof type).toString()
                        .toLocaleLowerCase().trim();
                    let valueType = (typeof value).toString()
                        .toLocaleLowerCase().trim();
                    if (['object', 'function', 'array', 'class'].includes(typeType)) {
                        return value instanceof type;
                    }
                    if ((value !== null) && typeType === 'string') {
                        typeString = (type.toString().toLocaleLowerCase().trim());
                        return [valueType].includes(typeString);
                    }
                    if ((value === null) && (typeType === 'string')) {
                        typeString = (type.toString().toLocaleLowerCase().trim());
                        return [valueType, 'null', 'undefined', 'object'].includes(typeString);
                    }
                    return false;
                } catch (e) {
                    return false;
                }
            },
            getStringsJsonsInText: function (value = null) {
                try {
                    let self = this;
                    let matchs = null;
                    let jsons = {};
                    let regex = '((\\[[^\\}]+)?\\{s*[^\\}\\{]{3,}?:.*\\}([^\\{]+\\])?)';
                    const regexMatch = new RegExp(regex, 'gm');
                    if (self.isEmpty(value)) {
                        return jsons;
                    }
                    if (self.isNotString(value)) {
                        return jsons;
                    }
                    while ((matchs = regexMatch.exec(value)) !== null) {
                        if (matchs.regexMatch === regexMatch.lastIndex) {
                            regexMatch.lastIndex++;
                        }
                        matchs.forEach((match, groupIndex) => {
                            if (match) {
                                jsons[groupIndex] = match;
                            }
                        });
                    }
                    jsons = Object.values(jsons).filter(function (value, index, self) {
                        return self.indexOf(value) === index;
                    });
                    return jsons;
                } catch (e) {
                    return [];
                }
            },
            ksort: function (value = null, order = 'asc', recurssive = false, group = false) {
                let self = this;
                if (value && self.isTypeOf(value, 'object')) {
                    let newValues = {};
                    let newValuesObjects = {};
                    let keys = self.getKeys(value).sort();
                    if (order.toLocaleLowerCase().trim() === 'desc') {
                        keys = keys.reverse();
                    }
                    keys.forEach(function (key) {
                        let val = value[key];
                        if (recurssive && val && self.isTypeOf(val, 'object')) {
                            val = self.ksort(val, order, recurssive, group);
                        }
                        if (group === false) {
                            newValues[key] = val;
                        }
                        if (group && self.isNotTypeOf(val, 'object')) {
                            newValues[key] = val;
                        }
                        if (group && self.isTypeOf(val, 'object')) {
                            newValuesObjects[key] = val;
                        }
                    });
                    return { ...newValues, ...newValuesObjects };
                }
                return value;
            },
            sort: function (value = null, order = 'asc', recurssive = false) {
                let self = this;
                let originalKeys = [];
                let originalValues = [];
                let originalValuesOrder = [];
                let valuesOrdered = {};
                if (value && self.isTypeOf(value, 'object')) {
                    originalKeys = self.getKeys(value);
                    originalValues = self.getValues(value);
                    originalValues.forEach(function (originalValue, originalKey) {
                        originalValuesOrder.push(
                            { index: originalKey, value: originalValue }
                        );
                    });
                    originalValuesOrder.sort(function (itemA, itemB) {
                        let valueA = self.isString(itemA.value)
                            ? self.getToString(itemA.value).toLocaleLowerCase() : itemA.value;
                        let valueB = self.isString(itemB.value)
                            ? self.getToString(itemB.value).toLocaleLowerCase() : itemB.value;
                        if (valueA > valueB) {
                            return 1;
                        }
                        if (valueA < valueB) {
                            return -1;
                        }
                        return 0;
                    });
                    if (order.toLocaleLowerCase().trim() === 'desc') {
                        originalValuesOrder = originalValuesOrder.reverse();
                    }
                    originalValuesOrder.forEach(function (item) {
                        if (item.value && self.isTypeOf(item.value, 'object') && recurssive) {
                            item.value = self.sort(item.value, order, recurssive);
                        }
                        valuesOrdered[originalKeys[item.index]] = item.value;
                    });
                    return valuesOrdered;
                }
                return value;
            },
            numberFormat: function (number, decimals, decPoint, thousandsPoint) {
                let splitNum = null;
                if (number == null || !isFinite(number)) {
                    return null;
                }
                if (!decimals) {
                    var len = number.toString().split('.').length;
                    decimals = len > 1 ? len : 0;
                }
                if (!decPoint) {
                    decPoint = '.';
                }
                if (!thousandsPoint) {
                    thousandsPoint = ',';
                }
                number = parseFloat(number).toFixed(decimals);
                number = number.replace(".", decPoint);
                splitNum = number.split(decPoint);
                splitNum[0] = splitNum[0].replace(/\B(?=(\d{3})+(?!\d))/g, thousandsPoint);
                number = splitNum.join(decPoint);
                return number;
            },
            vsPrintf: function (mask = '%s', value = null) {
                let self = this;
                let length = (mask.match(/\%s/g) || []).length;
                let values = (self.isObject(value, false) ? self.getValues(value) : value.split(''));
                let first = null
                let masked = mask;
                while (length > 0) {
                    length--;
                    first = values.shift();
                    masked = masked.replace(/\%s/, first);
                }
                return masked;
            },
            format: function (mask, string, complete = null, leftOrRight = 1) {
                let self = this;
                if (self.isNotEmpty(string)) {
                    complete = (self.isNotEmpty(complete) ? complete : "#");
                    let strLength = self.getLength(string);
                    let maskLength = (mask.match(/\#/g) || []).length;
                    let maxStrLength = ((maskLength > strLength) ? maskLength : strLength);
                    let maxMaskLength = ((strLength > maskLength) ? strLength : maskLength);
                    let maskPrint = leftOrRight ? mask.padStart(maxMaskLength, '#') : mask.padEnd(maxMaskLength, '#');
                    let stringPrint = leftOrRight ? string.padStart(maxStrLength, complete) : mask.padEnd(maxStrLength, complete);
                    maskPrint = maskPrint.replace(/\#/g, '%s');
                    return self.vsPrintf(maskPrint, stringPrint);
                }
                return string;
            },

            onlyNumbers: function (value = null) {
                let self = this;
                if (self.isNotObject(value, false) && self.getLength(value)) {
                    return self.getToString(value).replace(/[^0-9]/g, '');
                }
                return value;
            },
            toFloat: function (value = null) {
                let self = this;
                let newValue = null;
                if (self.isNotObject(value, false) && self.getLength(value)) {
                    newValue = self.getToString(value).match(/\-?\d*(?:\.\d+)?/g, '');
                    newValue = (newValue ? newValue.filter((v, i) => (typeof i === 'number') && v.length) : []);
                    return newValue.shift();
                }
                return value;
            },
            floatToMoneyBrasil: function (value = null, decimal = 2) {
                let self = this;
                let newValueReal = null;
                let newValueCases = null;
                let firstValueMatch = null;
                let regexReal = /^-?(\d+\.\d{3,3}\,\d{1,2})$/g;
                let regexMatchs = /^-?((\d+\.\d+)|(\d+\,\d+)|(\d+))$/g;
                if (self.isNotObject(value, false) && self.getLength(value)) {
                    newValueReal = self.getToString(value).replace(/[^0-9\.\,\-]/g, '');
                    newValueReal = self.getToString(newValueReal).match(regexReal);
                    newValueReal = (newValueReal ? newValueReal.filter((v, i) => (typeof i === 'number') && v.length) : []);
                    if (self.getLength(newValueReal) > 0) {
                        firstValueMatch = newValueReal.shift();
                        firstValueMatch = firstValueMatch.replace('.', '');
                        firstValueMatch = firstValueMatch.replace(',', '.');
                        return self.numberFormat(firstValueMatch, decimal, ',', '.');
                    }
                    newValueCases = self.getToString(value).replace(/[^0-9\.\,\-]/g, '');
                    newValueCases = self.getToString(newValueCases).match(regexMatchs);
                    newValueCases = (newValueCases ? newValueCases.filter((v, i) => (typeof i === 'number') && v.length) : []);
                    if (self.getLength(newValueCases) > 0) {
                        firstValueMatch = newValueCases.shift();
                        firstValueMatch = firstValueMatch.replace(',', '.');
                        return self.numberFormat(firstValueMatch, decimal, ',', '.');
                    }
                }
                return null;
            }
        };
        return help;
    }
}
