import {
    createStore
} from "vuex";


import {
    loader as LOADER_MODULE
} from './modules/loader'
import {
    toaster as TOASTER_MODULE
} from "./modules/toaster";
import {
    sidebar as SIDEBAR_MODULE
} from "./modules/sidebar";
import {
    select as SELECT_MODULE
} from "./modules/select";
import {
    filter as FILTER_MODULE
} from "./modules/filter";

import {cible as CIBLE_MODULE} from "./modules/cible";
import {search as CUSTOMERLIST_MODULE } from "./modules/search";

import { itemlist as ITEM_LIST_MODULE } from "./modules/component_store/itemlist";

import { orderstatetag as ORDERSTATETAG_MODULE } from "./modules/component_store/orderstatetag";

import { devislist as DEVIS_LIST_MODULE } from "./modules/devislist";

import { toggler as TOGGLER_MODULE } from "./modules/toggler"

import { PageBuilder as BUILDER_MODULE } from "./modules/PageBuilder"

import { pageBuilderList as BUILDER_MODULE_LIST } from "./modules/pageBuilderList"

import { reportsBuilderList as REPORTS_BUILDER_MODULE } from "./modules/reportsBuilderList"

import { mainSearch as SEARCH_MODULE } from './modules/mainSearch'

import { campagneList as CAMPAGNE_LIST_MODULE } from './modules/campagneList'

import {devisdetail as DEVIS_DETAIL_MODULE } from './modules/devisdetail'
import { entite as ENTITE_LIST_MODULE } from './modules/entite.js'


export default createStore({
    modules: {
        LOADER_MODULE,
        TOASTER_MODULE,
        CIBLE_MODULE,
        ITEM_LIST_MODULE,
        ORDERSTATETAG_MODULE,
        DEVIS_LIST_MODULE,
        SIDEBAR_MODULE,
        SELECT_MODULE,
        FILTER_MODULE,
        TOGGLER_MODULE,
        BUILDER_MODULE,
        BUILDER_MODULE_LIST,
        SEARCH_MODULE,
        CUSTOMERLIST_MODULE,
        CAMPAGNE_LIST_MODULE,
        REPORTS_BUILDER_MODULE,
        DEVIS_DETAIL_MODULE,
        ENTITE_LIST_MODULE
    }
});
