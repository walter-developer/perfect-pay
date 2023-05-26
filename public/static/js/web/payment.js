
export default new class Payment {

    constructor() {
        let self = this;
        self.format();
        self.submit();
    }

    format() {
        let self = this;
        self.formatDocument(document.getElementById('document'));
        self.formatTelephone(document.getElementById('phone'));
        self.formatTelephone(document.getElementById('cell_phone'));
        self.formatMoney(document.getElementById('value'));
        self.formatCep(document.getElementById('cep'));
        self.formatCardExpiration(document.getElementById('payment_card_expiration'));
    }

    submit() {
        $('[action-link]').on('click', function () {
            let form = $(this).parents('form');
            let paymentType = form.find('[id="payment_type"]');
            paymentType.attr('value', $(this).attr('payment-type'))
            form.attr('action', $(this).attr('action-link')).submit();
        });
    }

    formatCardExpiration(element) {
        let matchs = {
            "^(\\d{1,2})$": "$1",
            "^(\\d{2})(\\d{1,2})$": "$1\/$2",
        };
        $(element)
            .on('focusout', function (e) {
                let value = e.currentTarget.value.toString().replace(/\D/g, '').substr(0, 4);
                if (([4].includes(value.length) === false)) {
                    e.currentTarget.value = null;
                }
            })
            .on('keyup change', function (e) {
                let value = e.currentTarget.value.toString().replace(/\D/g, '').substr(0, 4);
                e.currentTarget.value = e.currentTarget.value.toString().replace(/\D|\//g, '').substr(0, 4);
                for (const match in matchs) {
                    let regex = new RegExp(match);
                    let replace = matchs[match];
                    if (value && value.match(regex)) {
                        e.currentTarget.value = value.replace(regex, replace);
                        return true;
                    }
                }
            });
    }

    formatCep(element) {
        let matchs = {
            "^(\\d{1,5})$": "$1",
            "^(\\d{5})(\\d{1,3})$": "$1\-$2",
        };
        $(element)
            .on('focusout', function (e) {
                let value = e.currentTarget.value.toString().replace(/\D/g, '').substr(0, 8);
                if (([8].includes(value.length) === false)) {
                    e.currentTarget.value = null;
                }
            })
            .on('keyup change', function (e) {
                let value = e.currentTarget.value.toString().replace(/\D/g, '').substr(0, 8);
                for (const match in matchs) {
                    let regex = new RegExp(match);
                    let replace = matchs[match];
                    if (value && value.match(regex)) {
                        e.currentTarget.value = value.replace(regex, replace);
                        return true;
                    }
                }
            });
    }

    formatMoney(element) {
        let matchs = {
            "^(\\d{2})$": "$1",
            "^(\\d{1,3})(\\d{2})$": "$1\,$2",
            "^(\\d+)(\\d{3})(\\d{2})$": "$1\.$2\,$3",
        };
        $(element)
            .on('keyup change', function (e) {
                let value = e.currentTarget.value.toString().replace(/\D/g, '');
                e.currentTarget.value = e.currentTarget.value.toString().replace(/\D|\.|\,/g, '');
                for (const match in matchs) {
                    let regex = new RegExp(match);
                    let replace = matchs[match];
                    if (value && value.match(regex)) {
                        e.currentTarget.value = value.replace(regex, replace);
                        return true;
                    }
                }
            });
    }

    formatTelephone(element) {
        let matchs = {
            "^(\\d{2})$": "\($1\)",
            "^(\\d{2})(\\d{1,4})$": "\($1\) $2",
            "^(\\d{2})(\\d{4})$": "\($1\) $2\s",
            "^(\\d{2})(\\d{4})(\\d{1,4})$": "\($1\) $2-$3",
            "^(\\d{2})(\\d{1})(\\d{4})(\\d{1,4})$": "\($1\) $2 $3-$4",
        };
        $(element)
            .on('focusout', function (e) {
                let value = e.currentTarget.value.toString().replace(/\D/g, '').substr(0, 11);
                if (([10, 11].includes(value.length) === false)) {
                    e.currentTarget.value = null;
                }
            })
            .on('keyup change', function (e) {
                let value = e.currentTarget.value.toString().replace(/\D/g, '').substr(0, 11);
                e.currentTarget.value = e.currentTarget.value.toString().replace(/\D|\(|\)|\-/g, '');
                if ([8, 46].includes(e.keyCode) && ([10, 11].includes(value.length) === false)) return;
                for (const match in matchs) {
                    let regex = new RegExp(match);
                    let replace = matchs[match];
                    if (value && value.match(regex)) {
                        e.currentTarget.value = value.replace(regex, replace);
                        return true;
                    }
                }
            });
        return this;
    }

    formatDocument(element) {
        let matchs = {
            "^(\\d{3})$": "$1.",
            "^(\\d{3})(\\d{1,2})$": "$1.$2",
            "^(\\d{3})(\\d{3})$": "$1.$2.",
            "^(\\d{3})(\\d{3})(\\d{1,2})$": "$1.$2.$3",
            "^(\\d{3})(\\d{3})(\\d{3})$": "$1.$2.$3-",
            "^(\\d{3})(\\d{3})(\\d{3})(\\d{1,2})$": "$1.$2.$3-$4",
            "^(\\d{2})(\\d{3})(\\d{3})(\\d{4})$": "$1.$2.$3/$4-",
            "^(\\d{2})(\\d{3})(\\d{3})(\\d{4})(\\d{1,2})$": "$1.$2.$3/$4-$5",
        };
        $(element)
            .on('focusout', function (e) {
                let value = e.currentTarget.value.toString().replace(/\D/g, '').substr(0, 14);
                if (([11, 14].includes(value.length) === false)) {
                    e.currentTarget.value = null;
                }
            })
            .on('keyup change', function (e) {
                let value = e.currentTarget.value.toString().replace(/\D/g, '').substr(0, 14);
                e.currentTarget.value = e.currentTarget.value.toString().replace(/\D|\/|\.|\-/g, '');
                if ([8, 46].includes(e.keyCode) && ([11, 14].includes(value.length) === false)) return;
                for (const match in matchs) {
                    let regex = new RegExp(match);
                    let replace = matchs[match];
                    if (value && value.match(regex)) {
                        e.currentTarget.value = value.replace(regex, replace);
                        return true;
                    }
                }
            });
        return this;
    }
}
