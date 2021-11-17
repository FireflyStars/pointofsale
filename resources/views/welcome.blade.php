<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon"
              type="image/png"
              href="/images/favicon.png">

        <title>LCDT &copy;</title>
        <link  rel="stylesheet" href="{{ mix('css/app.css') }}">
    </head>
    <body id="app"></body>
    <script src="{{ mix('js/app.js') }}"></script>
</html>
