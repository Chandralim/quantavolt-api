<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">

        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" type="image/png" href="/img/icon.png" />
        <meta name="csrf-token" content="{{csrf_token()}}">

        <title>Palm Oil X-Tion</title>

        <!-- Fonts -->
        {{-- <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet"> --}}
        <link href="/lib/fontawesome-free-5.10.1-web/css/all.min.css" rel="stylesheet" defer>
        <link href="/css/app.css" rel="stylesheet" defer>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tiny-slider/2.9.3/tiny-slider.css">
    </head>
    <body>
        <div id="app" class="w-100 h-100"> </div>
        <script src="{{asset('js/app.min.js')}}?v=6"> </script>
    </body>
</html>
