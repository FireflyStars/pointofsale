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

import { itemlistquicklink as ITEMLISTQUICKLINK_MODULE } from "./modules/component_store/itemlistquicklink";

import { orderstatetag as ORDERSTATETAG_MODULE } from "./modules/component_store/orderstatetag";

import { devislist as DEVIS_LIST_MODULE } from "./modules/devislist";

import { facturelist as FACTURE_LIST_MODULE } from "./modules/facturelist";
import { invoicestatetag as INVOICESTATETAG_MODULE } from "./modules/component_store/invoicestatetag";

import { toggler as TOGGLER_MODULE } from "./modules/toggler"

import { PageBuilder as BUILDER_MODULE } from "./modules/PageBuilder"

import { pageBuilderList as BUILDER_MODULE_LIST } from "./modules/pageBuilderList"

import { reportsBuilderList as REPORTS_BUILDER_MODULE } from "./modules/reportsBuilderList"

import { mainSearch as SEARCH_MODULE } from './modules/mainSearch'

import { campagneList as CAMPAGNE_LIST_MODULE } from './modules/campagneList'

import {devisdetail as DEVIS_DETAIL_MODULE } from './modules/devisdetail'
import { commandeDetails as COMMANDE_DETAIL_MODULE } from './modules/commandeDetail'

import {facturedetail as FACTURE_DETAIL_MODULE } from './modules/facturedetail'
import { entite as ENTITE_LIST_MODULE } from './modules/entite.js'

import { actionCommercial as ACTION_COMMERCIAL_MODULE } from './modules/actionCommercial.js'
import { actionCoStatusTag as ACTION_COMMERCIAL_STATUS_MODULE } from './modules/component_store/actionCoStatusTag'

import { contactList as CONTACT_LIST_MODULE } from './modules/contact'
import { personnel as PERSONNEL_LIST_MODULE } from './modules/personnel'

import { articles as ARTICLES_MODULE } from './modules/articles'
import { ouvrage as OUVRAGE_MODULE } from './modules/ouvrage'
import { ouvrageTag as OUVRAGE_STATE_MODULE } from './modules/component_store/OuvrageTag'

import { commande as COMMANDE_LIST_MODULE } from "./modules/commande"

import { intervention as INTERVENTION_LIST_MODULE } from './modules/intervention'
import { InterventionStatusTag as INTERVENTION_STATUS_MODULE } from './modules/component_store/InterventionStatusTag'


import { pointage as POINTAGE_LIST_MODULE } from './modules/pointage'
import { pointageStatusTag as POINTAGE_STATUS_MODULE } from './modules/component_store/pointageStatusTag'

import { paiement as PAIEMENT_LIST_MODULE } from "./modules/paiement"
import { paiementStatusTag as PAIEMENT_STATUS_MODULE } from './modules/component_store/paiementStatusTag'

import { menu as MENU_ITEMS_MODULE } from "./modules/menu"

import { fournisseurStatusTag as FOURNISSEUR_STATUS_MODULE } from "./modules/component_store/FournisseurTag"
import { fournisseur as FOURNISSEUR_LIST_MODULE } from "./modules/fournisseur"

import { commandeFournisseurStatusTag as COMMANDE_FOURNISSEUR_STATUS_MODULE } from "./modules/component_store/CommandeFournisseurTag"
import { commande_fournisseur as COMMANDE_FOURNISSEUR_LIST_MODULE } from "./modules/commande_fournisseur"

import { permis as PERMIS_LIST_MODULE } from "./modules/permis"



export default createStore({
    modules: {
        LOADER_MODULE,
        TOASTER_MODULE,
        CIBLE_MODULE,
        ITEM_LIST_MODULE,
        ITEMLISTQUICKLINK_MODULE,
        ORDERSTATETAG_MODULE,
        DEVIS_LIST_MODULE,
        FACTURE_LIST_MODULE,
        FACTURE_DETAIL_MODULE,
        INVOICESTATETAG_MODULE,
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
        ENTITE_LIST_MODULE,
        ACTION_COMMERCIAL_MODULE,
        ACTION_COMMERCIAL_STATUS_MODULE,
        CONTACT_LIST_MODULE,
        PERSONNEL_LIST_MODULE,
        ARTICLES_MODULE,
        OUVRAGE_MODULE,
        OUVRAGE_STATE_MODULE,
        COMMANDE_LIST_MODULE,
        COMMANDE_DETAIL_MODULE,
        INTERVENTION_LIST_MODULE,
        INTERVENTION_STATUS_MODULE,
        PAIEMENT_LIST_MODULE,
        PAIEMENT_STATUS_MODULE,
        POINTAGE_LIST_MODULE,
        POINTAGE_STATUS_MODULE,
        MENU_ITEMS_MODULE,
        FOURNISSEUR_STATUS_MODULE,
        FOURNISSEUR_LIST_MODULE,
        PERMIS_LIST_MODULE,
        COMMANDE_FOURNISSEUR_STATUS_MODULE,
        COMMANDE_FOURNISSEUR_LIST_MODULE
    }
});
