<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Report</title>
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-size: 16px;
        }
        main {
            position: relative;
            width: 100%;
            height: 100%;
        }
        .draggable, .item {
            z-index: 10;
            position: absolute;
        }
        button {
            display: flex;
            align-items:center;
            gap: 4px;
            padding: 7px 18px;
            position: relative;
            background: #ECECEC;
            border: 1px solid #47454B;
            box-sizing: border-box;
            border-radius: 4px;
            text-align: left;
            font-family: 'Almarai';
            font-style: normal;
            font-weight: bold;
            font-size: 16px;
            line-height: 140%;
            transition: all .1s;
            max-width: 91px;
            max-height: 38px;
        }
        button.danger {
            background: #FF0000;
            color: white;
        }
        button.green {
            background: #A1FA9F;
            color: #3E9A4D;
        }
        button.light-green, button.lightgreen {
            background: rgba(161, 250, 159, 0.47);
            color: rgba(0, 0, 0, 0.2);
        }
        button.primary {
            background: #1FB9FC;
            color: #fff;
        }
        button.success {
            background: #3E9A4D;
            color: white;
        }
        button.light-warning, button.lightWarning {
            background: rgba(232, 88, 27, 0.7);
            color: white;
        }
        button.warning {
            background: #E8581B;
            color: white;
        }
        .order-1 {
            order: 1 !important;
        }
        .template-affiliate {
            bottom: 20px;
            left: 0;
            width: 100%;
            position: absolute;
            padding: 1rem;
            font-size: 10px;
            font-family: inherit;
            display: grid;
            grid-template-columns: 10% 90%;
        }
        .template-affiliate .page-number {
            font-weight: bold;
            font-size: 12px;
        }
        .template-body span {
            word-break: break-all !important;
        }
        .template-body img {
            width: auto;
            height: auto;
            object-fit: cover;
            max-width: 25rem;
            max-height: 25rem;
            border: 3px solid orange;
            position: absolute;
        }

        .template-body .svg {
            width: 35px;
            height: 30px;
            border: none;
        }

        .draggable {
            cursor: move;
            z-index: 10;
            position: absolute;
        }

        .transparent-button {
            background: transparent;
            border-radius: 0;
            border: 2px solid #000;
            text-transform: uppercase;
            font-weight: 900;
            font-family: 'Almarai ExtraBold';
        }

        .title-bar {
            background: #797272;
            color: rgb(243, 243, 243);
            padding: 5px 1rem;
            margin: .8rem 0;
            text-transform: uppercase;
            font-size: 1.2rem;
            font-weight: 900;
            font-family: 'Almarai ExtraBold';
            width: 100%;
            display: flex;
            align-items: center;
        }

        .title-bar .content {
            flex-grow: 1;
        }

        .textarea {
            position: absolute !important;
            border: none;
            width: auto;
            height: auto;
            border: 1px solid #ccc;
            z-index: 13;
            word-wrap: normal;
            color: #000;
        }
       
        .textarea p {
            margin-bottom: 0 !important;
            margin-top: 0 !important;
        }

        .textarea .heading {
            font-style: normal;
            font-family: inherit;
            font-size: 20px;
            line-height: 29px;
            color: #212529;
            font-weight: 500;
        }

        .table {
            border-collapse: collapse;
            min-width: 350px;
            min-height: 50px;
            z-index: 10000;
            word-wrap: normal;
            padding: 1rem;
            text-align: center;
        }

        .table, .table th, .table td {
            border: 1px solid #ccc;
            padding: .5rem;
        }
        
    </style>

</head>

<body>

    @foreach ($pages as $page)
        <?php 
            $break_rule = $loop->last ? 'avoid-page': 'page';
            $background = $builder::get_page_background_by_id($page_files, $loop->index);
            $last = $loop->index == count((array) $pages) - 1;
            $first = $loop->first;
        ?>
        <main 
            style="
            break-after: {{ $break_rule }};
            position: relative; 
            background-image: url('{{$background}}'); 
            background-repeat: no-repeat; 
            background-size: cover; 
            background-position: center;"
        >

            <div class="template-body">

                @foreach ($page->elements as $element)
                    
                    @if (strtolower($element->name) == 'button')
                        <button 
                        class="{{ $element->attributes->kind ?? 'default' }} {{ $element->attributes->class }}" 
                        style="{{ $element->attributes->style ?? '' }}">
                            {{ $element->attributes->title ?? '' }}
                        </button>
                    @endif
        
                    @if (strtolower($element->name) == 'textarea')
                        <div 
                        class="{{ $element->attributes->class }} textarea" 
                        style="{{ $element->attributes->style ?? '' }} position: absolute; border: 0 !important;"
                        >{!! $element->content ?? '' !!}
                        </div>
                    @endif
            
                    @if (strtolower($element->name) == 'svg')
                        <?php 
                            $src = $builder::svg_base64_encode($element->attributes);
                        ?>
                        <img 
                            class="{{ $element->attributes->class ?? 'draggable' }} svg"
                            src="{{ $src }}" 
                            style="{{ $element->attributes->style ?? '' }} position: absolute;" 
                        /> 
                    @endif    
            
                    @if (strtolower($element->name) == 'img')
                        <?php
                            $src = $builder::get_page_image_file($page_files, $element->attributes->id);
                        ?>
    
                        <img 
                            class="{{ $element->attributes->class ?? 'draggable' }}"
                            src="{{ $src }}" 
                            style="{{ $element->attributes->style ?? '' }} position: absolute;" 
                        /> 
                    @endif
                    
                    @if (strtolower($element->name) == 'table')

                        <table 
                            class="{{ $element->attributes->class ?? 'draggable' }} table"
                            style="{{ $element->attributes->style ?? '' }} position: absolute;" 
                        >
                            <tr @if(optional($element->attributes)->headers ?? true) @endif>
                                @for ($i = 1; $i <= $element->attributes->cols; $i++)
                                    <?php $col = 'tr-1' . $i; ?>
                                    <th>{{ optional($element->content->header)->$col ?? 'title ' . $i }}</th>
                                @endfor    
                            </tr>
                            @for($row = 1; $row <= $element->attributes->rows; $row++)
                                <tr>
                                    @for($col = 1; $col <= $element->attributes->cols; $col++)
                                        <?php $rowValue = 'tr-' . $row .''. $col; ?>
                                        <td>{{ optional($element->content->body)->$rowValue ?? 'Col '. $col }}</td>
                                    @endfor
                                </tr>
                            @endfor
                        </table>
                        
                    @endif    
            
                @endforeach
        
            </div>
            @if (!$last && !$first)

                <div 
                    class="template-affiliate" 
                    style="display: flex;"
                >
                    <div class="page-number" style="margin-top: 20px !important;margin-left: 20px; color: #F45B1F; font-weight: bold;">{{ (int) $loop->index }}/{{ count((array)$pages) }}</div>
                    <div class="content" style="margin-left: 20px;">{!! optional($affiliate)->footerreport ?? '' !!}</div>
                </div>
            @endif

        </main>
        
    @endforeach

</body>


</html>