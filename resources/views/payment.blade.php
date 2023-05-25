@extends('app-template')
@section('view')
Pagamento
@endsection
@section('content')

<div class="container mb-4">
    <form method="POST" action="{{ route('payment.ticket') }}">
        <section>
            <div class="row">
                <div class="card p-0 mb-4">
                    <div class="card-header py-3">
                        <h5 class="mb-0">Cliente</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="inputEmail4" class="form-label">Nome completo</label>
                                <input type="text" class="form-control" id="inputEmail4" placeholder="Nome e sobrenome">
                            </div>
                            <div class="col-md-6">
                                <label for="inputEmail4" class="form-label">Documento</label>
                                <input type="text" class="form-control" id="inputEmail4" placeholder="000.000.000-00">
                            </div>
                            <div class="col-md-6">
                                <label for="inputEmail4" class="form-label">Email</label>
                                <input type="email" class="form-control" id="inputEmail4" placeholder="teste@teste.com">
                            </div>
                            <div class="col-md-6">
                                <label for="inputAddress" class="form-label">Telefone</label>
                                <input type="text" class="form-control" id="inputAddress" placeholder="(00)0000-0000">
                            </div>
                            <div class="col-md-6">
                                <label for="inputAddress" class="form-label">Celular</label>
                                <input type="text" class="form-control" id="inputAddress"
                                    placeholder="(00) 0 0000-0000">
                            </div>
                            <div class="col-md-6">
                                <label for="inputAddress" class="form-label">Cep</label>
                                <input type="text" class="form-control" id="inputAddress" placeholder="00000-000">
                            </div>
                            <div class="col-6">
                                <label for="inputAddress" class="form-label">End. Numero</label>
                                <input type="text" class="form-control" id="inputAddress" placeholder="00000">
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
                                <label for="inputEmail4" class="form-label">Valor da cobrança</label>
                                <input type="text" class="form-control" id="inputEmail4" placeholder="R$ Valor">
                            </div>
                        </div>
                    </div>
                    <div class="col-12 p-3">
                        <label for="inputEmail4" class="form-label">Formas de pagamento</label>

                        <div class="accordion" id="paymentsForms">
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingOne">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                        Pagamento via boleto
                                    </button>
                                </h2>
                                <div id="collapseOne" class="accordion-collapse collapse show"
                                    aria-labelledby="headingOne" data-bs-parent="#paymentsForms">
                                    <div class="accordion-body">
                                        @csrf
                                        <button type="button" class="btn btn-primary"
                                            action-link="{{ route('payment.ticket') }}">Gerar Boleto</button>
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingTwo">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                        Pagamento Via PIX
                                    </button>
                                </h2>
                                <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo"
                                    data-bs-parent="#paymentsForms">
                                    <div class="accordion-body">
                                        <form method="POST" action="{{ route('payment.pix') }}">
                                            @csrf
                                            <button type="button" class="btn btn-primary"
                                                action-link="{{ route('payment.pix') }}">Gerar Qrcode Pix</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingThree">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapseThree" aria-expanded="false"
                                        aria-controls="collapseThree">
                                        Pagamento via Cartão
                                    </button>
                                </h2>
                                <div id="collapseThree" class="accordion-collapse collapse"
                                    aria-labelledby="headingThree" data-bs-parent="#paymentsForms">
                                    <div class="accordion-body">
                                        <section id="payment">
                                            <div class="row">
                                                <div class="col-12 mb-4">
                                                    <div class="card mb-4">
                                                        <div class="card-body pt-5 pb-5">
                                                            <div class="row g-3">
                                                                <div class="col-md-6">
                                                                    <label for="inputEmail4" class="form-label">Nome do
                                                                        proprietário do cartão no cartão</label>
                                                                    <input type="text" class="form-control"
                                                                        id="inputEmail4" placeholder="Nome no cartão">
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <label for="inputEmail4" class="form-label">Numero
                                                                        do cartão</label>
                                                                    <input type="email" class="form-control"
                                                                        id="inputEmail4" placeholder="00000000000">
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <label for="inputAddress" class="form-label">Data
                                                                        expiração</label>
                                                                    <input type="text" class="form-control"
                                                                        id="inputAddress" placeholder="00/00">
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <label for="inputAddress" class="form-label">CCV do
                                                                        cartão</label>
                                                                    <input type="text" class="form-control"
                                                                        id="inputAddress" placeholder="ccv">
                                                                </div>
                                                                <div class="col-12">
                                                                    <button type="button" class="btn btn-primary"
                                                                        action-link="{{ route('payment.card') }}">Pagar
                                                                        com cartão</button>
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