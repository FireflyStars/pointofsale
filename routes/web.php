<?php

use TCG\Voyager\Facades\Voyager;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\CibleController;
use App\Http\Controllers\DevisController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\EntiteController;
use App\Http\Controllers\PermisController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\ArticlesController;
use App\Http\Controllers\CommandeController;
use App\Http\Controllers\CompagneController;
use App\Http\Controllers\ContactsController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\InvoicesController;
use App\Http\Controllers\OuvragesController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\LcdtAdminController;
use App\Http\Controllers\LcdtFrontController;
use App\Http\Controllers\PaiementsController;
use App\Http\Controllers\PointagesController;
use App\Http\Controllers\QuickLinkController;
use App\Http\Controllers\StatisticController;
use App\Http\Controllers\TemplatesController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\UnitStatesController;
use App\Http\Controllers\FournisseurController;
use App\Http\Controllers\CampagneListController;
use App\Http\Controllers\PageElementsController;
use App\Http\Controllers\InterventionsController;
use App\Http\Controllers\CommandeFounisseurController;
use App\Http\Controllers\ActionCommercialListController;
use App\Http\Controllers\HtmlTemplateController;
use App\Http\Controllers\SettingController;
use Illuminate\Support\Facades\DB;

Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();
    Route::post('/update-text-pos',[LcdtAdminController::class,'updateTextPos'])->name('update-text-pos');
    Route::post('/get-text-pos',[LcdtAdminController::class,'getTextPos'])->name('get-text-pos');
});
// Get logo image
Route::post('/get-header-setting', [ SettingController::class, 'getHeaderSetting'])->name('get.header.setting');
Route::post('/get-sidebar-setting', [ SettingController::class, 'getSidebarSetting'])->name('get.sidebar.setting');
Route::post('/get-404-setting', [ SettingController::class, 'get404Setting'])->name('get.404.setting');
Route::post('/get-login-setting', [ SettingController::class, 'getLoginSetting'])->name('get.login.setting');

Route::post('/save-page-elements', [PageElementsController::class, 'generate_pdf'])->middleware('auth');
Route::get('/generate-pdf/{report}', [PageElementsController::class, 'generate_pdf_by_id'])->middleware('auth');
Route::post('/generate-pdf/{report}', [PageElementsController::class, 'generate_pdf_by_id'])->middleware('auth');

Route::delete('delete-report/{report}', [PageElementsController::class, 'delete_report'])->middleware('auth');

Route::post('/report-templates', [TemplatesController::class, 'index'])->middleware('auth');
Route::get('/get-report-templates', [TemplatesController::class, 'report_templates'])->middleware('auth');
Route::post('/report-template', [TemplatesController::class, 'store'])->middleware('auth');
Route::get('/report-template/{template}', [TemplatesController::class, 'show'])->middleware('auth');
Route::post('/report-template/{template}', [TemplatesController::class, 'update'])->middleware('auth');
Route::post('/delete-template/{template}', [TemplatesController::class, 'delete'])->middleware('auth');

Route::post('/page-reports', [ReportsController::class, 'index'])->middleware('auth');
Route::get('/page-reports', [ReportsController::class, 'index'])->middleware('auth');
Route::post('/page-report', [ReportsController::class, 'store'])->middleware('auth');
Route::get('/page-report/{report}', [ReportsController::class, 'show'])->middleware('auth');
Route::post('/page-report/{report}', [ReportsController::class, 'update'])->middleware('auth');
Route::post('/delete-report/{report}', [ReportsController::class, 'delete'])->middleware('auth');


Route::get('/get-page-order/{order}', [PageElementsController::class, 'get_page_order'])->middleware('auth');
Route::get('/get-templates', [PageElementsController::class, 'get_page_templates'])->middleware('auth');

Route::get('/search', [SearchController::class, 'search'])->middleware('auth');
Route::get('/search-append', [SearchController::class, 'search_append'])->middleware('auth');

Route::post('/save-letter-pdf/{campagne}', [CompagneController::class, 'save_letter_pdf'])->middleware('auth');
Route::post('/save-letter-settings/{campagne}', [CompagneController::class, 'save_letter_settings'])->middleware('auth');
Route::get('/stream-letter-pdf/{campagne}', [CompagneController::class, 'stream_letter_pdf'])->middleware('auth');
Route::post('/save-flyer-pdf/{campagne}', [CompagneController::class, 'save_flyer_pdf'])->middleware('auth');
Route::get('/stream-flyer-pdf/{campagne}', [CompagneController::class, 'stream_flyer_pdf'])->middleware('auth');
Route::post('/save-mail-csv/{campagne}', [CompagneController::class, 'generate_mail_csv_and_store'])->middleware('auth');
Route::post('/validate-and-send-email/{campagne}', [CompagneController::class, 'validate_and_send_email'])->middleware('auth');
Route::get('/download-resource-file', [CompagneController::class, 'download_resource_file'])->middleware('auth');
Route::post('/get-campagne-list', [CampagneListController::class, 'index'])->middleware('auth');
Route::get('/get-campagne-details/{campagne}', [CompagneController::class, 'campagne_details'])->middleware('auth');
Route::post('/get-user-campagne-list', [CampagneListController::class, 'user_campagnes'])->middleware('auth');
Route::get('/get-campagne-category/{campagne}', [CompagneController::class, 'get_campagne_category'])->middleware('auth');
Route::post('/store-campagne-product/{campagne}', [CompagneController::class, 'store_campagne_product'])->middleware('auth');
Route::get('/get-fields-marketing/{campagne}', [CompagneController::class, 'fields_for_marketing'])->middleware('auth')->name('fields_marketing');
Route::get('/get-card-products', [CompagneController::class, 'get_card_products'])->middleware('auth');
Route::put('/card-product/{card}', [CompagneController::class, 'update_card_product'])->middleware('auth');
Route::delete('/card-product/{card}', [CompagneController::class, 'delete_card_product'])->middleware('auth');
Route::post('/valider-card', [CompagneController::class, 'valider_card'])->middleware('auth');
Route::post('/generate-campagne-product-pdf/{campagne}', [CompagneController::class, 'generate_pdf'])->middleware('auth');
Route::get('/generate-campagne-product-pdf/{campagne}', [CompagneController::class, 'generate_pdf'])->middleware('auth');
Route::get('/get-card-quantity', [CompagneController::class, 'get_card_quantity'])->middleware('auth');

Route::get('/get-entite-list', [EntiteController::class, 'index'])->middleware('auth');
Route::post('/get-entite-list', [EntiteController::class, 'index'])->middleware('auth');
Route::post('/get-entite-list-user', [EntiteController::class, 'index'])->middleware('auth');
Route::get('/get-entite-list-details/{customer}', [EntiteController::class, 'get_details'])->middleware('auth');
Route::post('/change-entite-actif/{customer}', [EntiteController::class, 'change_entite_actif'])->middleware('auth');
Route::post('/change-entite-litige/{customer}', [EntiteController::class, 'change_entite_litige'])->middleware('auth');
Route::get('/get-entite-results/{customer}', [EntiteController::class, 'get_entite_results'])->middleware('auth');

Route::get('/action-commercial-list', [ActionCommercialListController::class, 'index'])->middleware('auth');
Route::post('/action-commercial-list', [ActionCommercialListController::class, 'index'])->middleware('auth');
Route::get('/action-commercial-list-mes', [ActionCommercialListController::class, 'list_user'])->middleware('auth');
Route::post('/action-commercial-list-mes', [ActionCommercialListController::class, 'list_user'])->middleware('auth');
Route::get('/action-commercial-details/{id}', [ActionCommercialListController::class, 'get_details'])->middleware('auth');
Route::post('/action-commercial-statuses', [ActionCommercialListController::class, 'statuses_formatted'])->middleware('auth');
Route::post('/get-action-commercial-statuses', [ActionCommercialListController::class, 'statuses'])->middleware('auth');
Route::post('/change-action-commercial-event-date/{event}', [ActionCommercialListController::class, 'change_event_date'])->middleware('auth');
Route::post('/change-action-commercial-event-user/{event}', [ActionCommercialListController::class, 'change_event_user'])->middleware('auth');
Route::get('/get-action-commercial-event-users/{event}', [ActionCommercialListController::class, 'get_event_users'])->middleware('auth');
Route::get('/get-event-history/{event}', [ActionCommercialListController::class, 'get_event_history'])->middleware('auth');
Route::get('/get-event-statuses-all', [ActionCommercialListController::class, 'get_event_statuses'])->middleware('auth');
Route::post('/change-event-status/{event}', [ActionCommercialListController::class, 'change_event_status'])->middleware('auth');

Route::post('/get-contact-list', [ContactsController::class, 'index'])->middleware('auth');
Route::get('/get-contact-details/{contact}', [ContactsController::class, 'show'])->middleware('auth');
Route::get('/get-contact-results/{contact}', [ContactsController::class, 'contact_results'])->middleware('auth');

Route::get('/get-articles-list', [ArticlesController::class, 'index'])->middleware('auth');
Route::post('/get-articles-list', [ArticlesController::class, 'index'])->middleware('auth');
Route::get('/get-articles-details/{product}', [ArticlesController::class, 'show'])->middleware('auth');
Route::post('/validate-articles-product/{product}', [ArticlesController::class, 'valider'])->middleware('auth');
Route::post('/load-product-documents/{product}', [ArticlesController::class, 'load_product_documents'])->middleware('auth');
Route::post('/remove-product-document/{document}', [ArticlesController::class, 'remove_product_document'])->middleware('auth');
Route::post('/get-product-document-url/{document}', [ArticlesController::class, 'get_document_url'])->middleware('auth');
Route::post('/upload-product-document', [ArticlesController::class, 'upload_product_document'])->middleware('auth');
Route::get('/get-product-document-types', [ArticlesController::class, 'product_document_types'])->middleware('auth');

// contact
Route::post('/contact/add', [ ContactController::class, 'create' ])->middleware('auth')->name('add.contact');
Route::post('/contact/edit/{contact}', [ ContactController::class, 'edit' ])->middleware('auth')->name('edit.contact');
Route::post('/contact/update/{contact}', [ ContactController::class, 'update' ])->middleware('auth')->name('update.contact');
Route::post('/contact/delete/{contact}', [ ContactController::class, 'destroy' ])->middleware('auth')->name('delete.contact');
    
Route::get('/get-contact-statuses-all', [ContactsController::class, 'get_contact_statuses'])->middleware('auth');
Route::post('/change-contact-status/{contact}', [ContactsController::class, 'change_contact_status'])->middleware('auth');

Route::post('/get-user-list', [UsersController::class, 'index'])->middleware('auth');
Route::get('/get-user-details/{user}', [UsersController::class, 'get_details'])->middleware('auth');
Route::post('/get-user-info', [UsersController::class, 'getUserInfo'])->middleware('auth');
Route::post('/user/create', [UsersController::class, 'store'])->middleware('auth');
Route::post('/get-user-info/{user}', [UsersController::class, 'edit'])->middleware('auth');
Route::post('/user/update/{user}', [UsersController::class, 'update'])->middleware('auth');

Route::get('/load-user-documents/{user}', [UsersController::class, 'load_user_documents'])->middleware('auth');
Route::post('/load-user-documents/{user}', [UsersController::class, 'load_user_documents'])->middleware('auth');
Route::post('/remove-user-document/{document}', [UsersController::class, 'remove_user_document'])->middleware('auth');
Route::post('/get-user-document-url/{document}', [UsersController::class, 'get_document_url'])->middleware('auth');
Route::post('/upload-user-document', [UsersController::class, 'upload_user_document'])->middleware('auth');
Route::post('/delete-user/{user}', [UsersController::class, 'delete_user'])->middleware('auth');
Route::get('/get-user-permis-list', [UsersController::class, 'permis_list'])->middleware('auth');
// get user's permis
Route::post('/get-user-permis', [UsersController::class, 'getPermisByUser'])->middleware('auth')->name('get.user.permis');

Route::post('/get-ouvrage-list', [OuvragesController::class, 'index'])->middleware('auth');
Route::post('/get-ouvrage-list-installation', [OuvragesController::class, 'get_ouvrages_installation'])->middleware('auth');
Route::post('/get-ouvrage-list-prestation', [OuvragesController::class, 'get_ouvrages_prestation'])->middleware('auth');
Route::post('/get-ouvrage-list-securite', [OuvragesController::class, 'get_ouvrages_securite'])->middleware('auth');
Route::get('/get-ouvrage-list', [OuvragesController::class, 'index'])->middleware('auth');
Route::post('/get-unit-states', [UnitStatesController::class, 'index'])->middleware('auth');
Route::get('/get-ouvrage-details/{ouvrage}', [OuvragesController::class, 'show'])->middleware('auth');
Route::post('/valider-ouvrage/{ouvrage}', [OuvragesController::class, 'valider'])->middleware('auth');

Route::post('/get-commande-list', [CommandeController::class, 'index'])->middleware('auth');
Route::get('/get-commande-list', [CommandeController::class, 'index'])->middleware('auth');
Route::get('/get-commande-details/{order}', [CommandeController::class, 'show'])->middleware('auth');
Route::post('/get-commande-details/{order}', [CommandeController::class, 'show'])->middleware('auth');
Route::post('/get-order-detail-pointage/{order}', [CommandeController::class, 'get_pointage'])->middleware('auth');
Route::get('/get-order-detail-pointage/{order}', [CommandeController::class, 'get_pointage'])->middleware('auth');
Route::get('/get-personnel-list', [CommandeController::class, 'get_personnel_list'])->middleware('auth');
Route::get('/get-pointage-types', [CommandeController::class, 'get_pointage_types'])->middleware('auth');
Route::post('/get-pointage-types', [CommandeController::class, 'get_pointage_types'])->middleware('auth');
Route::post('/get-pointage-types-formatted', [CommandeController::class, 'get_pointage_types_formatted'])->middleware('auth');
Route::post('/create-pointage/{order}', [CommandeController::class, 'create_pointage'])->middleware('auth');

Route::post('/get-pointages-list', [PointagesController::class, 'index'])->middleware('auth');
Route::get('/get-pointages-list', [PointagesController::class, 'index'])->middleware('auth');
Route::post('/get-pointages-list-mes', [PointagesController::class, 'pointages_mes'])->middleware('auth');

Route::post('/get-interventions-list', [InterventionsController::class, 'index'])->middleware('auth');
Route::get('/get-interventions-list', [InterventionsController::class, 'index'])->middleware('auth');
Route::post('/get-interventions-list-mes', [InterventionsController::class, 'interventions_mes'])->middleware('auth');
Route::post('/get-intervention-status-formatted', [InterventionsController::class, 'get_intervention_status_formatted'])->middleware('auth');
Route::post('/get-intervention-status', [InterventionsController::class, 'get_intervention_status'])->middleware('auth');
Route::post('/get-intervention-types-formatted', [InterventionsController::class, 'get_intervention_types'])->middleware('auth');

Route::get('/get-menu-items', [MenuController::class, 'index'])->middleware('auth');

Route::get('/get-paiements-list', [PaiementsController::class, 'index'])->middleware('auth');
Route::post('/get-paiements-list', [PaiementsController::class, 'index'])->middleware('auth');
Route::post('/get-paiements-types-formatted', [PaiementsController::class, 'paiement_types_formatted'])->middleware('auth');
Route::post('/get-paiements-types', [PaiementsController::class, 'paiement_types'])->middleware('auth');
Route::post('/get-paiements-list-mes', [PaiementsController::class, 'paiements_mes'])->middleware('auth');
Route::post('/get-paiements-list-validar', [PaiementsController::class, 'paiements_validar'])->middleware('auth');
Route::get('/get-paiement-details/{paiement}', [PaiementsController::class, 'get_paiement_details'])->middleware('auth');
Route::get('/get-paiement-history/{paiement}', [PaiementsController::class, 'get_history'])->middleware('auth');
Route::post('/valider-paiement', [PaiementsController::class, 'valider_paiement'])->middleware('auth');

Route::get('/get-fournisseur-list', [FournisseurController::class, 'index'])->middleware('auth');
Route::post('/get-fournisseur-list', [FournisseurController::class, 'index'])->middleware('auth');
Route::post('/get-fournisseur-list-mes', [FournisseurController::class, 'fournisseur_mes'])->middleware('auth');
Route::post('/get-fournisseur-supplier-type-formatted', [FournisseurController::class, 'fournisseur_types'])->middleware('auth');
Route::post('/get-fournisseur-supplier-status-formatted', [FournisseurController::class, 'fournisseur_status'])->middleware('auth');
Route::post('/get-fournisseur-statuses', [FournisseurController::class, 'fournisseur_status_all'])->middleware('auth');
Route::get('/get-fournisseur-details/{supplier}', [FournisseurController::class, 'fournisseur_details'])->middleware('auth');
Route::get('/get-fournisseur-history/{supplier}', [FournisseurController::class, 'fournisseur_history'])->middleware('auth');

Route::get('/get-permis-list', [PermisController::class, 'index'])->middleware('auth');
Route::post('/get-permis-list', [PermisController::class, 'index'])->middleware('auth');

Route::post('/get-commande-fournisseur-list', [CommandeFounisseurController::class, 'index'])->middleware('auth');
Route::post('/get-commande-fournisseur-list-mes', [CommandeFounisseurController::class, 'mes_details'])->middleware('auth');
Route::post('/get-commande-fournisseur-supplier-status-formatted', [CommandeFounisseurController::class, 'get_order_states'])->middleware('auth');
Route::post('/get-commande-fournisseur-statuses', [CommandeFounisseurController::class, 'get_order_states_all'])->middleware('auth');

//Invoices
Route::post('/search-invoice', [InvoicesController::class, 'search'])->middleware('auth');
Route::post('/create-invoice', [InvoicesController::class, 'create'])->middleware('auth');
Route::post('/create-new-invoice', [InvoicesController::class, 'create_new_invoice'])->middleware('auth');
Route::get('/get-single-invoice-details', [InvoicesController::class, 'create'])->middleware('auth');
Route::get('/get-tax-list', [InvoicesController::class, 'get_tax_list'])->middleware('auth');
Route::post('/create-ligne/{invoice}', [InvoicesController::class, 'create_ligne'])->middleware('auth');
Route::delete('/delete-ligne/{invoice_detail_id}', [InvoicesController::class, 'delete_ligne'])->middleware('auth');

// create action
Route::post('/get-action-info', [ActionCommercialListController::class, 'getActionInfo'])->name('get.action.info');
Route::post('/action/create', [ActionCommercialListController::class, 'createAction'])->name('create.action');
Route::post('/action/edit/{event}', [ActionCommercialListController::class, 'updateAction'])->name('update.action');
Route::post('/get-action/{event}', [ActionCommercialListController::class, 'getAction'])->name('get.action');
Route::post('/get-actions-for-calendar', [ActionCommercialListController::class, 'getActionsForCalendar'])->name('get.actions.for.calendar');


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
    //Facture
    Route::post('/get-invoice-list',[InvoiceController::class,'loadInvoiceList'])->middleware('auth')->name('get-invoice-list');
    Route::post('/get-invoice-states-formatted',[InvoiceController::class,'getInvoiceStatesFormatted'])->middleware('auth')->name('get-invoice-states-formatted');
    Route::post('/get-invoice-states',[InvoiceController::class,'getInvoiceStates'])->middleware('auth')->name('get-invoice-states');
    Route::post('/get-invoice-detail',[InvoiceController::class,'getInvoiceDetail'])->middleware('auth')->name('get-invoice-detail');
    Route::post('/get-invoice-payments',[InvoiceController::class,'getInvoicePayments'])->middleware('auth')->name('get-invoice-payments');
    Route::post('/remove-invoice-payment',[InvoiceController::class,'removeInvoicePayment'])->middleware('auth')->name('remove-invoice-payment');
    Route::post('/add-invoice-payment',[InvoiceController::class,'addInvoicePayment'])->middleware('auth')->name('add-invoice-payment');
    Route::post('/update-invoice-state',[InvoiceController::class,'updateInvoiceState'])->middleware('auth')->name('update-invoice-state');
    
   
    
    //quicklink
    Route::post('/add-quick-link',[QuickLinkController::class,'addLink'])->middleware('auth')->name('add-quick-link');
    Route::post('/get-quick-links',[QuickLinkController::class,'getLinks'])->middleware('auth')->name('get-quick-links');
    Route::post('/remove-quick-link',[QuickLinkController::class,'removeLink'])->middleware('auth')->name('remove-quick-link');
    
    //htmltemplate
    //Route::get('/jsontohtml', [HtmlTemplateController::class, 'jsonToHtml']);//test table
    Route::post('/save-htmltemplate-conf',[HtmlTemplateController::class,'saveHtmlTemplateConf'])->middleware('auth')->name('save-htmltemplate-conf');
    Route::post('/get-htmltemplate-conf',[HtmlTemplateController::class,'getHtmlTemplateConf'])->middleware('auth')->name('get-htmltemplate-conf');
    Route::post('/save-htmltemplate-element',[HtmlTemplateController::class,'saveHtmlTemplateElement'])->middleware('auth')->name('save-htmltemplate-element');
    Route::post('/get-htmltemplate-elements',[HtmlTemplateController::class,'getHtmlTemplateElements'])->middleware('auth')->name('get-htmltemplate-elements');
    Route::post('/remove-htmltemplate-element',[HtmlTemplateController::class,'removeHtmlTemplateElement'])->middleware('auth')->name('remove-htmltemplate-element');
    Route::post('/reposition-htmltemplate-element',[HtmlTemplateController::class,'reposHtmlTemplateElement'])->middleware('auth')->name('reposition-htmltemplate-element');
    Route::post('/save-hf',[HtmlTemplateController::class,'SaveHf'])->middleware('auth')->name('save-hf');
    Route::get('/htmltemplate-generate-pdf-test/{id}',[HtmlTemplateController::class,'generatePdfTest'])->middleware('auth')->name('htmltemplate-generate-pdf-test');
    Route::post('/htmltemplate-generate-email-test',[HtmlTemplateController::class,'generateEmailTest'])->middleware('auth')->name('htmltemplate-generate-email-test');
    Route::get('/generation-doc-pdf/{uuid}',[HtmlTemplateController::class,'generatePdf'])->name('generation-doc-pdf');
    Route::post('/save-global-css',[HtmlTemplateController::class,'saveGlobalCss'])->middleware('auth')->name('save-global-css');
    Route::post('/get-htmltemplate-list',[HtmlTemplateController::class,'getHtmlTemplateLists'])->middleware('auth')->name('get-htmltemplate-list');
    Route::post('/get-htmltemplateheader-list',[HtmlTemplateController::class,'getHtmlTemplateHeaderLists'])->middleware('auth')->name('get-htmltemplateheader-list');
    Route::post('/get-htmltemplatefooter-list',[HtmlTemplateController::class,'getHtmlTemplateFooterLists'])->middleware('auth')->name('get-htmltemplatefooter-list');
    Route::post('/htmltemplate-duplicate-row',[HtmlTemplateController::class,'duplicateRow'])->middleware('auth')->name('htmltemplate-duplicate-row');
    Route::post('/htmltemplate-delete-row',[HtmlTemplateController::class,'deleteRow'])->middleware('auth')->name('htmltemplate-delete-row');
    Route::get('/run-notification-cron',[HtmlTemplateController::class,'runNotificationCron'])->name('run-notification-cron');
    // Route::get('/testemail',function(){
    //     $notification=Notification::find(110);
    //     Mail::to('reyewat@vpc-direct-services.com')->send(new NotificationMail($notification));
    //     return new NotificationMail($notification);
    // });
    

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
    Route::post('/get-devis/{order}', [DevisController::class, 'getDevis'])->middleware('auth')->name('get.devis');
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
    Route::post('/check-siret', [ CustomerController::class, 'checkSiret' ])->middleware('auth')->name('check.siren');
    // End Customer
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


    // statistique 
    Route::post('/statistique',   [StatisticController::class,'index'])->middleware('auth')->name('get.statis');
    Route::post('/homenews',   [HomeController::class,'index'])->middleware('auth')->name('get.home.news');

    // supplier
    Route::post('/supplier/create', [ SupplierController::class, 'store' ])->middleware('auth')->name('create.supplier');
    Route::post('/get-supplier/{id}', [ SupplierController::class, 'edit' ])->middleware('auth')->name('edit.supplier');
    Route::post('/supplier/update/{id}', [ SupplierController::class, 'update' ])->middleware('auth')->name('update.supplier');
    Route::post('/get-supplier-status-type', [ SupplierController::class, 'getSupplierStatusType' ])->middleware('auth')->name('get.supplier.status.type');
});
// Route::get('/statistique',   [StatisticController::class,'index'])->middleware('auth')->name('get.statis');
// Outlook Agenda
Route::get('/outlook/sync', [ ActionCommercialListController::class, 'syncOutlook' ])->middleware('auth')->name('outlook.sync');
Route::get('/callback', [ ActionCommercialListController::class, 'outlookSyncCallback' ])->middleware('auth')->name('outlook.sync.callback');

// 
Route::get('{any}', function () {
    return view('welcome'); 
})->where('any', '.*');