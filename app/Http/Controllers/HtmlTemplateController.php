<?php

namespace App\Http\Controllers;

use App\Models\HtmlTemplate;
use App\Models\HtmltemplateElement;
use App\Models\HtmltemplateFooter;
use App\Models\HtmltemplateHeader;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Doctrine\DBAL\Query\QueryException;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PDO;
use stdClass;

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

    public function index(Request $request){

        $html = '<!DOCTYPE  html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Commande Client</title>
    <meta name="author" content="cdt7u001"/>
    <style type="text/css"> * {
        margin: 0;
        padding: 0;
        text-indent: 0;
    }

    h1 {
        color: black;
        font-family: Arial, sans-serif;
        font-style: normal;
        font-weight: bold;
        text-decoration: none;
        font-size: 12pt;
    }

    .s1 {
        color: black;
        font-family: Arial, sans-serif;
        font-style: normal;
        font-weight: normal;
        text-decoration: none;
        font-size: 9.5pt;
    }

    .s2 {
        color: black;
        font-family: Arial, sans-serif;
        font-style: normal;
        font-weight: bold;
        text-decoration: underline;
        font-size: 9.5pt;
    }

    h3 {
        color: black;
        font-family: Arial, sans-serif;
        font-style: normal;
        font-weight: bold;
        text-decoration: none;
        font-size: 9.5pt;
    }

    .s3 {
        color: black;
        font-family: Arial, sans-serif;
        font-style: normal;
        font-weight: bold;
        text-decoration: none;
        font-size: 12pt;
    }

    .s4 {
        color: black;
        font-family: Arial, sans-serif;
        font-style: normal;
        font-weight: normal;
        text-decoration: none;
        font-size: 9.5pt;
    }

    .s5 {
        color: black;
        font-family: "Segoe UI", sans-serif;
        font-style: normal;
        font-weight: normal;
        text-decoration: none;
        font-size: 9pt;
    }

    .s6 {
        color: black;
        font-family: "Times New Roman", serif;
        font-style: normal;
        font-weight: normal;
        text-decoration: none;
        font-size: 9pt;
    }

    .s7 {
        color: #434343;
        font-family: "Segoe UI", sans-serif;
        font-style: normal;
        font-weight: normal;
        text-decoration: none;
        font-size: 8pt;
    }

    .s8 {
        color: #434343;
        font-family: "Times New Roman", serif;
        font-style: normal;
        font-weight: normal;
        text-decoration: none;
        font-size: 8pt;
    }

    .h4 {
        color: black;
        font-family: Calibri, sans-serif;
        font-style: normal;
        font-weight: bold;
        text-decoration: none;
        font-size: 6.5pt;
    }

    .s9 {
        color: black;
        font-family: "Times New Roman", serif;
        font-style: normal;
        font-weight: normal;
        text-decoration: none;
        font-size: 6.5pt;
    }

    .p, p {
        color: black;
        font-family: Calibri, sans-serif;
        font-style: normal;
        font-weight: normal;
        text-decoration: none;
        font-size: 6.5pt;
        margin: 0pt;
    }

    .s10 {
        color: black;
        font-family: Calibri, sans-serif;
        font-style: normal;
        font-weight: normal;
        text-decoration: none;
        font-size: 11pt;
    }

    .s11 {
        color: black;
        font-family: "Times New Roman", serif;
        font-style: normal;
        font-weight: normal;
        text-decoration: none;
        font-size: 11pt;
    }

    .s12 {
        color: black;
        font-family: Calibri, sans-serif;
        font-style: normal;
        font-weight: normal;
        text-decoration: none;
        font-size: 11pt;
        vertical-align: 4pt;
    }

    .s13 {
        color: black;
        font-family: "Times New Roman", serif;
        font-style: normal;
        font-weight: normal;
        text-decoration: none;
        font-size: 11pt;
        vertical-align: 4pt;
    }

    .s14 {
        color: black;
        font-family: Calibri, sans-serif;
        font-style: normal;
        font-weight: bold;
        text-decoration: none;
        font-size: 11pt;
    }

    .s15 {
        color: black;
        font-family: Calibri, sans-serif;
        font-style: normal;
        font-weight: normal;
        text-decoration: none;
        font-size: 11pt;
    }

    .s16 {
        color: black;
        font-family: "Times New Roman", serif;
        font-style: normal;
        font-weight: normal;
        text-decoration: none;
        font-size: 11pt;
    }

    .s17 {
        color: black;
        font-family: Calibri, sans-serif;
        font-style: normal;
        font-weight: bold;
        text-decoration: none;
        font-size: 12pt;
    }

    .s18 {
        color: black;
        font-family: "Times New Roman", serif;
        font-style: normal;
        font-weight: normal;
        text-decoration: none;
        font-size: 12pt;
    }

    .s19 {
        color: black;
        font-family: Arial, sans-serif;
        font-style: normal;
        font-weight: bold;
        text-decoration: underline;
        font-size: 9.5pt;
    }

    .s20 {
        color: black;
        font-family: Arial, sans-serif;
        font-style: normal;
        font-weight: bold;
        text-decoration: underline;
        font-size: 12pt;
    }

    .s21 {
        color: black;
        font-family: Arial, sans-serif;
        font-style: normal;
        font-weight: normal;
        text-decoration: none;
        font-size: 12pt;
    }

    .s22 {
        color: black;
        font-family: Arial, sans-serif;
        font-style: normal;
        font-weight: normal;
        text-decoration: none;
        font-size: 9.5pt;
    }

    h2 {
        color: black;
        font-family: Arial, sans-serif;
        font-style: normal;
        font-weight: bold;
        text-decoration: none;
        font-size: 11pt;
    }

    .s23 {
        color: black;
        font-family: Calibri, sans-serif;
        font-style: normal;
        font-weight: bold;
        text-decoration: underline;
        font-size: 12pt;
    }

    .s24 {
        color: black;
        font-family: "Times New Roman", serif;
        font-style: normal;
        font-weight: normal;
        text-decoration: underline;
        font-size: 12pt;
    }

    li {
        display: block;
    }

    #l1 {
        padding-left: 0pt;
    }

    #l1 > li > *:first-child:before {
        content: "- ";
        color: black;
        font-family: Calibri, sans-serif;
        font-style: normal;
        font-weight: normal;
        text-decoration: none;
        font-size: 11pt;
    }

    li {
        display: block;
    }

    #l2 {
        padding-left: 0pt;
        counter-reset: d1 1;
    }

    #l2 > li > *:first-child:before {
        counter-increment: d1;
        content: counter(d1, decimal) ". ";
        color: black;
        font-family: Arial, sans-serif;
        font-style: normal;
        font-weight: bold;
        text-decoration: none;
        font-size: 11pt;
    }

    #l2 > li:first-child > *:first-child:before {
        counter-increment: d1 0;
    }

    table, tbody {
        vertical-align: top;
        overflow: visible;
    }
    </style>
</head>
<body><p style="text-indent: 0pt;text-align: left;"><br/></p>
<h1 style="padding-left: 4pt;text-indent: 0pt;text-align: left;">SCI POTERNE</h1>
<p style="text-indent: 0pt;text-align: left;"><br/></p>
<p class="s1" style="padding-left: 4pt;text-indent: 0pt;text-align: left;">12, RUE PORTE POTERNE</p>
<p style="text-indent: 0pt;text-align: left;"><br/></p>
<p class="s1" style="padding-left: 4pt;text-indent: 0pt;text-align: left;">56000 VANNES</p>
<p style="text-indent: 0pt;text-align: left;"/>
<p class="s2" style="padding-top: 4pt;padding-left: 9pt;text-indent: 0pt;text-align: left;">Suivi par</p>
<h3 style="padding-top: 4pt;padding-left: 9pt;text-indent: 0pt;text-align: left;">EPIS GUILLAUME</h3>
<p style="text-indent: 0pt;text-align: left;"><br/></p>
<p class="s2" style="padding-left: 9pt;text-indent: 0pt;text-align: left;">Adresse du chantier</p>
<p style="text-indent: 0pt;text-align: left;"><br/></p>
<p class="s1" style="padding-top: 4pt;padding-left: 293pt;text-indent: 0pt;text-align: center;">FRANCE</p>
<p style="text-indent: 0pt;text-align: left;"><br/></p>
<p class="s2" style="padding-top: 4pt;padding-left: 9pt;text-indent: 0pt;text-align: left;">Chantier</p>
<h3 style="padding-top: 4pt;padding-left: 9pt;text-indent: 0pt;line-height: 110%;text-align: left;">Réfection toiture +
    reprise ardoise + changement gouttières</h3>
<p class="s2" style="padding-top: 7pt;padding-left: 9pt;text-indent: 0pt;text-align: left;">Nature des travaux</p>
<p style="text-indent: 0pt;text-align: left;"><br/></p>
<table style="border-collapse:collapse;width:100%" cellspacing="0">
    <tr style="height:15pt">
        <td style="width:199pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s3" style="padding-left: 3pt;text-indent: 0pt;line-height: 14pt;text-align: left;">Prestation</p>
        </td>
        <td style="width:54pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s3"
               style="padding-left: 1pt;padding-right: 1pt;text-indent: 0pt;line-height: 14pt;text-align: center;">
                Quantite</p></td>
        <td style="width:40pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s3" style="padding-left: 4pt;text-indent: 0pt;line-height: 14pt;text-align: left;">Unite</p></td>
        <td style="width:39pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s3" style="padding-left: 9pt;text-indent: 0pt;line-height: 14pt;text-align: left;">Fait</p></td>
        <td style="width:235pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
    </tr>
    <tr style="height:16pt">
        <td style="width:199pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
        <td style="width:54pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="text-indent: 0pt;text-align: center;">1</p></td>
        <td style="width:40pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
        <td style="width:39pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
        <td style="width:235pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
    </tr>
</table>
<p style="text-indent: 0pt;text-align: left;"><br/></p>
<table style="border-collapse:collapse;width:100%" cellspacing="0">
    <tr style="height:18pt">
        <td style="width:567pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt"
            colspan="5"><p style="text-indent: 0pt;text-align: left;"><br/></p></td>
    </tr>
    <tr style="height:15pt">
        <td style="width:199pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-left: 3pt;text-indent: 0pt;text-align: left;">Installation et Repli</p></td>
        <td style="width:54pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="text-indent: 0pt;text-align: center;">1</p></td>
        <td style="width:40pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
        <td style="width:39pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
        <td style="width:235pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-left: 3pt;text-indent: 0pt;text-align: left;">Installation et Repli</p></td>
    </tr>
    <tr style="height:16pt">
        <td style="width:199pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s3" style="padding-left: 45pt;text-indent: 0pt;line-height: 14pt;text-align: left;">Installation
                et Repli</p></td>
        <td style="width:54pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
        <td style="width:40pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
        <td style="width:39pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
        <td style="width:235pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
    </tr>
    <tr style="height:14pt">
        <td style="width:199pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-left: 3pt;text-indent: 0pt;text-align: left;">Installation/ Repli Chantier</p>
        </td>
        <td style="width:54pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="text-indent: 0pt;text-align: center;">1</p></td>
        <td style="width:40pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-left: 3pt;padding-right: 3pt;text-indent: 0pt;text-align: center;">ens</p></td>
        <td style="width:39pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt"
            rowspan="17"><p style="text-indent: 0pt;text-align: left;"><br/></p></td>
        <td style="width:235pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
    </tr>
    <tr style="height:14pt">
        <td style="width:199pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4"
               style="padding-top: 1pt;padding-left: 3pt;text-indent: 0pt;line-height: 10pt;text-align: left;">Pose</p>
        </td>
        <td style="width:54pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 1pt;text-indent: 0pt;line-height: 10pt;text-align: center;">1</p></td>
        <td style="width:40pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4"
               style="padding-top: 1pt;padding-left: 3pt;padding-right: 3pt;text-indent: 0pt;line-height: 10pt;text-align: center;">
                ens</p></td>
        <td style="width:235pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4"
               style="padding-top: 1pt;padding-left: 3pt;text-indent: 0pt;line-height: 10pt;text-align: left;">
                Approvisionnement du matériel et des matériaux sur</p></td>
    </tr>
    <tr style="height:12pt">
        <td style="width:199pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
        <td style="width:54pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
        <td style="width:40pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
        <td style="width:235pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-left: 3pt;text-indent: 0pt;line-height: 10pt;text-align: left;">site, compris
                trajet A/R</p></td>
    </tr>
    <tr style="height:12pt">
        <td style="width:199pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
        <td style="width:54pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
        <td style="width:40pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
        <td style="width:235pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-left: 3pt;text-indent: 0pt;line-height: 10pt;text-align: left;">Nettoyage fin
                de chantier y compris évacuation des</p></td>
    </tr>
    <tr style="height:12pt">
        <td style="width:199pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
        <td style="width:54pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
        <td style="width:40pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
        <td style="width:235pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-left: 3pt;text-indent: 0pt;line-height: 10pt;text-align: left;">déchets en
                décharge spécialisée et participation</p></td>
    </tr>
    <tr style="height:12pt">
        <td style="width:199pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
        <td style="width:54pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
        <td style="width:40pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
        <td style="width:235pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-left: 3pt;text-indent: 0pt;line-height: 10pt;text-align: left;">aux frais de
                traitement</p></td>
    </tr>
    <tr style="height:12pt">
        <td style="width:199pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
        <td style="width:54pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
        <td style="width:40pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
        <td style="width:235pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-left: 3pt;text-indent: 0pt;text-align: left;">Procédures de début / fin de
                chantier</p></td>
    </tr>
    <tr style="height:14pt">
        <td style="width:199pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-left: 3pt;text-indent: 0pt;text-align: left;">Location Manuscopique</p></td>
        <td style="width:54pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="text-indent: 0pt;text-align: center;">1</p></td>
        <td style="width:40pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-left: 3pt;padding-right: 3pt;text-indent: 0pt;text-align: center;">ens</p></td>
        <td style="width:235pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
    </tr>
    <tr style="height:14pt">
        <td style="width:199pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4"
               style="padding-top: 1pt;padding-left: 3pt;text-indent: 0pt;line-height: 10pt;text-align: left;">Pose</p>
        </td>
        <td style="width:54pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 1pt;text-indent: 0pt;line-height: 10pt;text-align: center;">6</p></td>
        <td style="width:40pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4"
               style="padding-top: 1pt;padding-left: 3pt;padding-right: 3pt;text-indent: 0pt;line-height: 10pt;text-align: center;">
                ens</p></td>
        <td style="width:235pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4"
               style="padding-top: 1pt;padding-left: 3pt;text-indent: 0pt;line-height: 10pt;text-align: left;">Location
                d&#39;un engin de levage de type</p></td>
    </tr>
    <tr style="height:12pt">
        <td style="width:199pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
        <td style="width:54pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
        <td style="width:40pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
        <td style="width:235pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-left: 3pt;text-indent: 0pt;line-height: 11pt;text-align: left;">manuscopique,
                compris transport A/R</p></td>
    </tr>
    <tr style="height:14pt">
        <td style="width:199pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-left: 3pt;text-indent: 0pt;text-align: left;">Echafaudage de pied</p></td>
        <td style="width:54pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="text-indent: 0pt;text-align: center;">1</p></td>
        <td style="width:40pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-left: 3pt;padding-right: 3pt;text-indent: 0pt;text-align: center;">ens</p></td>
        <td style="width:235pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
    </tr>
    <tr style="height:14pt">
        <td style="width:199pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4"
               style="padding-top: 1pt;padding-left: 3pt;text-indent: 0pt;line-height: 10pt;text-align: left;">Pose</p>
        </td>
        <td style="width:54pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 1pt;text-indent: 0pt;line-height: 10pt;text-align: center;">1</p></td>
        <td style="width:40pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4"
               style="padding-top: 1pt;padding-left: 3pt;padding-right: 3pt;text-indent: 0pt;line-height: 10pt;text-align: center;">
                ens</p></td>
        <td style="width:235pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4"
               style="padding-top: 1pt;padding-left: 3pt;text-indent: 0pt;line-height: 10pt;text-align: left;">Mise en
                place d&#39;un échafaudage de pied, y compris</p></td>
    </tr>
    <tr style="height:12pt">
        <td style="width:199pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
        <td style="width:54pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
        <td style="width:40pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
        <td style="width:235pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-left: 3pt;text-indent: 0pt;line-height: 10pt;text-align: left;">toutes
                sujétions et contrôle avant utilisation par un</p></td>
    </tr>
    <tr style="height:12pt">
        <td style="width:199pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
        <td style="width:54pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
        <td style="width:40pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
        <td style="width:235pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-left: 3pt;text-indent: 0pt;text-align: left;">installateur agréé.</p></td>
    </tr>
    <tr style="height:12pt">
        <td style="width:199pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
        <td style="width:54pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
        <td style="width:40pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
        <td style="width:235pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-left: 3pt;text-indent: 0pt;line-height: 10pt;text-align: left;">Prévoir
                réception avec l&#39;installateur du PV de</p></td>
    </tr>
    <tr style="height:12pt">
        <td style="width:199pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
        <td style="width:54pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
        <td style="width:40pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
        <td style="width:235pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-left: 3pt;text-indent: 0pt;line-height: 11pt;text-align: left;">Contrôle</p>
        </td>
    </tr>
    <tr style="height:16pt">
        <td style="width:199pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
        <td style="width:54pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="text-indent: 0pt;text-align: center;">1</p></td>
        <td style="width:40pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
        <td style="width:235pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
    </tr>
</table>
<p style="text-indent: 0pt;text-align: left;"><br/></p>
<table style="border-collapse:collapse;width: 100%" cellspacing="0">
    <tr style="height:18pt">
        <td style="width:567pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt"
            colspan="5"><p style="text-indent: 0pt;text-align: left;"><br/></p></td>
    </tr>
    <tr style="height:15pt">
        <td style="width:199pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-left: 3pt;text-indent: 0pt;text-align: left;">Sécurité</p></td>
        <td style="width:54pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="text-indent: 0pt;text-align: center;">1</p></td>
        <td style="width:40pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
        <td style="width:39pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
        <td style="width:235pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-left: 3pt;text-indent: 0pt;text-align: left;">Sécurité</p></td>
    </tr>
    <tr style="height:16pt">
        <td style="width:199pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s3"
               style="padding-left: 75pt;padding-right: 74pt;text-indent: 0pt;line-height: 14pt;text-align: center;">
                Sécurité</p></td>
        <td style="width:54pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
        <td style="width:40pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
        <td style="width:39pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
        <td style="width:235pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
    </tr>
    <tr style="height:15pt">
        <td style="width:199pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-left: 3pt;text-indent: 0pt;text-align: left;">Prestations</p></td>
        <td style="width:54pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="text-indent: 0pt;text-align: center;">1</p></td>
        <td style="width:40pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
        <td style="width:39pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
        <td style="width:235pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-left: 3pt;text-indent: 0pt;text-align: left;">Prestations</p></td>
    </tr>
    <tr style="height:15pt">
        <td style="width:199pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s3" style="padding-left: 66pt;text-indent: 0pt;line-height: 14pt;text-align: left;">
                Prestations</p></td>
        <td style="width:54pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
        <td style="width:40pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
        <td style="width:39pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
        <td style="width:235pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
    </tr>
    <tr style="height:49pt">
        <td style="width:199pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4"
               style="padding-left: 3pt;padding-right: 127pt;text-indent: 0pt;line-height: 144%;text-align: left;">
                Remaniage Pose</p></td>
        <td style="width:54pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-left: 2pt;padding-right: 1pt;text-indent: 0pt;text-align: center;">20</p>
            <p class="s4"
               style="padding-top: 4pt;padding-left: 2pt;padding-right: 1pt;text-indent: 0pt;text-align: center;">20</p>
        </td>
        <td style="width:40pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4"
               style="padding-left: 13pt;padding-right: 1pt;text-indent: 0pt;line-height: 144%;text-align: left;">m2
                m2</p></td>
        <td style="width:39pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
        <td style="width:235pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p>
            <p class="s4" style="padding-left: 3pt;text-indent: 0pt;line-height: 110%;text-align: left;">Remaniement d&#39;une
                couverture en ardoises comprenant : contrôle de l&#39;état du support (lattage),</p></td>
    </tr>
</table>
<p style="text-indent: 0pt;text-align: left;"><br/></p>
<table style="border-collapse:collapse;width:100%" cellspacing="0">
    <tr style="height:15pt">
        <td style="width:199pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s3" style="padding-left: 3pt;text-indent: 0pt;line-height: 14pt;text-align: left;">Prestation</p>
        </td>
        <td style="width:54pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s3" style="padding-left: 2pt;text-indent: 0pt;line-height: 14pt;text-align: left;">Quantite</p>
        </td>
        <td style="width:39pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s3" style="padding-left: 4pt;text-indent: 0pt;line-height: 14pt;text-align: left;">Unite</p></td>
        <td style="width:40pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s3" style="padding-left: 8pt;text-indent: 0pt;line-height: 14pt;text-align: left;">Fait</p></td>
        <td style="width:235pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
    </tr>
    <tr style="height:632pt">
        <td style="width:199pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p>
            <p class="s4"
               style="padding-left: 3pt;padding-right: 47pt;text-indent: 0pt;line-height: 145%;text-align: left;">
                Démoussage par pulvérisation Pose</p>
            <p style="text-indent: 0pt;text-align: left;"><br/></p>
            <p class="s4"
               style="padding-top: 7pt;padding-left: 3pt;text-indent: 0pt;line-height: 144%;text-align: left;">Réfection
                de la partie courante PS 120mm Dépose</p>
            <p style="text-indent: 0pt;text-align: left;"><br/></p>
            <p class="s4" style="padding-left: 3pt;text-indent: 0pt;text-align: left;">Pose</p></td>
        <td style="width:54pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p>
            <p class="s4" style="padding-left: 19pt;padding-right: 18pt;text-indent: 0pt;text-align: center;">60</p>
            <p class="s4"
               style="padding-top: 4pt;padding-left: 19pt;padding-right: 18pt;text-indent: 0pt;text-align: center;">
                60</p>
            <p style="text-indent: 0pt;text-align: left;"><br/></p>
            <p class="s4" style="padding-left: 19pt;padding-right: 18pt;text-indent: 0pt;text-align: center;">48</p>
            <p class="s4"
               style="padding-top: 4pt;padding-left: 19pt;padding-right: 18pt;text-indent: 0pt;text-align: center;">
                48</p>
            <p style="text-indent: 0pt;text-align: left;"><br/></p>
            <p class="s4" style="padding-left: 19pt;padding-right: 18pt;text-indent: 0pt;text-align: center;">48</p>
            <p style="text-indent: 0pt;text-align: left;"><br/></p>
            <p class="s4" style="padding-left: 19pt;padding-right: 18pt;text-indent: 0pt;text-align: center;">48</p>
            <p class="s4"
               style="padding-top: 4pt;padding-left: 19pt;padding-right: 18pt;text-indent: 0pt;text-align: center;">
                48</p>
            <p class="s4"
               style="padding-top: 4pt;padding-left: 19pt;padding-right: 18pt;text-indent: 0pt;text-align: center;">
                48</p>
            <p class="s4"
               style="padding-top: 4pt;padding-left: 19pt;padding-right: 18pt;text-indent: 0pt;text-align: center;">
                48</p>
            <p class="s4"
               style="padding-top: 4pt;padding-left: 19pt;padding-right: 18pt;text-indent: 0pt;text-align: center;">
                48</p>
            <p class="s4"
               style="padding-top: 4pt;padding-left: 19pt;padding-right: 18pt;text-indent: 0pt;text-align: center;">
                48</p>
            <p class="s4"
               style="padding-top: 4pt;padding-left: 19pt;padding-right: 18pt;text-indent: 0pt;text-align: center;">
                48</p>
            <p class="s4"
               style="padding-top: 4pt;padding-left: 19pt;padding-right: 18pt;text-indent: 0pt;text-align: center;">
                48</p>
            <p class="s4"
               style="padding-top: 4pt;padding-left: 19pt;padding-right: 18pt;text-indent: 0pt;text-align: center;">
                48</p>
            <p class="s4"
               style="padding-top: 4pt;padding-left: 19pt;padding-right: 18pt;text-indent: 0pt;text-align: center;">
                48</p>
            <p class="s4"
               style="padding-top: 4pt;padding-left: 19pt;padding-right: 18pt;text-indent: 0pt;text-align: center;">
                48</p>
            <p class="s4"
               style="padding-top: 4pt;padding-left: 19pt;padding-right: 18pt;text-indent: 0pt;text-align: center;">
                48</p>
            <p class="s4"
               style="padding-top: 4pt;padding-left: 19pt;padding-right: 18pt;text-indent: 0pt;text-align: center;">
                48</p>
            <p class="s4"
               style="padding-top: 4pt;padding-left: 19pt;padding-right: 18pt;text-indent: 0pt;text-align: center;">
                48</p>
            <p class="s4"
               style="padding-top: 4pt;padding-left: 19pt;padding-right: 18pt;text-indent: 0pt;text-align: center;">
                48</p>
            <p class="s4"
               style="padding-top: 4pt;padding-left: 19pt;padding-right: 18pt;text-indent: 0pt;text-align: center;">
                48</p>
            <p class="s4"
               style="padding-top: 4pt;padding-left: 19pt;padding-right: 18pt;text-indent: 0pt;text-align: center;">
                48</p></td>
        <td style="width:39pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p>
            <p class="s4"
               style="padding-left: 13pt;padding-right: 1pt;text-indent: 0pt;line-height: 145%;text-align: left;">m2
                m2</p>
            <p style="text-indent: 0pt;text-align: left;"><br/></p>
            <p class="s4"
               style="padding-top: 7pt;padding-left: 13pt;padding-right: 1pt;text-indent: 0pt;line-height: 144%;text-align: left;">
                m2 m2</p>
            <p style="text-indent: 0pt;text-align: left;"><br/></p>
            <p class="s4" style="padding-left: 13pt;text-indent: 0pt;text-align: left;">m2</p></td>
        <td style="width:40pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
        <td style="width:235pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4"
               style="padding-left: 2pt;padding-right: 12pt;text-indent: 0pt;line-height: 110%;text-align: left;">dépose
                en recherche des éléments cassés, fissurés, manquants. Fourniture et pose d&#39;éléments neufs dans la
                limite d&#39;un nombre d&#39;éléments défini. Compris remplacement du crochet au besoin.</p>
            <p class="s4"
               style="padding-top: 1pt;padding-left: 2pt;padding-right: 2pt;text-indent: 0pt;line-height: 110%;text-align: left;">
                ATTENTION : l&#39;estimation du nombre d&#39;éléments à changer est primordiale. Inclure un pourcentage
                de +</p>
            <p class="s4"
               style="padding-left: 2pt;padding-right: 12pt;text-indent: 0pt;line-height: 110%;text-align: left;">/-5% d&#39;éléments
                à changer dans les champs avertissements du devis + dans les fournitures</p>
            <p style="text-indent: 0pt;text-align: left;"><br/></p>
            <p class="s4"
               style="padding-left: 2pt;padding-right: 19pt;text-indent: 0pt;line-height: 110%;text-align: justify;">
                Application d&#39;un produit antimousse sur la toiture compris préparation du support et protection des
                éléments connexes à la toiture</p>
            <p class="s4"
               style="padding-top: 1pt;padding-left: 2pt;padding-right: 12pt;text-indent: 0pt;line-height: 110%;text-align: left;">
                Consommation d&#39;un produit antimousse type Algimouss ou Dalep 21000. Attention à la pose notamment en
                pourtour d&#39;éléments métalliques et de végétations. Produit souvent CORROSIF. Lire Notice de Pose et
                utilisation des EPIs adaptés.</p>
            <p style="text-indent: 0pt;text-align: left;"><br/></p>
            <p class="s5" style="padding-left: 2pt;text-indent: 0pt;text-align: justify;">Dépose<span
                    class="s6"> </span>d&#39;une<span class="s6"> </span>couverture<span class="s6"> </span>en<span
                    class="s6"> </span>Fibro-ciment</p>
            <p class="s4"
               style="padding-top: 3pt;padding-left: 2pt;padding-right: 12pt;text-indent: 0pt;line-height: 110%;text-align: left;">
                Dépose d&#39;une couverture en tôles d&#39;acier nervurées 75/100 y compris accessoires.</p>
            <p class="s4"
               style="padding-top: 1pt;padding-left: 2pt;padding-right: 12pt;text-indent: 0pt;line-height: 110%;text-align: left;">
                Fourniture et pose d&#39;une couverture en panneau sandwich isolation 120m comprenant fixations
                mécaniques.</p>
            <p class="s4"
               style="padding-top: 1pt;padding-left: 2pt;padding-right: 12pt;text-indent: 0pt;line-height: 110%;text-align: left;">
                Fourniture et pose de couverture en tôles d&#39;acier nervurées double peau avec isolation polyuréthane
                de 100mm compris fixations par vis 200mm pour support bois compris cavalier et rondelle EPDM</p></td>
    </tr>
</table>
<p style="text-indent: 0pt;text-align: left;"><br/></p>
<table style="border-collapse:collapse;width:100%" cellspacing="0">
    <tr style="height:15pt">
        <td style="width:199pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s3" style="padding-left: 3pt;text-indent: 0pt;line-height: 14pt;text-align: left;">Prestation</p>
        </td>
        <td style="width:54pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s3" style="padding-left: 2pt;text-indent: 0pt;line-height: 14pt;text-align: left;">Quantite</p>
        </td>
        <td style="width:39pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s3" style="padding-left: 4pt;text-indent: 0pt;line-height: 14pt;text-align: left;">Unite</p></td>
        <td style="width:40pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s3" style="padding-left: 8pt;text-indent: 0pt;line-height: 14pt;text-align: left;">Fait</p></td>
        <td style="width:235pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
    </tr>
    <tr style="height:632pt">
        <td style="width:199pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4"
               style="padding-left: 3pt;padding-right: 47pt;text-indent: 0pt;line-height: 144%;text-align: left;">
                Réfection de la rive en pénétration Pose</p>
            <p style="text-indent: 0pt;text-align: left;"><br/></p>
            <p class="s4"
               style="padding-top: 9pt;padding-left: 3pt;padding-right: 47pt;text-indent: 0pt;line-height: 144%;text-align: left;">
                Réfection de la rive en pénétration Dépose</p>
            <p style="text-indent: 0pt;text-align: left;"><br/></p>
            <p class="s4" style="padding-top: 8pt;padding-left: 3pt;text-indent: 0pt;text-align: left;">Pose</p>
            <p style="text-indent: 0pt;text-align: left;"><br/></p>
            <p class="s4" style="padding-left: 3pt;text-indent: 0pt;line-height: 110%;text-align: left;">Reprise et
                Chemisage du chéneau 2 en Membrane EPDM</p>
            <p class="s4" style="padding-top: 1pt;padding-left: 3pt;text-indent: 0pt;text-align: left;">pose</p>
            <p style="text-indent: 0pt;text-align: left;"><br/></p>
            <p class="s4"
               style="padding-left: 3pt;padding-right: 23pt;text-indent: 0pt;line-height: 144%;text-align: left;">Dépose
                de la toiture en fibro-ciment Dépose</p>
            <p style="text-indent: 0pt;text-align: left;"><br/></p>
            <p class="s4"
               style="padding-left: 3pt;padding-right: 23pt;text-indent: 0pt;line-height: 144%;text-align: left;">
                Création d&#39;un plancher bois en OSB Pose</p>
            <p style="text-indent: 0pt;text-align: left;"><br/></p>
            <p class="s4"
               style="padding-top: 6pt;padding-left: 3pt;padding-right: 49pt;text-indent: 0pt;line-height: 145%;text-align: left;">
                Pose d&#39;une membrane EPDM Pose</p></td>
        <td style="width:54pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="text-indent: 0pt;text-align: center;">5</p>
            <p class="s4" style="padding-top: 4pt;text-indent: 0pt;text-align: center;">5</p>
            <p style="text-indent: 0pt;text-align: left;"><br/></p>
            <p class="s4" style="padding-left: 19pt;padding-right: 18pt;text-indent: 0pt;text-align: center;">10</p>
            <p class="s4"
               style="padding-top: 4pt;padding-left: 19pt;padding-right: 18pt;text-indent: 0pt;text-align: center;">
                10</p>
            <p style="text-indent: 0pt;text-align: left;"><br/></p>
            <p class="s4" style="padding-left: 19pt;padding-right: 18pt;text-indent: 0pt;text-align: center;">10</p>
            <p style="text-indent: 0pt;text-align: left;"><br/></p>
            <p class="s4" style="padding-left: 19pt;padding-right: 18pt;text-indent: 0pt;text-align: center;">9,5</p>
            <p style="text-indent: 0pt;text-align: left;"><br/></p>
            <p class="s4" style="padding-left: 19pt;padding-right: 18pt;text-indent: 0pt;text-align: center;">9,5</p>
            <p style="text-indent: 0pt;text-align: left;"><br/></p>
            <p class="s4" style="padding-left: 19pt;padding-right: 18pt;text-indent: 0pt;text-align: center;">45</p>
            <p class="s4"
               style="padding-top: 4pt;padding-left: 19pt;padding-right: 18pt;text-indent: 0pt;text-align: center;">
                45</p>
            <p style="text-indent: 0pt;text-align: left;"><br/></p>
            <p class="s4" style="text-indent: 0pt;text-align: center;">1</p>
            <p class="s4"
               style="padding-top: 4pt;padding-left: 19pt;padding-right: 18pt;text-indent: 0pt;text-align: center;">
                50</p>
            <p style="text-indent: 0pt;text-align: left;"><br/></p>
            <p class="s4" style="padding-left: 19pt;padding-right: 18pt;text-indent: 0pt;text-align: center;">45</p>
            <p class="s4"
               style="padding-top: 4pt;padding-left: 19pt;padding-right: 18pt;text-indent: 0pt;text-align: center;">
                45</p></td>
        <td style="width:39pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4"
               style="padding-left: 13pt;padding-right: 11pt;text-indent: 0pt;line-height: 144%;text-align: center;">ml
                ml</p>
            <p style="text-indent: 0pt;text-align: left;"><br/></p>
            <p class="s4"
               style="padding-top: 9pt;padding-left: 13pt;padding-right: 11pt;text-indent: 0pt;line-height: 144%;text-align: center;">
                ml ml</p>
            <p style="text-indent: 0pt;text-align: left;"><br/></p>
            <p class="s4"
               style="padding-top: 8pt;padding-left: 13pt;padding-right: 11pt;text-indent: 0pt;text-align: center;">
                ml</p>
            <p style="text-indent: 0pt;text-align: left;"><br/></p>
            <p class="s4"
               style="padding-left: 13pt;padding-right: 11pt;text-indent: 0pt;line-height: 234%;text-align: center;">ml
                ml</p>
            <p style="text-indent: 0pt;text-align: left;"><br/></p>
            <p class="s4"
               style="padding-left: 13pt;padding-right: 11pt;text-indent: 0pt;line-height: 144%;text-align: center;">m2
                m2</p>
            <p style="text-indent: 0pt;text-align: left;"><br/></p>
            <p class="s4"
               style="padding-left: 13pt;padding-right: 11pt;text-indent: 0pt;line-height: 145%;text-align: center;">m2
                m2</p></td>
        <td style="width:40pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
        <td style="width:235pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p>
            <p class="s4"
               style="padding-left: 2pt;padding-right: 2pt;text-indent: 0pt;line-height: 112%;text-align: left;">
                Fourniture et pose d&#39;un profilé métallique 75/100 sur rive en pénétration y compris fixations
                mécaniques. Fourniture et pose d&#39;un profilé métallique laqué 75/ 100 sur rive en pénétration y
                compris fixations mécaniques.</p>
            <p style="text-indent: 0pt;text-align: left;"><br/></p>
            <p class="s4"
               style="padding-left: 2pt;padding-right: 12pt;text-indent: 0pt;line-height: 110%;text-align: left;">Dépose
                pour mise en démolition de la rive en pénétration comprenant les noquets, ainsi que trois rangs d&#39;ardoises.</p>
            <p class="s4"
               style="padding-top: 1pt;padding-left: 2pt;padding-right: 12pt;text-indent: 0pt;line-height: 110%;text-align: left;">
                Dépose pour mise en démolition de la rive en pénétration comprenant les noquets, ainsi que trois rangs d&#39;ardoises.</p>
            <p class="s4"
               style="padding-top: 1pt;padding-left: 2pt;padding-right: 9pt;text-indent: 0pt;line-height: 110%;text-align: left;">
                Fourniture et pose d&#39;une rive en pénétration posée à noquets comprenant trois rangs d&#39;ardoises,
                le lattage défectueux, les noquets zinc et le tranchis d&#39;ardoises.</p>
            <p class="s4"
               style="padding-top: 1pt;padding-left: 2pt;padding-right: 9pt;text-indent: 0pt;line-height: 110%;text-align: left;">
                Fourniture et pose d&#39;une rive en pénétration posée à noquets comprenant trois rangs d&#39;ardoises,
                le lattage défectueux, les noquets zinc 65/100 relevé de 0,10mm et le tranchis d&#39;ardoises.</p>
            <p style="text-indent: 0pt;text-align: left;"><br/></p>
            <p class="s7"
               style="padding-left: 2pt;padding-right: 12pt;text-indent: 0pt;line-height: 91%;text-align: left;">
                Préparation<span class="s8"> </span>du<span class="s8"> </span>support<span class="s8"> </span>comprenant<span
                    class="s8"> </span>enlèvement<span class="s8"> </span>des<span class="s8"> </span>déchets<span
                    class="s8"> </span>et<span class="s8"> </span>assèchement.</p>
            <p class="s7"
               style="padding-left: 2pt;padding-right: 2pt;text-indent: 0pt;line-height: 92%;text-align: left;">
                Reprise<span class="s8"> </span>du<span class="s8"> </span>chéneau<span class="s8"> </span>existant<span
                    class="s8"> </span>pour<span class="s8"> </span>faire<span class="s8"> </span>une<span
                    class="s8"> </span>pente.<span class="s8"> </span>Fourniture,<span class="s8"> </span>façonnage<span
                    class="s8"> </span>et<span class="s8"> </span>pose<span class="s8"> </span>d&#39;un<span
                    class="s8"> </span>chéneau<span class="s8"> </span>en<span class="s8"> </span>membrane<span
                    class="s8"> </span>EPDM<span class="s8"> </span>compris<span class="s8"> </span>encollage<span
                    class="s8"> </span>et<span class="s8"> </span>fixation<span class="s8"> </span>en<span
                    class="s8"> </span>tête<span class="s8"> </span>des<span class="s8"> </span>relevés<span
                    class="s8"> </span>de<span class="s8"> </span>la<span class="s8"> </span>membrane<span
                    class="s8"> </span>par<span class="s8"> </span>bande<span class="s8"> </span>serrage.</p>
            <p style="text-indent: 0pt;text-align: left;"><br/></p>
            <p class="s4"
               style="padding-left: 2pt;padding-right: 2pt;text-indent: 0pt;line-height: 110%;text-align: left;">
                Préparation du support comprenant enlèvement des déchets et assèchement. Fourniture, façonnage et pose d&#39;un
                chéneau en membrane PVC compris encollage par Flexocol A89 0,70Kg/m², membrane Flagon SFc 15/10 en
                adhérence totale et fixation en tête des relevés de la membrane par bande serrage Flagorail fixée
                mécaniquement.</p>
            <p style="text-indent: 0pt;text-align: left;"><br/></p>
            <p class="s4"
               style="padding-left: 2pt;padding-right: 2pt;text-indent: 0pt;line-height: 110%;text-align: left;">Dépose
                d&#39;une couverture en plaques fibres-ciment y compris accessoires.</p>
            <p class="s4"
               style="padding-top: 1pt;padding-left: 2pt;padding-right: 12pt;text-indent: 0pt;line-height: 110%;text-align: left;">
                Dépose d&#39;une couverture en plaques Eternit y compris accessoires.</p>
            <p style="text-indent: 0pt;text-align: left;"><br/></p>
            <p class="s5"
               style="padding-left: 2pt;padding-right: 12pt;text-indent: 0pt;line-height: 94%;text-align: left;">
                Pose<span class="s6"> </span>d&#39;un<span class="s6"> </span>plancher<span class="s6"> </span>bois<span
                    class="s6"> </span>en<span class="s6"> </span>OSB3<span class="s6"> </span>et<span
                    class="s6"> </span>création<span class="s6"> </span>de<span class="s6"> </span>ses<span
                    class="s6"> </span>relevés</p>
            <p style="text-indent: 0pt;text-align: left;"><br/></p>
            <p class="s7"
               style="padding-left: 2pt;padding-right: 14pt;text-indent: 0pt;line-height: 91%;text-align: left;">
                Fourniture<span class="s8"> </span>et<span class="s8"> </span>pose<span
                    class="s8"> </span>d&#39;une<span class="s8"> </span>étanchéité<span
                    class="s8"> </span>monocouche<span class="s8"> </span>EPDM<span class="s8"> </span>sur<span
                    class="s8"> </span>support<span class="s8"> </span>bois<span class="s8"> </span>en<span
                    class="s8"> </span>adhérence<span class="s8"> </span>totale<span class="s8"> </span>sans<span
                    class="s8"> </span>isolant<span class="s8"> </span>comprenant<span class="s8"> </span>colle<span
                    class="s8"> </span>de<span class="s8"> </span>fixation<span class="s8"> </span>et<span
                    class="s8"> </span>revêtement<span class="s8"> </span>d&#39;étanchéité<span class="s8"> </span>monocouche<span
                    class="s8"> </span>en<span class="s8"> </span>membrane<span class="s8"> </span>EPDM</p></td>
    </tr>
</table>
<p style="text-indent: 0pt;text-align: left;"><br/></p>
<table style="border-collapse:collapse;width:100%" cellspacing="0">
    <tr style="height:15pt">
        <td style="width:199pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s3" style="padding-left: 3pt;text-indent: 0pt;line-height: 14pt;text-align: left;">Prestation</p>
        </td>
        <td style="width:54pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s3" style="padding-left: 2pt;text-indent: 0pt;line-height: 14pt;text-align: left;">Quantite</p>
        </td>
        <td style="width:39pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s3" style="padding-left: 4pt;text-indent: 0pt;line-height: 14pt;text-align: left;">Unite</p></td>
        <td style="width:40pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s3" style="padding-left: 8pt;text-indent: 0pt;line-height: 14pt;text-align: left;">Fait</p></td>
        <td style="width:235pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
    </tr>
    <tr style="height:632pt">
        <td style="width:199pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p>
            <p class="s4"
               style="padding-top: 9pt;padding-left: 3pt;text-indent: 0pt;line-height: 110%;text-align: left;">Reprise
                et Chemisage du chéneau 1 en Membrane EPDM</p>
            <p class="s4" style="padding-top: 1pt;padding-left: 3pt;text-indent: 0pt;text-align: left;">pose</p>
            <p style="text-indent: 0pt;text-align: left;"><br/></p>
            <p class="s4" style="padding-left: 3pt;text-indent: 0pt;line-height: 110%;text-align: left;">Réfection de la
                protection de tête: solins/ joints - Bandes porte solin/ porte joint.</p>
            <p class="s4" style="padding-top: 1pt;padding-left: 3pt;text-indent: 0pt;text-align: left;">Pose</p>
            <p style="text-indent: 0pt;text-align: left;"><br/></p>
            <p class="s4"
               style="padding-left: 3pt;padding-right: 47pt;text-indent: 0pt;line-height: 144%;text-align: left;">
                Réfection de la rive en pénétration Pose</p>
            <p style="text-indent: 0pt;text-align: left;"><br/></p>
            <p class="s4"
               style="padding-top: 6pt;padding-left: 3pt;text-indent: 0pt;line-height: 144%;text-align: left;">Réfection
                du faitage par un faitage 3 bandes Dépose</p>
            <p style="text-indent: 0pt;text-align: left;"><br/></p>
            <p class="s4" style="padding-left: 3pt;text-indent: 0pt;text-align: left;">Pose</p></td>
        <td style="width:54pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p>
            <p class="s4"
               style="padding-top: 9pt;padding-left: 19pt;padding-right: 18pt;text-indent: 0pt;text-align: center;">
                9,5</p>
            <p style="text-indent: 0pt;text-align: left;"><br/></p>
            <p class="s4" style="padding-left: 19pt;padding-right: 18pt;text-indent: 0pt;text-align: center;">9,5</p>
            <p style="text-indent: 0pt;text-align: left;"><br/></p>
            <p class="s4" style="text-indent: 0pt;text-align: center;">6</p>
            <p style="text-indent: 0pt;text-align: left;"><br/></p>
            <p class="s4" style="text-indent: 0pt;text-align: center;">6</p>
            <p style="text-indent: 0pt;text-align: left;"><br/></p>
            <p class="s4" style="text-indent: 0pt;text-align: center;">5</p>
            <p class="s4" style="padding-top: 4pt;text-indent: 0pt;text-align: center;">5</p>
            <p style="text-indent: 0pt;text-align: left;"><br/></p>
            <p class="s4" style="text-indent: 0pt;text-align: center;">5</p>
            <p class="s4" style="padding-top: 4pt;text-indent: 0pt;text-align: center;">5</p>
            <p style="text-indent: 0pt;text-align: left;"><br/></p>
            <p class="s4" style="text-indent: 0pt;text-align: center;">5</p></td>
        <td style="width:39pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p>
            <p class="s4"
               style="padding-top: 9pt;padding-left: 13pt;padding-right: 11pt;text-indent: 0pt;line-height: 233%;text-align: center;">
                ml ml</p>
            <p style="text-indent: 0pt;text-align: left;"><br/></p>
            <p class="s4"
               style="padding-left: 13pt;padding-right: 11pt;text-indent: 0pt;line-height: 234%;text-align: center;">ml
                ml</p>
            <p style="text-indent: 0pt;text-align: left;"><br/></p>
            <p class="s4"
               style="padding-left: 13pt;padding-right: 11pt;text-indent: 0pt;line-height: 144%;text-align: center;">ml
                ml</p>
            <p style="text-indent: 0pt;text-align: left;"><br/></p>
            <p class="s4"
               style="padding-top: 6pt;padding-left: 13pt;padding-right: 11pt;text-indent: 0pt;line-height: 144%;text-align: center;">
                ml ml</p>
            <p style="text-indent: 0pt;text-align: left;"><br/></p>
            <p class="s4" style="padding-left: 13pt;padding-right: 11pt;text-indent: 0pt;text-align: center;">ml</p>
        </td>
        <td style="width:40pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
        <td style="width:235pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4"
               style="padding-left: 2pt;padding-right: 10pt;text-indent: 0pt;line-height: 110%;text-align: left;">
                Fourniture et pose d&#39;une étanchéité monocouche PVC sur support bois en adhérence totale sans isolant
                comprenant colle de fixation type Flexocol A89 0,7Kg/m² et revêtement d&#39;étanchéité monocouche en
                membrane PVC type Flagon SFc 15/10 collé en plein avec recouvrement entre lés de 0,10m</p>
            <p style="text-indent: 0pt;text-align: left;"><br/></p>
            <p class="s7"
               style="padding-left: 2pt;padding-right: 12pt;text-indent: 0pt;line-height: 92%;text-align: left;">
                Préparation<span class="s8"> </span>du<span class="s8"> </span>support<span class="s8"> </span>comprenant<span
                    class="s8"> </span>enlèvement<span class="s8"> </span>des<span class="s8"> </span>déchets<span
                    class="s8"> </span>et<span class="s8"> </span>assèchement.</p>
            <p class="s7"
               style="padding-left: 2pt;padding-right: 2pt;text-indent: 0pt;line-height: 91%;text-align: left;">
                Reprise<span class="s8"> </span>du<span class="s8"> </span>chéneau<span class="s8"> </span>existant<span
                    class="s8"> </span>pour<span class="s8"> </span>faire<span class="s8"> </span>une<span
                    class="s8"> </span>pente.<span class="s8"> </span>Fourniture,<span class="s8"> </span>façonnage<span
                    class="s8"> </span>et<span class="s8"> </span>pose<span class="s8"> </span>d&#39;un<span
                    class="s8"> </span>chéneau<span class="s8"> </span>en<span class="s8"> </span>membrane<span
                    class="s8"> </span>EPDM<span class="s8"> </span>compris<span class="s8"> </span>encollage<span
                    class="s8"> </span>et<span class="s8"> </span>fixation<span class="s8"> </span>en<span
                    class="s8"> </span>tête<span class="s8"> </span>des<span class="s8"> </span>relevés<span
                    class="s8"> </span>de<span class="s8"> </span>la<span class="s8"> </span>membrane<span
                    class="s8"> </span>par<span class="s8"> </span>bande<span class="s8"> </span>serrage.</p>
            <p class="s7"
               style="padding-top: 1pt;padding-left: 2pt;padding-right: 12pt;text-indent: 0pt;line-height: 92%;text-align: left;">
                Préparation<span class="s8"> </span>du<span class="s8"> </span>support<span class="s8"> </span>comprenant<span
                    class="s8"> </span>enlèvement<span class="s8"> </span>des<span class="s8"> </span>déchets<span
                    class="s8"> </span>et<span class="s8"> </span>assèchement.<span class="s8"> </span>Fourniture,<span
                    class="s8"> </span>façonnage<span class="s8"> </span>et<span class="s8"> </span>pose<span
                    class="s8"> </span>d&#39;un<span class="s8"> </span>chéneau<span class="s8"> </span>en<span
                    class="s8"> </span>membrane<span class="s8"> </span>EPDM<span class="s8"> </span>compris<span
                    class="s8"> </span>encollage<span class="s8"> </span>par<span class="s8"> </span>Flexocol<span
                    class="s8"> </span>A89<span class="s8"> </span>0,70Kg/m²,<span class="s8"> </span>membrane<span
                    class="s8"> </span>Flagon<span class="s8"> </span>SFc<span class="s8"> </span>15/10<span
                    class="s8"> </span>en<span class="s8"> </span>adhérence<span class="s8"> </span>totale<span
                    class="s8"> </span>et<span class="s8"> </span>fixation<span class="s8"> </span>en<span
                    class="s8"> </span>tête<span class="s8"> </span>des<span class="s8"> </span>relevés<span
                    class="s8"> </span>de<span class="s8"> </span>la<span class="s8"> </span>membrane<span
                    class="s8"> </span>par<span class="s8"> </span>bande<span class="s8"> </span>serrage<span
                    class="s8"> </span>Flagorail<span class="s8"> </span>fixée<span class="s8"> </span>mécaniquement.
            </p>
            <p style="text-indent: 0pt;text-align: left;"><br/></p>
            <p class="s4"
               style="padding-left: 2pt;padding-right: 2pt;text-indent: 0pt;line-height: 109%;text-align: left;">
                Fourniture et pose d&#39;une bande porte-solin / porte-joint fixée mécaniquement comprenant la pose d&#39;un
                solin / joint suivant support.</p>
            <p class="s4"
               style="padding-top: 1pt;padding-left: 2pt;padding-right: 12pt;text-indent: 0pt;line-height: 110%;text-align: left;">
                Fourniture et pose d&#39;une bande porte-solin en zinc 65/100 dev. 0,10m fixée mécaniquement par
                chevilles à frapper comprenant solin au mortier de couvreur / joint suivant support.</p>
            <p style="text-indent: 0pt;text-align: left;"><br/></p>
            <p class="s4"
               style="padding-left: 2pt;padding-right: 10pt;text-indent: 0pt;line-height: 110%;text-align: left;">
                Fourniture et pose d&#39;un vernis d&#39;impression, d&#39;une équerre de renfort soudée en plein et d&#39;une
                équerre de finition des relevés autoprotégée.</p>
            <p class="s4"
               style="padding-top: 1pt;padding-left: 2pt;padding-right: 10pt;text-indent: 0pt;line-height: 110%;text-align: left;">
                Fourniture et pose d&#39;un relevé d&#39;étanchéité d&#39;une hauteur de 0,15m comprenant un vernis d&#39;impression
                AQUADERE 0,30l/m², d&#39;une équerre de renfort SOPRALENE et d&#39;une équerre de finition des relevés
                SOPRALAST 50 TV ALU soudée en plein.</p>
            <p style="text-indent: 0pt;text-align: left;"><br/></p>
            <p class="s4"
               style="padding-left: 2pt;padding-right: 10pt;text-indent: 0pt;line-height: 110%;text-align: left;">Dépose
                pour mise en démolition du faîtage existant y compris ardoises adjacentes.</p>
            <p class="s4"
               style="padding-top: 1pt;padding-left: 2pt;padding-right: 10pt;text-indent: 0pt;line-height: 110%;text-align: left;">
                Dépose pour mise en démolition du faîtage existant y compris ardoises adjacentes.</p>
            <p class="s4"
               style="padding-top: 1pt;padding-left: 2pt;padding-right: 12pt;text-indent: 0pt;line-height: 110%;text-align: left;">
                Fourniture, Façonnage et pose d&#39;un faîtage trois bandes en zinc posé sur tasseau trapézoïdal en
                sapin traité, couvre-joint en zinc, fixation par pattes à ressort, bande d&#39;astragale en zinc 65/100
                avec pince et raccord ardoise sur trois rangs avec tranchis droit.</p></td>
    </tr>
</table>
<p style="text-indent: 0pt;text-align: left;"><br/></p>
<table style="border-collapse:collapse;width:100%" cellspacing="0">
    <tr style="height:15pt">
        <td style="width:199pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s3" style="padding-left: 3pt;text-indent: 0pt;line-height: 14pt;text-align: left;">Prestation</p>
        </td>
        <td style="width:54pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s3" style="padding-left: 2pt;text-indent: 0pt;line-height: 14pt;text-align: left;">Quantite</p>
        </td>
        <td style="width:39pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s3" style="padding-left: 4pt;text-indent: 0pt;line-height: 14pt;text-align: left;">Unite</p></td>
        <td style="width:40pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s3" style="padding-left: 8pt;text-indent: 0pt;line-height: 14pt;text-align: left;">Fait</p></td>
        <td style="width:235pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
    </tr>
    <tr style="height:527pt">
        <td style="width:199pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p>
            <p class="s4"
               style="padding-left: 3pt;padding-right: 23pt;text-indent: 0pt;line-height: 144%;text-align: left;">
                Changement de descentes Zinc 80 Dépose</p>
            <p style="text-indent: 0pt;text-align: left;"><br/></p>
            <p class="s4" style="padding-left: 3pt;text-indent: 0pt;text-align: left;">Pose</p>
            <p style="text-indent: 0pt;text-align: left;"><br/></p>
            <p class="s4"
               style="padding-left: 3pt;padding-right: 23pt;text-indent: 0pt;line-height: 110%;text-align: left;">
                Raccords d&#39;étanchéité en EPDM sur support zinc</p>
            <p class="s4" style="padding-top: 1pt;padding-left: 3pt;text-indent: 0pt;text-align: left;">Pose</p>
            <p style="text-indent: 0pt;text-align: left;"><br/></p>
            <p class="s4"
               style="padding-left: 3pt;padding-right: 6pt;text-indent: 0pt;line-height: 109%;text-align: left;">
                Remplacement d&#39;un dévoiement existant Zinc 100</p>
            <p class="s4" style="padding-top: 1pt;padding-left: 3pt;text-indent: 0pt;text-align: left;">Dépose</p>
            <p style="text-indent: 0pt;text-align: left;"><br/></p>
            <p class="s4" style="padding-left: 3pt;text-indent: 0pt;text-align: left;">Pose</p></td>
        <td style="width:54pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p>
            <p class="s4" style="padding-left: 19pt;padding-right: 18pt;text-indent: 0pt;text-align: center;">20</p>
            <p class="s4"
               style="padding-top: 4pt;padding-left: 19pt;padding-right: 18pt;text-indent: 0pt;text-align: center;">
                20</p>
            <p style="text-indent: 0pt;text-align: left;"><br/></p>
            <p class="s4" style="padding-left: 19pt;padding-right: 18pt;text-indent: 0pt;text-align: center;">20</p>
            <p style="text-indent: 0pt;text-align: left;"><br/></p>
            <p class="s4" style="text-indent: 0pt;text-align: center;">5</p>
            <p style="text-indent: 0pt;text-align: left;"><br/></p>
            <p class="s4" style="text-indent: 0pt;text-align: center;">5</p>
            <p style="text-indent: 0pt;text-align: left;"><br/></p>
            <p class="s4" style="text-indent: 0pt;text-align: center;">3</p>
            <p style="text-indent: 0pt;text-align: left;"><br/></p>
            <p class="s4" style="text-indent: 0pt;text-align: center;">3</p>
            <p style="text-indent: 0pt;text-align: left;"><br/></p>
            <p class="s4" style="text-indent: 0pt;text-align: center;">3</p></td>
        <td style="width:39pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p>
            <p class="s4"
               style="padding-left: 13pt;padding-right: 11pt;text-indent: 0pt;line-height: 144%;text-align: center;">ml
                ml</p>
            <p style="text-indent: 0pt;text-align: left;"><br/></p>
            <p class="s4" style="padding-left: 13pt;padding-right: 11pt;text-indent: 0pt;text-align: center;">ml</p>
            <p style="text-indent: 0pt;text-align: left;"><br/></p>
            <p class="s4"
               style="padding-left: 13pt;padding-right: 11pt;text-indent: 0pt;line-height: 233%;text-align: center;">un
                un</p>
            <p style="text-indent: 0pt;text-align: left;"><br/></p>
            <p class="s4"
               style="padding-top: 9pt;padding-left: 13pt;padding-right: 11pt;text-indent: 0pt;line-height: 233%;text-align: center;">
                un un</p>
            <p style="text-indent: 0pt;text-align: left;"><br/></p>
            <p class="s4" style="padding-left: 13pt;padding-right: 11pt;text-indent: 0pt;text-align: center;">un</p>
        </td>
        <td style="width:40pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
        <td style="width:235pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4"
               style="padding-left: 2pt;padding-right: 4pt;text-indent: 0pt;line-height: 110%;text-align: left;">
                Fourniture, Façonnage et pose d&#39;un faîtage trois bandes en zinc posé sur tasseau trapézoïdal 75/75/
                45 en sapin traité, couvre-joint en zinc 65/100 dev. 0, 16m fixation par pattes à ressort, bande d&#39;astragale
                en zinc 65/100 avec pince et raccord ardoise sur trois rangs avec tranchis droit.</p>
            <p style="text-indent: 0pt;text-align: left;"><br/></p>
            <p class="s4"
               style="padding-left: 2pt;padding-right: 12pt;text-indent: 0pt;line-height: 110%;text-align: left;">Dépose
                d&#39;une descente d&#39;eaux pluviales en zinc de 80 y compris tous accessoires.</p>
            <p class="s4"
               style="padding-top: 1pt;padding-left: 2pt;padding-right: 12pt;text-indent: 0pt;line-height: 110%;text-align: left;">
                Dépose d&#39;une descente d&#39;eaux pluviales en zinc de 80 y compris tous accessoires.</p>
            <p class="s4"
               style="padding-top: 1pt;padding-left: 2pt;padding-right: 10pt;text-indent: 0pt;line-height: 110%;text-align: left;">
                Fourniture et pose d&#39;une descente d&#39;eaux pluviales en zinc de 80 comprenant bagues, colliers,
                soudure et fixations</p>
            <p class="s4"
               style="padding-top: 1pt;padding-left: 2pt;padding-right: 6pt;text-indent: 0pt;line-height: 110%;text-align: left;">
                Fourniture et pose d&#39;une descente d&#39;eaux pluviales en zinc de 80 comprenant bagues soudées à l&#39;étain,
                colliers acier galvanisé à embase taraudée, fixations pattes à vis et cheville nylon 7/50 Surface maxi
                desservie par tuyau de Ø80 inf. 71,00m²</p>
            <p style="text-indent: 0pt;text-align: left;"><br/></p>
            <p class="s4"
               style="padding-left: 2pt;padding-right: 12pt;text-indent: 0pt;line-height: 110%;text-align: left;">
                Réalisation de pontages sur ouvrages métalliques en résine bicouche avec voile de renfort compris
                préparation du support et toutes sujétions.</p>
            <p class="s4"
               style="padding-top: 1pt;padding-left: 2pt;padding-right: 6pt;text-indent: 0pt;line-height: 110%;text-align: left;">
                Réalisation de pontages sur ouvrages métallique en résine bicouche comprenant décapage du support par
                TRIFLEX NETTOYANT 0,20l/m2, mise en place résine TRIFLEX PRODETAIL 3kg/m2 bicouche avec interposition d&#39;un
                voile de renfort TRIFLEX et couche d&#39;accroche par METALPRIMAIR 0,40kg/m²</p>
            <p style="text-indent: 0pt;text-align: left;"><br/></p>
            <p class="s4"
               style="padding-left: 2pt;padding-right: 12pt;text-indent: 0pt;line-height: 110%;text-align: left;">Dépose
                du dévoiement existant y compris tous accessoires</p>
            <p class="s4"
               style="padding-top: 1pt;padding-left: 2pt;padding-right: 12pt;text-indent: 0pt;line-height: 110%;text-align: left;">
                Dépose du dévoiement existant y compris tous accessoires</p>
            <p class="s4"
               style="padding-top: 1pt;padding-left: 2pt;padding-right: 12pt;text-indent: 0pt;line-height: 110%;text-align: left;">
                Fourniture et pose d&#39;un dévoiement zinc Ø100 compris jeu de coudes, soudure et raccordement à la
                descente</p>
            <p class="s4"
               style="padding-left: 2pt;padding-right: 12pt;text-indent: 0pt;line-height: 12pt;text-align: left;">
                Fourniture et pose d&#39;un dévoiement zinc Ø100 compris jeu de coudes, soudure étain et raccordement à
                la descente</p></td>
    </tr>
</table>
<p style="text-indent: 0pt;text-align: left;"><br/></p>
<p class="s9" style="text-indent: 0pt;line-height: 7pt;text-align: center;"><span
        style=" color: black; font-family:Calibri, sans-serif; font-style: normal; font-weight: bold; text-decoration: underline; font-size: 6.5pt;">LA</span><span
        style=" color: black; font-family:&quot;Times New Roman&quot;, serif; font-style: normal; font-weight: normal; text-decoration: underline; font-size: 6.5pt;"> </span><span
        style=" color: black; font-family:Calibri, sans-serif; font-style: normal; font-weight: bold; text-decoration: underline; font-size: 6.5pt;">MORBIHANNAISE</span><span
        style=" color: black; font-family:&quot;Times New Roman&quot;, serif; font-style: normal; font-weight: normal; text-decoration: underline; font-size: 6.5pt;"> </span><span
        style=" color: black; font-family:Calibri, sans-serif; font-style: normal; font-weight: bold; text-decoration: underline; font-size: 6.5pt;">DES</span><span
        style=" color: black; font-family:&quot;Times New Roman&quot;, serif; font-style: normal; font-weight: normal; text-decoration: underline; font-size: 6.5pt;"> </span><span
        style=" color: black; font-family:Calibri, sans-serif; font-style: normal; font-weight: bold; text-decoration: underline; font-size: 6.5pt;">TOITS</span>
    <span class="p">-</span> <span class="p">SARL</span> <span class="p">au</span> <span class="p">capital</span> <span
            class="p">de</span> <span class="p">15</span> <span class="p">000</span> <span class="p">€</span> <span
            class="p">-</span> <span class="p">21</span> <span class="p">rue</span> <span class="p">Denis</span> <span
            class="p">Papin</span> <span class="p">-</span> <span class="p">Zone</span> <span class="p">de</span> <span
            class="p">Kerniol</span> <span class="p">56000</span> <span class="p">Vannes</span></p>
<p style="text-indent: 0pt;text-align: center;">Tél<span class="s9"> </span>:<span class="s9"> </span>09<span
        class="s9"> </span>88<span class="s9"> </span>56<span class="s9"> </span>70<span class="s9"> </span>27<span
        class="s9"> </span>-<span class="s9"> </span>SIRET<span class="s9"> </span>90146884300015<span
        class="s9"> </span>-<span class="s9"> </span>TVA<span class="s9"> </span>Intra<span class="s9"> </span>FR78901468843<span
        class="s9"> </span>-<span class="s9"> </span>APE<span class="s9"> </span>:<span class="s9"> </span>4391B</p>
<p style="text-indent: 0pt;text-align: center;">Assurance<span class="s9"> </span>décennale<span class="s9"> </span>obligatoire,<span
        class="s9"> </span>souscrite<span class="s9"> </span>auprès<span class="s9"> </span>de<span class="s9"> </span>:<span
        class="s9"> </span>Les<span class="s9"> </span>Assureurs<span class="s9"> </span>Occitans<span
        class="s9"> </span>RINGEVAL<span class="s9"> </span>COURTAGE<span class="s9"> </span>-<span class="s9"> </span>1<span
        class="s9"> </span>Bd<span class="s9"> </span>des<span class="s9"> </span>Fossés<span class="s9"> </span>de<span
        class="s9"> </span>Raoul<span class="s9"> </span>82200<span class="s9"> </span>St<span class="s9"> </span>Nicolas<span
        class="s9"> </span>de<span class="s9"> </span>la<span class="s9"> </span>Grave.</p>
<p style="text-indent: 0pt;line-height: 8pt;text-align: center;">Adresse<span class="s9"> </span>réclamation<span
        class="s9"> </span>:<span class="s9"> </span>Les<span class="s9"> </span>Assureurs<span class="s9"> </span>Occitans<span
        class="s9"> </span>RINGEVAL<span class="s9"> </span>COURTAGE</p>
<p class="s1" style="text-indent: 0pt;line-height: 11pt;text-align: left;">5 / 10</p>
<p style="padding-left: 6pt;text-indent: 0pt;text-align: left;"/>
<p class="s9" style="padding-left: 56pt;text-indent: 0pt;line-height: 7pt;text-align: center;"><span
        class="h4">LA</span> <span class="h4">MORBIHANNAISE</span> <span class="h4">DES</span> <span
        class="h4">TOITS</span> <span class="p">-</span> <span class="p">SARL</span> <span class="p">au</span> <span
        class="p">capital</span> <span class="p">de</span> <span class="p">15</span> <span class="p">000</span> <span
        class="p">€</span> <span class="p">-</span> <span class="p">21</span> <span class="p">rue</span> <span
        class="p">Denis</span> <span class="p">Papin</span> <span class="p">-</span> <span class="p">Zone</span> <span
        class="p">de</span> <span class="p">Kerniol</span> <span class="p">56000</span> <span class="p">Vannes</span>
</p>
<p style="padding-left: 55pt;text-indent: 0pt;line-height: 7pt;text-align: center;">Tél<span class="s9"> </span>:<span
        class="s9"> </span>09<span class="s9"> </span>88<span class="s9"> </span>56<span class="s9"> </span>70<span
        class="s9"> </span>27<span class="s9"> </span>-<span class="s9"> </span>SIRET<span class="s9"> </span>90146884300015<span
        class="s9"> </span>-<span class="s9"> </span>TVA<span class="s9"> </span>Intra<span class="s9"> </span>FR78901468843<span
        class="s9"> </span>-<span class="s9"> </span>APE<span class="s9"> </span>:<span class="s9"> </span>4391B</p>
<p style="text-indent: 0pt;line-height: 10pt;text-align: left;">Assurance<span class="s9"> </span>décennale<span
        class="s9"> </span>obligatoire,<span class="s9"> </span>souscrite<span class="s9"> </span>auprès<span
        class="s9"> </span>de<span class="s9"> </span>:<span class="s9"> </span>Les<span
        class="s9"> </span>Assureurs<span class="s9"> </span>Occitans<span class="s9"> </span>RINGEVAL<span
        class="s9"> </span>COURTAGE<span class="s9"> </span>-<span class="s9"> </span>1<span class="s9"> </span>Bd<span
        class="s9"> </span>des<span class="s9"> </span>Fossés<span class="s9"> </span>de<span
        class="s9"> </span>Raoul<span class="s9"> </span>82200<span class="s9"> </span>St<span class="s9"> </span>Nicolas<span
        class="s9"> </span>de<span class="s9"> </span>la<span class="s9"> </span>Grave.<span class="s9">    </span><span
        class="s1">5 / 10</span></p>
<p style="padding-left: 56pt;text-indent: 0pt;line-height: 8pt;text-align: center;">Adresse<span class="s9"> </span>réclamation<span
        class="s9"> </span>:<span class="s9"> </span>Les<span class="s9"> </span>Assureurs<span class="s9"> </span>Occitans<span
        class="s9"> </span>RINGEVAL<span class="s9"> </span>COURTAGE</p>
<p style="text-indent: 0pt;text-align: left;"/>
<p style="text-indent: 0pt;text-align: left;"><br/></p>
<table style="border-collapse:collapse;width:100%" cellspacing="0">
    <tr style="height:15pt">
        <td style="width:473pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s3" style="padding-left: 3pt;text-indent: 0pt;line-height: 14pt;text-align: left;">Fourniture</p>
        </td>
        <td style="width:54pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s3"
               style="padding-left: 2pt;padding-right: 1pt;text-indent: 0pt;line-height: 14pt;text-align: center;">
                Quantite</p></td>
        <td style="width:39pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s3"
               style="padding-left: 3pt;padding-right: 3pt;text-indent: 0pt;line-height: 14pt;text-align: center;">
                Unite</p></td>
    </tr>
    <tr style="height:14pt">
        <td style="width:473pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-left: 3pt;text-indent: 0pt;text-align: left;">*** ARDOISE ESPAGNE CLASSIQUE 350
                32X22 EF</p></td>
        <td style="width:54pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-left: 2pt;text-indent: 0pt;text-align: center;">20</p></td>
        <td style="width:39pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="text-indent: 0pt;text-align: center;">u</p></td>
    </tr>
    <tr style="height:16pt">
        <td style="width:473pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 3pt;text-indent: 0pt;text-align: left;">*** ARDOISE
                ESPAGNE CLASSIQUE 350 32X22 EF</p></td>
        <td style="width:54pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 2pt;text-indent: 0pt;text-align: center;">80</p></td>
        <td style="width:39pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;text-indent: 0pt;text-align: center;">u</p></td>
    </tr>
    <tr style="height:16pt">
        <td style="width:473pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 3pt;text-indent: 0pt;text-align: left;">*** ARDOISE
                ESPAGNE CLASSIQUE 350 32X22 EF</p></td>
        <td style="width:54pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 2pt;text-indent: 0pt;text-align: center;">50</p></td>
        <td style="width:39pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;text-indent: 0pt;text-align: center;">u</p></td>
    </tr>
    <tr style="height:16pt">
        <td style="width:473pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 3pt;text-indent: 0pt;text-align: left;">*** ARDOISE
                ESPAGNE CLASSIQUE 350 35X25</p></td>
        <td style="width:54pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 2pt;text-indent: 0pt;text-align: center;">20</p></td>
        <td style="width:39pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;text-indent: 0pt;text-align: center;">u</p></td>
    </tr>
    <tr style="height:16pt">
        <td style="width:473pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 3pt;text-indent: 0pt;text-align: left;">*** ARDOISE
                ESPAGNE CLASSIQUE PC 40X20 EC</p></td>
        <td style="width:54pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 2pt;text-indent: 0pt;text-align: center;">20</p></td>
        <td style="width:39pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;text-indent: 0pt;text-align: center;">u</p></td>
    </tr>
    <tr style="height:16pt">
        <td style="width:473pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 3pt;text-indent: 0pt;text-align: left;">*** CHEVILLE
                NYLONG 10-10/60 TH - BOITE DE 50</p></td>
        <td style="width:54pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 2pt;text-indent: 0pt;text-align: center;">20</p></td>
        <td style="width:39pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;text-indent: 0pt;text-align: center;">u</p></td>
    </tr>
    <tr style="height:16pt">
        <td style="width:473pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 3pt;text-indent: 0pt;text-align: left;">*** CRAPAUDINE
                DROITE GALVA Ø80 A 100MM</p></td>
        <td style="width:54pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 2pt;text-indent: 0pt;text-align: center;">20</p></td>
        <td style="width:39pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;text-indent: 0pt;text-align: center;">u</p></td>
    </tr>
    <tr style="height:16pt">
        <td style="width:473pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 3pt;text-indent: 0pt;text-align: left;">*** CRAPAUDINE
                DROITE GALVA Ø80 A 100MM</p></td>
        <td style="width:54pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;text-indent: 0pt;text-align: center;">3</p></td>
        <td style="width:39pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;text-indent: 0pt;text-align: center;">u</p></td>
    </tr>
    <tr style="height:16pt">
        <td style="width:473pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 3pt;text-indent: 0pt;text-align: left;">Algimouss bidon
                de 30 l</p></td>
        <td style="width:54pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 2pt;text-indent: 0pt;text-align: center;">12</p></td>
        <td style="width:39pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;text-indent: 0pt;text-align: center;">l</p></td>
    </tr>
    <tr style="height:16pt">
        <td style="width:473pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 3pt;text-indent: 0pt;text-align: left;">Article de
                régule</p></td>
        <td style="width:54pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 2pt;text-indent: 0pt;text-align: center;">20</p></td>
        <td style="width:39pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
    </tr>
    <tr style="height:16pt">
        <td style="width:473pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 3pt;text-indent: 0pt;text-align: left;">Article de
                régule</p></td>
        <td style="width:54pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 2pt;text-indent: 0pt;text-align: center;">48</p></td>
        <td style="width:39pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
    </tr>
    <tr style="height:16pt">
        <td style="width:473pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 3pt;text-indent: 0pt;text-align: left;">Article de
                régule</p></td>
        <td style="width:54pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;text-indent: 0pt;text-align: center;">5</p></td>
        <td style="width:39pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
    </tr>
    <tr style="height:16pt">
        <td style="width:473pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 3pt;text-indent: 0pt;text-align: left;">Article de
                régule</p></td>
        <td style="width:54pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 2pt;text-indent: 0pt;text-align: center;">10</p></td>
        <td style="width:39pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
    </tr>
    <tr style="height:16pt">
        <td style="width:473pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 3pt;text-indent: 0pt;text-align: left;">Article de
                régule</p></td>
        <td style="width:54pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 2pt;text-indent: 0pt;text-align: center;">9,5</p></td>
        <td style="width:39pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
    </tr>
    <tr style="height:16pt">
        <td style="width:473pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 3pt;text-indent: 0pt;text-align: left;">Article de
                régule</p></td>
        <td style="width:54pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 2pt;text-indent: 0pt;text-align: center;">45</p></td>
        <td style="width:39pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
    </tr>
    <tr style="height:16pt">
        <td style="width:473pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 3pt;text-indent: 0pt;text-align: left;">Article de
                régule</p></td>
        <td style="width:54pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 2pt;text-indent: 0pt;text-align: center;">9,5</p></td>
        <td style="width:39pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
    </tr>
    <tr style="height:16pt">
        <td style="width:473pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 3pt;text-indent: 0pt;text-align: left;">Article de
                régule</p></td>
        <td style="width:54pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;text-indent: 0pt;text-align: center;">6</p></td>
        <td style="width:39pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
    </tr>
    <tr style="height:16pt">
        <td style="width:473pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 3pt;text-indent: 0pt;text-align: left;">Article de
                régule</p></td>
        <td style="width:54pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;text-indent: 0pt;text-align: center;">5</p></td>
        <td style="width:39pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
    </tr>
    <tr style="height:16pt">
        <td style="width:473pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 3pt;text-indent: 0pt;text-align: left;">Article de
                régule</p></td>
        <td style="width:54pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;text-indent: 0pt;text-align: center;">5</p></td>
        <td style="width:39pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
    </tr>
    <tr style="height:16pt">
        <td style="width:473pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 3pt;text-indent: 0pt;text-align: left;">Article de
                régule</p></td>
        <td style="width:54pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 2pt;text-indent: 0pt;text-align: center;">20</p></td>
        <td style="width:39pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
    </tr>
    <tr style="height:16pt">
        <td style="width:473pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 3pt;text-indent: 0pt;text-align: left;">Article de
                régule</p></td>
        <td style="width:54pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;text-indent: 0pt;text-align: center;">5</p></td>
        <td style="width:39pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
    </tr>
    <tr style="height:16pt">
        <td style="width:473pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 3pt;text-indent: 0pt;text-align: left;">Article de
                régule</p></td>
        <td style="width:54pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;text-indent: 0pt;text-align: center;">3</p></td>
        <td style="width:39pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
    </tr>
    <tr style="height:16pt">
        <td style="width:473pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 3pt;text-indent: 0pt;text-align: left;">Article vide
                (écrire ici)</p></td>
        <td style="width:54pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 2pt;text-indent: 0pt;text-align: center;">50</p></td>
        <td style="width:39pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
    </tr>
    <tr style="height:16pt">
        <td style="width:473pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 3pt;text-indent: 0pt;text-align: left;">BAGUE ZINC
                SIMPLE EXTENSIBLE Ø80MM</p></td>
        <td style="width:54pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 2pt;text-indent: 0pt;text-align: center;">20</p></td>
        <td style="width:39pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;text-indent: 0pt;text-align: center;">u</p></td>
    </tr>
    <tr style="height:16pt">
        <td style="width:473pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 3pt;text-indent: 0pt;text-align: left;">BANDE SOLIN
                BISEAU ZINC NATUREL VM DEV.100 EP.0,65MM 2ML</p></td>
        <td style="width:54pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 2pt;text-indent: 0pt;text-align: center;">6,3</p></td>
        <td style="width:39pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4"
               style="padding-top: 2pt;padding-left: 3pt;padding-right: 2pt;text-indent: 0pt;text-align: center;">ml</p>
        </td>
    </tr>
    <tr style="height:16pt">
        <td style="width:473pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 3pt;text-indent: 0pt;text-align: left;">CAVALIER ACIER
                LAQUE 39T + RONDELLE EPDM Ø6 RAL 1015</p></td>
        <td style="width:54pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 2pt;text-indent: 0pt;text-align: center;">192</p></td>
        <td style="width:39pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;text-indent: 0pt;text-align: center;">u</p></td>
    </tr>
    <tr style="height:16pt">
        <td style="width:473pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 3pt;text-indent: 0pt;text-align: left;">CHEVILLE A
                FRAPPER NU-ZZ N5X30/5 F - BOITE DE 100</p></td>
        <td style="width:54pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 2pt;text-indent: 0pt;text-align: center;">30</p></td>
        <td style="width:39pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;text-indent: 0pt;text-align: center;">u</p></td>
    </tr>
    <tr style="height:16pt">
        <td style="width:473pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 3pt;text-indent: 0pt;text-align: left;">CHIFFONS 1KG</p>
        </td>
        <td style="width:54pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;text-indent: 0pt;text-align: center;">5</p></td>
        <td style="width:39pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4"
               style="padding-top: 2pt;padding-left: 3pt;padding-right: 3pt;text-indent: 0pt;text-align: center;">
                forfa</p></td>
    </tr>
    <tr style="height:16pt">
        <td style="width:473pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 3pt;text-indent: 0pt;text-align: left;">CLIP INOX
                FIXATION BANDE DE RIVE OURLET DE 14 ZINC NATUREL - BOITE 100</p></td>
        <td style="width:54pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 2pt;text-indent: 0pt;text-align: center;">25</p></td>
        <td style="width:39pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;text-indent: 0pt;text-align: center;">u</p></td>
    </tr>
    <tr style="height:16pt">
        <td style="width:473pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 3pt;text-indent: 0pt;text-align: left;">COLLIER DE
                DESCENTE INOX Ø80 7X150MM</p></td>
        <td style="width:54pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 2pt;text-indent: 0pt;text-align: center;">20</p></td>
        <td style="width:39pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;text-indent: 0pt;text-align: center;">u</p></td>
    </tr>
    <tr style="height:16pt">
        <td style="width:473pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 3pt;text-indent: 0pt;text-align: left;">COUDE ZINC
                NATUREL VM A 72° P.TUYAU CYLINDRIQUE SOUDE B.A B. Ø100MM</p></td>
        <td style="width:54pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;text-indent: 0pt;text-align: center;">6</p></td>
        <td style="width:39pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;text-indent: 0pt;text-align: center;">u</p></td>
    </tr>
    <tr style="height:16pt">
        <td style="width:473pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 3pt;text-indent: 0pt;text-align: left;">COUVRE-JOINT
                ZINC NATUREL VM DEV.100 EP.0,65MM 2ML</p></td>
        <td style="width:54pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 2pt;text-indent: 0pt;text-align: center;">5,25</p></td>
        <td style="width:39pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4"
               style="padding-top: 2pt;padding-left: 3pt;padding-right: 2pt;text-indent: 0pt;text-align: center;">ml</p>
        </td>
    </tr>
    <tr style="height:16pt">
        <td style="width:473pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 3pt;text-indent: 0pt;text-align: left;">CROCHET D&#39;ARDOISE
                ANJOU COLOR 17% Ø2,4MM 9 AGRAFE - BOITE 700</p></td>
        <td style="width:54pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 2pt;text-indent: 0pt;text-align: center;">1,4</p></td>
        <td style="width:39pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;text-indent: 0pt;text-align: center;">u</p></td>
    </tr>
    <tr style="height:16pt">
        <td style="width:473pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 3pt;text-indent: 0pt;text-align: left;">CROCHET D&#39;ARDOISE
                ANJOU COLOR 17% Ø2,4MM 9 AGRAFE - BOITE 700</p></td>
        <td style="width:54pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 2pt;text-indent: 0pt;text-align: center;">0,7</p></td>
        <td style="width:39pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;text-indent: 0pt;text-align: center;">u</p></td>
    </tr>
    <tr style="height:16pt">
        <td style="width:473pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 3pt;text-indent: 0pt;text-align: left;">CROCHET D&#39;ARDOISE
                ANJOU COLOR 17% Ø2,4MM 9 POINTE - BOITE 900</p></td>
        <td style="width:54pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 2pt;text-indent: 0pt;text-align: center;">4,4</p></td>
        <td style="width:39pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;text-indent: 0pt;text-align: center;">u</p></td>
    </tr>
    <tr style="height:16pt">
        <td style="width:473pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 3pt;text-indent: 0pt;text-align: left;">CROCHET D&#39;ARDOISE
                ANJOU COLOR 17% Ø2,4MM 9 POINTE - BOITE 900</p></td>
        <td style="width:54pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 2pt;text-indent: 0pt;text-align: center;">0,7</p></td>
        <td style="width:39pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;text-indent: 0pt;text-align: center;">u</p></td>
    </tr>
    <tr style="height:16pt">
        <td style="width:473pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 3pt;text-indent: 0pt;text-align: left;">CROCHET D&#39;ARDOISE
                ANJOU COLOR 17% Ø2,7MM 11 AGRAFE - BOITE 500</p></td>
        <td style="width:54pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 2pt;text-indent: 0pt;text-align: center;">4,4</p></td>
        <td style="width:39pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;text-indent: 0pt;text-align: center;">u</p></td>
    </tr>
    <tr style="height:16pt">
        <td style="width:473pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 3pt;text-indent: 0pt;text-align: left;">CROCHET D&#39;ARDOISE
                CROSINUS ANJOU COLOR 17% Ø2,7MM 12 POINTE - BOITE 700</p></td>
        <td style="width:54pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 2pt;text-indent: 0pt;text-align: center;">1,4</p></td>
        <td style="width:39pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;text-indent: 0pt;text-align: center;">u</p></td>
    </tr>
    <tr style="height:16pt">
        <td style="width:473pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 3pt;text-indent: 0pt;text-align: left;">CROCHET D&#39;ARDOISE
                CROSINUS ANJOU COLOR 17% Ø2,7MM 14 AGRAFE - BOITE 500</p></td>
        <td style="width:54pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 2pt;text-indent: 0pt;text-align: center;">4,4</p></td>
        <td style="width:39pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;text-indent: 0pt;text-align: center;">u</p></td>
    </tr>
    <tr style="height:20pt">
        <td style="width:473pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 3pt;text-indent: 0pt;text-align: left;">CROCHET D&#39;ARDOISE
                CROSINUS ANJOU COLOR 17% Ø2,7MM 15 POINTE - BOITE 500</p></td>
        <td style="width:54pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 2pt;text-indent: 0pt;text-align: center;">1,4</p></td>
        <td style="width:39pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;text-indent: 0pt;text-align: center;">u</p></td>
    </tr>
</table>
<p style="text-indent: 0pt;text-align: left;"><br/></p>
<table style="border-collapse:collapse;width: 100%" cellspacing="0">
    <tr style="height:15pt">
        <td style="width:473pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s3" style="padding-left: 3pt;text-indent: 0pt;line-height: 14pt;text-align: left;">Fourniture</p>
        </td>
        <td style="width:54pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s3"
               style="padding-left: 2pt;padding-right: 1pt;text-indent: 0pt;line-height: 14pt;text-align: center;">
                Quantite</p></td>
        <td style="width:39pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s3"
               style="padding-left: 3pt;padding-right: 3pt;text-indent: 0pt;line-height: 14pt;text-align: center;">
                Unite</p></td>
    </tr>
    <tr style="height:14pt">
        <td style="width:473pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-left: 3pt;text-indent: 0pt;text-align: left;">Etanchéité mono PVC colle
                Flexocol A89 pour support maçonnerie et bois 0,7kg/m2 bidon de 12kg</p></td>
        <td style="width:54pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-left: 2pt;text-indent: 0pt;text-align: center;">2,85</p></td>
        <td style="width:39pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-left: 3pt;padding-right: 2pt;text-indent: 0pt;text-align: center;">kg</p></td>
    </tr>
    <tr style="height:16pt">
        <td style="width:473pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 3pt;text-indent: 0pt;text-align: left;">Etanchéité mono
                PVC colle Flexocol A89 pour support maçonnerie et bois 0,7kg/m2 bidon de 12kg</p></td>
        <td style="width:54pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 2pt;text-indent: 0pt;text-align: center;">22,5</p></td>
        <td style="width:39pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4"
               style="padding-top: 2pt;padding-left: 3pt;padding-right: 2pt;text-indent: 0pt;text-align: center;">kg</p>
        </td>
    </tr>
    <tr style="height:16pt">
        <td style="width:473pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 3pt;text-indent: 0pt;text-align: left;">Etanchéité mono
                PVC colle Flexocol A89 pour support maçonnerie et bois 0,7kg/m2 bidon de 12kg</p></td>
        <td style="width:54pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 2pt;text-indent: 0pt;text-align: center;">2,85</p></td>
        <td style="width:39pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4"
               style="padding-top: 2pt;padding-left: 3pt;padding-right: 2pt;text-indent: 0pt;text-align: center;">kg</p>
        </td>
    </tr>
    <tr style="height:16pt">
        <td style="width:473pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 3pt;text-indent: 0pt;text-align: left;">Etanchéité mono
                PVC rail de fixation type Flagorail long 3ml</p></td>
        <td style="width:54pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 2pt;text-indent: 0pt;text-align: center;">9,5</p></td>
        <td style="width:39pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4"
               style="padding-top: 2pt;padding-left: 3pt;padding-right: 2pt;text-indent: 0pt;text-align: center;">ml</p>
        </td>
    </tr>
    <tr style="height:16pt">
        <td style="width:473pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 3pt;text-indent: 0pt;text-align: left;">Etanchéité mono
                PVC rail de fixation type Flagorail long 3ml</p></td>
        <td style="width:54pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 2pt;text-indent: 0pt;text-align: center;">9,5</p></td>
        <td style="width:39pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4"
               style="padding-top: 2pt;padding-left: 3pt;padding-right: 2pt;text-indent: 0pt;text-align: center;">ml</p>
        </td>
    </tr>
    <tr style="height:16pt">
        <td style="width:473pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 3pt;text-indent: 0pt;text-align: left;">Etanchéité
                monocouche PVC membrane Flagon SFc 15/10 en adhérence totale rouleau de 20,00mx1,60m</p></td>
        <td style="width:54pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 2pt;text-indent: 0pt;text-align: center;">9,5</p></td>
        <td style="width:39pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4"
               style="padding-top: 2pt;padding-left: 3pt;padding-right: 2pt;text-indent: 0pt;text-align: center;">m²</p>
        </td>
    </tr>
    <tr style="height:16pt">
        <td style="width:473pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 3pt;text-indent: 0pt;text-align: left;">Etanchéité
                monocouche PVC membrane Flagon SFc 15/10 en adhérence totale rouleau de 20,00mx1,60m</p></td>
        <td style="width:54pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 2pt;text-indent: 0pt;text-align: center;">45</p></td>
        <td style="width:39pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4"
               style="padding-top: 2pt;padding-left: 3pt;padding-right: 2pt;text-indent: 0pt;text-align: center;">m²</p>
        </td>
    </tr>
    <tr style="height:16pt">
        <td style="width:473pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 3pt;text-indent: 0pt;text-align: left;">Etanchéité
                monocouche PVC membrane Flagon SFc 15/10 en adhérence totale rouleau de 20,00mx1,60m</p></td>
        <td style="width:54pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 2pt;text-indent: 0pt;text-align: center;">9,5</p></td>
        <td style="width:39pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4"
               style="padding-top: 2pt;padding-left: 3pt;padding-right: 2pt;text-indent: 0pt;text-align: center;">m²</p>
        </td>
    </tr>
    <tr style="height:16pt">
        <td style="width:473pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 3pt;text-indent: 0pt;text-align: left;">Etanchéité
                monocouche PVC nettoyant soudure Flagon PVC cleaner bidon de 3 litres</p></td>
        <td style="width:54pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 2pt;text-indent: 0pt;text-align: center;">2,85</p></td>
        <td style="width:39pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;text-indent: 0pt;text-align: center;">l</p></td>
    </tr>
    <tr style="height:16pt">
        <td style="width:473pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 3pt;text-indent: 0pt;text-align: left;">Etanchéité
                monocouche PVC nettoyant soudure Flagon PVC cleaner bidon de 3 litres</p></td>
        <td style="width:54pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 2pt;text-indent: 0pt;text-align: center;">2,85</p></td>
        <td style="width:39pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;text-indent: 0pt;text-align: center;">l</p></td>
    </tr>
    <tr style="height:16pt">
        <td style="width:473pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 3pt;text-indent: 0pt;text-align: left;">Étanchéité
                SOPREMA enduit d&#39;imprégnation AQUADERE env. 0,30l/m² suivant support bidon de 5 litres</p></td>
        <td style="width:54pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 2pt;text-indent: 0pt;text-align: center;">1,5</p></td>
        <td style="width:39pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;text-indent: 0pt;text-align: center;">l</p></td>
    </tr>
    <tr style="height:16pt">
        <td style="width:473pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 3pt;text-indent: 0pt;text-align: left;">Étanchéité
                SOPREMA équerre de renfort SOPRALENE rouleau de 10,00m x0,25m</p></td>
        <td style="width:54pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 2pt;text-indent: 0pt;text-align: center;">2,5</p></td>
        <td style="width:39pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4"
               style="padding-top: 2pt;padding-left: 3pt;padding-right: 2pt;text-indent: 0pt;text-align: center;">ml</p>
        </td>
    </tr>
    <tr style="height:16pt">
        <td style="width:473pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 3pt;text-indent: 0pt;text-align: left;">Étanchéité
                SOPREMA étanchéité autoprotégée SOPRALAST 50TV rouleau de 6,00m x 1,00m</p></td>
        <td style="width:54pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;text-indent: 0pt;text-align: center;">3</p></td>
        <td style="width:39pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4"
               style="padding-top: 2pt;padding-left: 3pt;padding-right: 2pt;text-indent: 0pt;text-align: center;">m²</p>
        </td>
    </tr>
    <tr style="height:16pt">
        <td style="width:473pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 3pt;text-indent: 0pt;text-align: left;">FEUILLE ZINC
                NATUREL VM 2000X1000MM EP.0,60</p></td>
        <td style="width:54pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 2pt;text-indent: 0pt;text-align: center;">2,5</p></td>
        <td style="width:39pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4"
               style="padding-top: 2pt;padding-left: 3pt;padding-right: 2pt;text-indent: 0pt;text-align: center;">m²</p>
        </td>
    </tr>
    <tr style="height:16pt">
        <td style="width:473pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 3pt;text-indent: 0pt;text-align: left;">GANTS DE TRAVAIL
                AVEC PAUME EN NITRILE NOIR/GRIS (PROTECTION NIV 3) TAILLE 10</p></td>
        <td style="width:54pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;text-indent: 0pt;text-align: center;">5</p></td>
        <td style="width:39pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;text-indent: 0pt;text-align: center;">u</p></td>
    </tr>
    <tr style="height:16pt">
        <td style="width:473pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 3pt;text-indent: 0pt;text-align: left;">LITEAU
                SAPIN-EPICEA NORD TRAITE CLASSE 2 15X50</p></td>
        <td style="width:54pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 2pt;text-indent: 0pt;text-align: center;">20</p></td>
        <td style="width:39pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;text-indent: 0pt;text-align: center;">u</p></td>
    </tr>
    <tr style="height:16pt">
        <td style="width:473pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 3pt;text-indent: 0pt;text-align: left;">MASTIC GLR PU -
                ETANCO - 310ML</p></td>
        <td style="width:54pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 2pt;text-indent: 0pt;text-align: center;">1,2</p></td>
        <td style="width:39pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;text-indent: 0pt;text-align: center;">u</p></td>
    </tr>
    <tr style="height:16pt">
        <td style="width:473pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 3pt;text-indent: 0pt;text-align: left;">NOQUET ZINC
                COUVERTURE 33/12,5MM</p></td>
        <td style="width:54pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 2pt;text-indent: 0pt;text-align: center;">115</p></td>
        <td style="width:39pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;text-indent: 0pt;text-align: center;">u</p></td>
    </tr>
    <tr style="height:16pt">
        <td style="width:473pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 3pt;text-indent: 0pt;text-align: left;">NOQUET ZINC
                COUVERTURE 33/12,5MM</p></td>
        <td style="width:54pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 2pt;text-indent: 0pt;text-align: center;">115</p></td>
        <td style="width:39pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;text-indent: 0pt;text-align: center;">u</p></td>
    </tr>
    <tr style="height:16pt">
        <td style="width:473pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 3pt;text-indent: 0pt;text-align: left;">PANNEAU
                ONDATHERM 1040TS EP.120MM LAQUE 25µ 63/100 EXT.</p></td>
        <td style="width:54pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 2pt;text-indent: 0pt;text-align: center;">52,8</p></td>
        <td style="width:39pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4"
               style="padding-top: 2pt;padding-left: 3pt;padding-right: 2pt;text-indent: 0pt;text-align: center;">m²</p>
        </td>
    </tr>
    <tr style="height:16pt">
        <td style="width:473pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 3pt;text-indent: 0pt;text-align: left;">PATTE A VIS BOIS
                M8 L70 - BOITE DE 100</p></td>
        <td style="width:54pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 2pt;text-indent: 0pt;text-align: center;">20</p></td>
        <td style="width:39pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;text-indent: 0pt;text-align: center;">u</p></td>
    </tr>
    <tr style="height:16pt">
        <td style="width:473pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 3pt;text-indent: 0pt;text-align: left;">PATTE
                COULISSANTE SYSTEME DELTA-VM ZINC - BOITE DE 250</p></td>
        <td style="width:54pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 2pt;text-indent: 0pt;text-align: center;">10</p></td>
        <td style="width:39pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;text-indent: 0pt;text-align: center;">u</p></td>
    </tr>
    <tr style="height:16pt">
        <td style="width:473pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 3pt;text-indent: 0pt;text-align: left;">PATTE INOX POUR
                FIXATION DE COUVRE-JOINT ZINC NATUREL - BOITE 100</p></td>
        <td style="width:54pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 2pt;text-indent: 0pt;text-align: center;">5,25</p></td>
        <td style="width:39pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;text-indent: 0pt;text-align: center;">u</p></td>
    </tr>
    <tr style="height:16pt">
        <td style="width:473pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 3pt;text-indent: 0pt;text-align: left;">Pattes de lapin,
                10 cm large</p></td>
        <td style="width:54pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;text-indent: 0pt;text-align: center;">5</p></td>
        <td style="width:39pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;text-indent: 0pt;text-align: center;">u</p></td>
    </tr>
    <tr style="height:16pt">
        <td style="width:473pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 3pt;text-indent: 0pt;text-align: left;">Pinceau à
                radiateurs, 60 mm large</p></td>
        <td style="width:54pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;text-indent: 0pt;text-align: center;">5</p></td>
        <td style="width:39pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;text-indent: 0pt;text-align: center;">u</p></td>
    </tr>
    <tr style="height:16pt">
        <td style="width:473pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 3pt;text-indent: 0pt;text-align: left;">POINTE ACIER
                TETE PLATE ORDINAIRE Ø2,7X50MM - BOITE 5KG</p></td>
        <td style="width:54pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 2pt;text-indent: 0pt;text-align: center;">0,2</p></td>
        <td style="width:39pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;text-indent: 0pt;text-align: center;">u</p></td>
    </tr>
    <tr style="height:16pt">
        <td style="width:473pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 3pt;text-indent: 0pt;text-align: left;">POINTE INOX 17%
                TETE EXTRA LARGE RONDE LISSE Ø2,7X27MM - BOITE 1KG</p></td>
        <td style="width:54pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 2pt;text-indent: 0pt;text-align: center;">0,5</p></td>
        <td style="width:39pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;text-indent: 0pt;text-align: center;">u</p></td>
    </tr>
    <tr style="height:16pt">
        <td style="width:473pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 3pt;text-indent: 0pt;text-align: left;">POINTE INOX 17%
                TETE EXTRA LARGE RONDE LISSE Ø2,7X27MM - BOITE 1KG</p></td>
        <td style="width:54pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 2pt;text-indent: 0pt;text-align: center;">0,25</p></td>
        <td style="width:39pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;text-indent: 0pt;text-align: center;">u</p></td>
    </tr>
    <tr style="height:16pt">
        <td style="width:473pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 3pt;text-indent: 0pt;text-align: left;">POINTE INOX 17%
                TETE EXTRA LARGE RONDE LISSE Ø2,7X27MM - BOITE 1KG</p></td>
        <td style="width:54pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 2pt;text-indent: 0pt;text-align: center;">0,25</p></td>
        <td style="width:39pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;text-indent: 0pt;text-align: center;">u</p></td>
    </tr>
    <tr style="height:16pt">
        <td style="width:473pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 3pt;text-indent: 0pt;text-align: left;">POLYANE TYPE 150
                EN 3 ML 342 M2</p></td>
        <td style="width:54pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 2pt;text-indent: 0pt;text-align: center;">600</p></td>
        <td style="width:39pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4"
               style="padding-top: 2pt;padding-left: 3pt;padding-right: 2pt;text-indent: 0pt;text-align: center;">m²</p>
        </td>
    </tr>
    <tr style="height:16pt">
        <td style="width:473pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 3pt;text-indent: 0pt;text-align: left;">RIVE SOLIN -
                2,10ML RAL 7016</p></td>
        <td style="width:54pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 2pt;text-indent: 0pt;text-align: center;">5,5</p></td>
        <td style="width:39pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4"
               style="padding-top: 2pt;padding-left: 3pt;padding-right: 2pt;text-indent: 0pt;text-align: center;">ml</p>
        </td>
    </tr>
    <tr style="height:16pt">
        <td style="width:473pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 3pt;text-indent: 0pt;text-align: left;">SAKRET - MORTIER
                COUVREUR SAC/25KG GRIS</p></td>
        <td style="width:54pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 2pt;text-indent: 0pt;text-align: center;">15</p></td>
        <td style="width:39pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4"
               style="padding-top: 2pt;padding-left: 3pt;padding-right: 2pt;text-indent: 0pt;text-align: center;">kg</p>
        </td>
    </tr>
    <tr style="height:16pt">
        <td style="width:473pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 3pt;text-indent: 0pt;text-align: left;">Seau plastique
                gradué universel 2,5 kg</p></td>
        <td style="width:54pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;text-indent: 0pt;text-align: center;">5</p></td>
        <td style="width:39pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;text-indent: 0pt;text-align: center;">u</p></td>
    </tr>
    <tr style="height:16pt">
        <td style="width:473pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 3pt;text-indent: 0pt;text-align: left;">SOUDURE ETAIN
                TARGETTE 33</p></td>
        <td style="width:54pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;text-indent: 0pt;text-align: center;">2</p></td>
        <td style="width:39pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4"
               style="padding-top: 2pt;padding-left: 3pt;padding-right: 3pt;text-indent: 0pt;text-align: center;">
                forfa</p></td>
    </tr>
    <tr style="height:16pt">
        <td style="width:473pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 3pt;text-indent: 0pt;text-align: left;">SOUDURE ETAIN
                TARGETTE 33</p></td>
        <td style="width:54pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 2pt;text-indent: 0pt;text-align: center;">0,3</p></td>
        <td style="width:39pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4"
               style="padding-top: 2pt;padding-left: 3pt;padding-right: 3pt;text-indent: 0pt;text-align: center;">
                forfa</p></td>
    </tr>
    <tr style="height:16pt">
        <td style="width:473pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 3pt;text-indent: 0pt;text-align: left;">Sous-traitance
                échafaudage</p></td>
        <td style="width:54pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;text-indent: 0pt;text-align: center;">1</p></td>
        <td style="width:39pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4"
               style="padding-top: 2pt;padding-left: 3pt;padding-right: 3pt;text-indent: 0pt;text-align: center;">
                forfa</p></td>
    </tr>
    <tr style="height:16pt">
        <td style="width:473pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 3pt;text-indent: 0pt;text-align: left;">Spatule bois
                (450 x 35 mm)</p></td>
        <td style="width:54pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;text-indent: 0pt;text-align: center;">5</p></td>
        <td style="width:39pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;text-indent: 0pt;text-align: center;">u</p></td>
    </tr>
    <tr style="height:16pt">
        <td style="width:473pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 3pt;text-indent: 0pt;text-align: left;">TASSEAU
                SAPIN-EPICEA TRAITE CLASSE 2 40X40X25</p></td>
        <td style="width:54pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 2pt;text-indent: 0pt;text-align: center;">5,25</p></td>
        <td style="width:39pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4"
               style="padding-top: 2pt;padding-left: 3pt;padding-right: 2pt;text-indent: 0pt;text-align: center;">ml</p>
        </td>
    </tr>
    <tr style="height:16pt">
        <td style="width:473pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 3pt;text-indent: 0pt;text-align: left;">Triflex Metal
                Primaire</p></td>
        <td style="width:54pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;text-indent: 0pt;text-align: center;">2</p></td>
        <td style="width:39pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;text-indent: 0pt;text-align: center;">l</p></td>
    </tr>
    <tr style="height:20pt">
        <td style="width:473pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 3pt;text-indent: 0pt;text-align: left;">Triflex
                nettoyant 9 l</p></td>
        <td style="width:54pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;text-indent: 0pt;text-align: center;">1</p></td>
        <td style="width:39pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;text-indent: 0pt;text-align: center;">l</p></td>
    </tr>
</table>
<p style="text-indent: 0pt;text-align: left;"><br/></p>
<table style="border-collapse:collapse;width:100%" cellspacing="0">
    <tr style="height:15pt">
        <td style="width:473pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s3" style="padding-left: 3pt;text-indent: 0pt;line-height: 14pt;text-align: left;">Fourniture</p>
        </td>
        <td style="width:54pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s3"
               style="padding-left: 2pt;padding-right: 1pt;text-indent: 0pt;line-height: 14pt;text-align: center;">
                Quantite</p></td>
        <td style="width:39pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s3"
               style="padding-left: 3pt;padding-right: 3pt;text-indent: 0pt;line-height: 14pt;text-align: center;">
                Unite</p></td>
    </tr>
    <tr style="height:14pt">
        <td style="width:473pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-left: 3pt;text-indent: 0pt;text-align: left;">Triflex ProDetail été gris
                pierre</p></td>
        <td style="width:54pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="text-indent: 0pt;text-align: center;">8</p></td>
        <td style="width:39pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-left: 3pt;padding-right: 2pt;text-indent: 0pt;text-align: center;">kg</p></td>
    </tr>
    <tr style="height:16pt">
        <td style="width:473pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 3pt;text-indent: 0pt;text-align: left;">Triflex
                ProDetail été gris pierre</p></td>
        <td style="width:54pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 2pt;text-indent: 0pt;text-align: center;">15</p></td>
        <td style="width:39pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4"
               style="padding-top: 2pt;padding-left: 3pt;padding-right: 2pt;text-indent: 0pt;text-align: center;">kg</p>
        </td>
    </tr>
    <tr style="height:16pt">
        <td style="width:473pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 3pt;text-indent: 0pt;text-align: left;">Triflex Scotch
                armé pour surface minérale, 50 mm</p></td>
        <td style="width:54pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 2pt;text-indent: 0pt;text-align: center;">0,5</p></td>
        <td style="width:39pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;text-indent: 0pt;text-align: center;">m</p></td>
    </tr>
    <tr style="height:16pt">
        <td style="width:473pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 3pt;text-indent: 0pt;text-align: left;">Triflex Voile de
                renfort 26,25 cm large</p></td>
        <td style="width:54pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 2pt;text-indent: 0pt;text-align: center;">15</p></td>
        <td style="width:39pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;text-indent: 0pt;text-align: center;">m</p></td>
    </tr>
    <tr style="height:16pt">
        <td style="width:473pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 3pt;text-indent: 0pt;text-align: left;">Triflex Voile de
                renfort 26,25 cm large</p></td>
        <td style="width:54pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 2pt;text-indent: 0pt;text-align: center;">5,5</p></td>
        <td style="width:39pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4"
               style="padding-top: 2pt;padding-left: 3pt;padding-right: 2pt;text-indent: 0pt;text-align: center;">ml</p>
        </td>
    </tr>
    <tr style="height:16pt">
        <td style="width:473pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 3pt;text-indent: 0pt;text-align: left;">TUYAU ZINC
                NATUREL VM TRONCONIQUE SOUDE Ø 80 0,65MM 2ML</p></td>
        <td style="width:54pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 2pt;text-indent: 0pt;text-align: center;">20</p></td>
        <td style="width:39pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4"
               style="padding-top: 2pt;padding-left: 3pt;padding-right: 2pt;text-indent: 0pt;text-align: center;">ml</p>
        </td>
    </tr>
    <tr style="height:16pt">
        <td style="width:473pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 3pt;text-indent: 0pt;text-align: left;">VINYTEC VIS TETE
                TORX 4X30 NATUREL - CARTON DE 500</p></td>
        <td style="width:54pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 2pt;text-indent: 0pt;text-align: center;">47,5</p></td>
        <td style="width:39pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;text-indent: 0pt;text-align: center;">u</p></td>
    </tr>
    <tr style="height:16pt">
        <td style="width:473pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 3pt;text-indent: 0pt;text-align: left;">VINYTEC VIS TETE
                TORX 4X30 NATUREL - CARTON DE 500</p></td>
        <td style="width:54pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 2pt;text-indent: 0pt;text-align: center;">47,5</p></td>
        <td style="width:39pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;text-indent: 0pt;text-align: center;">u</p></td>
    </tr>
    <tr style="height:16pt">
        <td style="width:473pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 3pt;text-indent: 0pt;text-align: left;">ZACROVIS 6 TH12
                2C POINTE FORET LAQUEE 6,3X38 RAL 7016</p></td>
        <td style="width:54pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 2pt;text-indent: 0pt;text-align: center;">15</p></td>
        <td style="width:39pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;text-indent: 0pt;text-align: center;">u</p></td>
    </tr>
    <tr style="height:17pt">
        <td style="width:473pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 3pt;text-indent: 0pt;text-align: left;">ZACROVIS 6 TH12
                2C POINTE FORET LAQUEE 6X100 RAL 5008</p></td>
        <td style="width:54pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;padding-left: 2pt;text-indent: 0pt;text-align: center;">192</p></td>
        <td style="width:39pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-top: 2pt;text-indent: 0pt;text-align: center;">u</p></td>
    </tr>
</table>
<p style="text-indent: 0pt;text-align: left;"><br/></p>
<p style="text-indent: 0pt;text-align: left;"><br/></p>
<h3 style="padding-left: 2pt;text-indent: 0pt;text-align: left;">Devis N° : DV01-21-0028</h3>
<p style="padding-top: 9pt;padding-left: 2pt;text-indent: 0pt;text-align: left;"><span
        style=" color: black; font-family:Arial, sans-serif; font-style: italic; font-weight: normal; text-decoration: none; font-size: 9.5pt;">Date de l&#39;accueil chantier :</span>
</p>
<p style="padding-left: 5pt;text-indent: 0pt;text-align: left;"/>
<p style="text-indent: 0pt;text-align: left;"><br/></p>
<table style="border-collapse:collapse;width:100%" cellspacing="0">
    <tr style="height:26pt">
        <td style="width:121pt;border-top-style:solid;border-top-width:2pt;border-left-style:solid;border-left-width:2pt;border-bottom-style:solid;border-bottom-width:2pt;border-right-style:solid;border-right-width:1pt"
            rowspan="2"><p style="text-indent: 0pt;text-align: left;"><br/></p>
            <p class="s3" style="padding-left: 46pt;padding-right: 45pt;text-indent: 0pt;text-align: center;">Date</p>
        </td>
        <td style="width:120pt;border-top-style:solid;border-top-width:2pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:2pt;border-right-style:solid;border-right-width:1pt"
            rowspan="2"><p style="text-indent: 0pt;text-align: left;"><br/></p>
            <p class="s3" style="padding-left: 31pt;text-indent: 0pt;text-align: left;">Opérateur</p></td>
        <td style="width:200pt;border-top-style:solid;border-top-width:2pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt"
            colspan="2"><p class="s3" style="padding-top: 5pt;padding-left: 42pt;text-indent: 0pt;text-align: left;">
            Heure d&#39;intervention</p></td>
        <td style="width:125pt;border-top-style:solid;border-top-width:2pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:2pt;border-right-style:solid;border-right-width:2pt"
            rowspan="2"><p class="s3"
                           style="padding-top: 1pt;padding-left: 13pt;padding-right: 13pt;text-indent: -1pt;text-align: center;">
            Visa si présent à l&#39;accueil chantier</p>
            <p class="s3"
               style="padding-left: 22pt;padding-right: 20pt;text-indent: 0pt;line-height: 14pt;text-align: center;">
                (Engagement)</p></td>
    </tr>
    <tr style="height:17pt">
        <td style="width:100pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:2pt;border-right-style:solid;border-right-width:1pt">
            <p class="s3"
               style="padding-top: 1pt;padding-left: 33pt;padding-right: 32pt;text-indent: 0pt;line-height: 14pt;text-align: center;">
                Matin</p></td>
        <td style="width:100pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:2pt;border-right-style:solid;border-right-width:1pt">
            <p class="s3"
               style="padding-top: 1pt;padding-left: 19pt;text-indent: 0pt;line-height: 14pt;text-align: left;">
                Après-midi</p></td>
    </tr>
    <tr style="height:46pt">
        <td style="width:121pt;border-top-style:solid;border-top-width:2pt;border-left-style:solid;border-left-width:2pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
        <td style="width:120pt;border-top-style:solid;border-top-width:2pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
        <td style="width:100pt;border-top-style:solid;border-top-width:2pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
        <td style="width:100pt;border-top-style:solid;border-top-width:2pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
        <td style="width:125pt;border-top-style:solid;border-top-width:2pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:2pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
    </tr>
    <tr style="height:47pt">
        <td style="width:121pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:2pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
        <td style="width:120pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
        <td style="width:100pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
        <td style="width:100pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
        <td style="width:125pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:2pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
    </tr>
    <tr style="height:47pt">
        <td style="width:121pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:2pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
        <td style="width:120pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
        <td style="width:100pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
        <td style="width:100pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
        <td style="width:125pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:2pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
    </tr>
    <tr style="height:47pt">
        <td style="width:121pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:2pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
        <td style="width:120pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
        <td style="width:100pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
        <td style="width:100pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
        <td style="width:125pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:2pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
    </tr>
    <tr style="height:47pt">
        <td style="width:121pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:2pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
        <td style="width:120pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
        <td style="width:100pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
        <td style="width:100pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
        <td style="width:125pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:2pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
    </tr>
    <tr style="height:47pt">
        <td style="width:121pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:2pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
        <td style="width:120pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
        <td style="width:100pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
        <td style="width:100pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
        <td style="width:125pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:2pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
    </tr>
    <tr style="height:47pt">
        <td style="width:121pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:2pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
        <td style="width:120pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
        <td style="width:100pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
        <td style="width:100pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
        <td style="width:125pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:2pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
    </tr>
    <tr style="height:46pt">
        <td style="width:121pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:2pt;border-bottom-style:solid;border-bottom-width:2pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
        <td style="width:120pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:2pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
        <td style="width:100pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:2pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
        <td style="width:100pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:2pt;border-right-style:solid;border-right-width:1pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
        <td style="width:125pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:2pt;border-right-style:solid;border-right-width:2pt">
            <p style="text-indent: 0pt;text-align: left;"><br/></p></td>
    </tr>
</table>
<p style="text-indent: 0pt;text-align: left;"><br/></p>
<p style="text-indent: 0pt;text-align: left;"><span><table border="0" cellspacing="0" cellpadding="0"><tr><td><img
        width="388" height="65"
        src="data:image/jpg;base64,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"/></td></tr></table></span>
</p>
<p class="s10" style="padding-top: 2pt;padding-left: 109pt;text-indent: 0pt;line-height: 11pt;text-align: left;">Au<span
        class="s11"> </span>cours<span class="s11"> </span>de<span class="s11"> </span>l&#39;intervention<span
        class="s11"> </span>avez-vous<span class="s11"> </span>remarqué<span class="s11"> </span>une</p>
<p class="s10" style="padding-left: 121pt;text-indent: 0pt;line-height: 16pt;text-align: left;">situation<span
        class="s11"> </span>dangereuse<span class="s11"> </span>ou<span class="s11"> </span>un<span class="s11"> </span>presque<span
        class="s11"> </span>accident<span class="s11"> </span>?<span class="s11">        </span><span
        class="s12">Oui</span><span class="s13">        </span><span class="s12">Non</span></p>
<p class="s11" style="padding-top: 14pt;padding-left: 102pt;text-indent: 0pt;text-align: left;"><span
        class="s10">Si</span> <span class="s10">oui,</span> <span class="s10">remplissez</span> <span
        class="s10">une</span> <span class="s14">&quot;</span> <span class="s14">Fiche</span> <span
        class="s14">de</span> <span class="s14">signalement</span> <span class="s14">de</span> <span class="s14">situation</span>
    <span class="s14">dangereuse,</span> <span class="s14">presqu&#39;accident</span> <span class="s14">&quot;</span>
</p>
<p class="s10" style="padding-top: 1pt;padding-left: 291pt;text-indent: 0pt;text-align: left;">avec<span
        class="s11"> </span>votre<span class="s11"> </span>responsable<span class="s11"> </span>de<span
        class="s11"> </span>chantier.</p>
<p style="text-indent: 0pt;text-align: left;"><br/></p>
<table style="border-collapse:collapse;width:100%" cellspacing="0">
    <tr style="height:113pt">
        <td style="width:566pt;border-top-style:solid;border-top-width:2pt;border-left-style:solid;border-left-width:2pt;border-bottom-style:solid;border-bottom-width:2pt;border-right-style:solid;border-right-width:2pt"
            colspan="2"><p class="s15" style="padding-top: 7pt;padding-left: 9pt;text-indent: 0pt;text-align: left;">
            En<span class="s16"> </span>signant<span class="s16"> </span>ce<span class="s16"> </span>document<span
                class="s16"> </span>je<span class="s16"> </span>reconnais<span class="s16"> </span>avoir<span
                class="s16"> </span>:</p>
            <ul id="l1">
                <li data-list-text="-"><p class="s15"
                                          style="padding-top: 1pt;padding-left: 14pt;padding-right: 28pt;text-indent: 0pt;text-align: left;">
                    reçu<span class="s16"> </span>une<span class="s16"> </span>formation,<span
                        class="s16"> </span>ou<span class="s16"> </span>un<span class="s16"> </span>rappel<span
                        class="s16"> </span>des<span class="s16"> </span>règles<span class="s16"> </span>de<span
                        class="s16"> </span>Sécurité<span class="s16"> </span>applicables<span
                        class="s16"> </span>sur<span class="s16"> </span>le<span class="s16"> </span>chantier<span
                        class="s16"> </span>au<span class="s16"> </span>travers<span class="s16"> </span>de<span
                        class="s16"> </span>la<span class="s16"> </span>fiche<span class="s16"> </span>de<span
                        class="s16"> </span>suivi<span class="s16"> </span>commentée<span class="s16"> </span>par<span
                        class="s16"> </span>l&#39;accueillant.</p></li>
                <li data-list-text="-"><p class="s15"
                                          style="padding-top: 7pt;padding-left: 20pt;text-indent: -6pt;text-align: left;">
                    pris<span class="s16"> </span>connaissance<span class="s16"> </span>des<span class="s16"> </span>risques<span
                        class="s16"> </span>liés<span class="s16"> </span>à<span class="s16"> </span>l&#39;intervention
                </p></li>
            </ul>
            <p class="s17"
               style="padding-top: 8pt;padding-left: 82pt;padding-right: 77pt;text-indent: 0pt;text-align: center;">
                Je<span class="s18"> </span>m&#39;engage<span class="s18"> </span>à<span
                    class="s18"> </span>respecter<span class="s18"> </span>les<span class="s18"> </span>règles<span
                    class="s18"> </span>de<span class="s18"> </span>sécurité<span class="s18"> </span>applicables</p>
            <p class="s17"
               style="padding-top: 1pt;padding-left: 82pt;padding-right: 77pt;text-indent: 0pt;text-align: center;">lors<span
                    class="s18"> </span>de<span class="s18"> </span>l&#39;exécution<span class="s18"> </span>des<span
                    class="s18"> </span>travaux<span class="s18"> </span>et<span class="s18"> </span>pour<span
                    class="s18"> </span>l&#39;utilisation<span class="s18"> </span>du<span
                    class="s18"> </span>matériel<span class="s18"> </span>qui<span class="s18"> </span>m&#39;est<span
                    class="s18"> </span>confié.</p></td>
    </tr>
    <tr style="height:63pt">
        <td style="width:324pt;border-top-style:solid;border-top-width:2pt;border-left-style:solid;border-left-width:2pt;border-bottom-style:solid;border-bottom-width:2pt;border-right-style:solid;border-right-width:2pt">
            <p class="s17" style="padding-left: 3pt;text-indent: 0pt;text-align: left;">Nom<span
                    class="s18"> </span>et<span class="s18"> </span>signature<span class="s18"> </span>du<span
                    class="s18"> </span>responsable<span class="s18"> </span>chantier<span class="s18"> </span>et<span
                    class="s18"> </span>sécurité<span class="s18"> </span>chantier</p></td>
        <td style="width:242pt;border-top-style:solid;border-top-width:2pt;border-left-style:solid;border-left-width:2pt;border-bottom-style:solid;border-bottom-width:2pt;border-right-style:solid;border-right-width:2pt">
            <p class="s17" style="padding-left: 3pt;text-indent: 0pt;text-align: left;">Nom<span
                    class="s18"> </span>et<span class="s18"> </span>signature<span class="s18"> </span>de<span
                    class="s18"> </span>l&#39;accueillant</p></td>
    </tr>
</table>
<p style="text-indent: 0pt;text-align: left;"><br/></p>
<p style="text-indent: 0pt;text-align: left;"><span><table border="0" cellspacing="0" cellpadding="0"><tr><td><img
        width="172" height="105"
        src="data:image/jpg;base64,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"/></td></tr></table></span>
</p>
<h1 style="padding-top: 4pt;padding-left: 9pt;text-indent: 0pt;text-align: left;">Devis N° : DV01-21-0028 <span
        class="s19">Adresse du chantier</span></h1>
<p class="s20" style="padding-top: 7pt;padding-left: 9pt;text-indent: 0pt;text-align: left;">Date de l&#39;intervention
    :</p>
<h1 style="padding-top: 7pt;padding-left: 9pt;text-indent: 0pt;text-align: left;">
    .................../.................../ 20.............</h1>
<p class="s21" style="padding-top: 4pt;padding-left: 9pt;text-indent: 0pt;text-align: left;">Je soussigné <span
        class="s22">(nom et fonction du représentant du site d&#39;intervention). ................................................................................</span>
</p>
<p class="s21" style="padding-top: 1pt;padding-left: 9pt;text-indent: 0pt;line-height: 14pt;text-align: left;">. après
    avoir procédé aux examens</p>
<p class="s21" style="padding-left: 9pt;text-indent: 0pt;line-height: 14pt;text-align: left;">et vérifications
    nécessaires, constate que :</p>
<ol id="l2">
    <li data-list-text="1."><h2 style="padding-top: 10pt;padding-left: 105pt;text-indent: -12pt;text-align: left;">Les
        travaux et prestations :</h2>
        <p style="text-indent: 0pt;text-align: left;"/>
        <p class="s1" style="padding-top: 3pt;padding-left: 141pt;text-indent: 0pt;text-align: left;">Ont été
            effectués</p>
        <p style="text-indent: 0pt;text-align: left;"/>
        <p style="text-indent: 0pt;text-align: left;"/>
        <p class="s1" style="padding-top: 4pt;padding-left: 141pt;text-indent: 0pt;line-height: 137%;text-align: left;">
            Ont été effectués mais ne sont pas concluants N&#39;ont pas été effectués</p>
        <p style="text-indent: 0pt;text-align: left;"><br/></p></li>
    <li data-list-text="2."><h2 style="padding-left: 105pt;text-indent: -12pt;text-align: left;">Les installations de
        chantier :</h2>
        <p style="text-indent: 0pt;text-align: left;"/>
        <p class="s1" style="padding-top: 3pt;padding-left: 141pt;text-indent: 0pt;text-align: left;">Ont été
            repliées</p>
        <p style="text-indent: 0pt;text-align: left;"/>
        <p class="s1" style="padding-top: 4pt;padding-left: 141pt;text-indent: 0pt;text-align: left;">N&#39;ont pas été
            repliées</p>
        <p style="text-indent: 0pt;text-align: left;"><br/></p></li>
    <li data-list-text="3."><h2 style="padding-top: 7pt;padding-left: 105pt;text-indent: -12pt;text-align: left;">Les
        terrains et lieux :</h2></li>
</ol>
<p style="text-indent: 0pt;text-align: left;"/>
<p class="s1" style="padding-top: 3pt;padding-left: 141pt;text-indent: 0pt;text-align: left;">Ont été remis en état</p>
<p style="text-indent: 0pt;text-align: left;"/>
<p class="s1" style="padding-top: 4pt;padding-left: 141pt;text-indent: 0pt;text-align: left;">N&#39;ont pas été remis en
    état</p>
<p style="text-indent: 0pt;text-align: left;"><br/></p>
<p class="s23" style="padding-top: 2pt;padding-left: 9pt;text-indent: 0pt;text-align: left;">Commentaires<span
        class="s24"> </span>ou<span class="s24"> </span>Réserves<span class="s24"> </span>:</p>
<p style="text-indent: 0pt;text-align: left;"><br/></p>
<p style="padding-left: 6pt;text-indent: 0pt;line-height: 1pt;text-align: left;"/>
<p style="text-indent: 0pt;text-align: left;"><br/></p>
<p style="padding-left: 6pt;text-indent: 0pt;line-height: 1pt;text-align: left;"/>
<p style="text-indent: 0pt;text-align: left;"><br/></p>
<p style="padding-left: 6pt;text-indent: 0pt;line-height: 1pt;text-align: left;"/>
<p style="text-indent: 0pt;text-align: left;"><br/></p>
<p style="padding-left: 6pt;text-indent: 0pt;line-height: 1pt;text-align: left;"/>
<p style="text-indent: 0pt;text-align: left;"><br/></p>
<p style="padding-left: 6pt;text-indent: 0pt;line-height: 1pt;text-align: left;"/>
<p style="text-indent: 0pt;text-align: left;"><br/></p>
<p style="padding-left: 6pt;text-indent: 0pt;line-height: 1pt;text-align: left;"/>
<p style="text-indent: 0pt;text-align: left;"><br/></p>
<p style="padding-left: 6pt;text-indent: 0pt;line-height: 1pt;text-align: left;"/>
<p style="text-indent: 0pt;text-align: left;"><br/></p>
<p style="padding-left: 6pt;text-indent: 0pt;line-height: 1pt;text-align: left;"/>
<p style="text-indent: 0pt;text-align: left;"><br/></p>
<p style="padding-left: 6pt;text-indent: 0pt;line-height: 1pt;text-align: left;"/>
<p style="text-indent: 0pt;text-align: left;"><br/></p>
<p style="padding-left: 6pt;text-indent: 0pt;line-height: 1pt;text-align: left;"/>
<p style="text-indent: 0pt;text-align: left;"><br/></p>
<p class="s21" style="padding-left: 2pt;text-indent: 0pt;line-height: 13pt;text-align: left;">Le Représentant de la
    société intervenante</p>
<p class="s24" style="padding-top: 1pt;padding-left: 2pt;text-indent: 0pt;text-align: left;"><span
        style=" color: black; font-family:Calibri, sans-serif; font-style: normal; font-weight: normal; text-decoration: underline; font-size: 12pt;">Nom</span>
    <span style=" color: black; font-family:Calibri, sans-serif; font-style: normal; font-weight: normal; text-decoration: underline; font-size: 12pt;">/</span>
    <span style=" color: black; font-family:Calibri, sans-serif; font-style: normal; font-weight: normal; text-decoration: underline; font-size: 12pt;">Signature</span>
    <span style=" color: black; font-family:Calibri, sans-serif; font-style: normal; font-weight: normal; text-decoration: underline; font-size: 12pt;">/</span>
    <span style=" color: black; font-family:Calibri, sans-serif; font-style: normal; font-weight: normal; text-decoration: underline; font-size: 12pt;">Cachet</span><span
            style=" color: black; font-family:&quot;Times New Roman&quot;, serif; font-style: normal; font-weight: normal; text-decoration: none; font-size: 12pt;"> </span><span
            style=" color: black; font-family:Calibri, sans-serif; font-style: normal; font-weight: normal; text-decoration: underline; font-size: 12pt;">:</span>
</p>
<p class="s21" style="padding-left: 2pt;text-indent: 0pt;line-height: 13pt;text-align: left;">Le Représentant du site d&#39;intervention.</p>
<p class="s24" style="padding-top: 1pt;padding-left: 2pt;text-indent: 0pt;text-align: left;"><span
        style=" color: black; font-family:Calibri, sans-serif; font-style: normal; font-weight: normal; text-decoration: underline; font-size: 12pt;">Nom</span>
    <span style=" color: black; font-family:Calibri, sans-serif; font-style: normal; font-weight: normal; text-decoration: underline; font-size: 12pt;">/</span>
    <span style=" color: black; font-family:Calibri, sans-serif; font-style: normal; font-weight: normal; text-decoration: underline; font-size: 12pt;">Signature</span>
    <span style=" color: black; font-family:Calibri, sans-serif; font-style: normal; font-weight: normal; text-decoration: underline; font-size: 12pt;">/</span>
    <span style=" color: black; font-family:Calibri, sans-serif; font-style: normal; font-weight: normal; text-decoration: underline; font-size: 12pt;">Cachet</span><span
            style=" color: black; font-family:&quot;Times New Roman&quot;, serif; font-style: normal; font-weight: normal; text-decoration: none; font-size: 12pt;"> </span><span
            style=" color: black; font-family:Calibri, sans-serif; font-style: normal; font-weight: normal; text-decoration: underline; font-size: 12pt;">:</span>
</p>
<p style="padding-left: 5pt;text-indent: 0pt;text-align: left;"/></body>
</html>
';
     /*   $snappy = App::make('snappy.pdf');
// //To file

// $snappy->generateFromHtml($html, '/tmp/bill-123.pdf');
// $snappy->generate('http://www.github.com', '/tmp/github.pdf');

//Or output:
return response(
    

    $snappy->getOutputFromHtml($html),
    200,
    array(
        'Content-Type'          => 'application/pdf',
        'Content-Disposition'   => 'attachment; filename="file.pdf"'
    )
);*/
return SnappyPdf::loadHTML($html)->setPaper('a4')->setOption('dpi',150)->setOrientation('portrait')->setOption('margin-top', '70px')->setOption('margin-bottom', '70px')->setOption('margin-left', '10px')->setOption('margin-right', '10px')
->setOption('header-html','<!DOCTYPE  html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html><body>
<div style="display: -webkit-box;"><p style="color:red;font-size:30px;margin-top:0;  -webkit-box-flex: 1;-webkit-flex: 1;flex:1;">HELLO HEADER</p><p style="-webkit-box-flex: 1;-webkit-flex: 1;flex:1">xxxxxxxxx</p></div></body></html>')
->setOption('footer-html','<!DOCTYPE  html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html><body><p style="color:green;font-size:30px;margin-top:0;">HELLO FOOTER</p><p>xxxxxxxxx</p></body></html>')
->setOption('header-line',false)
->inline('github.pdf');
//return Snappy::loadFile('http://www.github.com')->inline('github.pdf');
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
        return response($this->htmltable($a,$global_sql_variables));
    }

    public function htmltable($a,$global_sql_variables,$addtional_vars=[]){
        
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
     
   
        $html_table='<table id="'.$a->table_id.'" '.$this->attrs($a).'>';
        //thead
        $html_table.='<thead  '.$this->attrs($a->thead).'>';
        foreach($a->thead->tr as $theadtr){
            $html_table.='<tr '.$this->attrs($theadtr).'>';
            foreach($theadtr->th as $th){
                $html_table.='<th  '.$this->attrs($th).'>'.(isset($th->name)?$th->name:'').'</th>';
            }
            $html_table.='</tr>';
        }
        
        $html_table.='</thead>';
        //end thead

        //tbody
        $html_table.='<tbody  '.$this->attrs($a->tbody).'>';
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
            $html_table.='<tr '.$this->attrs($a->tbody->tr).'>';

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
                            $html_table.='<td '.$this->attrs($a->tbody->tr->td).'>'.$this->htmltable($th->table,$global_sql_variables,$d).'</td>';
                        }else{
                            $html_table.='<td '.$this->attrs($a->tbody->tr->td).'>'.($th->type=="empty"?'':$d->{$th->identifier}).'</td>';
                        }
                    }
                }else{
                    $html_table.='<td colspan="'.count($a->thead->tr[0]->th).'" '.$this->attrs($a->tbody->tr->td).'>'.$this->htmltable($a->tbody->tr->td->table,$global_sql_variables,$d).'</td>';
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
                $htmltemplate=HtmlTemplate::find($template_id);
                if($htmltemplate==null)
                return response('Template non trouvée',509);

                if($htmltemplate->affiliate_id!=$user->affiliate_id)
                return response('Impossible de sauvegarder. Le template n\'est pas dans la même affiliée que l\'utilisateur',509);

            }else{
                $htmltemplate=new HtmlTemplate();
                $htmltemplate->affiliate_id=$user->affiliate_id;
            }
            $htmltemplate->name=$conf['name'];
            $htmltemplate->type=$conf['type'];
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
            $htmltemplate=HtmlTemplate::find($template_id);
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
            return response()->json(array('conf'=>$htmltemplate,'headerList'=>$headerList,'footerList'=>$footerList,'currentFooter'=>$currentFooter,'currentHeader'=>$currentHeader));
        }else{
            return response('Template id est requis',509);
        }

    }
    public function removeHtmlTemplateElement(Request $request){
      
        $element=$request->get('element');
        $user=Auth::user();
        $el=HtmltemplateElement::find($element['id']);
        if($el->htmltemplate_id==$element['htmltemplate_id']){
            $htmltemplate=HtmlTemplate::find($el->htmltemplate_id);
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
            $htmltemplate=HtmlTemplate::find($template_id);
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
            $htmltemplate=HtmlTemplate::find($template_id);
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
                        $el->rendered_data=$this->htmltable(json_decode($el->data),$global_test_vars);
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
            $htmltemplate=HtmlTemplate::find($template_id);
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
        
            if($hf['type']=='header'){
                $hfobj=HtmltemplateHeader::find($hf['id']);
            }else if($hf['type']=='footer'){
                $hfobj=HtmltemplateFooter::find($hf['id']);
            }else{
                return response('Impossible de sauvegarder. Type de l\'objet inconnu',509);
            }

            $user=Auth::user();
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

    public function generateTest(Request $request){
        $id=$request->post('id');
        $ht=HtmlTemplate::find($id);
        //validations

        //make html and insert in table

        //pdf is downloaded field;
        //regen html field

       return $this->renderPDF($notification=Notification::add($ht->name,json_decode($ht->global_test_vars,true),1));




    }


    public function generatePdf(Request $request){

    }


    public function renderPDF(Notification $notification){
        $ht=HtmlTemplate::find($notification->template);
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
        ->inline('github.pdf');
    }

    
}


