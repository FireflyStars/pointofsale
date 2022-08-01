@php
    $edit = !is_null($dataTypeContent->getKey());
    $add  = is_null($dataTypeContent->getKey());
@endphp

@extends('voyager::master')

@section('css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        #modalDraggable{
            border:thin solid #000;
        }

        .textpos{
            font-size:14pt;
            position: absolute;
            top:0;
            left:0;
        }

        .textpos.active-text{
            background:rgba(255,255,255,0.5);
        }

        .textpos:hover{
            cursor: move;
        }

        .remove-img-pos{
            font-size:20pt;
            margin-top: -10px;
        }


        .switch-wrapper{
        display: flex;
        align-items: center;
    }
    .switch-wrapper label{
        cursor: pointer;
    }
    .switch{
        background-color: #C3C3C3;
        height: 28px;
        padding: 5px 3px 5px 8px;
        border-radius: 40px;
        display: flex;
        align-items: center;
        width: 65px;
        position: relative;
        cursor: pointer;
        transition: 0.3s ease-in-out background-color;
    }
    .switch span{
        color:#FFF;
    }
    .switch:before{
        content: " ";
        width: 18px;
        height: 18px;
        position: absolute;
        background: #FFF;
        top:5px;
        left:3px;
        border-radius: 50%;
        transition: 0.3s ease-in-out left;
    }
    .switch span{
        transition: 0.2s ease-in-out opacity;
    }
    .switch.on{
        background-color: #42A71E;
    }
    .switch.on:before{
        left: 44px;
    }
    .switch span:first-child{
        opacity: 0;
    }
    .switch span:last-child{
        opacity: 1;
    }
    .switch.on span:last-child{
        opacity: 0;
    }
    .switch.on span:first-child{
        opacity: 1;
    }
    .disabled .switch{
        opacity: 0.5;
        cursor: default;
        pointer-events: none;
    }
    .disabled label{
        opacity: 0.5;
        pointer-events: none;
        cursor: default;

    }
    .switch-wrapper label:first-child{
        margin-right: 8px;
    }
    .switch-wrapper label:last-child{
        margin-left: 8px;
    }
    .d-inline-block{
        display: inline-block;
    }

    /* Chrome, Safari, Edge, Opera */
#modal_pos  input::-webkit-outer-spin-button,
#modal_pos  input::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

/* Firefox */
#modal_pos  input[type=number] {
  -moz-appearance: textfield;
  -webkit-appearance: textfield;
  width: 40px;
  height: 25px;
vertical-align: middle;
}
#modal_pos .color{
    height: 20px;
    width: 20px;
    overflow: hidden;
    border-radius: 20px;
    vertical-align: middle;
    display: inline-block;
    box-shadow: 1px 1px 6px;
}
#modal_pos [type="color"] {
  border: 0;
  padding: 0;
  width: 200%;
  height: 200%;
  cursor: pointer;
  transform: translate(-25%, -25%)
}
.police{
    width: 100px;
    vertical-align: middle;
    line-height: 25px;
}
.rw{
    display: flex;
}
.cl{
    padding: 0 4px;
    line-height: 35px;
}
.fields h5{
    width: 130px;
}
    </style>
@stop

@section('page_title', __('voyager::generic.'.($edit ? 'edit' : 'add')).' '.$dataType->getTranslatedAttribute('display_name_singular'))

@section('page_header')
    <h1 class="page-title">
        <i class="{{ $dataType->icon }}"></i>
        {{ __('voyager::generic.'.($edit ? 'edit' : 'add')).' '.$dataType->getTranslatedAttribute('display_name_singular') }}
    </h1>
    @include('voyager::multilingual.language-selector')
@stop

@section('content')
    <div class="page-content edit-add container-fluid">
        <div class="row">
            <div class="col-md-12">

                <div class="panel panel-bordered">
                    <!-- form start -->
                    <form role="form"
                            class="form-edit-add"
                            action="{{ $edit ? route('voyager.'.$dataType->slug.'.update', $dataTypeContent->getKey()) : route('voyager.'.$dataType->slug.'.store') }}"
                            method="POST" enctype="multipart/form-data">
                        <!-- PUT Method if we are editing -->
                        @if($edit)
                            {{ method_field("PUT") }}
                        @endif

                        <!-- CSRF TOKEN -->
                        {{ csrf_field() }}

                        <div class="panel-body">

                            @if (count($errors) > 0)
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <!-- Adding / Editing -->
                            @php
                                $dataTypeRows = $dataType->{($edit ? 'editRows' : 'addRows' )};
                            @endphp

                            @foreach($dataTypeRows as $row)
                                <!-- GET THE DISPLAY OPTIONS -->

                                @php
                                    $display_options = $row->details->display ?? NULL;
                                    if ($dataTypeContent->{$row->field.'_'.($edit ? 'edit' : 'add')}) {
                                        $dataTypeContent->{$row->field} = $dataTypeContent->{$row->field.'_'.($edit ? 'edit' : 'add')};
                                    }
                                @endphp
                                @if (isset($row->details->legend) && isset($row->details->legend->text))
                                    <legend class="text-{{ $row->details->legend->align ?? 'center' }}" style="background-color: {{ $row->details->legend->bgcolor ?? '#f0f0f0' }};padding: 5px;">{{ $row->details->legend->text }}</legend>
                                @endif

                                <div class="form-group @if($row->type == 'hidden') hidden @endif col-md-{{ $display_options->width ?? 12 }} {{ $errors->has($row->field) ? 'has-error' : '' }}" @if(isset($display_options->id)){{ "id=$display_options->id" }}@endif>
                                    {{ $row->slugify }}
                                    <label class="control-label" for="name">{{ $row->getTranslatedAttribute('display_name') }}</label>
                                    @include('voyager::multilingual.input-hidden-bread-edit-add')
                                    @if (isset($row->details->view))
                                        @include($row->details->view, ['row' => $row, 'dataType' => $dataType, 'dataTypeContent' => $dataTypeContent, 'content' => $dataTypeContent->{$row->field}, 'action' => ($edit ? 'edit' : 'add'), 'view' => ($edit ? 'edit' : 'add'), 'options' => $row->details])
                                    @elseif ($row->type == 'relationship')
                                        @include('voyager::formfields.relationship', ['options' => $row->details])
                                    @else
                                        {!! app('voyager')->formField($row, $dataType, $dataTypeContent) !!}
                                    @endif

                                    @foreach (app('voyager')->afterFormFields($row, $dataType, $dataTypeContent) as $after)
                                        {!! $after->handle($row, $dataType, $dataTypeContent) !!}
                                    @endforeach
                                    @if ($errors->has($row->field))
                                        @foreach ($errors->get($row->field) as $error)
                                            <span class="help-block">{{ $error }}</span>
                                        @endforeach
                                    @endif
                                </div>
                            @endforeach

                        </div><!-- panel-body -->

                        <div class="panel-footer">
                            @section('submit-buttons')
                                <button type="submit" class="btn btn-primary save">{{ __('voyager::generic.save') }}</button>
                            @stop
                            @yield('submit-buttons')
                        </div>
                    </form>

                    <iframe id="form_target" name="form_target" style="display:none"></iframe>
                    <form id="my_form" action="{{ route('voyager.upload') }}" target="form_target" method="post"
                            enctype="multipart/form-data" style="width:0;height:0;overflow:hidden">
                        <input name="image" id="upload_file" type="file"
                                 onchange="$('#my_form').submit();this.value='';">
                        <input type="hidden" name="type_slug" id="type_slug" value="{{ $dataType->slug }}">
                        {{ csrf_field() }}
                    </form>

                </div>
            </div>
        </div>
    </div>

<!--Modal positiioning  old background styles background-size:auto 100%;width:210mm;height:297mm;background-repeat: no-repeat; -->

<div id="modal_pos" class="modal fade">
    <div class="modal-dialog" role="document" style="width:auto !important;">
        <div class="modal-content" style="display:table; margin:auto;">
            <div class="modal-header">
                <h5 class="modal-title float-left">Positionner text sur image</h5>
                <button type="button" class="btn btn-secondary float-right" data-dismiss="modal">Fermer</button>
            </div>
            <div class="modal-body" style="display:table; width:1000px;">
            <div class="float-left">
                <div class="modalPosDiv float-left" id="div_modal_{{ $dataTypeContent->getKey() }}" 
                    style="background-size:auto; background-repeat: no-repeat;">

                    <div id="modalDraggable" style="background:rgba(0,0,0,0.5); position: relative;">
                   
                    </div>
                </div>
                </div>
                <div class="float-left" style=" padding-left:20px; margin-top:50px">
                <datalist id="fontsizes">
                                <option value="8">
                                <option value="9">
                                <option value="10">
                                <option value="11">
                                <option value="12">
                                <option value="14">
                                <option value="16">    
                                <option value="18">    
                                <option value="20">    
                                <option value="22">    
                                <option value="24">    
                                <option value="26">    
                                <option value="28">  
                                <option value="36">      
                                <option value="48">   
                                <option value="72">   
                            </datalist> 
                        <div id="field_template" class="rw hidden">
                        <div class="cl"><h5>Nom Agence</h5></div>
                        <div class="cl">
                            <div class="d-inline-block">
                                <div class="switch-wrapper" >
                                    <div class="switch"  >
                                            <span class="body_medium noselect">On</span>
                                            <span class="body_medium noselect">Off</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="cl">
                            <input type="number" list="fontsizes" placeholder="Taille" class="taille"/>
                        </div>
                        <div class="cl">
                            <select class="police">
                                <option value="">Choisir une police</option>
                                <option value="arial">Arial</option>
                                <option value="helvetica">Helvetica</option>
                                <option value="open sans">Open Sans</option>
                            </select>
                        </div>
                        <div class="cl">
                            <div class="color">
                                <input type="color" class="couleur" />
                                <input type="hidden" class="x" />
                                <input type="hidden" class="y"/>
                            </div>
                        </div>

                    </div>   
                    <div style="margin-bottom:20px;" class="fields">
        



                    </div>

                   

                    <a href="javascript:void(0);" class="btn btn-default" onclick="updateTextCoords({{ $dataTypeContent->getKey() }})">Sauvegarder</a>
                </div>

                <div style="margin:20px 0;">

                </div>
            </div>
        </div>
    </div>
</div>


    <div class="modal fade modal-danger" id="confirm_delete_modal">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"
                            aria-hidden="true">&times;</button>
                    <h4 class="modal-title"><i class="voyager-warning"></i> {{ __('voyager::generic.are_you_sure') }}</h4>
                </div>

                <div class="modal-body">
                    <h4>{{ __('voyager::generic.are_you_sure_delete') }} '<span class="confirm_delete_name"></span>'</h4>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{{ __('voyager::generic.cancel') }}</button>
                    <button type="button" class="btn btn-danger" id="confirm_delete">{{ __('voyager::generic.delete_confirm') }}</button>
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" id="cur_id" value="{{ $dataTypeContent->getKey() }}"/>
    <!-- End Delete File Modal -->
@stop

@section('javascript')

<script type="text/javascript" src="/jquery/jquery-ui/jquery-ui.min.js"></script>

    <script>
        var params = {};
        var $file;

        let cur_id = $('#cur_id').val();


        function deleteHandler(tag, isMulti) {
          return function() {
            $file = $(this).siblings(tag);

            params = {
                slug:   '{{ $dataType->slug }}',
                filename:  $file.data('file-name'),
                id:     $file.data('id'),
                field:  $file.parent().data('field-name'),
                multi: isMulti,
                _token: '{{ csrf_token() }}'
            }

            $('.confirm_delete_name').text(params.filename);
            $('#confirm_delete_modal').modal('show');
          };
        }

 
        $('document').ready(function () {

            

            $('.toggleswitch').bootstrapToggle();

            //Init datepicker for date fields if data-datepicker attribute defined
            //or if browser does not handle date inputs
            $('.form-group input[type=date]').each(function (idx, elt) {
                if (elt.hasAttribute('data-datepicker')) {
                    elt.type = 'text';
                    $(elt).datetimepicker($(elt).data('datepicker'));
                } else if (elt.type != 'date') {
                    elt.type = 'text';
                    $(elt).datetimepicker({
                        format: 'L',
                        extraFormats: [ 'YYYY-MM-DD' ]
                    }).datetimepicker($(elt).data('datepicker'));
                }
            });

            @if ($isModelTranslatable)
                $('.side-body').multilingual({"editing": true});
            @endif

            $('.side-body input[data-slug-origin]').each(function(i, el) {
                $(el).slugify();
            });

            $('.form-group').on('click', '.remove-multi-image', deleteHandler('img', true));
            $('.form-group').on('click', '.remove-single-image', deleteHandler('img', false));
            $('.form-group').on('click', '.remove-multi-file', deleteHandler('a', true));
            $('.form-group').on('click', '.remove-single-file', deleteHandler('a', false));

            $('#confirm_delete').on('click', function(){
                $.post('{{ route('voyager.'.$dataType->slug.'.media.remove') }}', params, function (response) {
                    if ( response
                        && response.data
                        && response.data.status
                        && response.data.status == 200 ) {

                        toastr.success(response.data.message);
                        $file.parent().fadeOut(300, function() { $(this).remove(); });

                        $('#btn_modal_'+params.id).remove();


                    } else {
                        toastr.error("Error removing file.");
                    }
                });

                $('#confirm_delete_modal').modal('hide');
            });
            $('[data-toggle="tooltip"]').tooltip();


            let img1 = $('#campagne_category_imagetemplate');
            $('#modalDraggable').height('297mm');
            $('#modalDraggable').width('210mm');
            // nheight = img1.prop('height')
            // console.log(nheight);
            // if(img1.length > 0){
            //     let nwidth = img1.prop('naturalWidth');
            //     let nheight = img1.prop('naturalHeight');

            //     //$('.modalPosDiv').width(nwidth);
            //     // $('#modalDraggable').width(nwidth);
            //     // $('#modalDraggable').height(nheight);

            // }


  


            $(document).bind('keydown', function(e) {
                let activetexts = $('.active-text');

                //console.log(e);
                //console.log(activetexts);

                if(activetexts.length > 0){

                    var position,
                        draggable = $('.active-text'),
                        container = $('#div_modal_'+cur_id),
                        distance = 1; // Distance in pixels the draggable should be moved

                    position = draggable.position();

                    // Reposition if one of the directional keys is pressed
                    switch (e.keyCode) {
                        case 37: position.left -= distance; break; // Left
                        case 38: position.top  -= distance; break; // Up
                        case 39: position.left += distance; break; // Right
                        case 40: position.top  += distance; break; // Down
                        default: return true; // Exit and bubble
                    }

                    // Keep draggable within container
                    if (position.left >= 0 && position.top >= 0 &&
                        position.left + draggable.width() <= container.width() &&
                        position.top + draggable.height() <= container.height()) {
                        draggable.css(position);
                    }

                    let id = draggable.attr('id');

                    $('#'+id+'_x').html([position.left]);
                    $('#'+id+'_y').html([position.top]);


                    // Don't scroll page
                    e.preventDefault();
                }
            });



        });
        let fieldsdef;
        function renderfields(){
            let modalDraggable=document.getElementById('modalDraggable');
            modalDraggable.innerHTML='';

                for(const prop in fieldsdef){
                    if(fieldsdef[prop].active==1){
                            const span = document.createElement("span");
                            // and give it some content
                            const newContent = document.createTextNode(fieldsdef[prop].name);
                            // add the text node to the newly created div
                            span.appendChild(newContent);
                            span.classList.add('textpos');
                            span.setAttribute('id',`#${prop}`);
                            span.style.color=fieldsdef[prop].color;
                            span.style.left=`${fieldsdef[prop].x}px`;
                            span.style.top=`${fieldsdef[prop].y}px`;
                            span.style.fontSize=`${fieldsdef[prop].size}px`;
                            span.style.fontFamily=`${fieldsdef[prop].font}`;
                            modalDraggable.appendChild(span);
                    }
                            
                }
setTimeout(function(){
    $('.textpos').draggable({
                containment: "#modalDraggable",
                scroll: false,
                stop: function(ev, ui){
                   let el = $(ev.target);
                   let id = el.attr('id');
                    //console.log(id);
                    let prop=id.replace('#','');
                    var position = ui.position;
                    var originalPosition = ui.originalPosition;
                    fieldsdef[prop].x=parseFloat(position.left);
                    fieldsdef[prop].y=parseFloat(position.top);
                  

                    

                  }
            });

            $('.textpos').click(function(){
                let el = $(this);

                $('.textpos').not(this).removeClass('active-text');
                el.toggleClass('active-text');
            });
},100)
               
     
        }

        function loadPosModal(id,imageUrl){
            let params = {};
            params['id'] = id;



            $.post('/admin/get-text-pos', params, function (response) {
                if(response.FIELDS_DEF){
                    fieldsdef=response.FIELDS_DEF;
                    document.querySelector('.fields').innerHTML='';
                    field_template= document.getElementById("field_template");
                    for(const prop in response.FIELDS_DEF){
                       field=field_template.cloneNode(true);
                       field.setAttribute('id',prop);
                       field.classList.remove('hidden');
                       field.querySelector('h5').innerText= response.FIELDS_DEF[prop].name;
                       if(response.FIELDS_DEF[prop].active==1)
                       field.querySelector('.switch').classList.add('on');

                       let switchbtn = field.querySelector('.switch');

                       switchbtn.addEventListener('click', function(event){
                               

                                switchbtn.classList.toggle("on");
                               
                                fieldsdef[prop].active=switchbtn.classList.contains("on")?1:0;
                                renderfields();
                                }, true);
                                                    
                       field.querySelector('.taille').value=response.FIELDS_DEF[prop].size;
                       function f_taille(event){
                        fieldsdef[prop].size=event.target.value;
                        renderfields();
                       }
                       let taille= field.querySelector('.taille');
                           taille.addEventListener('paste',f_taille);         
                           taille.addEventListener('keyup',f_taille);     

                       field.querySelector('.police').value=response.FIELDS_DEF[prop].font;

                       function f_police(event){
                        fieldsdef[prop].font=event.target.value;
                        renderfields();
                       }
                       let police= field.querySelector('.police');
                            police.addEventListener('change',f_police);         
                
                       field.querySelector('.couleur').value=response.FIELDS_DEF[prop].color;

                       function f_couleur(event){
                        fieldsdef[prop].color=event.target.value;
                        renderfields();
                       }
                       let couleur= field.querySelector('.couleur');
                       couleur.addEventListener('change',f_couleur); 

                       field.querySelector('.x').value=response.FIELDS_DEF[prop].x;
                       field.querySelector('.y').value=response.FIELDS_DEF[prop].y;
                       document.querySelector('.fields').appendChild(field);
                   
                    }
                    renderfields();
                }
               
            }).done(function() {
                //alert( "second success" );
            })
            .fail(function() {
                //alert( "error" );
            })
            .always(function() {
                //alert( "finished" );
                $('#div_modal_'+id).css('background-image', 'url(' + imageUrl + ')');
                $('#modal_pos').modal('show');
            });
        }


        function updateTextCoords(id){
            let params = {};
            params['id_cc'] = id;

            $('.textpos').removeClass('active-text');

            params['fieldsdef'] = fieldsdef;

            $.post('/admin/update-text-pos', params, function (response) {
                    console.log(response);
                    if ( response.updated) {

                        toastr.success('Position sauvegardé');


                    } else {
                        //toastr.error("Error removing file.");
                    }
                });


        }


    </script>
@stop
