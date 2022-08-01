<?php

namespace App\Http\Controllers;

use App\Models\CampagneCategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class LcdtAdminController extends Controller{
    public function getTextPos(Request $request){
        $id = $request->post('id');

        $cc=CampagneCategory::find($id);

        if($cc==null){
            return response('Campagne Category not found',509);  
        }
        $F=json_decode($cc->fields);
        
        $FIELDS=array( 
            "Nom_agence",
            "Telephone_agence" ,
            "Email_agence" ,
            "Adresse_agence",
            "CP_agence",
            "Ville_agence",
            "Page_agence",
            "Linkedin_agence",
            "Prenom_dirigeant",
            "Nom_dirigeant",
            "Email_dirigeant",
            "Portable_dirigeant",
            "STATUS",
            "SIRET",
            "APE",
            "TVA",
        );
        $FIELDS_DEF=array();
        foreach($FIELDS as $FIELD){
            $fname=str_replace('_',' ',$FIELD);
           $FIELDS_DEF[$FIELD]=array(
            'name'=>($fname=='Prenom dirigeant'?'Prénom dirigeant':($fname=='Telephone agence'?'Téléphone agence':$fname)),
            'active'=>isset($F->{$FIELD})?$F->{$FIELD}->active:0,
            'x'=>isset($F->{$FIELD})?$F->{$FIELD}->x:'10',
            'y'=>isset($F->{$FIELD})?$F->{$FIELD}->y:'10',
            'color'=>isset($F->{$FIELD})?$F->{$FIELD}->color:'#FFFFFF',
            'size'=>isset($F->{$FIELD})?$F->{$FIELD}->size:'18',
            'font'=>isset($F->{$FIELD})?$F->{$FIELD}->font:'arial',
           );
        }
       

        return response()->json([
            'FIELDS_DEF'=>  $FIELDS_DEF,
            'cc'=>$cc,
            'post'=>$request->all(),
        ]);

    }


    public function updateTextPos(Request $request){
        $id = $request->post('id_cc');
        $FIELDS_DEf= $request->post('fieldsdef');
        $cc=CampagneCategory::find($id);

        if($cc==null){
            return response('Campagne Category not found',509);  
        }
        $cc->fields= $FIELDS_DEf;
        $cc->save();


        return response()->json([
            'updated'=>1
        ]);
    }

}
