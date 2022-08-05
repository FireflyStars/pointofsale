<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
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
            font-family: arial;
        }
        .item {
            position: absolute !important;
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
                <span>

                        <span 
                        class="item"
                        style="
                            color: {{ $item->color }}; 
                            font-size: {{ $item->size }}; 
                            font-family: {{ $item->font }}; 
                            top: {{ $item->y }}px;
                            left: {{ $item->x }}px;
                            z-index: 10;
                        ">
                            {{ optional($item)->value }}
                        </span>

                </span>

            @endif
                
        @endforeach


    </div>


   
</body>

</html>