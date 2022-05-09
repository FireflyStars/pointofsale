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

//DEVIS LIST STORE
export const DEVIS_LIST_MODULE='DEVIS_LIST_MODULE/'//namespace
export const GET_DEVIS_LIST_DEF='GET_DEVIS_LIST_DEF'//getters
export const DEVISLIST_LOAD_TAB='DEVISLIST_LOAD_TAB'//action
export const DEVISLIST_SET_LIST='DEVISLIST_SET_LIST'//mutations

export const TOASTER_MODULE = 'TOASTER_MODULE/'; //namespace
export const TOASTER_ADD_TOAST = 'TOASTER_ADD_TOAST'; //mutations
export const TOASTER_REMOVE_TOAST = 'TOASTER_REMOVE_TOAST'; //mutations
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

export const BUILDER_MODULE = 'BUILDER_MODULE'
export const GENERATE_PDF = 'GENERATE_PDF'
export const ADD_PAGE = 'AddPage'
export const DELETE_PAGE = 'deletePage'
export const GET_REPORTS = 'getReports'
export const GET_REPORT = 'getReport'
export const SAVE_REPORT = 'saveReport'
export const SAVE_REPORTS = 'saveReports'
export const SAVE_PAGE_ELEMENTS = 'SAVE_PAGE_ELEMENTS'
export const GET_ORDER_DETAILS = 'getOrderDetails'
export const SAVE_PAGE_ORDER = 'savePageOrder'
export const GET_TEMPLATES = 'getTemplates'
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

export const SAVE_META = 'saveMeta'

// customer module
export const CUSTOMERLIST_MODULE='CUSTOMERLIST_MODULE/';//namespace
export const CUSTOMER_SEARCH_LOAD_LIST='CUSTOMER_SEARCH_LOAD_LIST';//action
export const CUSTOMER_GET_SEARCH_LIST='CUSTOMER_GET_SEARCH_LIST'//getters
export const CUSTOMER_SET_SEARCH_LIST='CUSTOMER_SET_SEARCH_LIST'//getters
export const CUSTOMER_GET_SEARCH_COUNT='CUSTOMER_GET_SEARCH_COUNT'//getters
export const CUSTOMER_SET_SEARCH_COUNT='CUSTOMER_SET_SEARCH_COUNT'//getters
export const CUSTOMER_SET_LOADER = 'CUSTOMER_SET_LOADER'//getters