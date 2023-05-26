@extends('app-template')
@section('view')
Pagamento
@endsection
@section('content')
<div class="container mb-4">
    <form method="POST" action="{{ route('payment.ticket') }}">
        @csrf
        <input type="hidden" id="payment_type" name="payment_type" value="{{ @old('payment_type', 1) }}">
        <section>
            <div class="row">
                <div class="card p-0 mb-4">
                    <div class="card-header py-3">
                        <h5 class="mb-0">Cliente</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Nome completo</label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ @old('name') }}"
                                    placeholder="Nome e sobrenome">
                            </div>
                            <div class="col-md-6">
                                <label for="document" class="form-label">Documento</label>
                                <input type="text" class="form-control" id="document" name="document"
                                    value="{{ @old('document') }}" placeholder="000.000.000-00">
                            </div>
                            <div class="col-md-6">
                                <label for="birth_date" class="form-label">Data Nascimento</label>
                                <input type="date" class="form-control" id="birth_date" name="birth_date"
                                    value="{{ @old('birth_date') }}" placeholder="01/01/2000">
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email"
                                    value="{{ @old('email') }}" placeholder="teste@teste.com">
                            </div>
                            <div class="col-md-6">
                                <label for="phone" class="form-label">Telefone</label>
                                <input type="text" class="form-control" id="phone" name="phone"
                                    value="{{ @old('phone') }}" placeholder="(00)0000-0000">
                            </div>
                            <div class="col-md-6">
                                <label for="cell_phone" class="form-label">Celular</label>
                                <input type="text" class="form-control" id="cell_phone" name="cell_phone"
                                    value="{{ @old('cell_phone') }}" placeholder="(00) 0 0000-0000">
                            </div>
                            <div class="col-md-6">
                                <label for="cep" class="form-label">Cep</label>
                                <input type="text" class="form-control" id="cep" name="cep" value="{{ @old('cep') }}"
                                    placeholder="00000-000">
                            </div>
                            <div class="col-6">
                                <label for="number" class="form-label">End. Numero</label>
                                <input type="text" class="form-control" id="number" name="number"
                                    value="{{ @old('number') }}" placeholder="00000">
                            </div>
                            <div class="col-6">
                                <label for="observation" class="form-label">End. Observação</label>
                                <input type="text" class="form-control" id="observation" name="observation"
                                    value="{{ @old('observation') }}" placeholder="observação para o endereço">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section>
            <div class="row">
                <div class="card p-0 mb-4">
                    <div class="card-header py-3">
                        <h5 class="mb-0">Cobrança</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="value" class="form-label">Valor da cobrança</label>
                                <input type="text" class="form-control" id="value" name="value"
                                    value="{{ @old('value') }}" placeholder="R$ Valor">
                            </div>
                            <div class="col-md-6">
                                <label for="due_date" class="form-label">Vencimento da cobrança</label>
                                <input type="date" class="form-control" id="due_date" name="due_date"
                                    value="{{ @old('due_date') }}" placeholder="01/01/2000">
                            </div>
                            <div class="col-md-6">
                                <label for="description" class="form-label">Descrição / Identificador da
                                    cobrança</label>
                                <input type="text" class="form-control" id="description" name="description"
                                    value="{{ @old('description') }}" placeholder="Descrição da cobrança">
                            </div>
                        </div>
                    </div>
                    <div class="col-12 p-3">
                        <label for="inputEmail4" class="form-label">Formas de pagamento</label>

                        <div class="accordion" id="paymentsForms">
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingOne">
                                    <button
                                        class="accordion-button {{ in_array(@old('payment_type', 1),[1]) ? '' : 'collapsed' }}"
                                        type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne"
                                        aria-expanded="{{ in_array(@old('payment_type', 1),[1]) ? 'true' : 'false' }}"
                                        aria-controls="collapseOne">
                                        Pagamento via boleto
                                    </button>
                                </h2>
                                <div id="collapseOne"
                                    class="accordion-collapse collapse {{ in_array(@old('payment_type', 1),[1]) ? 'show' : '' }}"
                                    aria-labelledby="headingOne" data-bs-parent="#paymentsForms">
                                    <div class="accordion-body">
                                        <button type="button" class="btn btn-primary"
                                            action-link="{{ route('payment.ticket') }}" payment-type="1">Gerar
                                            Boleto</button>
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingTwo">
                                    <button
                                        class="accordion-button {{ in_array(@old('payment_type', 1),[2]) ? '' : 'collapsed' }}"
                                        type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo"
                                        aria-expanded="{{ in_array(@old('payment_type', 1),[2]) ? 'true' : 'false' }}"
                                        aria-controls="collapseTwo">
                                        Pagamento Via PIX
                                    </button>
                                </h2>
                                <div id="collapseTwo"
                                    class="accordion-collapse collapse {{ in_array(@old('payment_type', 1),[2]) ? 'show' : '' }}"
                                    aria-labelledby="headingTwo" data-bs-parent="#paymentsForms">
                                    <div class="accordion-body">
                                        <button type="button" class="btn btn-primary"
                                            action-link="{{ route('payment.pix') }}" payment-type="2">Gerar Qrcode
                                            Pix</button>
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingThree">
                                    <button
                                        class="accordion-button {{ in_array(@old('payment_type', 1),[3]) ? '' : 'collapsed' }}"
                                        type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree"
                                        aria-expanded="{{ in_array(@old('payment_type', 1),[3]) ? 'true' : 'false' }}"
                                        aria-controls="collapseThree">
                                        Pagamento via Cartão
                                    </button>
                                </h2>
                                <div id="collapseThree"
                                    class="accordion-collapse collapse {{ in_array(@old('payment_type', 1),[3]) ? 'show' : '' }}"
                                    aria-labelledby="headingThree" data-bs-parent="#paymentsForms">
                                    <div class="accordion-body">
                                        <section id="payment">
                                            <div class="row">
                                                <div class="col-12 mb-4">
                                                    <div class="card mb-4">
                                                        <div class="card-body pt-5 pb-5">
                                                            <div class="row g-3">
                                                                <div class="col-md-6">
                                                                    <label for="payment_card_name" class="form-label">
                                                                        Nome descrito no cartão
                                                                    </label>
                                                                    <input type="text" class="form-control"
                                                                        id="payment_card_name"
                                                                        name="payment[card][name]"
                                                                        value="{{ @old('payment.card.name') }}"
                                                                        placeholder="Nome no cartão">
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <label for="payment_card_number" class="form-label">
                                                                        Numero do cartão
                                                                    </label>
                                                                    <input type="text" class="form-control"
                                                                        id="payment_card_number"
                                                                        name="payment[card][number]"
                                                                        value="{{ @old('payment.card.number') }}"
                                                                        placeholder="00000000000">
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <label for="payment_card_expiration"
                                                                        class="form-label">
                                                                        Data expiração
                                                                    </label>
                                                                    <input type="text" class="form-control"
                                                                        id="payment_card_expiration"
                                                                        name="payment[card][expiration]"
                                                                        value="{{ @old('payment.card.expiration') }}"
                                                                        placeholder="00/00">
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <label for="payment_card_ccv" class="form-label">
                                                                        CCV do cartão
                                                                    </label>
                                                                    <input type="text" class="form-control"
                                                                        id="payment_card_ccv" name="payment[card][ccv]"
                                                                        value="{{ @old('payment.card.ccv') }}"
                                                                        placeholder="ccv">
                                                                </div>
                                                                <div class="col-12">
                                                                    <button type="button" class="btn btn-primary"
                                                                        action-link="{{ route('payment.card') }}"
                                                                        payment-type="3">
                                                                        Pagar com cartão
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </section>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </form>
</div>
@endsection
@section('include-js')
<script type="module" src="{{ asset('static/js/web/payment.min.js') }}">
</script>