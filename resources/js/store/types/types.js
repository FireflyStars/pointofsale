export const LOADER_MODULE = 'LOADER_MODULE/'; //namespace
export const SET_SHOW_LOADER = 'SET_SHOW_LOADER'; //mutations
export const SET_LOADER_MSG = 'SET_LOADER_MSG'; //mutations
export const GET_SHOW_LOADER = 'GET_SHOW_LOADER'; //getters
export const GET_LOADER_MSG = 'GET_LOADER_MSG'; // getters
export const DISPLAY_LOADER = 'DISPLAY_LOADER'; //actions
export const HIDE_LOADER = 'HIDE_LOADER'; //actions

//ITEM LIST TABLE
export const ITEM_LIST_MODULE='ITEM_LIST_MODULE/'//namespace
export const ITEM_LIST_TABLEDEF='ITEM_LIST_TABLEDEF'//action
export const ITEM_LIST_TABLE_RELOAD='ITEM_LIST_TABLE_RELOAD'//action
export const ITEM_LIST_SET_TABLEDEF='ITEM_LIST_SET_TABLEDEF'//mutations
export const ITEM_LIST_GET_TABLES='ITEM_LIST_GET_TABLES'//getters
export const ITEM_LIST_FILTER='ITEM_LIST_FILTER'//action
export const ITEM_LIST_UPDATE_FILTER='ITEM_LIST_UPDATE_FILTER'//mutations
export const ITEM_LIST_SET_LIST='ITEM_LIST_SET_LIST'//mutations
export const ITEM_LIST_GET_LISTS='ITEM_LIST_GET_LISTS'//getters
export const ITEM_LIST_GET_COLUMN_FILTERS='ITEM_LIST_GET_COLUMN_FILTERS'//getters
export const ITEM_LIST_SELECT_CURRENT='ITEM_LIST_SELECT_CURRENT'//action
export const ITEM_LIST_SET_CURRENT='ITEM_LIST_SET_CURRENT'//mutations
export const ITEM_LIST_GET_CURRENT='ITEM_LIST_GET_CURRENT'//getters
export const ITEM_LIST_LOAD_MORE='ITEM_LIST_LOAD_MORE'//action
export const ITEM_LIST_SET_PAGINATION='ITEM_LIST_RESET_PAGINATION'//mutation
export const ITEM_LIST_GET_IDENTIFIER='ITEM_LIST_GET_IDENTIFIER'//getters
export const ITEM_LIST_MULTI_CHECK='ITEM_LIST_MULTI_CHECK';//mutations
export const ITEM_LIST_MULTI_UNCHECK='ITEM_LIST_MULTI_UNCHECK';//mutations
export const ITEM_LIST_RESET_MULTI_CHECK='ITEM_LIST_RESET_MULTI_CHECK';//mutations
export const ITEM_LIST_MULTI_CHECK_LISTS='ITEM_LIST_MULTI_CHECK_LISTS';//getters
export const ITEM_LIST_SET_TABLE='ITEM_LIST_SET_TABLE'//mutations
export const ITEM_LIST_SORT='ITEM_LIST_SORT'//action
export const ITEM_LIST_SET_SORT='ITEM_LIST_SET_SORT'//action
export const ITEM_LIST_GET_SORT='ITEM_LIST_GET_SORT'//getters
export const ITEM_LIST_GET_FILTER_OPTIONS='ITEM_LIST_GET_FILTER_OPTIONS'//getters
export const ITEM_LIST_LOAD_FILTER_OPTIONS='ITEM_LIST_LOAD_FILTER_OPTIONS'//action
export const ITEM_LIST_SET_FILTER_OPTIONS='ITEM_LIST_SET_FILTER_OPTIONS'//mutations
export const ITEM_LIST_UPDATE_ROW='ITEM_LIST_UPDATE_ROW'//mutation
export const ITEM_LIST_REMOVE_ROW='ITEM_LIST_REMOVE_ROW'//mutation

//ITEM LIST QUICK LINKS
export const ITEMLISTQUICKLINK_MODULE='ITEMLISTQUICKLINK_MODULE/'//namespace
export const ITEMLISTQUICKLINK_SET_LINKS='ITEMLISTQUICKLINK_SET_LINKS'//mutations
export const ITEMLISTQUICKLINK_SET_SUPER='ITEMLISTQUICKLINK_SET_SUPER'//mutations
export const ITEMLISTQUICKLINK_SET_LOADED='ITEMLISTQUICKLINK_SET_LOADED'//mutations
export const ITEMLISTQUICKLINK_UNSET_LINK='ITEMLISTQUICKLINK_UNSET_LINK'//mutations
export const ITEMLISTQUICKLINK_SET_LINK='ITEMLISTQUICKLINK_SET_LINK'//mutations
export const ITEMLISTQUICKLINK_LOAD_LINKS='ITEMLISTQUICKLINK_LOAD_LINKS'//actions
export const ITEMLISTQUICKLINK_REMOVE_LINK='ITEMLISTQUICKLINK_REMOVE_LINK'//actions
export const ITEMLISTQUICKLINK_ADD_LINK='ITEMLISTQUICKLINK_ADD_LINK'//actions
export const ITEMLISTQUICKLINK_GET_LINKS='ITEMLISTQUICKLINK_GET_LINKS'//getters
export const ITEMLISTQUICKLINK_GET_LOADED='ITEMLISTQUICKLINK_GET_LOADED'//getters
export const ITEMLISTQUICKLINK_GET_ISSUPERADMIN='ITEMLISTQUICKLINK_GET_ISSUPERADMIN'//getters

//ORDER STATE TAG
export const ORDERSTATETAG_MODULE='ORDERSTATETAG_MODULE/';
export const ORDERSTATETAG_SET_ORDER_STATES='ORDERSTATETAG_SET_ORDER_STATES'//mutation
export const ORDERSTATETAG_LOAD_ORDER_STATES='ORDERSTATETAG_LOAD_ORDER_STATES'//action
export const ORDERSTATETAG_GET_ORDER_STATES='ORDERSTATETAG_GET_ORDER_STATES'//getter
export const ORDERSTATETAG_GET_LOADED='ORDERSTATETAG_GET_LOADED'//getter
export const ORDERSTATETAG_SET_LOADED='ORDERSTATETAG_SET_LOADED'//mutation

//DEVIS LIST STORE
export const DEVIS_LIST_MODULE='DEVIS_LIST_MODULE/'//namespace
export const GET_DEVIS_LIST_DEF='GET_DEVIS_LIST_DEF'//getters
export const DEVISLIST_LOAD_TAB='DEVISLIST_LOAD_TAB'//action
export const DEVISLIST_SET_LIST='DEVISLIST_SET_LIST'//mutations

//devis detail 
export const DEVIS_DETAIL_MODULE='DEVIS_DETAIL_MODULE/'//namespace
export const DEVIS_DETAIL_LOAD='DEVIS_DETAIL_LOAD'//action
export const DEVIS_DETAIL_SET='DEVIS_DETAIL_SET'//mutation
export const DEVIS_DETAIL_GET='DEVIS_DETAIL_GET'//getter
export const DEVIS_DETAIL_UPDATE_ORDER_STATE='DEVIS_DETAIL_UPDATE_ORDER_STATE'//action
export const DEVIS_DETAIL_SET_ORDER_STATE='DEVIS_DETAIL_SET_ORDER_STATE'//mutation
export const DEVIS_DETAIL_GET_FACTURATION='DEVIS_DETAIL_GET_FACTURATION'//getters
export const DEVIS_DETAIL_SET_FACTURATION='DEVIS_DETAIL_SET_FACTURATION'//mutation
export const DEVIS_DETAIL_NEW_FACTURATION='DEVIS_DETAIL_NEW_FACTURATION'//action
export const DEVIS_DETAIL_CREATE_FACTURATION='DEVIS_DETAIL_CREATE_FACTURATION'//action
export const DEVIS_DETAIL_UPDATE_FACTURATION='DEVIS_DETAIL_UPDATE_FACTURATION'//mutations
export const DEVIS_DETAIL_REMOVE_FACTURATION='DEVIS_DETAIL_REMOVE_FACTURATION'//action
export const DEVIS_DETAIL_UNSET_FACTURATION='DEVIS_DETAIL_UNSET_FACTURATION'//mutation
export const DEVIS_DETAIL_LOAD_FACTURATION='DEVIS_DETAIL_LOAD_FACTURATION'//action
export const DEVIS_DETAIL_LOAD_ORDER_DOCUMENTS='DEVIS_DETAIL_LOAD_ORDER_DOCUMENTS'//getters
export const DEVIS_DETAIL_GET_ORDER_DOCUMENTS='DEVIS_DETAIL_GET_ORDER_DOCUMENTS'//getters
export const DEVIS_DETAIL_SET_ORDER_DOCUMENTS='DEVIS_DETAIL_SET_ORDER_DOCUMENTS'//mutation
export const DEVIS_DETAIL_UNSET_ORDER_DOCUMENT='DEVIS_DETAIL_UNSET_ORDER_DOCUMENT'//mutation
export const DEVIS_DETAIL_UPLOAD_DOCUMENT='DEVIS_DETAIL_UPLOAD_DOCUMENT'//mutation
export const DEVIS_DETAIL_REMOVE_DOCUMENT='DEVIS_DETAIL_REMOVE_DOCUMENT'//action
export const DEVIS_DETAIL_GET_DOCUMENT_URL='DEVIS_DETAIL_GET_DOCUMENT_URL'//action

export const COMMANDE_DETAIL_MODULE='COMMANDE_DETAIL_MODULE/'//namespace
export const COMMANDE_DETAIL_LOAD='COMMANDE_DETAIL_LOAD'//action
export const COMMANDE_DETAIL_SET='COMMANDE_DETAIL_SET'//mutation
export const COMMANDE_DETAIL_GET='COMMANDE_DETAIL_GET'//getter
export const COMMANDE_DETAIL_UPDATE_ORDER_STATE='COMMANDE_DETAIL_UPDATE_ORDER_STATE'//action
export const COMMANDE_DETAIL_SET_ORDER_STATE='COMMANDE_DETAIL_SET_ORDER_STATE'//mutation
export const COMMANDE_DETAIL_GET_FACTURATION='COMMANDE_DETAIL_GET_FACTURATION'//getters
export const COMMANDE_DETAIL_SET_FACTURATION='COMMANDE_DETAIL_SET_FACTURATION'//mutation
export const COMMANDE_DETAIL_NEW_FACTURATION='COMMANDE_DETAIL_NEW_FACTURATION'//action
export const COMMANDE_DETAIL_CREATE_FACTURATION='COMMANDE_DETAIL_CREATE_FACTURATION'//action
export const COMMANDE_DETAIL_UPDATE_FACTURATION='COMMANDE_DETAIL_UPDATE_FACTURATION'//mutations
export const COMMANDE_DETAIL_REMOVE_FACTURATION='COMMANDE_DETAIL_REMOVE_FACTURATION'//action
export const COMMANDE_DETAIL_UNSET_FACTURATION='COMMANDE_DETAIL_UNSET_FACTURATION'//mutation
export const COMMANDE_DETAIL_LOAD_FACTURATION='COMMANDE_DETAIL_LOAD_FACTURATION'//action
export const COMMANDE_DETAIL_LOAD_ORDER_DOCUMENTS='COMMANDE_DETAIL_LOAD_ORDER_DOCUMENTS'//getters
export const COMMANDE_DETAIL_GET_ORDER_DOCUMENTS='COMMANDE_DETAIL_GET_ORDER_DOCUMENTS'//getters
export const COMMANDE_DETAIL_SET_ORDER_DOCUMENTS='COMMANDE_DETAIL_SET_ORDER_DOCUMENTS'//mutation
export const COMMANDE_DETAIL_UNSET_ORDER_DOCUMENT='COMMANDE_DETAIL_UNSET_ORDER_DOCUMENT'//mutation
export const COMMANDE_DETAIL_UPLOAD_DOCUMENT='COMMANDE_DETAIL_UPLOAD_DOCUMENT'//mutation
export const COMMANDE_DETAIL_REMOVE_DOCUMENT='COMMANDE_DETAIL_REMOVE_DOCUMENT'//action
export const COMMANDE_DETAIL_GET_DOCUMENT_URL='COMMANDE_DETAIL_GET_DOCUMENT_URL'//action
export const COMMANDE_DETAIL_LOAD_POINTAGE='COMMANDE_DETAIL_LOAD_POINTAGE'//action
export const COMMANDE_DETAIL_SET_POINTAGE='COMMANDE_DETAIL_SET_POINTAGE'//action
export const SAVE_PERSONNEL_LIST='SAVE_PERSONNEL_LIST'//mutation
export const GET_POINTAGE_TYPES='GET_POINTAGE_TYPES'//action
export const SAVE_POINTAGE_TYPES='SAVE_POINTAGE_TYPES'//mutation
export const COMMANDE_CREATE_POINTAGE='COMMANDE_CREATE_POINTAGE'//action
export const UPDATE_POINTAGE='UPDATE_POINTAGE'//mutation

//FACTURE LIST STORE
export const FACTURE_LIST_MODULE='FACTURE_LIST_MODULE/'//namespace
export const FACTURELIST_LOAD_TAB='FACTURELIST_LOAD_TAB'//action
export const FACTURELIST_SET_LIST='FACTURELIST_SET_LIST'//mutation
export const GET_FACTURE_LIST_DEF='GET_FACTURE_LIST_DEF'//getters

//FACTURE DETAIL
export const FACTURE_DETAIL_MODULE='FACTURE_DETAIL_MODULE/'//namespace
export const FACTURE_DETAIL_SET='FACTURE_DETAIL_SET'//mutation
export const FACTURE_DETAIL_LOAD='FACTURE_DETAIL_LOAD'//action
export const FACTURE_DETAIL_GET='FACTURE_DETAIL_GET'//getter
export const FACTURE_DETAIL_SET_PAYMENTS='FACTURE_DETAIL_SET_PAYMENTS'//mutation
export const FACTURE_DETAIL_SET_PAYMENT='FACTURE_DETAIL_SET_PAYMENT'//mutation
export const FACTURE_DETAIL_UNSET_PAYMENT='FACTURE_DETAIL_UNSET_PAYMENT'//mutation
export const FACTURE_DETAIL_LOAD_PAYMENTS='FACTURE_DETAIL_LOAD_PAYMENTS'//action
export const FACTURE_DETAIL_ADD_PAYMENT='FACTURE_DETAIL_ADD_PAYMENT'//action
export const FACTURE_DETAIL_REMOVE_PAYMENT='FACTURE_DETAIL_REMOVE_PAYMENT'//action
export const FACTURE_DETAIL_GET_PAYMENTS='FACTURE_DETAIL_GET_PAYMENTS'//getter
export const FACTURE_DETAIL_SET_PAYMENT_STATES='FACTURE_DETAIL_SET_PAYMENT_STATES'//mutation
export const FACTURE_DETAIL_SET_PAYMENT_TYPES='FACTURE_DETAIL_SET_PAYMENT_TYPES'//mutation
export const FACTURE_DETAIL_GET_PAYMENT_STATES='FACTURE_DETAIL_GET_PAYMENT_STATES'//getter
export const FACTURE_DETAIL_GET_PAYMENT_TYPES='FACTURE_DETAIL_GET_PAYMENT_TYPES'//getter
export const FACTURE_DETAIL_UPDATE_INVOICE_STATE='FACTURE_DETAIL_UPDATE_INVOICE_STATE'//action
export const FACTURE_DETAIL_SET_STATE='FACTURE_DETAIL_SET_STATE'//mutation
export const FACTURE_DETAIL_REMOVE_FACTURATION='FACTURE_DETAIL_REMOVE_FACTURATION'//action
export const FACTURE_DETAIL_SET_MODE_PAIEMENTS='FACTURE_DETAIL_SET_MODE_PAIEMENTS'//mutation
export const FACTURE_DETAIL_SET_REFERENCE='FACTURE_DETAIL_SET_REFERENCE'//mutation

//INVOICE STATE TAG
export const INVOICESTATETAG_MODULE='INVOICESTATETAG_MODULE/';
export const INVOICESTATETAG_SET_INVOICE_STATES='INVOICESTATETAG_SET_INVOICE_STATES'//mutation
export const INVOICESTATETAG_LOAD_INVOICE_STATES='INVOICESTATETAG_LOAD_INVOICE_STATES'//action
export const INVOICESTATETAG_GET_INVOICE_STATES='INVOICESTATETAG_GET_INVOICE_STATES'//getter
export const INVOICESTATETAG_GET_LOADED='INVOICESTATETAG_GET_LOADED'//getter
export const INVOICESTATETAG_SET_LOADED='INVOICESTATETAG_SET_LOADED'//mutation


export const TOASTER_MODULE = 'TOASTER_MODULE/'; //namespace
export const TOASTER_ADD_TOAST = 'TOASTER_ADD_TOAST'; //mutations
export const TOASTER_REMOVE_TOAST = 'TOASTER_REMOVE_TOAST'; //mutations
export const TOASTER_CLEAR_TOASTS = 'TOASTER_CLEAR_TOASTS'; //mutations
export const TOASTER_MESSAGE = 'TOASTER_MESSAGE'; //action
export const TOASTER_GET_ALL = 'TOASTER_GET_ALL'; //getters

//cible selection module
export const CIBLE_MODULE='CIBLE_MODULE/'; //namespace
export const CIBLE_INIT='CIBLE_INIT';//action
export const CIBLE_SET_CAMPAGNE_CATEGORY_ID='CIBLE_SET_CAMPAGNE_CATEGORY_ID'//mutations
export const CIBLE_SET_CUSTOMER_STATUT='CIBLE_SET_CUSTOMER_STATUT';//mutations
export const CIBLE_GET_CUSTOMER_STATUT='CIBLE_GET_CUSTOMER_STATUT';//getters
export const CIBLE_SET_NAF='CIBLE_SET_NAF';//mutations
export const CIBLE_GET_NAF='CIBLE_GET_NAF';//getters
export const CIBLE_TOGGLE='CIBLE_TOGGLE';//action
export const CIBLE_SET_SELECTION='CIBLE_SET_SELECTION';//mutations
export const CIBLE_UNSET_SELECTION='CIBLE_UNSET_SELECTION';//mutations
export const CIBLE_GET_SELECTION='CIBLE_GET_SELECTION';//getters
export const CIBLE_SET_PREVIOUS_CAMPAGNE_LIST='CIBLE_SET_PREVIOUS_CAMPAGNE_LIST';//mutations
export const CIBLE_GET_PREVIOUS_CAMPAGNE_LIST='CIBLE_GET_PREVIOUS_CAMPAGNE_LIST';//mutations
export const CIBLE_ADD_TO_ALL_CONTACTS='CIBLE_ADD_TO_ALL_CONTACTS';//mutations
export const CIBLE_GET_ALL_CONTACTS='CIBLE_GET_ALL_CONTACTS';//getters
export const CIBLE_CAMPAGNE_TOGGLE='CIBLE_CAMPAGNE_TOGGLE';//action
export const CIBLE_SET_CAMPAGNE_SELECTION='CIBLE_SET_CAMPAGNE_SELECTION';//mutations
export const CIBLE_UNSET_CAMPAGNE_SELECTION='CIBLE_UNSET_CAMPAGNE_SELECTION';//mutations
export const CIBLE_GET_CAMPAGNE_SELECTION='CIBLE_GET_CAMPAGNE_SELECTION'//getters
export const CIBLE_SET_UNIQUE_CONTACTS='CIBLE_SET_UNIQUE_CONTACTS';//mutations
export const CIBLE_GET_UNIQUE_CONTACTS='CIBLE_GET_UNIQUE_CONTACTS';//mutations
export const CIBLE_GET_UNSELECTED_EMAILS='CIBLE_GET_UNSELECTED_EMAILS';//getters
export const CIBLE_SET_UNSELECTED_EMAIL='CIBLE_SET_UNSELECTED_EMAIL';//mutations
export const CIBLE_UNSET_UNSELECTED_EMAIL='CIBLE_UNSET_UNSELECTED_EMAIL';//mutations
export const CIBLE_SET_FILTERED_EMAILS='CIBLE_SET_FILTERED_EMAILS';//getters
export const CIBLE_GET_FILTERED_EMAILS='CIBLE_GET_FILTERED_EMAILS';//getters
export const CIBLE_SET_PRICE='CIBLE_SET_PRICE';//mutations
export const CIBLE_GET_PRICE='CIBLE_GET_PRICE';//getters
export const CIBLE_CREATE_CAMPAGNE='CIBLE_GET_PRICE';//action
export const CIBLE_SET_CAMPAGNE_CATEGORY='CIBLE_SET_CAMPAGNE_CATEGORY';//mutations
export const CIBLE_GET_CAMPAGNE_CATEGORY='CIBLE_GET_CAMPAGNE_CATEGORY';//getters
export const CIBLE_RESET_STATE='CIBLE_RESET_STATE';//mutations
export const GET_CAMPAGNE_CATEGORY='GET_CAMPAGNE_CATEGORY'
export const SAVE_CAMPAGNE_CATEGORY='SAVE_CAMPAGNE_CATEGORY'
export const GET_CAMPAGNE_FIELDS='GET_CAMPAGNE_FIELDS'
export const SAVE_CAMPAGNE_FIELDS='SAVE_CAMPAGNE_FIELDS'
export const STORE_PRODUCT = 'STORE_PRODUCT'
export const GET_CARD_PRODUCTS = 'GET_CARD_PRODUCTS'
export const SAVE_CARD_PRODUCTS = 'SAVE_CARD_PRODUCTS'
export const UPDATE_CARD = 'UPDATE_CARD'
export const DELETE_CARD = 'DELETE_CARD'
export const VALIDER_CARD = 'VALIDER_CARD'
export const RESET_PRODUCTS = 'RESET_PRODUCTS'
export const GET_CAMPAGNE_DETAILS = 'GET_CAMPAGNE_DETAILS'
export const SAVE_CAMPAGNE_DETAILS = 'SAVE_CAMPAGNE_DETAILS'
export const GENERATE_PRODUCT_PDF = 'GENERATE_PRODUCT_PDF'
export const GET_CARD_QUANTITY = 'GET_CARD_QUANTITY'
export const SAVE_CARD_QUANTITY = 'SAVE_CARD_QUANTITY'


export const SIDEBAR_MODULE = 'SIDEBAR_MODULE/' //namespace
export const SIDEBAR_SET_SLIDEIN = 'SIDEBAR_SET_SLIDEIN' //mutations
export const SIDEBAR_GET_SLIDEIN = 'SIDEBAR_GET_SLIDEIN' //gettes


export const SELECT_MODULE = 'SELECT_MODULE/'; //namespace
export const SET_CURRENT_SELECT = 'SET_CURRENT_SELECT'; //mutations
export const GET_CURRENT_SELECT = 'GET_CURRENT_SELECT'; //getters


export const FILTER_MODULE = 'FILTER_MODULE'; //namespace
export const SET_ITEMS = 'SET_ITEMS'; //action
export const SET_SELECTED_BOXES = 'SET_SELECTED_BOXES'; //action
export const RESET_FILTER = 'RESET_FILTER'; //action
export const GET_ITEMS = 'GET_ITEMS'; //getters
export const GET_SELECTED_BOXES = 'GET_SELECTED_BOXES'; //getters

export const TOGGLER_MODULE = 'TOGGLER_MODULE'
export const ACTIVE_ITEM = 'ACTIVE_ITEM' //getters
export const SET_TOGGLER_ITEM = 'SET_TOGGLER_ITEM' //mutations

//Reports module

export const BUILDER_MODULE = 'BUILDER_MODULE'
export const BUILDER_MODULE_LIST = 'BUILDER_MODULE_LIST/'
export const BUILDER_DELETE_TEMPLATE = 'BUILDER_DELETE_TEMPLATE'
export const REPORTS_BUILDER_MODULE = 'REPORTS_BUILDER_MODULE/'
export const BUILDER_DELETE_REPORT = 'BUILDER_DELETE_REPORT'
export const GENERATE_PDF = 'GENERATE_PDF'
export const GENERATE_PDF_BY_ID = 'GENERATE_PDF_BY_ID'
export const ADD_PAGE = 'AddPage'
export const DELETE_PAGE = 'deletePage'
export const GET_REPORTS = 'getReports'
export const GET_REPORT = 'getReport'
export const SAVE_REPORT = 'saveReport'
export const SAVE_REPORTS = 'saveReports'
export const SAVE_PAGE_ELEMENTS = 'SAVE_PAGE_ELEMENTS'
export const GET_ORDER_DETAILS = 'getOrderDetails'
export const SAVE_PAGE_ORDER = 'savePageOrder'
export const SAVE_TEMPLATES = 'saveTemplates'
export const SAVE_REPORT_TEMPLATE = 'saveReportTemplate'
export const SAVE_REPORT_TEMPLATES = 'saveReportTemplates'
export const UPDATE_REPORT_TEMPLATE = 'updateReportTemplates'
export const GET_REPORT_TEMPLATE = 'getReportTemplates'
export const GET_REPORT_TEMPLATES = 'fetchReportTemplates'
export const SET_ACTIVE_TEMPLATE = 'setActiveTemplate'
export const LOAD_REPORT_PAGES = 'loadReportPages'
export const SAVE_REPORT_PAGES = 'saveReportPages'
export const RESET_PAGES = 'resetPages'
export const ASSIGN_TEMPLATE = 'assignTemplate'
export const DELETE_ITEM = 'deleteItem'
export const GENERATE_ELEMENT = 'generateElement'
export const SET_ACTIVE_PAGE = 'setActivePage'
export const UPDATE_ELEMENT_STYLES = 'updateElementStyles'
export const UPDATE_ELEMENT_CONTENT = 'updateElementContent'
export const UPDATE_ELEMENT_TABLE = 'updateElementTable'
export const SET_PAGE_BACKGROUND = 'setPageBackground'
export const SET_LOADING = 'setLoading'
export const UPDATE_SVG = 'updateSvg'
export const DELETE_REPORT = 'DELETE_REPORT'

export const SAVE_META = 'saveMeta'

// customer module
export const CUSTOMERLIST_MODULE='CUSTOMERLIST_MODULE/';//namespace
export const CUSTOMER_SEARCH_LOAD_LIST='CUSTOMER_SEARCH_LOAD_LIST';//action
export const MASTER_SEARCH_LOAD_LIST='MASTER_SEARCH_LOAD_LIST';//action
export const CUSTOMER_GET_SEARCH_LIST='CUSTOMER_GET_SEARCH_LIST'//getters
export const CUSTOMER_SET_SEARCH_LIST='CUSTOMER_SET_SEARCH_LIST'//getters
export const CUSTOMER_GET_SEARCH_COUNT='CUSTOMER_GET_SEARCH_COUNT'//getters
export const CUSTOMER_SET_SEARCH_COUNT='CUSTOMER_SET_SEARCH_COUNT'//getters
export const CUSTOMER_SET_LOADER = 'CUSTOMER_SET_LOADER'//getters


//search module
export const SEARCH_MODULE = 'SEARCH_MODULE'
export const GET_SEARCH_RESULTS = 'getSearchResults'
export const SET_SEARCH_RESULTS = 'setSearchResults'
export const SET_SEARCH = 'setSearch'
export const SEARCH_MORE = 'searchMore'
export const APPEND_SEARCH = 'appendSearch'
export const INCREMENT_ITERATION = 'incrementIteration'
export const RESET_SEARCH = 'resetSearch'

export const CAMPAGNE_LIST_MODULE = 'CAMPAGNE_LIST_MODULE/'
export const GET_CAMPAGNE_LIST = 'GET_CAMPAGNE_LIST'
export const GET_USER_CAMPAGNE_LIST = 'GET_USER_CAMPAGNE_LIST'
export const SAVE_CAMPAGNE_LIST = 'SAVE_CAMPAGNE_LIST'

//Entite Module
export const ENTITE_LIST_MODULE = 'ENTITE_LIST_MODULE/'
export const GET_ENTITE_LIST = 'GET_ENTITE_LIST'
export const SAVE_ENTITE_LIST = 'SAVE_ENTITE_LIST'
export const GET_ENTITE_LIST_MES = 'GET_ENTITE_LIST_MES'
export const GET_ENTITE_DETAILS = 'GET_ENTITE_DETAILS'
export const SAVE_ENTITE_DETAILS = 'SAVE_ENTITE_DETAILS'
export const CHANGE_LITIGE = 'CHANGE_LITIGE'
export const CHANGE_ACTIF = 'CHANGE_ACTIF'
export const GET_ENTITE_RESULTS = 'GET_ENTITE_RESULTS'
export const UPDATE_ENTITE_RESULTS = 'UPDATE_ENTITE_RESULTS'

//Action commercial list
export const ACTION_COMMERCIAL_MODULE = 'ACTION_COMMERCIAL_MODULE/'
export const GET_ACTION_COMMERCIAL = 'GET_ACTION_COMMERCIAL'
export const GET_ACTION_COMMERCIAL_MES = 'GET_ACTION_COMMERCIAL_MES'
export const GET_ACTION_COMMERCIAL_DETAILS = 'GET_ACTION_COMMERCIAL_DETAILS'
export const SAVE_ACTION_COMMERCIAL_DETAILS = 'SAVE_ACTION_COMMERCIAL_DETAILS'
export const CHANGE_EVENT_DATE = 'CHANGE_EVENT_DATE'
export const GET_EVENT_USER_LIST = 'GET_EVENT_USER_LIST'
export const SAVE_EVENT_USER_LIST = 'SAVE_EVENT_USER_LIST'
export const CHANGE_EVENT_USER = 'CHANGE_EVENT_USER'
export const GET_EVENT_HISTORY = 'GET_EVENT_HISTORY'
export const SAVE_EVENT_HISTORY = 'SAVE_EVENT_HISTORY'
export const FORCE_SET_FETCH_HISTORY_FALSE = 'FORCE_SET_FETCH_HISTORY_FALSE'
export const RESET_DETAILS = 'RESET_DETAILS'
export const GET_EVENT_STATUSES = 'GET_EVENT_STATUSES'
export const SAVE_EVENT_STATUSES = 'SAVE_EVENT_STATUSES'
export const CHANGE_EVENT_STATUS = 'CHANGE_EVENT_STATUS'

export const ACTION_COMMERCIAL_STATUS_MODULE = 'ACTION_COMMERCIAL_STATUS_MODULE/'

//contact list module
export const CONTACT_LIST_MODULE = 'CONTACT_LIST_MODULE/'
export const GET_CONTACT_LIST = 'GET_CONTACT_LIST'
export const GET_CONTACT_LIST_USER = 'GET_CONTACT_LIST_USER'
export const GET_CONTACT_DETAILS = 'GET_CONTACT_DETAILS'
export const SAVE_CONTACT_DETAILS = 'SAVE_CONTACT_DETAILS'
export const GET_CONTACT_RESULTS = 'GET_CONTACT_RESULTS'
export const UPDATE_CONTACT_RESULTS = 'UPDATE_CONTACT_RESULTS'
export const GET_CONTACT_STATUSES = 'GET_CONTACT_STATUSES'
export const SAVE_CONTACT_STATUSES = 'SAVE_CONTACT_STATUSES'
export const CHANGE_CONTACT_STATUS = 'CHANGE_CONTACT_STATUS'

//personnel list module
export const PERSONNEL_LIST_MODULE = 'PERSONNEL_LIST_MODULE/'
export const GET_PERSONNEL_LIST = 'GET_PERSONNEL_LIST'
export const GET_PERSONNEL_DETAILS = 'GET_PERSONNEL_DETAILS'
export const SAVE_PERSONNEL_DETAILS = 'SAVE_PERSONNEL_DETAILS'
export const PERSONNEL_SET_USER_DOCUMENTS = 'PERSONNEL_SET_USER_DOCUMENTS'
export const PERSONNEL_LOAD_USER_DOCUMENTS = 'PERSONNEL_LOAD_USER_DOCUMENTS'
export const PERSONNEL_UNSET_ORDER_DOCUMENT = 'PERSONNEL_UNSET_ORDER_DOCUMENT'
export const PERSONNEL_REMOVE_DOCUMENT = 'PERSONNEL_REMOVE_DOCUMENT'
export const PERSONNEL_GET_DOCUMENT_URL = 'PERSONNEL_GET_DOCUMENT_URL'
export const PERSONNEL_UPLOAD_DOCUMENT = 'PERSONNEL_UPLOAD_DOCUMENT'
export const DELETE_PERSONNEL = 'DELETE_PERSONNEL'
export const PERSONNEL_SAVE_PERMIS_LIST = 'PERSONNEL_SAVE_PERMIS_LIST'
export const PERSONNEL_GET_PERMIS_LIST = 'PERSONNEL_GET_PERMIS_LIST'

//Articles list module
export const ARTICLES_MODULE = 'ARTICLES_MODULE/'
export const GET_ARTICLES_LIST = 'GET_ARTICLES_LIST'
export const GET_ARTICLES_DETAILS = 'GET_ARTICLES_DETAILS'
export const SAVE_ARTICLES_DETAILS = 'SAVE_ARTICLES_DETAILS'
export const ARTICLES_VALIDATE_PRODUCT = 'ARTICLES_VALIDATE_PRODUCT'
export const ARTICLES_LOAD_PRODUCT_DOCUMENTS = 'ARTICLES_LOAD_PRODUCT_DOCUMENTS'
export const ARTICLES_SET_PRODUCT_DOCUMENTS = 'ARTICLES_SET_PRODUCT_DOCUMENTS'
export const ARTICLES_REMOVE_DOCUMENT = 'ARTICLES_REMOVE_DOCUMENT'
export const ARTICLES_UNSET_PRODUCT_DOCUMENT = 'ARTICLES_UNSET_PRODUCT_DOCUMENT'
export const ARTICLES_GET_DOCUMENT_URL = 'ARTICLES_GET_DOCUMENT_URL'
export const ARTICLES_UPLOAD_DOCUMENT = 'ARTICLES_UPLOAD_DOCUMENT'
export const GET_PRODUCT_DOCUMENT_TYPES = 'GET_PRODUCT_DOCUMENT_TYPES'
export const SAVE_PRODUCT_DOCUMENT_TYPES = 'SAVE_PRODUCT_DOCUMENT_TYPES'


//ouvrage module
export const OUVRAGE_MODULE = 'OUVRAGE_MODULE/'
export const SAVE_OUVRAGE_DETAILS = 'SAVE_OUVRAGE_DETAILS'
export const UPDATE_OUVRAGE_RESULTS = 'UPDATE_OUVRAGE_RESULTS'
export const GET_OUVRAGE_LIST = 'GET_OUVRAGE_LIST'
export const GET_OUVRAGE_DETAILS = 'GET_OUVRAGE_DETAILS'
export const GET_OUVRAGE_RESULTS = 'GET_OUVRAGE_RESULTS'
export const VALIDER_OUVRAGE = 'VALIDER_OUVRAGE'
export const GET_OUVRAGE_LIST_PRESTATION = 'GET_OUVRAGE_LIST_PRESTATION'
export const GET_OUVRAGE_LIST_INSTALLATION = 'GET_OUVRAGE_LIST_INSTALLATION'
export const GET_OUVRAGE_LIST_SECURITE = 'GET_OUVRAGE_LIST_SECURITE'

//ouvrage Tag

export const OUVRAGE_GET_LOADED = 'OUVRAGE_GET_LOADED'
export const OUVRAGE_GET_TAG_STATES = 'OUVRAGE_GET_TAG_STATES'
export const OUVRAGE_LOAD_TAG_STATES = 'OUVRAGE_LOAD_TAG_STATES'
export const OUVRAGE_SET_LOADED = 'OUVRAGE_SET_LOADED'
export const OUVRAGE_SET_TAG_STATES = 'OUVRAGE_SET_TAG_STATES'
export const OUVRAGE_STATE_MODULE = 'OUVRAGE_STATE_MODULE/'

//Commande
export const COMMANDE_LOAD_TAB = 'COMMANDE_LOAD_TAB'
export const COMMANDE_SET_LIST = 'COMMANDE_SET_LIST'
export const COMMANDE_LIST_MODULE = 'COMMANDE_LIST_MODULE/'
export const GET_COMMANDE_LIST_DEF = 'GET_COMMANDE_LIST_DEF'


export const INTERVENTION_LIST_MODULE = 'INTERVENTION_LIST_MODULE/'
export const GET_INTERVENTION_LIST = 'GET_INTERVENTION_LIST'
export const GET_INTERVENTION_LIST_MES = 'GET_INTERVENTION_LIST_MES'

export const INTERVENTION_STATUS_MODULE = 'INTERVENTION_STATUS_MODULE/'
export const INTERVENTION_LOAD_ORDER_STATES = 'INTERVENTION_LOAD_ORDER_STATES'
export const INTERVENTION_SET_STATES = 'INTERVENTION_SET_STATES'
export const INTERVENTION_SET_LOADED = 'INTERVENTION_SET_LOADED'

export const PAIEMENT_LIST_MODULE = 'PAIEMENT_LIST_MODULE/'
export const GET_PAIEMENT_LIST = 'GET_PAIEMENT_LIST'
export const GET_PAIEMENT_LIST_MES = 'GET_PAIEMENT_LIST_MES'
export const GET_PAIEMENT_LIST_VALIDER = 'GET_PAIEMENT_LIST_VALIDER'

export const PAIEMENT_STATUS_MODULE = 'PAIEMENT_STATUS_MODULE/'
export const PAIEMENT_LOAD_STATES = 'PAIEMENT_LOAD_STATES'
export const PAIEMENT_SET_LOADED = 'PAIEMENT_SET_LOADED'
export const PAIEMENT_SET_STATES = 'PAIEMENT_SET_STATES'
export const GET_PAIEMENT_DETAILS = 'GET_PAIEMENT_DETAILS'
export const SAVE_PAIEMENT_DETAILS = 'SAVE_PAIEMENT_DETAILS'
export const VALIDER_PAIEMENT = 'VALIDER_PAIEMENT'
export const GET_PAIEMENT_RESULTS = 'GET_PAIEMENT_RESULTS'
export const SAVE_PAIEMENT_RESULTS = 'SAVE_PAIEMENT_RESULTS'

//pointage
export const POINTAGE_LIST_MODULE = 'POINTAGE_LIST_MODULE/'
export const GET_POINTAGE_LIST = 'GET_POINTAGE_LIST'
export const GET_POINTAGE_LIST_MES = 'GET_POINTAGE_LIST_MES'

//pointage status
export const POINTAGE_STATUS_MODULE = 'POINTAGE_STATUS_MODULE/'
export const POINTAGE_LOAD_ORDER_STATES = 'POINTAGE_LOAD_ORDER_STATES'
export const POINTAGE_SET_STATES = 'POINTAGE_SET_STATES'
export const POINTAGE_SET_LOADED = 'POINTAGE_SET_LOADED'

export const MENU_ITEMS_MODULE = 'MENU_ITEMS_MODULE/'
export const GET_MENU_ITEMS = 'GET_MENU_ITEMS'
export const SAVE_MENU_ITEMS = 'SAVE_MENU_ITEMS'

//fournisseur
export const FOURNISSEUR_LIST_MODULE = 'FOURNISSEUR_LIST_MODULE/'
export const FOURNISSEUR_GET_LIST = 'FOURNISSEUR_GET_LIST'
export const FOURNISSEUR_GET_LIST_MES = 'FOURNISSEUR_GET_LIST_MES'
export const FOURNISSEUR_GET_DETAILS = 'FOURNISSEUR_GET_DETAILS'
export const FOURNISSEUR_SAVE_DETAILS = 'FOURNISSEUR_SAVE_DETAILS'
export const FOURNISSEUR_APPEND_RESULTS = 'FOURNISSEUR_APPEND_RESULTS'
export const FOURNISSEUR_SAVE_RESULTS = 'FOURNISSEUR_SAVE_RESULTS'

export const FOURNISSEUR_STATUS_MODULE = 'FOURNISSEUR_STATUS_MODULE/'

export const PERMIS_LIST_MODULE = 'PERMIS_LIST_MODULE/'
export const PERMIS_GET_LIST = 'PERMIS_GET_LIST'