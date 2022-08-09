<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Product</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            width: 100% !important;
            height: 100% !important;
        }
        .container {
            width: 100% !important;
            height: 100% !important;
            overflow: auto;
        }
        .link {
            position: absolute;
            top: 53px;
            left: 52px;
            color: rgb(223, 0, 0);
            font-size: 12px;
            font-family: "Arial";
        }
        .item {
            position: absolute !important;
        }

        @font-face {
            font-family: 'arial';
            src: url("/fonts/Arialn.ttf") format("truetype")
        }

        @font-face {
            font-family: 'helvetica';
            src: url("/fonts/Helvetica.ttf") format("truetype")
        }

        @font-face {
            font-family: 'open sans';
            src: url("/fonts/OpenSans-Regular.ttf") format("truetype")
        }

    </style>
</head>

<body
    style="min-width: 795px; min-height: 1124px;"
>

    @php
        $backgroundImage = $builder::convert_base64(public_path() . '/storage' . '/' . $data['image2']);
    @endphp

    <div 
        class="container"
        style="
            position: relative !important;
            background-image: url('{{ $backgroundImage }}');
            background-size: contain;
            background-repeat: no-repeat;
        "
    >
        {{-- <img 
            src="{{ $backgroundImage }}" 
            alt="Flyer background"
            style="width: auto; height: auto; object-fit: cover; position: fixed; z-index: -1"
        > --}}

        
        @foreach ($data['fields'] as $key => $item)

            @if (isset($item->active) && $item->active == 1)
                <span 
                style="
                    color: {{ $item->color }}; 
                    font-size: {{ $item->size }}px; 
                    font-family: {{ $item->font }};
                    top: {{ $item->y }}px;
                    left: {{ $item->x }}px;
                    z-index: 10;
                    position: absolute;
                ">
                    {{-- {{ $item->color }}<br>
                    {{ $item->size }}px<br>
                    {{ ucFirst($item->font) }}<br>
                    {{ $item->y }}px<br>
                    {{ $item->x }}px<br> --}}
                    {{ optional($item)->value }}
                </span>

            @endif
                
        @endforeach


    </div>


   
</body>

</html>