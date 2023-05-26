<!DOCTYPE html>
<html lang="{{ config('app.configuration.lang', str_replace('_', '-', app()->getLocale())) }}">

<head>
    <meta charset="{{ config('app.configuration.charset') }}">
    <title>{{ config('app.name') }} - @yield('view')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="content-type" content="text/html; charset={{ config('app.configuration.charset') }}">
    <meta name="viewport" content="width=device-width, height=device-height, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link rel="stylesheet" type="text/css"
        href="{{ asset('static/plugins/bootstrap-5.0.2/dist/css/bootstrap.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('static/css/web/template.css') }}">
    @yield('include-css')
</head>

<body>
    <main>
        <header class="p-3 mb-3 border-bottom">
            <div class="container">
                <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">
                    <label>Perfect Pay</label>
                </div>
            </div>
        </header>
        <section id="alert" class="container container-alert" {{ !$errors->any() ? 'hidden' : ''}}>
            @if($errors->any())
            <div class="container p-5">
                @foreach($errors->getMessages() as $errors)
                <div class="alert alert-danger m-3 text-center" role="alert">
                    {{ $errors[0] ?? 'Um erro ocorreu, tente novamente!'}}
                </div>
                @endforeach
            </div>
            @endif
        </section>
        <section id="content" class="container">
            @yield('content')
        </section>
    </main>
    <footer>
        <script type="text/javascript" src="{{ asset('static/plugins/jquery-3.6.4/jquery.min.js') }}"></script>
        <script type="text/javascript" src="{{ asset('static/plugins/bootstrap-5.0.2/dist/js/bootstrap.min.js') }}">
            <script type="text/javascript" src="{{ asset('static/plugins/bootstrap-5.0.2/dist/js/bootstrap.bundle.min.js') }}">
        </script>
        @yield('include-js')
    </footer>
</body>

</html>