
export default new class Collection {

    constructor() {
        let self = this;
        self.submit();
    }

    submit() {
        $('[action-link]').on('click', function () {
            let form = $(this).parents('form');
            let paymentType = form.find('[id="payment_type"]');
            paymentType.attr('value', $(this).attr('payment-type'))
            form.attr('action', $(this).attr('action-link')).submit();
        });
    }
}
