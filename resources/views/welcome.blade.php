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
    <body id="app">
        
    </body>
    @if(session()->has('outlookSyncResult') && session('outlookSyncResult'))
    <script>
        window.outlookSynced = true;
    </script>
    @endif
    @if(session()->has('outlookSyncResult') && !session('outlookSyncResult'))
    <script>
        window.outlookSynced = false;
        window.outlookSyncError = '{{ session("outlookSyncError") }}';
        window.outlookSyncErrorDetail = '{{ session("outlookSyncErrorDetail") }}';
    </script>
    @endif
    <script defer src="https://maps.googleapis.com/maps/api/js?&libraries=geometry,places&key=AIzaSyDPDKvButmMnw0VRFaY5mZIwDaEl5N9Xow"></script>
    <script src="{{ mix('js/app.js') }}"></script>
</html>
