<?php

use App\Http\Controllers\ActionCommercialListController;
use App\Models\User;
use App\Models\Campagne;
use App\Models\page_builder;
use TCG\Voyager\Facades\Voyager;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\CibleController;
use App\Http\Controllers\DevisController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\CompagneController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\LcdtAdminController;
use App\Http\Controllers\LcdtFrontController;
use App\Http\Controllers\TemplatesController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CampagneListController;
use App\Http\Controllers\EntiteController;
use App\Http\Controllers\PageElementsController;


Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();
    Route::post('/update-text-pos',[LcdtAdminController::class,'updateTextPos'])->name('update-text-pos');
    Route::post('/get-text-pos',[LcdtAdminController::class,'getTextPos'])->name('get-text-pos');
});

Route::post('/save-page-elements', [PageElementsController::class, 'generate_pdf']);
Route::get('/generate-pdf/{report}', [PageElementsController::class, 'generate_pdf_by_id']);
Route::post('/generate-pdf/{report}', [PageElementsController::class, 'generate_pdf_by_id']);

Route::delete('delete-report/{report}', [PageElementsController::class, 'delete_report']);

Route::post('/report-templates', [TemplatesController::class, 'index']);
Route::get('/get-report-templates', [TemplatesController::class, 'report_templates']);
Route::post('/report-template', [TemplatesController::class, 'store']);
Route::get('/report-template/{template}', [TemplatesController::class, 'show']);
Route::post('/report-template/{template}', [TemplatesController::class, 'update']);

Route::post('/page-reports', [ReportsController::class, 'index'])->middleware('auth');
Route::post('/page-report', [ReportsController::class, 'store']);
Route::get('/page-report/{report}', [ReportsController::class, 'show']);
Route::post('/page-report/{report}', [ReportsController::class, 'update']);


Route::get('/get-page-order/{order}', [PageElementsController::class, 'get_page_order']);
Route::get('/get-templates', [PageElementsController::class, 'get_page_templates']);

Route::get('/search', [SearchController::class, 'search']);
Route::get('/search-append', [SearchController::class, 'search_append']);

Route::post('/save-letter-pdf/{campagne}', [CompagneController::class, 'save_letter_pdf']);
Route::post('/save-letter-settings/{campagne}', [CompagneController::class, 'save_letter_settings']);
Route::get('/stream-letter-pdf/{campagne}', [CompagneController::class, 'stream_letter_pdf']);
Route::post('/save-flyer-pdf/{campagne}', [CompagneController::class, 'save_flyer_pdf']);
Route::get('/stream-flyer-pdf/{campagne}', [CompagneController::class, 'stream_flyer_pdf']);
Route::post('/save-mail-csv/{campagne}', [CompagneController::class, 'generate_mail_csv_and_store']);
Route::post('/validate-and-send-email/{campagne}', [CompagneController::class, 'validate_and_send_email']);
Route::get('/download-resource-file', [CompagneController::class, 'download_resource_file']);
Route::post('/get-campagne-list', [CampagneListController::class, 'index']);
Route::get('/get-campagne-details/{campagne}', [CompagneController::class, 'campagne_details']);
Route::post('/get-user-campagne-list', [CampagneListController::class, 'user_campagnes']);
Route::get('/get-campagne-category/{campagne}', [CompagneController::class, 'get_campagne_category']);
Route::post('/store-campagne-product/{campagne}', [CompagneController::class, 'store_campagne_product']);
Route::get('/get-fields-marketing/{campagne}', [CompagneController::class, 'fields_for_marketing'])->middleware('auth')->name('fields_marketing');
Route::get('/get-card-products', [CompagneController::class, 'get_card_products']);
Route::put('/card-product/{card}', [CompagneController::class, 'update_card_product'])->middleware('auth');
Route::delete('/card-product/{card}', [CompagneController::class, 'delete_card_product'])->middleware('auth');
Route::post('/valider-card', [CompagneController::class, 'valider_card'])->middleware('auth');
Route::post('/generate-campagne-product-pdf/{campagne}', [CompagneController::class, 'generate_pdf']);
Route::get('/generate-campagne-product-pdf/{campagne}', [CompagneController::class, 'generate_pdf']);


Route::get('/get-entite-list', [EntiteController::class, 'index']);
Route::post('/get-entite-list', [EntiteController::class, 'index']);
Route::post('/get-entite-list-user', [EntiteController::class, 'index']);
Route::get('/get-entite-list-details/{customer}', [EntiteController::class, 'get_details']);
Route::post('/change-entite-actif/{customer}', [EntiteController::class, 'change_entite_actif']);
Route::post('/change-entite-litige/{customer}', [EntiteController::class, 'change_entite_litige']);
Route::get('/get-entite-results/{customer}', [EntiteController::class, 'get_entite_results']);

Route::get('/action-commercial-list', [ActionCommercialListController::class, 'index']);
Route::post('/action-commercial-list', [ActionCommercialListController::class, 'index']);
Route::get('/action-commercial-list-mes', [ActionCommercialListController::class, 'list_user']);
Route::post('/action-commercial-list-mes', [ActionCommercialListController::class, 'list_user']);
Route::get('/action-commercial-details/{id}', [ActionCommercialListController::class, 'get_details']);
Route::post('/action-commercial-statuses', [ActionCommercialListController::class, 'statuses_formatted']);
Route::post('/get-action-commercial-statuses', [ActionCommercialListController::class, 'statuses']);
Route::post('/change-action-commercial-event-date/{event}', [ActionCommercialListController::class, 'change_event_date']);
Route::post('/change-action-commercial-event-user/{event}', [ActionCommercialListController::class, 'change_event_user']);
Route::get('/get-action-commercial-event-users/{event}', [ActionCommercialListController::class, 'get_event_users']);
Route::get('/get-event-history/{event}', [ActionCommercialListController::class, 'get_event_history']);
Route::get('/get-event-statuses-all', [ActionCommercialListController::class, 'get_event_statuses']);
Route::post('/change-event-status/{event}', [ActionCommercialListController::class, 'change_event_status']);
// create action
Route::post('/get-action-info', [ActionCommercialListController::class, 'getActionInfo'])->name('get.action.info');
Route::post('/action/create', [ActionCommercialListController::class, 'createAction'])->name('create.action');
Route::post('/action/edit/{event}', [ActionCommercialListController::class, 'updateAction'])->name('update.action');
Route::post('/get-action/{event}', [ActionCommercialListController::class, 'getAction'])->name('get.action');


Route::post('/api',[ApiController::class,'index'])->middleware('cors')->name('api');

Route::post('/authenticate',[LoginController::class, 'authenticate'])->name('authenticate');
Route::get('/logout',[LoginController::class, 'logout'])->name('logout');
Route::get('/test',[TestController::class, 'test'])->name('test');
Route::post('/auth/login',function () {
    return view('welcome');
})->name('login');


Route::group([
    'middleware' => 'web',
    'namespace'  => 'App\Http\Controllers'
], function () {

    Route::get('/getCustomerStatut',[CompagneController::class, 'getCustomerStatut'])->middleware('auth')->name('getCustomerStatut');
    Route::get('/getCampagneCategory',[CompagneController::class, 'getCampagneCategory'])->middleware('auth')->name('getCampagneCategory');
    Route::get('/getTemplates/{id}',[CompagneController::class, 'getTemplates'])->middleware('auth')->name('getTemplates');
    Route::get('/campagne_id/{id}',[CompagneController::class, 'campagne_id'])->middleware('auth')->name('campagne_id');
    Route::get('/getCampagneCampagneCategory/{id}',[CompagneController::class, 'getCampagneCampagneCategory'])->middleware('auth')->name('getCampagneCampagneCategory');
    Route::get('/getCampagny_Cible',[CompagneController::class, 'getCampagny_Cible'])->middleware('auth')->name('getCampagny_Cible');
    Route::post('/ciblesum',[CompagneController::class, 'ciblesum'])->middleware('auth')->name('ciblesum');
    Route::post('/sendEmailReminder',[CompagneController::class, 'sendEmailReminder'])->middleware('auth')->name('sendEmailReminder');
    Route::post('/creatCompagne',[CompagneController::class, 'creatCompagne'])->middleware('auth')->name('creatCompagne');
    Route::get('/getCampagneCible/{id}',[CompagneController::class, 'getCampagneCible'])->middleware('auth')->name('getCampagneCible');
    Route::get('/datarequest',[CompagneController::class, 'datarequest'])->middleware('auth')->name('datarequest');
    Route::get('/getStatusName',[CompagneController::class, 'getStatusName'])->middleware('auth')->name('getStatusName');
    Route::get('/getCibleByCampagne/{id}',[CompagneController::class, 'getCibleByCampagne'])->middleware('auth')->name('getCibleByCampagne');
    Route::get('/SubCategory/{id}',[CompagneController::class, 'SubCategory'])->middleware('auth')->name('SubCategory');
    Route::get('/list',[CompagneController::class, 'list'])->middleware('auth')->name('list');
    Route::get('/getCompgneCibleSelected/{id}',[CompagneController::class, 'getCompgneCibleSelected'])->middleware('auth')->name('getCompgneCibleSelected');
    Route::post('contentform/{id}', [CompagneController::class, 'contentform'])->middleware('auth')->name('contentform');

    Route::get('/cible/load/{campagne_category_id}',[CibleController::class,'initialload'])->middleware('auth')->name('initialload');
    Route::get('/cible/loadcible/{naf_selection}/{customer_statut_id}/{type}',[CibleController::class,'loadcible'])->middleware('auth')->name('loadcible');
    Route::post('/cible/createcampagne',[CibleController::class,'createcampagne'])->middleware('auth')->name('createcampagne');

    // Devis
    Route::post('/get-devis-list',[DevisController::class,'loadList'])->middleware('auth')->name('get-devis-list');
    Route::post('/get-order-detail',[DevisController::class,'getOrderDetail'])->middleware('auth')->name('get-order-detail');
    Route::post('/set-order-state',[DevisController::class,'setOrderState'])->middleware('auth')->name('set-order-state');
    Route::post('/get-order-detail-facturation',[DevisController::class,'loadOrderInvoices'])->middleware('auth')->name('get-order-detail-facturation');
    Route::post('/new-order-invoice',[DevisController::class,'newOrderInvoice'])->middleware('auth')->name('new-order-invoice');
    Route::post('/load-order-documents',[DevisController::class,'loadOrderDocuments'])->middleware('auth')->name('load-order-documents');
    Route::post('/upload-order-document',[DevisController::class,'uploadOrderDocument'])->middleware('auth')->name('upload-order-document');
    Route::post('/remove-order-document',[DevisController::class,'removeOrderDocument'])->middleware('auth')->name('remove-order-document');
    Route::post('/get-order-document-url',[DevisController::class,'getOrderDocumentUrl'])->middleware('auth')->name('get-order-document-url');
    
    
    Route::post('/remove-order-invoice',[DevisController::class,'removeOrderInvoice'])->middleware('auth')->name('remove-order-invoice');
    Route::post('/validate-order-invoice',[DevisController::class,'validateOrderInvoice'])->middleware('auth')->name('validate-order-invoice');
    Route::post('/get-order-states',[DevisController::class,'getOrderStates'])->middleware('auth')->name('get-order-states');
    Route::post('/get-order-states-formatted',[DevisController::class,'getOrderStatesFormatted'])->middleware('auth')->name('get-order-states-formatted');
    Route::post('/get-ged-categories', [DevisController::class,'getGedCategories'])->middleware('auth')->name('get.ged.categories');
    Route::post('/get-all-toits', [DevisController::class,'getAllToits'])->middleware('auth')->name('get.all.toits');
    Route::post('/get-prestation-ouvrages', [DevisController::class,'getPrestationOuvrages'])->middleware('auth')->name('get.all.toits');
    Route::post('/search-ouvrage', [DevisController::class,'searchOuvrage'])->middleware('auth')->name('search.ouvrage');
    Route::post('/search-product', [DevisController::class,'searchProduct'])->middleware('auth')->name('search.product');
    Route::post('/get-ouvrage', [DevisController::class,'getOuvrage'])->middleware('auth')->name('get.ouvrage');
    Route::post('/get-suppliers', [DevisController::class,'getSuppliers'])->middleware('auth')->name('get.taxes');
    Route::post('/store-devis', [DevisController::class,'storeDevis'])->middleware('auth')->name('store.devis');
    Route::post('/get-info-for-emtpy-ouvrage', [DevisController::class,'getInfoForEmtpyOuvrage'])->middleware('auth')->name('get.info.for.empty.ouvrage');
    Route::post('/get-units', [DevisController::class,'getUnits'])->middleware('auth')->name('get.unit');
    Route::post('/get-interim-data', [DevisController::class,'getInterimData'])->middleware('auth')->name('get.interim.data');
    Route::post('/get-labor-data', [DevisController::class,'getLaborData'])->middleware('auth')->name('get.labor.data');
    Route::post('/get-devis/{id}', [DevisController::class, 'getDevis'])->middleware('auth')->name('get.devis');
    Route::post('/update-devis/{id}', [DevisController::class,'updateDevis'])->middleware('auth')->name('update.devis');
    // End Devis

    // Customer
    Route::post('/get-list-info-for-customer', [ CustomerController::class, 'getListInfoForCustomer' ])->middleware('auth')->name('get.list.info.for.customer');
    Route::post('/add-customer', [ CustomerController::class, 'storeCustomer' ])->middleware('auth')->name('store.customer');
    Route::post('/get-customer/{id}', [ CustomerController::class, 'getCustomer' ])->middleware('auth')->name('get.customer');
    Route::post('/update-customer', [ CustomerController::class, 'updateCustomer' ])->middleware('auth')->name('update.customer');
    Route::post('/search-customer', [ CustomerController::class, 'searchCustomer' ])->middleware('auth')->name('search.customer');
    Route::post('/search-master', [ CustomerController::class, 'searchMaster' ])->middleware('auth')->name('search.master');
    Route::post('/get-customer-addresses', [ CustomerController::class, 'getCustomerAddresses' ])->middleware('auth')->name('get.customer.addresses');
    Route::post('/get-customer-contacts', [ CustomerController::class, 'getCustomerContacts' ])->middleware('auth')->name('get.customer.contacts');
    Route::post('/add-customer-address', [ CustomerController::class, 'addCustomerAddress' ])->middleware('auth')->name('add.customer.address');
    Route::post('/check-email-exists', [ CustomerController::class, 'checkEmailExists' ])->middleware('auth')->name('check.email.exists');
    // End Customer

    // contact
    Route::post('/add-customer-contact', [ ContactController::class, 'create' ])->middleware('auth')->name('add.contact');
    
    Route::put('deleteCompagneCible/', [CompagneController::class, 'deleteCompagneCible'])->middleware('auth')->name('deleteCompagneCible');
    Route::put('insertCompagneCible/', [CompagneController::class, 'insertCompagneCible'])->middleware('auth')->name('insertCompagneCible');
    Route::get('/getCourrier',[CompagneController::class, 'getCourrier'])->middleware('auth')->name('getCourrier');
    Route::get('/getOldCompagne',[CompagneController::class, 'getOldCompagne'])->middleware('auth')->name('getOldCompagne');
    Route::put('deleteOldCompagne/', [CompagneController::class, 'deleteOldCompagne'])->middleware('auth')->name('deleteOldCompagne');

    Route::get('/exportCsv/{id}',[CompagneController::class, 'exportCsv'])->middleware('auth')->name('exportCsv');
    Route::post('/downloadPdf',[CompagneController::class, 'downloadPdf'])->middleware('auth')->name('downloadPdf');

    Route::get('/index',[CompagneController::class, 'index'])->middleware('auth')->name('index');
    Route::post('/insertdestinataire/{id}',[CompagneController::class, 'insertdestinataire'])->middleware('auth')->name('insertdestinataire');
    // Route::get('/envoi/{id}',[CompagneController::class, 'envoi'])->name('envoi');
    Route::post('/envoiprogramme/{id}',[CompagneController::class, 'envoiprogramme'])->middleware('auth')->name('envoiprogramme');
    Route::get('/getCount_cible/{id}',[CompagneController::class, 'getCount_cible'])->middleware('auth')->name('getCount_cible');
    Route::get('/envoi_lettre/{data}/{data_csv}',[CompagneController::class, 'envoi_lettre'])->middleware('auth')->name('envoi_lettre');
    Route::post('/createdata/{id}',[CompagneController::class, 'createdata'])->middleware('auth')->name('createdata');
    Route::get('/getTempname/{id}',[CompagneController::class, 'getTempname'])->middleware('auth')->name('getTempname');
    Route::get('/getContentImage/{id}',[CompagneController::class, 'getContentImage'])->middleware('auth')->name('getContentImage');
    Route::get('/imagefield/{id}',[CompagneController::class, 'imagefield'])->middleware('auth')->name('imagefield');
    Route::get('/fields/{id}',[CompagneController::class, 'fields'])->middleware('auth')->name('fields');
    Route::get('/download',[CompagneController::class, 'downloadPdfFile'])->middleware('auth')->name('downloadPdfFile');
    Route::get('/lettredata/{id}',[CompagneController::class, 'lettredata'])->middleware('auth')->name('lettredata');
    Route::post('/get-affiliate-detail',[LcdtFrontController::class,'getAffiliateDetail'])->middleware('auth')->name('get-affiliate-detail');
    Route::post('/get-campagne-details',[LcdtFrontController::class, 'getCampagneCategory'])->middleware('auth')->name('get-campagne-details');
    Route::post('/get-cible-emails',[LcdtFrontController::class,'getCibleEmails'])->middleware('auth')->name('get-cible-emails');
});

Route::get('{any}', function () {
    return view('welcome'); 
})->where('any','.*');


