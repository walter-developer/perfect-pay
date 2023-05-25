import CollectionFormatter from "./CollectionFormatter.js";

export default class Collection {
    items = {};
    constructor(data = {}) {
        let self = this;
        self.items = data;
        return self.refresh();
    }
    count(attribute = null) {
        let self = this;
        return self
            .length(attribute);
    }
    isset(attribute = null) {
        let self = this;
        return self
            .exists(attribute);
    }
    empty(attribute = null) {
        let self = this;
        return self
            .isEmpty(attribute);
    }
    notEmpty(attribute = null) {
        let self = this;
        return self
            .isNotEmpty(attribute);
    }
    isNotEmpty(attribute = null) {
        let self = this;
        return self.isEmpty(attribute) === false;
    }
    isNotNull(attribute = null) {
        let self = this;
        return self.isNull(attribute) === false;
    }
    forIn(callback, reverse = true) {
        let self = this;
        return self
            .forEach(callback, reverse);
    }
    forInRecurssive(callback, reverse = true) {
        let self = this;
        return self
            .forEachRecurssive(callback, reverse);
    }
    forInCollections(callback, reverse = true) {
        let self = this;
        return self
            .forEachInCollections(callback, reverse);
    }
    forInCollectionsRecurssive(callback, reverse = true) {
        let self = this;
        return self
            .forEachInCollectionsRecurssive(callback, reverse);
    }
    first(attribute = null) {
        let self = this;
        let help = self.help();
        if (help.isObject(self.items, false) && help.isNotEmpty(attribute)) {
            return help.getFirst(self.get(attribute));
        }
        if (help.isNotObject(self.items, false) && help.isNotEmpty(attribute)) {
            return false;
        }
        return help.getFirst(self.items);
    }
    length(attribute = null) {
        let self = this;
        let help = self.help();
        if (help.isObject(self.items, false) && help.isNotEmpty(attribute)) {
            return help.getLength(self.get(attribute));
        }
        if (help.isNotObject(self.items, false) && help.isNotEmpty(attribute)) {
            return false;
        }
        return help.getLength(self.items);
    }
    isNull(attribute = null) {
        let self = this;
        let help = self.help();
        if (help.isObject(self.items, false) && help.isNotEmpty(attribute)) {
            return help.isNull(self.get(attribute));
        }
        if (help.isNull(self.items) && help.isEmpty(attribute)) {
            return true;
        }
        return false;
    }
    isEmpty(attribute = null) {
        let self = this;
        let help = self.help();
        if (help.isObject(self.items, false) && help.isNotEmpty(attribute)) {
            return help.isEmpty(self.get(attribute));
        }
        if (help.isEmpty(self.items) && help.isEmpty(attribute)) {
            return true;
        }
        return false;
    }
    isObject(attribute = null, implict = true) {
        let self = this;
        let help = self.help();
        if (help.isObject(self.items, false) && help.isNotEmpty(attribute)) {
            return help.isObject(self.get(attribute), implict);
        }
        if (help.isObject(self.items, implict) && help.isEmpty(attribute)) {
            return true;
        }
        return false;
    }
    isArray(attribute = null, implict = true) {
        let self = this;
        let help = self.help();
        if (help.isObject(self.items, false) && help.isNotEmpty(attribute)) {
            return help.isArray(self.get(attribute), implict);
        }
        if (help.isArray(self.items, implict) && help.isEmpty(attribute)) {
            return true;
        }
        return false;
    }
    isCollection(attribute = null) {
        let self = this;
        let help = self.help();
        if (help.isObject(self.items, false) && help.isNotEmpty(attribute)) {
            return help.isInstanceOf(self.get(attribute), Collection);
        }
        if (help.isInstanceOf(self.get(attribute), Collection) && help.isEmpty(attribute)) {
            return true;
        }
        return false;
    }
    print() {
        let self = this;
        console.log(self.items);
    }
    console() {
        let self = this;
        console.log(self.items, self);
    }
    formatter(attribute, recurssive = true) {
        let self = this;
        let help = self.help();
        let keys = help.getSplitNotEmpty(attribute);
        let nivel = self.nivel(attribute);
        let lastedArray = keys.slice(-1);
        let lasted = lastedArray.shift();
        return new CollectionFormatter(nivel, lasted, recurssive);
    }
    all() {
        let self = this;
        let help = self.help();
        let items = self.items;
        if (help.isObject(self.items, false)) {
            for (let prop in items) {
                let value = items[prop];
                let isObject = (value && help.isObject(value, false));
                let isObjecCollection = (value && help.isInstanceOf(value, Collection));
                items[prop] = isObject && isObjecCollection ? value.all() : value;
            }
        }
        return items;
    }
    item(attribute = null) {
        let self = this;
        let help = self.help();
        if (help.isObject(self.items, false) && help.isNotEmpty(attribute)) {
            if (help.isAttribute(self.items, firstKey)) {
                return self.items[attribute];
            }
            return null;
        }
        if (help.isNotEmpty(self.items, false) && help.isNotEmpty(attribute)) {
            return self.items;
        }
        return null;
    }
    toArray(attribute = null, valueDefault = null) {
        let self = this;
        let help = self.help();
        if (help.isNotEmpty(self.items) && help.isEmpty(attribute) && help.isEmpty(valueDefault)) {
            self.items = help.toArray(self.items);
            return self.refresh();
        }
        if (help.isObject(self.items, false) && help.isNotEmpty(attribute) && self.exists(attribute)) {
            self.set(attribute, help.toArray(self.get(attribute, valueDefault)));
            return self;
        }
        return self;
    }
    toObject(attribute = null, valueDefault = null) {
        let self = this;
        let help = self.help();
        if (help.isNotEmpty(self.items) && help.isEmpty(attribute) && help.isEmpty(valueDefault)) {
            self.items = help.toObject(self.items);
        }
        if (help.isObject(self.items, false) && help.isNotEmpty(attribute) && self.exists(attribute)) {
            self.set(attribute, help.toObject(self.get(attribute, valueDefault)));
            return self;
        }
        return self;
    }
    copy(attribute = null, source = null, valueDefault = null) {
        let self = this;
        let help = self.help();
        if (help.isObject(self.items, false)
            && help.isNotEmpty(attribute)
            && help.isNotEmpty(source)) {
            self.set(source, self.get(attribute, valueDefault));
            return self;
        }
        return self;
    }
    default(attribute = null, valueDefault = null) {
        let self = this;
        let help = self.help();
        if (help.isObject(self.items, false) && help.isNotEmpty(attribute)) {
            self.set(attribute, self.get(attribute, valueDefault));
            return self;
        }
        return self;
    }
    sort(attribute = null, order = 'asc', recurssive = false) {
        let self = this;
        let help = self.help();
        if (help.isObject(self.items, false) && help.isNotEmpty(attribute)) {
            self.set(attribute, help.sort(self.get(attribute), order, recurssive));
            return self;
        }
        if (help.isObject(self.items, false) && help.isEmpty(attribute) && help.isNotEmpty(order)) {
            self.items = help.sort(self.items, order, recurssive);
            return self;
        }
        return self;
    }
    ksort(attribute = null, order = 'asc', recurssive = false) {
        let self = this;
        let help = self.help();
        if (help.isObject(self.items, false) && help.isNotEmpty(attribute)) {
            self.set(attribute, help.ksort(self.get(attribute), order, recurssive));
            return self;
        }
        if (help.isObject(self.items, false) && help.isEmpty(attribute) && help.isNotEmpty(order)) {
            self.items = help.ksort(self.items, order, recurssive);
            return self;
        }
        return self;
    }
    move(attribute = null, source = null) {
        let self = this;
        let help = self.help();
        if (help.isObject(self.items, false) && help.isNotEmpty(attribute) && help.isNotEmpty(source)) {
            self.copy(attribute, source).delete(attribute);
        }
        return self;
    }
    refresh() {
        let self = this;
        let help = self.help();
        let items = self.items;
        if (help.isObject(self.items, false)) {
            for (let prop in items) {
                let value = items[prop];
                let isObject = (value && help.isObject(value, false));
                if (isObject && help.isInstanceOf(value, Collection)) {
                    value.refresh();
                }
                if (isObject && help.isNotInstanceOf(value, Collection)) {
                    value = new Collection(value);
                }
                items[prop] = value;
            }
            self.items = items;
        }
        return self;
    }
    exists(attribute = null) {
        let self = this;
        let help = self.help();
        let keys = help.getSplitNotEmpty(attribute);
        let firstKey = keys.shift();
        let firstValue = null;
        let isCollection = false;
        let isRecurssive = false;
        if (help.isNotObject(self.items, false) && help.isNotEmpty(attribute)) {
            return self.items == attribute;
        }
        if (help.isObject(self.items, false) && help.isAttribute(self.items, firstKey)) {
            firstValue = self.items[firstKey];
            isRecurssive = (firstValue && (help.getLength(keys) > 0));
            isCollection = (firstValue && help.isInstanceOf(firstValue, Collection));
            if (isRecurssive && isCollection) {
                return firstValue.exists(keys.join('.'));
            }
            return true;
        }
        return false;
    }
    get(attribute = null, valueDefault = null) {
        let self = this;
        let help = self.help();
        let keys = help.getSplitNotEmpty(attribute);
        let firstKey = keys.shift();
        let firstValue = null;
        let isCollection = false;
        let isRecurssive = false;
        if (help.isNull(attribute) && help.isNull(valueDefault)) {
            return self.items;
        }
        if (help.isObject(self.items, false) && help.isAttribute(self.items, firstKey)) {
            firstValue = self.items[firstKey];
            isRecurssive = (firstValue && (help.getLength(keys) > 0));
            isCollection = (firstValue && help.isInstanceOf(firstValue, Collection));
            if (isRecurssive && isCollection) {
                return firstValue.get(keys.join('.'), valueDefault);
            }
            if (help.isNotNull(firstValue)) {
                return firstValue;
            }
        }
        return valueDefault;
    }
    delete(attribute = null) {
        let self = this;
        let help = self.help();
        let keys = help.getSplitNotEmpty(attribute);
        let firstKey = keys.shift();
        let firstValue = null;
        let isCollection = false;
        let isRecurssive = false;
        if (help.isNull(attribute)) {
            self.items = null;
            return self.items;
        }
        if (help.isObject(self.items, false) && help.isAttribute(self.items, firstKey)) {
            firstValue = self.items[firstKey];
            isRecurssive = (firstValue && (help.getLength(keys) > 0));
            isCollection = (firstValue && help.isInstanceOf(firstValue, Collection));
            if (isRecurssive && isCollection) {
                return firstValue.delete(keys.join('.'));
            }
            delete self.items[firstKey];
            self.refresh();
        }
        return self;
    }
    set(attribute = null, value = null) {
        let self = this;
        let help = self.help();
        let keys = help.getSplitNotEmpty(attribute);
        let firstKey = keys.shift();
        let firstValue = self.get(firstKey);
        if (help.isInstanceOf(firstValue, Collection) && help.getLength(keys) > 0) {
            return self.set(firstKey, firstValue.set(keys.join('.'), value));
        }
        if (help.isNotInstanceOf(firstValue, Collection) && help.getLength(keys) > 0) {
            return self.set(firstKey, (new Collection()).set(keys.join('.'), value));
        }
        if ((help.getLength(firstKey) == 0) && (help.getLength(keys) == 0)) {
            self.items = value;
            return self;
        }
        if (help.getLength(firstKey) && help.isObject(self.items, false) && help.getLength(keys) == 0) {
            self.items[firstKey] = value;
            return self;
        }
        if (help.getLength(firstKey) && help.isNotObject(self.items, false) && help.getLength(keys) == 0) {
            self.items = {};
            self.items[firstKey] = value;
            return self;
        }
        return self;
    }
    nivel(attribute = null) {
        let self = this;
        let help = self.help();
        let keys = help.getSplitNotEmpty(attribute);
        let firstKey = keys.shift();
        let firstValue = null;
        let isCollection = false;
        let isRecurssive = false;
        if (help.isNull(attribute) || help.isNotObject(self.items, false)) {
            return self;
        }
        if (help.isObject(self.items, false) && help.isAttribute(self.items, firstKey)) {
            firstValue = self.items[firstKey];
            isRecurssive = (firstValue && (help.getLength(keys) > 0));
            isCollection = (firstValue && help.isInstanceOf(firstValue, Collection));
            if (isRecurssive && isCollection) {
                return firstValue.nivel(keys.join('.'));
            }
            if (isCollection && !isRecurssive) {
                return firstValue;
            }
        }
        return self;
    }
    forEach(callback, reverse = true) {
        let self = this;
        let help = self.help();
        let newValue = null;
        let items = help.isObject(self.items, false) ? self.items : [self.items];
        for (let prop in items) {
            newValue = items[prop];
            if (reverse) {
                newValue = callback(items[prop], prop, self);
            } else {
                newValue = callback(prop, items[prop], self);
            }
            if (newValue && help.isInstanceOf(newValue, Collection)) {
                newValue.refresh();
            }
            if (help.isTypeOf(newValue, 'undefined')) {
                newValue = items[prop];
            }
            newValue = items[prop];
            items[prop] = newValue;
        }
        self.items = items;
        return self;
    }
    forEachRecurssive(callback, reverse = true) {
        let self = this;
        let help = self.help();
        let newValue = null;
        let items = help.isObject(self.items, false) ? self.items : [self.items];
        for (let prop in items) {
            if (reverse) {
                newValue = callback(items[prop], prop, self);
            } else {
                newValue = callback(prop, items[prop], self);
            }
            if (newValue && help.isInstanceOf(newValue, Collection)) {
                newValue.refresh();
            }
            if (items[prop] && help.isInstanceOf(items[prop], Collection)) {
                items[prop]
                    .forEachInCollectionsRecurssive(callback, reverse);
            }
            if (help.isTypeOf(newValue, 'undefined')) {
                newValue = items[prop];
            }
            items[prop] = newValue;
        }
        self.items = items;
        return self;
    }
    forEachInCollections(callback, reverse = true) {
        let self = this;
        let help = self.help();
        let newValue = null;
        let items = help.isObject(self.items, false) ? self.items : [self.items];
        for (let prop in items) {
            if (items[prop] && help.isInstanceOf(items[prop], Collection)) {
                if (reverse) {
                    newValue = callback(items[prop], prop, self);
                } else {
                    newValue = callback(prop, items[prop], self)
                }
                if (newValue && help.isInstanceOf(newValue, Collection)) {
                    newValue.refresh();
                }
                if (help.isTypeOf(newValue, 'undefined')) {
                    newValue = items[prop];
                }
                items[prop] = newValue;
            }
        }
        self.items = items;
        return self;
    }
    forEachInCollectionsRecurssive(callback, reverse = true) {
        let self = this;
        let help = self.help();
        let newValue = null;
        let items = help.isObject(self.items, false) ? self.items : [self.items];
        for (let prop in items) {
            if (items[prop] && help.isInstanceOf(items[prop], Collection)) {
                if (reverse) {
                    newValue = callback(items[prop], prop, self);
                } else {
                    newValue = callback(prop, items[prop], self)
                }
                if (newValue && help.isInstanceOf(newValue, Collection)) {
                    newValue.refresh();
                }
                if (items[prop] && help.isInstanceOf(items[prop], Collection)) {
                    items[prop]
                        .forEachInCollectionsRecurssive(callback, reverse);
                }
                if (help.isTypeOf(newValue, 'undefined')) {
                    newValue = items[prop];
                }
                items[prop] = newValue;
            }
        }
        self.items = items;
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
            }
        };
        return help;
    }
}
