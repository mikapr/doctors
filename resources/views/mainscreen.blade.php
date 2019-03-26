<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">
        <title> Запись к врачу</title>

        <!-- Bootstrap core CSS -->
        <link href="{{asset("/css/bootstrap.min.css")}}" rel="stylesheet">

        @yield('styles')
    </head>

    <body>

        @yield('content')

        @yield('scripts')

        <script src="{{ asset('/js/jquery-3.3.1.min.js') }}"></script>
        <script src="{{ asset('/js/bootstrap.min.js') }}"></script>
    </body>
</html>
