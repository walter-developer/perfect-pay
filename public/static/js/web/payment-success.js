
export default new class PaymentSuccess {

    constructor() {
        let self = this;
        self.copyPix();
    }

    copyPix() {
        let self = this;
        $('#copy-pix').on('click', function () {
            self.copyToClipboard('pix');
        });
    }

    copyToClipboard(elementId) {
        let text = document.getElementById(elementId).value;
        let aux = document.createElement("input");
        aux.setAttribute("value", text);
        aux.select();
        aux.setSelectionRange(0, 99999);
        if (!navigator.clipboard) {
            document.body.appendChild(aux);
            aux.select();
            document.execCommand("copy");
            document.body.removeChild(aux);
            return true;
        }
        navigator.clipboard.writeText(text);
        return true;
    }
}
