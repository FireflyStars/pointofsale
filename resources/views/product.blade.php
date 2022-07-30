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

<body>

        
    @php
        $backgroundImage = $builder::convert_base64(public_path() . '/storage' . '/' . $data['image2']);
    @endphp

    <div 
        class="container"
        style="
            position: relative !important;
        "
    >
        <img 
            src="{{ $backgroundImage }}" 
            alt="Flyer background"
            {{-- style="width: 100%; height: 100%; object-fit: cover; position: fixed; z-index: -1" --}}
            style="width: auto; height: auto; object-fit: cover; position: fixed; z-index: -1"
        >

        
        @foreach ($data['fields'] as $key => $item)

            @if (isset($item->active) && $item->active == 1)
                <span>

                    {{-- @if ($key == 'Prenom_dirigeant') --}}
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
                            {{ $item->value }}
                        </span>
                    {{-- @endif --}}

                    {{-- @if ($key == 'Nom_dirigeant')
                        <span 
                        class="item"
                        style="
                            color: {{ $item->color }}; 
                            font-size: 18px; 
                            font-family: {{ $item->font}}; 
                            top: {{ 874 }}px;
                            left: {{ 374 }}px;
                            z-index: 10;
                        ">
                            {{ $item->value }}
                        </span>
                    @endif

                    @if ($key == 'Email_agence')
                        <span 
                        class="item"
                        style="
                            color: {{ $item->color }}; 
                            font-size: 18px; 
                            font-family: {{ $item->font}}; 
                            top: {{ 900 }}px;
                            left: {{ 274 }}px;
                            z-index: 10;
                        ">
                            {{ $item->value }}
                        </span>
                    @endif

                    @if ($key == 'Telephone_agence')
                        <span 
                        class="item"
                        style="
                            color: {{ $item->color }}; 
                            font-size: 18px; 
                            font-family: {{ $item->font}}; 
                            top: {{ 926 }}px;
                            left: {{ 274 }}px;
                            z-index: 10;
                        ">
                            {{ $item->value }}
                        </span>
                    @endif --}}

                </span>

            @endif
                
        @endforeach


    </div>


   
</body>

</html>