<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    @vite(['resources/scss/app.scss', 'resources/js/app.js'])
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <title>Layout base | @yield('titlePage')</title>

</head>
<body>

    @include('partials.navbar')

    <div class="d-flex">
        @include('partials.sidebar')

        <div class="content w-100">
            @yield('content')
        </div>
    </div>

    @include('partials.footer')

</body>
</html>
