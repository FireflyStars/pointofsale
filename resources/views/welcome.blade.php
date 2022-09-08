<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
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
    <script defer src="https://maps.googleapis.com/maps/api/js?&libraries=geometry,places&key= AIzaSyADM1OoIDe-K_AxYXWwGi8Lu41wMeYhLg0"></script>
    <script src="{{ mix('js/app.js') }}"></script>
</html>
