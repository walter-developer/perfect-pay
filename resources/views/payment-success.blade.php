@extends('app-template')
@section('view')
Cobrança concluída
@endsection
@section('content')
<div class="container">
    <div class="row">
        @if (($payment['charge_type'] ?? 0) == 1 )
        <div class="col-12">
            <div class="alert alert-success text-center" role="alert">
                Cobrança realizada com sucesso
            </div>
        </div>
        <div class="col-12 d-flex justify-content-center m-2">
            <a href="{{ $payment['bankSlipUrl'] }}" target="_blank" class="btn btn-success col-8">Visualizar boleto para
                pagamento</a>
        </div>
        <div class="col-12 d-flex justify-content-center m-2">
            <a href="{{ route('view.payment') }}" class="btn btn-primary col-8">Lançar nova cobrança</a>
        </div>
        @endif

        @if (($payment['charge_type'] ?? 0) == 3 )
        <div class="col-12">
            <div class="alert alert-success text-center" role="alert">
                Cobrança realizada com sucesso
            </div>
        </div>
        <div class="col-12 row">
            <div class="col-12">
                <label for="pix" class="form-label">
                    Chave Pix Copia e Cola
                </label>
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <button id="copy-pix" class="btn btn-outline-secondary" type="button">Copiar <i
                                class="bi bi-subtract"></i>
                        </button>
                    </div>
                    <input type="text" class="form-control" name="pix" id="pix"
                        value="{{ $payment['pix']['payload'] ?? null }}">
                </div>
            </div>
            <div class="col-12 d-block justyfy-content-center">
                <img class="rounded mx-auto d-block"
                    src="data:image/png;base64,{{ $payment['pix']['encodedImage'] ?? null }}" width="300" height="300">
            </div>
            <div class="col-12 d-flex justify-content-center  m-2">
                <a href="{{ route('view.payment') }}" class="btn btn-primary col-8">Lançar nova cobrança</a>
            </div>
        </div>
        @endif

        @if (($payment['charge_type'] ?? 0) == 2 )
        <div class="col-12">
            @if (($payment['charge_status'] ?? 0) == 4 )
            <div class="alert alert-success text-center" role="alert">
                Pagamento via cartão realizado com sucesso
            </div>
            @endif
            @if (($payment['charge_status'] ?? 0) <> 4 )
                <div class="alert alert-error text-center" role="alert">
                    Falha ao realizar o pagamento via cartão de crédito, tente uma nova cobrança ou entre em contato com
                    a equipe de TI.
                </div>
                @endif
        </div>
        <div class="col-12 row">
            <div class="col-12 d-flex justify-content-center m-2">
                <a href="{{ route('view.payment') }}"
                    class="btn {{ (($payment['charge_status'] ?? 0) == 4 ) ? 'btn-success' : btn-danger }} col-8">
                    Voltar para tela de cadastro de cobrança
                </a>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
@section('include-js')
<script type="module" src="{{ asset('static/js/web/payment-success.min.js') }}">
</script>
@endsection