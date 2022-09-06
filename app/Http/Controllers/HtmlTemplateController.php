<?php

namespace App\Http\Controllers;

use App\Models\Htmltemplate;
use App\Models\HtmltemplateElement;
use App\Models\HtmltemplateFooter;
use App\Models\HtmltemplateHeader;
use App\Models\Notification;
use Illuminate\Http\Request;

use Barryvdh\Snappy\Facades\SnappyPdf;
use Beta\Microsoft\Graph\ManagedTenants\Model\Setting;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class HtmlTemplateController extends Controller
{
    //
    public static $JSONEXAMPLE='{
        "sql":"SELECT CONCAT(customers.firstname,\' \',customers.name) as customer_name,orders.customer_id,orders.id,orders.total,orders.datecommande,orders.nbheure FROM `orders` left join `customers` on(customers.id=orders.customer_id and orders.order_state_id={id} ) where orders.datecommande is not null having customer_name is not null",
        "table_id":"table01",
        "style": "width:100%;border-collapse:collapse;",
        "class":"standard_facture_table",
        "thead": {
            "style": "background-color:orange",
            "class":"thead",
            "tr": [{
                "style":"color:#FFF;font-size:18px;",
                "class":"thead_tr",
                "th":[
                    {
                        "style":"",
                        "class":"",
                        "type":"empty",
                        "name":""
                    },
                    {
                        "identifier":"id",
                        "style":"",
                        "name":"Numéro commande",
                        "class":"",
                        "type":"string",
                        "prefix":"",
                        "suffix":""
                    },
                    {
                        "identifier":"customer_name",
                        "name":"Client",
                        "style":"",
                        "class":"",
                        "type":"table",
                        "prefix":"",
                        "suffix":"",
                        "table":{
                            "sql":"SELECT customers.firstname,customers.name as lastname FROM `customers` where customers.id={customer_id}",
                            "table_id":"table02",
                            "style": "width:100%;border-collapse:collapse;",
                            "class":"standard_facture_table",
                            "thead": {
                                "style": "background-color:orange",
                                "class":"thead",
                                "tr": [{
                                    "style":"color:#FFF;font-size:18px;",
                                    "class":"thead_tr",
                                    "th":[
                                       
                                        {
                                            "identifier":"firstname",
                                            "name":"Client firstname",
                                            "style":"",
                                            "class":"",
                                            "type":"string",
                                            "prefix":"",
                                            "suffix":""
                                         
                                        },
                                        {
                                            "identifier":"lastname",
                                            "name":"Client lastname",
                                            "style":"",
                                            "class":"",
                                            "type":"string",
                                            "prefix":"",
                                            "suffix":""
                                           
                                        }
                                    ]
                                }]
                            },
                            "tbody": {
                                "style":"",
                                "class":"",
                                "prefix":[
                            
                                ],
                                "tr":{
                                    "style":"background-color:green;",
                                    "class":"rw",
                                 
                                    "td":{
                                        "style":"",
                                        "class":"cell",
                                        "tableX":""
                                    }
                                   
                                },
                                "suffix":[
                                   
                                ]
                            
                            },
                            "tfoot": {
                                "style": ""
                            }
                            }
                    },
                    {
                        "identifier":"datecommande",
                        "name":"Date de commande",
                        "style":"",
                        "class":"",
                        "type":"string",
                        "decimal":"2",
                        "prefix":"",
                        "suffix":""
                    },
                    {
                        "identifier":"nbheure",
                        "name":"Nombre heure",
                        "style":"",
                        "class":"",
                        "type":"number",
                        "decimal":"0",
                        "prefix":"",
                        "suffix":""
                    },
                    {
                        "identifier":"total",
                        "name":"Total HT",
                        "style":"",
                        "class":"",
                        "type":"number",
                        "decimal":"2",
                        "prefix":"",
                        "suffix":""
                    }
        
                ]
            }]
        },
        "tbody": {
            "style":"",
            "class":"",
            "prefix":[
                "<tr><td  ><u>{num_devis}</u> </td><td colspan=\"5\"><b>{description_devis}</b></td></tr>",
                "<tr><td colspan=\"6\" ><b>Installation et Repli</b></td></tr>"
            ],
            "tr":{
                "style":"background-color:green;",
                "class":"rw",
             
                "td":{
                    "style":"",
                    "class":"cell",
                    "tableX":"<table style=\"width:100%\"><tbody><tr><td>{description_devis}</td></tr></tbody></table>"
                }
               
            },
            "suffix":[
                "<tr><td colspan=\"5\"><b>TOTAL Installation et Repli</b></td><td><u>{+total}</u> </td></tr>",
                "<tr><td colspan=\"4\"><b>TOTAL</b></td><td>{+nbheure}</td><td><b>{+total}</b> </td></tr>"
            ]
        
        },
        "tfoot": {
            "style": ""
        }
        }';

   
    public function getHtmlTemplateLists(Request $request){
        $column_filters=$request->post('column_filters');
        $column_sortby=$request->post('sortby');
        $skip=$request->post('skip');
        $take=$request->post('take');
        $user=Auth::user();

        $list=DB::table('htmltemplates')
        ->select(['htmltemplates.*','htmltemplates.id as rowaction']);
        $list=$list->where('htmltemplates.affiliate_id','=',$user->affiliate->id);
        $list=$list->whereNull('htmltemplates.deleted_at');
        //column filters
        if($column_filters!=null)
        foreach($column_filters as $column_filter){
       
            if($column_filter['type']=='date'){
                if(isset($column_filter['having'])&&$column_filter['having']==true){
                    if($column_filter['word']['from']!=''){
                        $list=$list->having($column_filter['id'],'>=',$column_filter['word']['from']);
                    }
                    if($column_filter['word']['to']!=''){
                        $list=$list->having($column_filter['id'],'<',$column_filter['word']['to']);
                    }
                }else{
                   if($column_filter['word']['from']!=''){
                        $list=$list->whereDate((isset($column_filter['table'])?$column_filter['table'].'.':'').$column_filter['id'],'>=',$column_filter['word']['from']);
                    }
                    if($column_filter['word']['to']!=''){
                        $list=$list->whereDate((isset($column_filter['table'])?$column_filter['table'].'.':'').$column_filter['id'],'<',$column_filter['word']['to']);
                    }
                }
            }elseif(isset($column_filter['filter_options'])&&count($column_filter['word'])>0){
           
                if(isset($column_filter['having'])&&$column_filter['having']==true){
                   
                    $list=$list->havingRaw($column_filter['id']." IN ('".implode("','",$column_filter['word'])."')");
                }else{
                    $list=$list->whereIn($column_filter['id'],$column_filter['word']);
                }
            
            }else{
                if(isset($column_filter['having'])&&$column_filter['having']==true){
                    $list=$list->having($column_filter['id'],'LIKE','%'.$column_filter['word'].'%');
                }else{
                    $list=$list->where((isset($column_filter['table'])?$column_filter['table'].'.':'').$column_filter['id'],'LIKE','%'.$column_filter['word'].'%');
                }
            }
        }

        //sortby
        if($column_sortby!=null){
        foreach($column_sortby as $sortby){
            $list=$list->orderBy($sortby['id'],$sortby['orderby']);
        }
        }else{//by default newest first
            $list=$list->orderBy('htmltemplates.id','desc');
        }

        $list=$list->groupBy('htmltemplates.id')->skip($skip)->take($take)->get();

        return response()->json($list);
    }

    public function getHtmlTemplateHeaderLists(Request $request){
        $column_filters=$request->post('column_filters');
        $column_sortby=$request->post('sortby');
        $skip=$request->post('skip');
        $take=$request->post('take');
        $user=Auth::user();

        $list=DB::table('htmltemplate_headers')
        ->select(['htmltemplate_headers.*','htmltemplate_headers.id as rowaction']);
        $list=$list->where('htmltemplate_headers.affiliate_id','=',$user->affiliate->id);
        $list=$list->whereNull('htmltemplate_headers.deleted_at');
        //column filters
        if($column_filters!=null)
        foreach($column_filters as $column_filter){
       
            if($column_filter['type']=='date'){
                if(isset($column_filter['having'])&&$column_filter['having']==true){
                    if($column_filter['word']['from']!=''){
                        $list=$list->having($column_filter['id'],'>=',$column_filter['word']['from']);
                    }
                    if($column_filter['word']['to']!=''){
                        $list=$list->having($column_filter['id'],'<',$column_filter['word']['to']);
                    }
                }else{
                   if($column_filter['word']['from']!=''){
                        $list=$list->whereDate((isset($column_filter['table'])?$column_filter['table'].'.':'').$column_filter['id'],'>=',$column_filter['word']['from']);
                    }
                    if($column_filter['word']['to']!=''){
                        $list=$list->whereDate((isset($column_filter['table'])?$column_filter['table'].'.':'').$column_filter['id'],'<',$column_filter['word']['to']);
                    }
                }
            }elseif(isset($column_filter['filter_options'])&&count($column_filter['word'])>0){
           
                if(isset($column_filter['having'])&&$column_filter['having']==true){
                   
                    $list=$list->havingRaw($column_filter['id']." IN ('".implode("','",$column_filter['word'])."')");
                }else{
                    $list=$list->whereIn($column_filter['id'],$column_filter['word']);
                }
            
            }else{
                if(isset($column_filter['having'])&&$column_filter['having']==true){
                    $list=$list->having($column_filter['id'],'LIKE','%'.$column_filter['word'].'%');
                }else{
                    $list=$list->where((isset($column_filter['table'])?$column_filter['table'].'.':'').$column_filter['id'],'LIKE','%'.$column_filter['word'].'%');
                }
            }
        }

        //sortby
        if($column_sortby!=null){
        foreach($column_sortby as $sortby){
            $list=$list->orderBy($sortby['id'],$sortby['orderby']);
        }
        }else{//by default newest first
            $list=$list->orderBy('htmltemplate_headers.id','desc');
        }

        $list=$list->groupBy('htmltemplate_headers.id')->skip($skip)->take($take)->get();

        return response()->json($list);
    }
    public function getHtmlTemplateFooterLists(Request $request){
        $column_filters=$request->post('column_filters');
        $column_sortby=$request->post('sortby');
        $skip=$request->post('skip');
        $take=$request->post('take');
        $user=Auth::user();

        $list=DB::table('htmltemplate_footers')
        ->select(['htmltemplate_footers.*','htmltemplate_footers.id as rowaction']);
        $list=$list->where('htmltemplate_footers.affiliate_id','=',$user->affiliate->id);
        $list=$list->whereNull('htmltemplate_footers.deleted_at');
        //column filters
        if($column_filters!=null)
        foreach($column_filters as $column_filter){
       
            if($column_filter['type']=='date'){
                if(isset($column_filter['having'])&&$column_filter['having']==true){
                    if($column_filter['word']['from']!=''){
                        $list=$list->having($column_filter['id'],'>=',$column_filter['word']['from']);
                    }
                    if($column_filter['word']['to']!=''){
                        $list=$list->having($column_filter['id'],'<',$column_filter['word']['to']);
                    }
                }else{
                   if($column_filter['word']['from']!=''){
                        $list=$list->whereDate((isset($column_filter['table'])?$column_filter['table'].'.':'').$column_filter['id'],'>=',$column_filter['word']['from']);
                    }
                    if($column_filter['word']['to']!=''){
                        $list=$list->whereDate((isset($column_filter['table'])?$column_filter['table'].'.':'').$column_filter['id'],'<',$column_filter['word']['to']);
                    }
                }
            }elseif(isset($column_filter['filter_options'])&&count($column_filter['word'])>0){
           
                if(isset($column_filter['having'])&&$column_filter['having']==true){
                   
                    $list=$list->havingRaw($column_filter['id']." IN ('".implode("','",$column_filter['word'])."')");
                }else{
                    $list=$list->whereIn($column_filter['id'],$column_filter['word']);
                }
            
            }else{
                if(isset($column_filter['having'])&&$column_filter['having']==true){
                    $list=$list->having($column_filter['id'],'LIKE','%'.$column_filter['word'].'%');
                }else{
                    $list=$list->where((isset($column_filter['table'])?$column_filter['table'].'.':'').$column_filter['id'],'LIKE','%'.$column_filter['word'].'%');
                }
            }
        }

        //sortby
        if($column_sortby!=null){
        foreach($column_sortby as $sortby){
            $list=$list->orderBy($sortby['id'],$sortby['orderby']);
        }
        }else{//by default newest first
            $list=$list->orderBy('htmltemplate_footers.id','desc');
        }

        $list=$list->groupBy('htmltemplate_footers.id')->skip($skip)->take($take)->get();

        return response()->json($list);
    }
    

    public function jsonToHtml(Request $request){
    echo json_encode(['user_id'=>1]);
        $jsonstr=HtmlTemplateController::$JSONEXAMPLE;
           
            $a=json_decode($jsonstr);
            $global_sql_variables['id']=2;
            $global_sql_variables['num_devis']=50;
            $global_sql_variables['description_devis']='la description du devis';
            echo '<pre>';
            print_r($a);
            echo '</pre>';
        return response(Notification::htmltable($a,$global_sql_variables));
    }

    

    public function attrs($obj){
        return $this->attr('style',$obj).' '.$this->attr('class',$obj);
    }
    public function attr($attribute_name,$obj){
        if(isset($obj->{$attribute_name})&&trim($obj->{$attribute_name})!='')
        return $attribute_name.'="'.$obj->{$attribute_name}.'"';

        return '';
    }

    public function saveHtmlTemplateConf(Request $request){
            $conf=$request->get('conf');
            $template_id=$request->get('template_id');
            $user=Auth::user();
       
            if($template_id>0){
                $htmltemplate=Htmltemplate::find($template_id);
                if($htmltemplate==null)
                return response('Template non trouvée',509);

                if($htmltemplate->affiliate_id!=$user->affiliate_id)
                return response('Impossible de sauvegarder. Le template n\'est pas dans la même affiliée que l\'utilisateur',509);

            }else{
                $htmltemplate=new Htmltemplate();
                $htmltemplate->affiliate_id=$user->affiliate_id;
            }
            $htmltemplate->name=$conf['name'];
            $htmltemplate->type=$conf['type'];
            $htmltemplate->pdf_filename_format= $htmltemplate->type=='pdf'?$conf['pdf_filename_format']:null;
            $htmltemplate->test_email= $htmltemplate->type=='email'?$conf['test_email']:null;
            $htmltemplate->email_subject_format= $htmltemplate->type=='email'?$conf['email_subject_format']:null;
            $htmltemplate->measuringunit=$conf['measuringunit'];
            $htmltemplate->htmltemplate_header_id=$conf['htmltemplate_header_id'];
            $htmltemplate->htmltemplate_footer_id=$conf['htmltemplate_footer_id'];
            $htmltemplate->margin_top=$conf['pagemargin']['top'];
            $htmltemplate->margin_right=$conf['pagemargin']['right'];
            $htmltemplate->margin_bottom=$conf['pagemargin']['bottom'];
            $htmltemplate->margin_left=$conf['pagemargin']['left'];
            $htmltemplate->global_sql=$conf['global_sql'];
            $htmltemplate->global_test_vars=$conf['global_test_vars'];
            $htmltemplate->qrcode=$conf['qrcode'];
            $htmltemplate->save();
            $htmltemplate->fresh();

            return response()->json(array('id'=>$htmltemplate->id));
    }

    public function getHtmlTemplateConf(Request $request){
        $template_id=$request->get('template_id');
        $user=Auth::user();
       
        if($template_id>0){
            $htmltemplate=Htmltemplate::find($template_id);
            if($htmltemplate==null)
            return response('Template non trouvée',509);
            if($htmltemplate->affiliate_id!=$user->affiliate_id)
            return response('Impossible de charger. Le template n\'est pas dans la même affiliée que l\'utilisateur',509);
            $htmltemplate->example=HtmlTemplateController::$JSONEXAMPLE;
            $headerList=HtmltemplateHeader::where('affiliate_id','=',$user->affiliate_id)->whereNull('deleted_at')->get();
            $footerList=HtmltemplateFooter::where('affiliate_id','=',$user->affiliate_id)->whereNull('deleted_at')->get();
            $currentFooter=HtmltemplateFooter::find($htmltemplate->htmltemplate_footer_id);
            $currentHeader=HtmltemplateHeader::find($htmltemplate->htmltemplate_header_id);
            $htmltemplate->qrcode_rendered='';
            if($htmltemplate->qrcode==1){
                $global_test_vars=json_decode($htmltemplate->global_test_vars,true);
                if(isset($global_test_vars['order_id']))
                $htmltemplate->qrcode_rendered=Notification::getQrCodeImg('LCDT-'.$global_test_vars['order_id']);
            }
            return response()->json(array('conf'=>$htmltemplate,'headerList'=>$headerList,'footerList'=>$footerList,'currentFooter'=>$currentFooter,'currentHeader'=>$currentHeader,'globalcss'=>setting('lcdt.htmltemplate_global_css')));
        }else{
            return response('Template id est requis',509);
        }

    }
    public function removeHtmlTemplateElement(Request $request){
      
        $element=$request->get('element');
        $user=Auth::user();
        $el=HtmltemplateElement::find($element['id']);
        if($el->htmltemplate_id==$element['htmltemplate_id']){
            $htmltemplate=Htmltemplate::find($el->htmltemplate_id);
            if($htmltemplate==null)
            return response('Template non trouvée',509);
            if($htmltemplate->affiliate_id!=$user->affiliate_id)
            return response('Impossible de charger. Le template n\'est pas dans la même affiliée que l\'utilisateur',509);
            $el->delete();
            return response()->json(array('delete'=>'ok'));

        }else{
            return response('Element du template invalide.',509);
        }

       
    }
    public function saveHtmlTemplateElement(Request $request){
        $template_id=$request->get('template_id');
        $element=$request->get('element');
        $user=Auth::user();

        if($template_id>0){
            $htmltemplate=Htmltemplate::find($template_id);
            if($htmltemplate==null)
            return response('Template non trouvée',509);
            if($htmltemplate->affiliate_id!=$user->affiliate_id)
            return response('Impossible de sauvegarder. Le template n\'est pas dans la même affiliée que l\'utilisateur',509);

            if($element['id']!=null){
                $el=HtmltemplateElement::find($element['id']);

                if($el==null){
                    return response('Element du template non trouvée',509);
                }
            }else{
                $el=new HtmltemplateElement();

                $lastpos=DB::table('htmltemplate_elements')->where('htmltemplate_id','=',$template_id)->whereNull('deleted_at')->max('position');
                if($lastpos==null)
                $lastpos=0;

                $element['pos']=$lastpos+1;
            }

            $el->htmltemplate_id=$template_id;
            $el->type=$element['type'];
            $el->sql=$element['sql'];
            $el->data=$element['data'];
            $el->position=$element['pos'];
            $el->save();
        }else{
            return response('Template id est requis',509);
        }

    }

    public function getHtmlTemplateElements(Request $request){
        $template_id=$request->get('template_id');
        $rendersql=$request->get('rendersql');
        $user=Auth::user();
        if($template_id>0){
            $htmltemplate=Htmltemplate::find($template_id);
            if($htmltemplate==null)
            return response('Template non trouvée',509);
            if($htmltemplate->affiliate_id!=$user->affiliate_id)
            return response('Impossible de sauvegarder. Le template n\'est pas dans la même affiliée que l\'utilisateur',509);

            $elements=HtmltemplateElement::where('htmltemplate_id','=',$template_id)->orderBy('position')->get();
            $currentHeader=HtmltemplateHeader::find($htmltemplate->htmltemplate_header_id);
               
            if($currentHeader!=null){
                $currentHeader->rendered_data=$currentHeader->html;
                //if($rendersql){
                    $currentHeader->rendered_data=str_replace('{APP_URL}',getenv('APP_URL'),$currentHeader->rendered_data);
                // }else{
                //     $currentHeader->rendered_data=str_replace('{APP_URL}','',$currentHeader->rendered_data);
                // }
            }
            $currentFooter=HtmltemplateFooter::find($htmltemplate->htmltemplate_footer_id);
            if($currentFooter!=null){
                $currentFooter->rendered_data=$currentFooter->html;
                //if($rendersql){
                    $currentFooter->rendered_data=str_replace('{APP_URL}',getenv('APP_URL'),$currentFooter->rendered_data);
                // }else{
                //     $currentFooter->rendered_data=str_replace('{APP_URL}','',$currentFooter->rendered_data);
                // }
            }

            foreach($elements as &$el){
                $el->rendered_data=$el->data;
                if($rendersql){
                    $el->rendered_data=str_replace('{APP_URL}',getenv('APP_URL'),$el->rendered_data);
                }else{
                    $el->rendered_data=str_replace('{APP_URL}','',$el->rendered_data);
                }
            }
            $global_test_vars  =json_decode($htmltemplate->global_test_vars);
            if($rendersql){
                
                $global_sql=$htmltemplate->global_sql;
                foreach($global_test_vars as $k=>$v){
                 
                    $global_sql=str_replace('{'.$k.'}',$v,$global_sql);
                }
                try{
                $global_sql_vars=DB::select($global_sql);
                     }catch(\Illuminate\Database\QueryException $q){
                        return response($q->getMessage(),509);
               }
                if(count($global_sql_vars)>0)
                $global_sql_vars=$global_sql_vars[0];
               

                foreach($elements as &$el){
                    if(trim($el->sql)!=''){
                        $elSql=$el->sql;
                        foreach($global_sql_vars as $k=>$v){
                            $elSql=str_replace('{'.$k.'}',$v,$elSql);
                        }

                        foreach($global_test_vars as $k=>$v){
                            $elSql=str_replace('{'.$k.'}',$v,$elSql);
                        }
                       try{
                        $elSqlVars=DB::select($elSql);
                       }catch(\Illuminate\Database\QueryException $q){
                        $el->rendered_data='<div class="alert alert-danger position-static">'.$q->getMessage().'</div>';
                        continue;
                       }
                        if(count($elSqlVars)>0)
                        $elSqlVars=$elSqlVars[0];

                        foreach($elSqlVars as $k=>$v){
                            $el->rendered_data=str_replace('{'.$k.'}',$v,$el->rendered_data);
                        }

                        foreach($global_sql_vars as $k=>$v){
                            $el->rendered_data=str_replace('{'.$k.'}',$v,$el->rendered_data);
                        }
                        foreach($global_test_vars as $k=>$v){
                            $el->rendered_data=str_replace('{'.$k.'}',$v,$el->rendered_data);
                        }

                    }

                    if($el->type=='table'){
                        $el->rendered_data=Notification::htmltable(json_decode($el->data),$global_test_vars);
                    }

                
                   
                }
            


                
                if($currentHeader!=null){
                   // $currentHeader->rendered_data=$currentHeader->html;
                    $currentHeaderSql=$currentHeader->sql;
                    if(trim($currentHeaderSql)!=''){
                        foreach($global_test_vars as $k=>$v){
                        
                            $currentHeaderSql=str_replace('{'.$k.'}',$v,$currentHeaderSql);
                            
                        }
                        try{
                                $header_sql_vars=DB::select($currentHeaderSql);

                                if(count($header_sql_vars)>0){
                                        $header_sql_vars=$header_sql_vars[0];
                                        foreach($header_sql_vars as $k=>$v){
                                            $currentHeader->rendered_data=str_replace('{'.$k.'}',$v,$currentHeader->rendered_data);
                                        }
                                        foreach($global_test_vars as $k=>$v){
                                            $currentHeader->rendered_data=str_replace('{'.$k.'}',$v,$currentHeader->rendered_data);
                                        }
                                    }

                            }catch(\Illuminate\Database\QueryException $q){
                                return response($q->getMessage(),509);
                        }
                    }
                }

                
                if($currentFooter!=null){
                //$currentFooter->rendered_data=$currentFooter->html;
                
                $currentFooterSql=$currentFooter->sql;
                if(trim($currentFooterSql)!=''){
                    foreach($global_test_vars as $k=>$v){
                    
                        $currentFooterSql=str_replace('{'.$k.'}',$v,$currentFooterSql);
                        
                    }
                    try{
                            $footer_sql_vars=DB::select($currentFooterSql);

                            if(count($footer_sql_vars)>0){
                                    $footer_sql_vars=$footer_sql_vars[0];
                                    foreach($footer_sql_vars as $k=>$v){
                                        $currentFooter->rendered_data=str_replace('{'.$k.'}',$v,$currentFooter->rendered_data);
                                    }
                                    foreach($global_test_vars as $k=>$v){
                                        $currentFooter->rendered_data=str_replace('{'.$k.'}',$v,$currentFooter->rendered_data);
                                    }
                                }

                        }catch(\Illuminate\Database\QueryException $q){
                            return response($q->getMessage(),509);
                        }
                        }
                }
            }
        

            return response()->json(array('elements'=>$elements,'currentHeader'=>$currentHeader,'currentFooter'=>$currentFooter));
        }else{
            return response('Template id est requis',509);
        }
    }

    public function reposHtmlTemplateElement(Request $request){
        $template_id=$request->post('template_id');
        $payload=$request->post('payload');

        $user=Auth::user();
        if($template_id>0){
            $htmltemplate=Htmltemplate::find($template_id);
            if($htmltemplate==null)
            return response('Template non trouvée',509);
            if($htmltemplate->affiliate_id!=$user->affiliate_id)
            return response('Impossible de sauvegarder. Le template n\'est pas dans la même affiliée que l\'utilisateur',509);

            $element=$payload['el'];
            $sibling_id=$payload['sibling_id'];
            $pos=$payload['pos'];

            $elements=HtmltemplateElement::where('htmltemplate_id','=',$template_id)->whereNull('deleted_at')->orderBy('position','asc')->get();
            $posoffset=0;
            $newpos=0;
           
            foreach( $elements as $el){
            
                if($el->id==$sibling_id){
                        if($pos=='before'){
                            $newpos=$el->position;
                            $el->position= $el->position+1;
                        }
                        if($pos=='after'){
                            $newpos=$el->position+1;
                        }
                        $currentel=HtmltemplateElement::find($element['id']);
                        if($currentel==null){
                            return response('L\'élément a positioner non trouvée',509);
                        }
                        if($currentel->htmltemplate_id!=$template_id)
                        return response('Erreur template id',509);

                        $currentel->position=$newpos;
                        $currentel->save();
                        $posoffset=1;
       
                }else if($el->id!=$element['id']){
                        $el->position= $el->position+$posoffset;
                
                }
                
                $el->save();
            }
        
            $elements=HtmltemplateElement::where('htmltemplate_id','=',$template_id)->whereNull('deleted_at')->orderBy('position','asc')->get();
            $count=0;
            foreach( $elements as $el){
                $count++;
                $el->position= $count;
                $el->save();
            }
            return response()->json(array('reposition'=>'ok'));
        }else{
            return response('Template id est requis',509);
        }
    
    }

    public function SaveHf(Request $request){
            $hf=$request->post('payload');
            $user=Auth::user();
            if($hf['type']=='header'){
                $hfobj=HtmltemplateHeader::find($hf['id']);
                if(  $hf['id']==null){
                    $hfobj=new HtmltemplateHeader();
                    $hfobj->affiliate_id=$user->affiliate_id;
                }
            }else if($hf['type']=='footer'){
                $hfobj=HtmltemplateFooter::find($hf['id']);
                if(  $hf['id']==null){
                    $hfobj=new HtmltemplateFooter();
                    $hfobj->affiliate_id=$user->affiliate_id;
                }
            }else{
                return response('Impossible de sauvegarder. Type de l\'objet inconnu',509);
            }
           
            if($user->affiliate_id!=$hfobj->affiliate_id&&$hfobj!=null)
            return response('Impossible de sauvegarder. '.($hf['type']=='header'?'L\'en-tête de page':$hf['type']=='footer'?'Le pied de page':'l\'object').' n\'est pas dans la même affiliée que l\'utilisateur',509);

            if($hfobj==null)
            if($hf['type']=='header'){
                $hfobj=new HtmltemplateHeader();
            }else{
                $hfobj=new HtmltemplateFooter();
            }
           
         
           $hfobj->affiliate_id=$user->affiliate_id;
           $hfobj->name=$hf['name'];
           $hfobj->height=$hf['height'];
           $hfobj->html=$hf['html'];
           $hfobj->sql=$hf['sql'];
           $hfobj->save();
    }

    public function generatePdfTest(Request $request){

        $ht=Htmltemplate::find($request->id);
        if($ht->type!='pdf')
        return response ('Génération d\'un fichier pdf avec un type de template invalide. Le type de template actuel est "'.$ht->type."'",509);
        $user=Auth::user();
        if($user->affiliate_id!=$ht->affiliate_id&&$ht!=null)
        return response ('Impossible de générer le pdf. Le template n\'est pas dans la même affiliée que l\'utilisateur',509);
       return $this->renderPDF(Notification::add($ht->name,json_decode($ht->global_test_vars,true),1));
    }

    public function generateEmailTest(Request $request){
        $ht=Htmltemplate::find($request->post('template_id'));
        if($ht->type!='email')
        return response ('Envoie email avec un type de template invalide. Le type de template actuel est "'.$ht->type."'",509);
        $user=Auth::user();
        if($ht==null)
        return response ('Template non trouvé',509);

        if($ht!=null&&$user->affiliate_id!=$ht->affiliate_id)
        return response ('Impossible de générer l\'email. Le template n\'est pas dans la même affiliée que l\'utilisateur',509);
        
        
       $notification= Notification::add($ht->name,json_decode($ht->global_test_vars,true),1,$ht->test_email);
       $notification->send();

    }

    public function saveGlobalCss(Request $request){
        $globalcss=$request->post('globalcss');
        DB::table('settings')->updateOrInsert(['key'=>'lcdt.htmltemplate_global_css'],
        [
            'display_name'=>'Htmltemplate global css',
            'value'=>$globalcss,
            'order'=>9,
            'group'=>'LCDT'
        ]);
    }
    public function generatePdf(Request $request){
        $notification=Notification::where('pdf_uuid','=',$request->uuid)->first();
        if($notification==null)
        return response ('Erreur, le document pdf n\'a pas été trouvé',509);
       

       return $this->renderPDF($notification);


    }


    public function renderPDF(Notification $notification){
        $ht=Htmltemplate::find($notification->template);
        $headerH=$ht->margin_top;
        $footerH=$ht->margin_bottom;
        if($ht->htmltemplate_header_id>0){
            $h=HtmltemplateHeader::find($ht->htmltemplate_header_id);
            $headerH=$h->height;
        }
        if($ht->htmltemplate_footer_id>0){
            $f=HtmltemplateFooter::find($ht->htmltemplate_footer_id);
            $footerH=$f->height;
        }
        $notification->downloaded_at=date('Y-m-d H:i:s');
        $notification->save();
        return SnappyPdf::loadHTML($notification->html)->setPaper('a4')
        ->setOrientation('portrait')->setOption('margin-top',$headerH.$ht->measuringunit)->setOption('margin-bottom', $footerH.$ht->measuringunit)->setOption('margin-left', $ht->margin_left.$ht->measuringunit)->setOption('margin-right', $ht->margin_right.$ht->measuringunit)
  
        ->setOption('header-html',$notification->html_header)
        ->setOption('footer-html',$notification->html_footer)
        ->setOption('disable-smart-shrinking',true)
       
        ->setOption('header-line',false)
        ->setOption('header-spacing',0)
        ->setOption('encoding', 'UTF-8')
        //->setOption('footer-right','Page [page] sur [toPage]')
        ->setOption('footer-right','[page] / [toPage]')
        ->download($notification->pdf_filename.'.pdf');
    }


    public function duplicateRow(Request $request){
            $type=$request->post('type');
            $id=$request->post('id');
            $name=$request->post('name');
            $user=Auth::user();
            $o=null;
            if($type=="template")
                $o=Htmltemplate::find($id);
            
            if($type=="header")
                $o=HtmltemplateHeader::find($id);
            
            if($type=="footer")
                $o=HtmltemplateFooter::find($id);
            
                if($o==null)
                    return response ('Erreur, objet non trouvé. Impossible de dupliquer',509);
                
                if($o->affiliate_id!=$user->affiliate_id)
                    return response ('Erreur,L\'objet n\'est pas dans la même affiliée que l\'utilisateur. Impossible de dupliquer',509);

        $new_o=$o->replicate();
        $new_o->created_at = now();
        $new_o->name=$name;
        try{
        $new_o->save();
        }catch(Exception $e){
            return response ($e->getMessage(),509);
        }
        $new_o->fresh();

        //only if type is template

        if($type=='template'){
            $elements=$o->elements()->get();
     
            foreach($elements as $element){
         
                $new_element=$element->replicate();
                $new_element->created_at = now();
                $new_element->htmltemplate_id=$new_o->id;
                $new_element->save();
            }
        }

        return response()->json($new_o);

    }
    public function deleteRow(Request $request){
        $type=$request->post('type');
        $id=$request->post('id');
        $user=Auth::user();
        $o=null;
        if($type=="template")
            $o=Htmltemplate::find($id);
        
        if($type=="header")
            $o=HtmltemplateHeader::find($id);
        
        if($type=="footer")
            $o=HtmltemplateFooter::find($id);
        
            if($o==null)
                return response ('Erreur, objet non trouvé. Impossible de supprimer',509);
            
            if($o->affiliate_id!=$user->affiliate_id)
                return response ('Erreur,L\'objet n\'est pas dans la même affiliée que l\'utilisateur. Impossible de supprimer',509);
            $o->delete();
            $o->refresh();
            $o->delete=$o->deleted_at;
            $o->save();
    }


    public function runNotificationCron(Request $request){
        $unsentNotifcations=Notification::where('typeNotification','!=','PDF')->where('sent','=',0)->get();

        foreach($unsentNotifcations  as $notification){
            $notification->send();
        }
    }
}


