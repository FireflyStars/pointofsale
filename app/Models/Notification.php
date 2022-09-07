<?php

namespace App\Models;

use App\Mail\NotificationMail;
use Exception;
use Facade\FlareClient\Stacktrace\Stacktrace;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class Notification extends Model
{
    use HasFactory;



    public static function add($template_name,$parameters=[],$test=0,$email='',$phone=''){
            $ht=Htmltemplate::where('name','=',$template_name)->whereNull('deleted_at')->first();
            if($ht==null)
            throw new \Exception("Htmltemplate ".$template_name." not found.");

            $globalvars=json_decode($ht->global_test_vars);
            
            $compiled_vars=[];
            foreach($globalvars as $varname=>$v){
                    if(!key_exists($varname,$parameters))
                    throw new \Exception("Parameter $varname is missing.");
                    $compiled_vars[$varname]=$parameters[$varname];
            }

            if($ht->qrcode==1&&!key_exists('order_id',$parameters))
            throw new \Exception("Missing order_id in parameters. The template contains has a QRCODE.");


            

            $notification=new Notification();
            $notification->TypeNotification=strtoupper($ht->type);
            $notification->template=$ht->id;
            $notification->Parametres=json_encode($compiled_vars);
            $notification->test=$test;
            $notification->Email=$email;
            $notification->Phone=$phone;
            $notification->pdf_filename=$notification->TypeNotification=='PDF'?Notification::compilePdfFilename($ht,$parameters):'';
            $notification->email_object=$notification->TypeNotification=='EMAIL'?Notification::compileEmailSubject($ht,$parameters):'';
            $notification->qrcode=isset($parameters['order_id'])&&$ht->qrcode==1?'LCDT-'.$parameters['order_id']:'';

             //compile main content
             $global_sql_vars=[];
             $notification->html=Notification::htmlbody('<div></div>'.Notification::getQrCodeImg($notification->qrcode).Notification::compileHtml($ht,$parameters,$global_sql_vars),$notification->TypeNotification=='PDF');
 
             //compile header
            if($ht->htmltemplate_header_id>0){
                $h=HtmltemplateHeader::find($ht->htmltemplate_header_id);
                if($h==null){
                    throw new \Exception("Htmltemplate header id: $ht->htmltemplate_header_id specfied but could not be loaded.");
                }
                $notification->html_header=Notification::compileHeaderFooter($ht,$h,$parameters,$global_sql_vars, $notification->TypeNotification=='PDF');
            }

            //compile footer
            if($ht->htmltemplate_footer_id>0){
                $f=HtmltemplateFooter::find($ht->htmltemplate_footer_id);
                if($f==null){
                    throw new \Exception("Htmltemplate footer id: $ht->htmltemplate_footer_id specfied but could not be loaded.");
                }
                $notification->html_footer=Notification::compileHeaderFooter($ht,$f,$parameters,$global_sql_vars,$notification->TypeNotification=='PDF');
            }

   

            $notification->save();
            $notification->refresh();

     
           return $notification;

    }

    public static function compilePdfFilename($htmltemplate,$parameters){

        $global_sql_vars=[];
        $pdf_filename=$htmltemplate->pdf_filename_format;

            
        $global_sql=$htmltemplate->global_sql;
        foreach($parameters as $k=>$v){
         
            $global_sql=str_replace('{'.$k.'}',$v,$global_sql);
        }
        
        $global_sql_vars=DB::select($global_sql);
   
        
        if(count($global_sql_vars)>0){
            $global_sql_vars=$global_sql_vars[0];
            
            foreach($global_sql_vars as $k=>$v){
           
                $pdf_filename=str_replace('{'.$k.'}',$v,$pdf_filename);
       
            }
        }
  
        foreach($parameters as $k=>$v){
            $pdf_filename=str_replace('{'.$k.'}',$v,$pdf_filename);
        }
       
        return $pdf_filename;

    }

    public static function compileEmailSubject($htmltemplate,$parameters){
        $global_sql_vars=[];
        $subject=$htmltemplate->email_subject_format;

            
        $global_sql=$htmltemplate->global_sql;
        foreach($parameters as $k=>$v){
         
            $global_sql=str_replace('{'.$k.'}',$v,$global_sql);
        }
        
        $global_sql_vars=DB::select($global_sql);
   
        
        if(count($global_sql_vars)>0){
            $global_sql_vars=$global_sql_vars[0];
            
            foreach($global_sql_vars as $k=>$v){
           
                $subject=str_replace('{'.$k.'}',$v,$subject);
       
            }
        }
  
        foreach($parameters as $k=>$v){
            $subject=str_replace('{'.$k.'}',$v,$subject);
        }
       
        return $subject;

    }
    public static function removeUnRenderedToken($html){
        $output_array=[];
        preg_match_all('/{(.*?)}/',$html, $output_array);
        if(!empty($output_array))
        foreach($output_array[0] as $token){
            $html=str_replace($token,'',$html);
        }
        return $html;
    }
    public static function compileHeaderFooter($ht,$hfobj,$parameters,$global_sql_vars,$ispdf){

        $html=$hfobj->html;

        $currentHFSql=$hfobj->sql;
        if(trim($currentHFSql)!=''){
            foreach($global_sql_vars as $k=>$v){
                $currentHFSql=str_replace('{'.$k.'}',$v,$currentHFSql);
            }
            foreach($parameters as $k=>$v){
            
                $currentHFSql=str_replace('{'.$k.'}',$v,$currentHFSql);
                
            }
            $hf_sql_vars=DB::select($currentHFSql);

            if(count($hf_sql_vars)>0){
                    $hf_sql_vars=$hf_sql_vars[0];
                    foreach($hf_sql_vars as $k=>$v){
                        $html=str_replace('{'.$k.'}',$v,$html);
                    }
                 
            }

                
        }
        foreach($global_sql_vars as $k=>$v){
            $html=str_replace('{'.$k.'}',$v,$html);
        }
        foreach($parameters as $k=>$v){
            $html=str_replace('{'.$k.'}',$v,$html);
        }

        return Notification::htmlbody('<div style="display:block;height:'.$hfobj->height.$ht->measuringunit.'">'.Notification::removeUnRenderedToken($html).'</div>',$ispdf);

    }

    public static function compileHtml($htmltemplate,$parameters,&$global_sql_vars=[]){
        $html="";

        $elements=HtmltemplateElement::where('htmltemplate_id','=',$htmltemplate->id)->orderBy('position')->get();
   
        

   
            
            $global_sql=$htmltemplate->global_sql;
            foreach($parameters as $k=>$v){
             
                $global_sql=str_replace('{'.$k.'}',$v,$global_sql);
            }
            
            $global_sql_vars=DB::select($global_sql);
       
            if(count($global_sql_vars)>0)
            $global_sql_vars=$global_sql_vars[0];
           

            foreach($elements as $el){
                $data=$el->data;
                
                if(trim($el->sql)!=''){
                    $elSql=$el->sql;
                    foreach($global_sql_vars as $k=>$v){
                        $elSql=str_replace('{'.$k.'}',$v,$elSql);
                    }

                    foreach($parameters as $k=>$v){
                        $elSql=str_replace('{'.$k.'}',$v,$elSql);
                    }
                 
                    $elSqlVars=DB::select($elSql);
                  
                    if(count($elSqlVars)>0)
                    $elSqlVars=$elSqlVars[0];

                    foreach($elSqlVars as $k=>$v){
                        $data=str_replace('{'.$k.'}',$v, $data);
                    }
                    if(!empty($global_sql_vars))
                    foreach($global_sql_vars as $k=>$v){
                        $data=str_replace('{'.$k.'}',$v, $data);
                    }
                    if(!empty($parameters))
                    foreach($parameters as $k=>$v){
                        $data=str_replace('{'.$k.'}',$v, $data);
                    }
                    
                    $html.=($el->type=="address"?'<div style="position:absolute;right: 14px;top: 126px;width:282px;">'.$data.'</div>':$data);
                }else if(in_array($el->type,['html','address'])){
                    if(!empty($global_sql_vars))
                    foreach($global_sql_vars as $k=>$v){
                        $data=str_replace('{'.$k.'}',$v, $data);
                    }
                    if(!empty($parameters))
                    foreach($parameters as $k=>$v){
                        $data=str_replace('{'.$k.'}',$v, $data);
                    }
                    $html.=($el->type=="address"?'<div style="position:absolute;right: 14px;top: 126px;width:282px;">'.$data.'</div>':$data);
                }
                if($el->type=='pagebreak'){
                    $html.='<div style="page-break-before: always;"></div>';
                }
                if($el->type=='table'){
                    $html.=Notification::htmltable(json_decode($el->data),$parameters);
                }
               
            }
        


        return Notification::removeUnRenderedToken($html);
    }

    public static function htmlbody($str,$ispdf=true){
        if(!$ispdf)
        return '<div style="position:relative;">'.str_replace('{APP_URL}',getenv('APP_URL'),$str).'<div>';


        return '<!DOCTYPE  html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"><link rel="stylesheet" href="'.getenv('APP_URL').'/css/htmltemplate.css"></head><style>'.setting('lcdt.htmltemplate_global_css').'</style><body><div class="paper">'.str_replace('{APP_URL}',getenv('APP_URL'),$str).'</div></body></html>';
    }


    public static function htmltable($a,$global_sql_variables,$addtional_vars=[]){
        
        $local_variables=[];
        if(gettype($a)!='object'){
            $statictable=$a;
            foreach($addtional_vars as $k=>$id){
                $statictable=str_replace('{'.$k.'}',$id, $statictable);
            }
    
            foreach($global_sql_variables as $k=>$id){
                $statictable=str_replace('{'.$k.'}',$id, $statictable);
            }
            return $statictable;
        }
     
   
        $html_table='<table id="'.$a->table_id.'" '.Notification::attrs($a).'>';
        //thead
        $html_table.='<thead  '.Notification::attrs($a->thead).'>';
        foreach($a->thead->tr as $theadtr){
            $html_table.='<tr '.Notification::attrs($theadtr).'>';
            foreach($theadtr->th as $th){
                $html_table.='<th  '.Notification::attrs($th).'>'.(isset($th->name)?$th->name:'').'</th>';
            }
            $html_table.='</tr>';
        }
        
        $html_table.='</thead>';
        //end thead

        //tbody
        $html_table.='<tbody  '.Notification::attrs($a->tbody).'>';
        foreach($a->tbody->prefix as $prefix){
            foreach($global_sql_variables as $k=>$v){
                $prefix=str_replace('{'.$k.'}',$v,$prefix);
            }
            $html_table.=$prefix;
            
        }
        $sql=$a->sql;
 
        foreach($addtional_vars as $k=>$id){
            $sql=str_replace('{'.$k.'}',$id, $sql);
        }

        foreach($global_sql_variables as $k=>$id){
            $sql=str_replace('{'.$k.'}',$id, $sql);
        }
        try{
            $data=DB::select($sql);
        }catch(\Illuminate\Database\QueryException $q){
            return '<div class="alert alert-danger position-static">'.$q->getMessage().'</div>';
        }
            if(!empty($data))
            foreach($data as $d){
            $html_table.='<tr '.Notification::attrs($a->tbody->tr).'>';

            foreach($a->thead->tr[0]->th as $th){
                if($th->type=='number')
                    if(!isset($local_variables['+'.$th->identifier])){
                        $local_variables['+'.$th->identifier]=0;
                    }else{
                        $local_variables['+'.$th->identifier]+=$d->{$th->identifier};
                    }

                
            }

                if(!isset($a->tbody->tr->td->table)){

              
                    foreach($a->thead->tr[0]->th as $th){
              
                        if($th->type=='table'){
                            $html_table.='<td '.Notification::attrs($a->tbody->tr->td).'>'.Notification::htmltable($th->table,$global_sql_variables,$d).'</td>';
                        }else{
                            $html_table.='<td '.Notification::attrs($a->tbody->tr->td).'>'.(property_exists($th,'prefix')?$th->prefix:'').($th->type=="empty"?'':$d->{$th->identifier}).(property_exists($th,'suffix')?$th->suffix:'').'</td>';
                        }
                    }
                }else{
                    $html_table.='<td colspan="'.count($a->thead->tr[0]->th).'" '.Notification::attrs($a->tbody->tr->td).'>'.Notification::htmltable($a->tbody->tr->td->table,$global_sql_variables,$d).'</td>';
                }

            $html_table.='</tr>';
            }

        foreach($a->tbody->suffix as $suffix){
            foreach($local_variables as $k=>$v){
                $suffix=str_replace('{'.$k.'}',$v,$suffix);
            }
            $html_table.=$suffix;
        }


        $html_table.='</tbody>';
        //end tbody

        $html_table.='</table>';
        return $html_table;
    }

    public static function attrs($obj){
        return Notification::attr('style',$obj).' '.Notification::attr('class',$obj);
    }
    public static function attr($attribute_name,$obj){
        if(isset($obj->{$attribute_name})&&trim($obj->{$attribute_name})!='')
        return $attribute_name.'="'.$obj->{$attribute_name}.'"';

        return '';
    }

    public static function getQrCodeImg($str){
     
        if($str=='')return '';
        $qrcode=getenv('BARCODE_URL').'/barcode.php?s=qr&wq=0&d='.$str;
        return '<img  class="qrcode" src="'.$qrcode.'" style="position:absolute; right:20px;top:0px;"/>';
    }

    public static function getPdfLink($uuid){
        return env('APP_URL').'/generation-doc-pdf/'.$uuid;
    }

    public function send(){
        if($this->id>0&&$this->typeNotification=='EMAIL'){
            try{
                Mail::to($this->email)->send(new NotificationMail($this));
                $this->error_msg=null;
                $this->sent=1;
                $this->sent_on=date('Y-m-d H:i:s');
                $this->save();
            }catch(Exception $e){
                $this->error_msg=$e->getMessage();
                $this->sent=0;
                $this->sent_on=null;
                $this->save();
                return false;
            }
        }

        return true;
    }
}
