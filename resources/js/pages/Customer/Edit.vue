<template>
  <router-view>
    <transition enter-active-class="animate__animated animate__fadeIn">
      <div class="container-fluid h-100 bg-color" id="container">
        <main-header />
        <div class="row d-flex align-content-stretch align-items-stretch flex-row hmax main-view-wrap reports-page" style="z-index:100" >
            <side-bar />
            <div class="col main-view container">
                <h1 class="d-flex align-items-center m-0">
                  <span class="customer-icon"></span>
                  <span class="ms-3 font-22 almarai_extrabold_normal_normal">EDITION ENTITE</span>
                </h1>
                <ul class="full-nav d-flex p-0 m-0 bg-white">
                    <li class="full-nav-item title border-right col-4 d-flex align-items-center justify-content-center"
                        :class="{ active: step == 'client-detail'}"
                        @click="selectNav('client-detail')"
                    >
                        <svg class="icon" width="22" height="20" viewBox="0 0 22 20" fill="none" xmlns="http://www.w3.org/2000/svg"
                            v-if="step == 'address' || step == 'information' || step == 'contact'"
                        >
                            <circle cx="10.9058" cy="10" r="9" stroke="#42A71E" stroke-width="2"/>
                            <g clip-path="url(#clip0_807_2682)">
                                <path d="M10.6555 15.0751L4.58057 9.07506L6.23053 7.42505L10.6555 11.925L19.5806 2.92505L21.2305 4.57506L10.6555 15.0751Z" fill="#05944F"/>
                            </g>
                            <defs>
                                <clipPath id="clip0_807_2682">
                                    <rect width="18" height="18" fill="white" transform="translate(3.90576)"/>
                                </clipPath>
                            </defs>
                        </svg>
                        <svg v-if="step != 'address' && step != 'information' && step != 'contact'" class="icon" width="21" height="20" viewBox="0 0 21 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="10.9058" cy="10" r="9" stroke="#47454B" stroke-width="2"/>
                        </svg>
                        ENTITE
                    </li>
                    <li class="full-nav-item title border-right col-4 d-flex align-items-center justify-content-center"
                        :class="{ active: step == 'address'}"
                        @click="selectNav('address')"
                    >
                        <svg class="icon" width="22" height="20" viewBox="0 0 22 20" fill="none" xmlns="http://www.w3.org/2000/svg"
                            v-if="step == 'information' || step == 'contact'"
                        >
                            <circle cx="10.9058" cy="10" r="9" stroke="#42A71E" stroke-width="2"/>
                            <g clip-path="url(#clip0_807_2682)">
                                <path d="M10.6555 15.0751L4.58057 9.07506L6.23053 7.42505L10.6555 11.925L19.5806 2.92505L21.2305 4.57506L10.6555 15.0751Z" fill="#05944F"/>
                            </g>
                            <defs>
                                <clipPath id="clip0_807_2682">
                                    <rect width="18" height="18" fill="white" transform="translate(3.90576)"/>
                                </clipPath>
                            </defs>
                        </svg>
                        <svg v-if="step != 'information' && step != 'contact'" class="icon" width="21" height="20" viewBox="0 0 21 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="10.9058" cy="10" r="9" stroke="#47454B" stroke-width="2"/>
                        </svg>
                        BATIMENTS
                    </li>
                    <li class="full-nav-item title border-right col-4 d-flex align-items-center justify-content-center"
                        :class="{ active: step == 'contact'}"
                        @click="selectNav('contact')"
                    >
                        <svg class="icon" width="21" height="20" viewBox="0 0 21 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="10.9058" cy="10" r="9" stroke="#47454B" stroke-width="2"/>
                        </svg>
                        CONTACT
                    </li>
                </ul>
                <transition name="list" appear v-if="step =='client-detail'">
                    <div class="cust-page-content client-detail m-auto pt-5">
                        <div class="page-section">
                            <h3 class="m-0 mulish-extrabold font-22">ENTITE</h3>
                            <div class="d-flex mt-3">
                                <div class="col-4">
                                    <div class="form-group">
                                        <label class="mulish-medium font-16 text-nowrap">RAISON SOCIALE *</label>
                                        <input type="text" v-model="form.raisonsociale" placeholder="Raison sociale" class="form-control">
                                    </div>
                                </div>
                                <div class="col-4 ps-2">
                                    <div class="form-group">
                                        <label class="mulish-medium font-16 text-nowrap">NOM COMMERCIAL</label>
                                        <input type="text" v-model="form.raisonsociale2" placeholder="Raison2 sociale" class="form-control">
                                    </div>                                        
                                </div>
                                <div class="col-4 ps-2">
                                    <select-box v-model="form.customerTax" :options="customerTaxes" :label="'TVA'" :name="'customerTax'"></select-box>
                                </div>
                            </div>    
                            <div class="d-flex mt-3">
                                <div class="col-7 d-flex">
                                    <div class="d-flex col-8">
                                        <div class="form-group col-9">
                                            <label>SIRET</label>
                                            <input type="text" v-model="form.siret" class="form-control" v-mask="'##############'">
                                        </div>
                                        <div class="form-group col-3 px-2">
                                            <label>&nbsp;</label>
                                            <button class="btn btn-primary" @click="checkSiret">VERIFIER</button>
                                        </div>
                                    </div>
                                    <div class="col-1"></div>
                                    <div class="col-3">
                                        <div class="form-group">
                                            <label class="text-nowrap">NUM LCDT</label>
                                            <input type="text" v-model="form.numLCDT" class="form-control" placeholder="GX-LCDT">
                                        </div>                                 
                                    </div>
                                </div>
                                <div class="col-1"></div>
                                <div class="col-4 ps-2">
                                    <div class="form-group">
                                        <label class="mulish-medium font-16 text-nowrap">NUM TVA</label>
                                        <input type="text" v-model="form.numtva" placeholder="" class="form-control">
                                    </div>                                        
                                </div>
                            </div>                                           
                        </div>
                        <div class="page-section mt-3">
                            <h3 class="m-0 mulish-extrabold font-22">SITUATION</h3>
                            <div class="d-flex">
                                <div class="col-6"></div>
                                <div class="col-6 d-flex">
                                    <CheckBox v-model="form.litige" :checked="form.litige" class="ms-5" :title="'LITIGE'"></CheckBox>
                                    <CheckBox v-model="form.active" :checked="form.active" class="ms-5" :title="'ACTIF'"></CheckBox>
                                    <CheckBox v-model="form.descision" :checked="form.descision" class="ms-5" :title="'DECISIONNAIRE'"></CheckBox>
                                </div>
                            </div>
                            <div class="d-flex mt-3">
                                <div class="col-4 pe-3">
                                    <select-box v-model="form.customerOrigin"
                                        :options="customerOrigins"
                                        :name="'customerOrigin'"
                                        :label="'ORIGINE ENTITE'"
                                        ></select-box>
                                </div>
                                <div class="col-4 pe-3">
                                    <select-box v-model="form.customerStatus"
                                        :options="customerStatuses"
                                        :name="'customerStatus'"
                                        :label="'STATUT'"
                                        ></select-box>
                                </div>
                                <div class="col-4">
                                    <div class="form-group">
                                        <label>ENTITE RATTACHEE</label>
                                        <SearchMaster v-model="form.masterId" name="search" :droppos="{top:'auto',right:'auto',bottom:'auto',left:'0',transformOrigin:'top right'}"></SearchMaster>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex mt-3">
                                <div class="col-9 form-group">
                                    <label class="text-nowrap">NOTES / INFORMATIONS / COMMENTAIRES</label>
                                    <textarea rows="4" class="form-control" v-model="form.note"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="page-section mt-3">
                            <h3 class="m-0 mulish-extrabold font-22">INFORMATION</h3>
                            <div class="d-flex mt-3">
                                <div class="col-6 d-flex">
                                    <div class="col-7 form-group">
                                        <label>SEGMENTATION</label>
                                        <input v-model="form.segmentation" type="text" class="form-control" readonly>
                                    </div>
                                    <div class="col-1"></div>
                                    <div class="col-4 form-group">
                                        <label>NAF</label>
                                        <input v-model="form.naf" type="text" class="form-control" v-mask="'####A'">
                                    </div>
                                </div>
                                <div class="col-6 ps-3 form-group">
                                    <label class="text-nowrap">NOM NAF</label>
                                    <input type="text" v-model="form.nomNaf" class="form-control" readonly>
                                </div>
                            </div>
                            <div class="d-flex mt-3">
                                <div class="col-3 pe-3">
                                    <div class="form-group">
                                        <label class="text-nowrap">TRANCHE EFFECTIF</label>
                                        <input type="text" v-model="form.trancheEffectif" class="form-control">
                                    </div>
                                </div>
                                <div class="col-3 pe-3">
                                    <div class="form-group">
                                        <label class="text-nowrap">TRANCHE CA</label>
                                        <input type="text" v-model="form.trancheCA" class="form-control">
                                    </div>
                                </div>
                                <div class="col-3 pe-3">
                                    <div class="form-group">
                                        <label class="text-nowrap">TRANCHE COMMUNE</label>
                                        <input type="text" v-model="form.trancheCommune" class="form-control">
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="form-group">
                                        <label class="text-nowrap">DATE CREATION ENTITE</label>
                                        <Datepicker v-model="form.dateCreated" calendarCellClassName="dp-cell-bgcolor" position="left" :hideInputIcon="true" inputClassName="form-control" autoApply :format="dateFormat"/>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex mt-3">
                                <div class="col-6 pe-3">
                                    <div class="form-group">
                                        <select-box v-model="form.customerCat" :options="customerCats" :name="'customerCat'" :label="'CATEGORIE JURIDIQUE'"></select-box>
                                    </div>
                                </div>
                                <div class="col-6 ps-3">
                                    <div class="form-group">
                                        <select-box v-model="form.customerPaiement" :options="customerPaiements" :name="'customerPaiement'" :label="'MODE DE PAIEMENT *'"></select-box>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex mt-3">
                                <div class="col-4 pe-3">
                                    <div class="form-group">
                                        <label>LINKEDIN</label>
                                        <input type="text" v-model="form.linkedin" class="form-control" placeholder="LINKEDIN">
                                    </div>
                                </div>
                                <div class="col-4 pe-3">
                                    <div class="form-group">
                                        <label>SITE WEB</label>
                                        <input type="text" v-model="form.website" class="form-control" placeholder="Http://">
                                    </div>
                                </div>
                                <div class="col-3">
                                </div>
                            </div>
                            <div class="d-flex col-11 mt-3 justify-content-between">
                                <div class="col-3">
                                    <div class="form-group">
                                        <label class="text-nowrap">STATUS ETABLISSEMENT</label>
                                        <input type="text" placeholder="STATUS ETABLISSEMENT" v-model="form.statusEtablissement" class="form-control">
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="form-group">
                                        <label>ENVIRONEMENT</label>
                                        <input type="text" placeholder="ENVIRONEMENT" v-model="form.environment" class="form-control">
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="form-group">
                                        <label>ZPE</label>
                                        <input type="text" placeholder="ZPE" v-model="form.zpe" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="btns d-flex justify-content-end mb-3">
                            <button class="custom-btn btn-cancel me-3" @click="cancel">Annuler</button>
                            <button class="custom-btn btn-ok text-uppercase" @click="nextStep">Suivant</button>
                        </div>
                    </div>
                </transition>
                <transition name="list" appear v-if="step == 'address'">
                    <div class="cust-page-content m-auto pt-5">
                        <div class="btns d-flex justify-content-end my-3">
                            <button class="custom-btn btn-ok text-uppercase" @click="addAddress">AJOUTER ADRESSE</button>
                        </div>
                        <div class="page-section" v-for="(address, index) in form.addresses" :key="index">
                            <h3 class="m-0 mulish-extrabold font-22">BATIMENTS</h3>
                            <div class="d-flex">
                                <div class="col-5 pe-3">
                                    <select-box v-model="address.addressType" :options="addressTypes" :label="'TYPE ADRESSE *'" :name="'addressType'+index"></select-box>
                                </div>
                                <div class="col-7 d-flex">
                                    <div class="col-7">
                                        <div class="form-group">
                                            <label class="text-nowrap">PRENOM / NOM BATIMENT *</label>
                                            <input type="text" v-model="address.firstName" placeholder="FirstName" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-5 ps-3">
                                        <div class="form-group">
                                            <label>NOM</label>
                                            <input type="text" v-model="address.nom" placeholder="Name" class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex mt-3">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label class="text-nowrap">ADRESSE 1 (N° et libellé de voie…)  * </label>
                                        <GoogleAddress v-if="useGoogleService" :address="address.address1" :index="index" :placeholder="'Adresse1'" @updateAddressInfo="updateAddressInfo"></GoogleAddress>
                                        <input v-else type="text" placeholder="Adresse1" v-model="address.address1" class="form-control">
                                    </div>
                                </div>
                                <div class="col-6 ps-3">
                                    <div class="form-group">
                                        <label class="text-nowrap">ADRESSE 3 (Lieu-dit, bâtiment, boîte postale…)</label>
                                        <input type="text" placeholder="Adresse3" v-model="address.address3" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex mt-3">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label class="text-nowrap">ADRESSE 2 (ZI, ZA…)</label>
                                        <input type="text" v-model="address.address2" placeholder="Address2" class="form-control">
                                    </div>
                                </div>
                                <div class="col-6 ps-3 d-flex">
                                    <div class="col-5">
                                        <div class="form-group">
                                            <label class="text-nowrap">CODE POSTAL *</label>
                                            <input type="text" v-model="address.postCode" class="form-control" v-mask="'#####'">
                                        </div>
                                    </div>
                                    <div class="col-7 ps-3">
                                        <div class="form-group">
                                            <label>VILLE *</label>
                                            <input type="text" v-model="address.city" class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex mt-3">
                                <div class="col-3">
                                    <div class="form-group">
                                        <label>EMAIL GENERIQUE</label>
                                        <input type="text" v-model="address.receiptEmail" placeholder="E-receipt email" class="form-control">
                                    </div>
                                </div>
                                <div class="customer-phone col-5 ps-3">
                                    <div>
                                        <label class="text-uppercase">STANDARD TELEPHONIQUE</label>
                                    </div>
                                    <div class="d-flex">
                                        <div class="phone-country-code">
                                            <select-box
                                                v-model="address.phoneCode"
                                                :options="phoneCodesSorted"
                                                :styles="{ width: '100px'}"
                                                :name="'phoneCountryCode'">
                                            </select-box>
                                        </div>
                                        <div class="form-group ms-2">
                                            <input type="text" v-model="address.phoneNumber" class="form-control custom-input">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4 px-2">
                                    <div class="address-map" >
                                        <GoogleMap v-model:latitude="address.latitude" v-model:longitude="address.longitude"></GoogleMap>
                                    </div>
                                </div>
                            </div>
                            <hr class="border-bottom border-dark border-2 mt-3">
                            <div class="d-flex mt-3">
                                <div class="col-4 pe-3">
                                    <div class="form-group">
                                        <label>PENTE (o)</label>
                                        <input v-model="address.pente" type="text" class="form-control" v-mask="'##.##'">
                                    </div>
                                </div>
                                <div class="col-4 pe-3">
                                    <div class="form-group">
                                        <label>SURFACE TOITURE (M2)</label>
                                        <input v-model="address.surfacetoiture" type="text" class="form-control" v-mask="'##.##'">
                                    </div>
                                </div>
                                <div class="col-4">
                                    <select-box
                                        v-model="address.materiau"
                                        :options="customerMateriaus"
                                    :label="'MATERIAU (X)'"
                                    :name="'materiau'"></select-box>
                                </div>
                            </div>
                            <div class="d-flex mt-3">
                                <div class="col-4 pe-3">
                                    <select-box
                                        v-model="address.presenceamiante"
                                        :options="[
                                            { value: 'OUI', display: 'OUI' },
                                            { value: 'NON', display: 'NON' },
                                            { value: 'PAS CONNU', display: 'PAS CONNU' },
                                        ]"
                                    :label="'PRESENTE D AMIANTE'"
                                    :name="'amiante'"></select-box>
                                </div>
                                <div class="col-4 pe-3">
                                    <select-box
                                        v-model="address.accesinterieur"
                                        :options="[
                                            { value: 'OUI', display: 'OUI' },
                                            { value: 'NON', display: 'NON' },
                                            { value: 'PAS CONNU', display: 'PAS CONNU' },
                                        ]"
                                    :label="'ACCES SECURISE PAR L INTERIEUR'"
                                    :name="'interieur'"></select-box>
                                </div>
                                <div class="col-4">
                                    <select-box
                                        v-model="address.accesexterieur"
                                        :options="[
                                            { value: 'OUI', display: 'OUI' },
                                            { value: 'NON', display: 'NON' },
                                            { value: 'PAS CONNU', display: 'PAS CONNU' },
                                        ]"
                                    :label="'ACCES SECURISER PAR L EXTERIEUR'"
                                    :name="'exterieur'"></select-box>
                                </div>
                            </div>
                            <div class="d-flex mt-3">
                                <div class="col-4 pe-3">
                                    <select-box
                                        v-model="address.presenceepc"
                                        :options="[
                                            { value: 'OUI', display: 'OUI' },
                                            { value: 'NON', display: 'NON' },
                                            { value: 'PAS CONNU', display: 'PAS CONNU' },
                                        ]"
                                    :label="'PRESENCE EPC'"
                                    :name="'epc'"></select-box>
                                </div>
                                <div class="col-4 pe-3">
                                    <select-box
                                        v-model="address.etattoiture"
                                        :options="[
                                            { value: 'Neuf', display: 'Neuf' },
                                            { value: 'Bon', display: 'Bon' },
                                            { value: 'Vétuste', display: 'Vétuste' },
                                            { value: 'Ne Sait pas', display: 'Ne Sait pas' },
                                        ]"
                                    :label="'ETAT TOITURE'"
                                    :name="'ETAT TOITURE'"></select-box>
                                </div>
                                <div class="col-4 pe-3">
                                    <select-box
                                        v-model="address.hauteurbatiment"
                                        :options="[
                                            { value: '1 à 5M', display: '1 à 5M' },
                                            { value: '6 à 10M', display: '6 à 10M' },
                                            { value: '10 à Plus', display: '10 à Plus' },
                                            { value: 'Ne Sait pas', display: 'Ne Sait pas' },
                                        ]"
                                    :label="'HAUTEUR BATIMENT'"
                                    :name="'HAUTEUR BATIMENT'"></select-box>
                                </div>
                            </div>
                            <div class="d-flex mt-3">
                                <div class="col-4 pe-3">
                                    <select-box
                                        v-model="address.typebatiment"
                                        :options="customerTypeBatiments"
                                    :label="'TYPE BATIMENT'"
                                    :name="'BATIMENT'"></select-box>
                                </div>
                                <div class="col-4 pe-3">
                                    <select-box
                                        v-model="address.presenceapportlumiere"
                                        :options="[
                                            { value: 'OUI', display: 'OUI' },
                                            { value: 'NON', display: 'NON' },
                                            { value: 'PAS CONNU', display: 'PAS CONNU' },
                                        ]"
                                    :label="'PRESENCE APPORT LUMIERE'"
                                    :name="'PRESENCE APPORT LUMIERE'"></select-box>
                                </div>
                            </div>

                        <div class="d-flex mt-3">
                            <div class="col-8 pe-3">
                                <div class="form-group">
                                    <label class="text-nowrap">NOTES / INFORMATIONS / COMMENTAIRES</label>
                                    <textarea rows="4" class="form-control" v-model="address.infoNote"></textarea>
                                </div>
                            </div>
                            <div class="col-4 d-flex align-items-end justify-content-end">
                                <button @click="removeAddress(index)" class="custom-btn btn-danger text-nowrap">SUPPRIMER ADRESSE</button>
                            </div>
                        </div>
                        </div>
                        <div class="btns d-flex justify-content-end mb-3">
                            <button class="custom-btn btn-cancel me-3" @click="cancel">Annuler</button>
                            <button class="custom-btn btn-ok text-uppercase" @click="nextStep">Suivant</button>
                        </div>
                    </div>
                </transition>
                <transition name="list" appear v-if="step =='contact'">
                    <div class="cust-page-content client-detail m-auto pt-5">
                        <div class="btns d-flex justify-content-end my-3">
                            <button class="custom-btn btn-ok text-uppercase" @click="addContact">AJOUTER CONTACT</button>
                        </div>
                        <div class="page-section" v-for="(contact, index) in form.contacts" :key="index">
                            <h3 class="m-0 mulish-extrabold font-22">CONTACT</h3>
                            <div class="d-flex mt-3">
                                <div class="col-9"></div>
                                <div class="col-3">
                                    <CheckBox v-model="contact.actif" :checked="contact.actif ? true : false" :title="'ACTIF'"></CheckBox>
                                </div>
                            </div>
                            <div class="d-flex mt-3">
                                <div class="col-4">
                                    <select-box v-model="contact.type" :options="contactTypes" :name="'contactType'+index" :label="'TYPE CONTACT *'"></select-box>
                                </div>
                                <div class="col-8 d-flex ps-3">
                                    <div class="col-2 form-group">
                                        <select-box v-model="contact.gender"
                                            :options="[
                                                { value: 'MADAME', display: 'Madame' },
                                                { value: 'MONSIEUR', display: 'Monsieur' },
                                                { value: 'Mademoiselle', display: 'Mademoiselle' },
                                            ]" 
                                            :name="'customerGender'+index"
                                            :label="'&nbsp;'"
                                            ></select-box>
                                    </div>
                                    <div class="col-5 ps-2 form-group">
                                        <label for="nom-client" class="mulish-medium font-16">PRENOM *</label>
                                        <input type="text" class="form-control" v-model="contact.firstName" placeholder="First Name">
                                    </div>
                                    <div class="col-5 ps-2 form-group">
                                        <label class="mulish-medium font-16">NOM *</label>
                                        <input type="text" v-model="contact.name" placeholder="Name" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex mt-3">
                                <div class="col-7">
                                    <select-box v-model="contact.qualite"
                                        :options="contactQualites"
                                        :name="'QUANTITE'+index"
                                        :label="'QUANTITE'"
                                        ></select-box>
                                </div>
                                <div class="col-5 ps-4">
                                    <div class="d-flex justify-content-between">
                                        <div class="phone-country-code">
                                            <select-box
                                                v-model="contact.phoneCountryCode1"
                                                :options="phoneCodesSorted"
                                                :styles="{ width: '100px'}"
                                                :label="'&nbsp;'"
                                                :name="'phoneCountryCode'">
                                            </select-box>
                                        </div>
                                        <div class="form-group ms-2">
                                            <label class="text-uppercase">TELEPHONE FIXE</label>
                                            <input type="text" placeholder="telephone" v-model="contact.phoneNumber1" class="form-control custom-input">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex mt-3">
                                <div class="col-7">
                                    <select-box v-model="contact.address"
                                        :options="customerAddresses"
                                        :name="'ADRESSE_BATIMENTS'+index"
                                        :label="'ADRESSE / BATIMENTS'"
                                        ></select-box>
                                </div>
                                <div class="col-5 ps-4">
                                    <div class="d-flex justify-content-between">
                                        <div class="phone-country-code">
                                            <select-box
                                                v-model="contact.phoneCountryCode2"
                                                :options="phoneCodesSorted"
                                                :styles="{ width: '100px'}"
                                                :label="'&nbsp;'"
                                                :name="'phoneCountryCode'">
                                            </select-box>
                                        </div>
                                        <div class="form-group ms-2">
                                            <label class="text-uppercase">TELEPHONE MOBILE</label>
                                            <input type="text" placeholder="mobile" v-model="contact.phoneNumber2" class="form-control custom-input">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex mt-3">
                                <div class="col-7">
                                    <div class="form-group">
                                        <label class="mulish-medium font-16">EMAIL *</label>
                                        <input type="text" v-model="contact.email" @change="validationUniqueEmail($event, 'contacts', contact.id)" placeholder="email" class="form-control">
                                    </div>
                                </div>
                                <div class="col-5 ps-4">
                                    <div class="form-group">
                                        <label class="mulish-medium font-16">PROFIL LINKEDIN</label>
                                        <input type="text" v-model="contact.profilLinedin" placeholder="profillinedin" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex mt-3">
                                <div class="col-9">
                                    <div class="d-flex">
                                        <div class="col-7">
                                            <div class="form-group">
                                                <label>COMMENTAIRES</label>
                                                <input type="text" v-model="contact.note" placeholder="comment" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-5 ps-3 d-flex">
                                            <div class="form-group">
                                                <label>NUM-GX</label>
                                                <input type="text" v-model="contact.numGx" class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex mt-3">
                                        <div class="col-4">
                                            <CheckBox v-model="contact.acceptSMS" :checked="contact.acceptSMS" :title="'SMS Marketing'"></CheckBox>
                                        </div>
                                        <div class="col-4">
                                            <CheckBox v-model="contact.acceptmarketing" :checked="contact.acceptmarketing" :title="'Email Marketing'"></CheckBox>
                                        </div>
                                        <div class="col-4">
                                            <CheckBox v-model="contact.acceptcourrier" :checked="contact.acceptmarketing"  :title="'Courrier Marketing'"></CheckBox>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-3 d-flex align-items-center ps-3">
                                    <button @click="removeContact(index)" class="custom-btn btn-danger text-nowrap">SUPPRIMER CONTACT</button>
                                </div>
                            </div>
                        </div>
                        <div class="btns d-flex justify-content-end mb-3">
                            <button class="custom-btn btn-cancel me-3" @click="cancel">Annuler</button>
                            <button class="custom-btn btn-ok text-uppercase" @click="submit">VALIDATION</button>
                        </div>
                    </div>
                </transition>
            </div>
        </div>
      </div>
    </transition>
  </router-view>
</template>
<script>
import { ref, onMounted, watch } from 'vue';
import SelectBox from '../../components/miscellaneous/SelectBox';
import CheckBox from '../../components/miscellaneous/CheckBox';
import GoogleMap from '../../components/miscellaneous/GoogleMap';
import GoogleAddress from '../../components/miscellaneous/GoogleAddress';
import SearchMaster from '../../components/miscellaneous/SearchMaster';
import { phoneCountryCode as phoneCodes } from '../../static/PhoneCountryCodes';
import Datepicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css'
import Swal from 'sweetalert2';
import {
  DISPLAY_LOADER,
  HIDE_LOADER,
  LOADER_MODULE,
  TOASTER_MESSAGE,
  TOASTER_MODULE
  } from '../../store/types/types';

import axios from 'axios';
import { useStore } from 'vuex';
import { useRouter, useRoute } from 'vue-router';
import { mask } from 'vue-the-mask';

export default {
    directives: {
        mask
    },
    components:{
        SelectBox,
        CheckBox,
        GoogleMap,
        GoogleAddress,
        SearchMaster,
        Datepicker
    },
    setup() {
        const store = useStore();
        const router = useRouter();
        const route = useRoute();
        const step = ref('client-detail');
        const uniqueEmail = ref({ status: true, msg: '' });
        // const step = ref('address');
        const customerStatuses  = ref([]);
        const customerOrigins  = ref([]);
        const customerTaxes    = ref([]);
        var customerNafs    = [];
        const customerCats   = ref([]);
        const customerPentes   = ref([]);
        const customerQualites   = ref([]);
        const contactQualites   = ref([]);
        const customerTypeBatiments   = ref([]);
        const customerMateriaus   = ref([]);
        const customerAddresses   = ref([]);
        const customerPaiements   = ref([]);
        const addressTypes     = ref([]);
        const contactTypes     = ref([]);
        const useGoogleService     = ref(false);
        const form = ref({
            id: '',
            created_at: '',
            updated_at: '',
            raisonsociale: '',
            raisonsociale2: '',
            siret: '',
            siretValidation: true,
            numLCDT: '',
            company: '',
            customerOrigin: 2,
            customerStatus: 0,
            segmentation: '',
            customerCat: 0,
            customerPaiement: 1,
            naf: '',
            nomNaf: '',
            gender: 'MADAME',
            firstName: '',
            lastName: '',
            phoneCountryCode: '+33',
            phoneNumber: '',
            email: '',
            customerTax: 0,
            numtva: '',
            litige: false,
            active: true,
            descision: true,
            masterId: '',
            linkedin: '',
            website: '',
            note: '',
            trancheEffectif: '',
            trancheCA: '',
            trancheCommune: '',
            statusEtablissement: '',
            environment: '',
            dateCreated: '',
            zpe: '',
            // address tab
            addresses: [{
                id: '',
                addressType: 3,
                firstName: '',
                nom: '',
                address1: '',
                address2: '',
                address3: '',
                postCode: '',
                city: '',
                state: '',
                receiptEmail: '',
                phoneCode: '+33',
                phoneNumber: '',
                latitude: 48.85560142492883,
                longitude: 2.3491914978706396,
                pente: '',
                surfacetoiture: '',
                materiau: '',
                presenceamiante: '',
                presenceepc: '',
                accesexterieur: '',
                presenceapportlumiere: '',
                etattoiture: '',
                accesinterieur: '',
                hauteurbatiment: '',
                typebatiment: '',
                infoNote: '',
            }],
            // contacts
            contacts: [{
                id: '',
                type: '',
                actif: true,
                qualite: '',
                gender: 'MADAME',
                firstName: '',
                address: '',
                profilLinedin: '',
                name: '',
                email: '',
                note: '',
                numGx: '',
                phoneCountryCode1: '+33',
                phoneNumber1: '',
                phoneCountryCode2: '+33',
                phoneNumber2: '',
                acceptSMS: true,
                acceptmarketing: true,
                acceptcourrier: true,
            }],
        });
        const dateFormat = (date) => {
            const day = date.getDate();
            const month = date.getMonth() + 1;
            const year = date.getFullYear();
            return `${month.toString().padStart(2, '0')}/${day.toString().padStart(2, '0')}/${year}`;
        }
        const updateAddressInfo = (data)=>{
            form.value.addresses[data.index].address1 = data.street;
            form.value.addresses[data.index].latitude = data.lat;
            form.value.addresses[data.index].longitude = data.lon;
            form.value.addresses[data.index].city = data.city;
            form.value.addresses[data.index].postCode = data.postcode;
        }
        const checkSiret = ()=>{
            if(form.value.siret.length == 9){
                store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`, [true, 'checking siret ...']);
                axios.post('/check-siret', { 'siret' : form.value.siret }).then((res)=>{
                    if(res.data.success){
                        // form.value.siretValidation = true;
                        form.value.naf = res.data.data.activitePrincipaleUniteLegale.replace('.', '');
                        form.value.raisonsociale    = res.data.data.denominationUniteLegale;
                        form.value.raisonsociale2   = res.data.data.denominationUsuelle1UniteLegale;
                    }else{
                        store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                            type: 'danger',
                            message: res.data.error,
                            ttl: 5,
                        });
                    }
                    console.log(res.data);
                }).catch((error)=>{
                    console.log(error);
                }).finally(()=>{
                    store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`);
                })
            }else{
                store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                    type: 'danger',
                    message: 'Siret must be 9 digits',
                    ttl: 5,
                });
            }
        }
        const selectNav = (value)=>{
            if(step.value == 'client-detail'){
                if(form.value.raisonsociale == ''){
                    store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                        type: 'danger',
                        message: 'Veuillez entrer RAISON SOCIALE',
                        ttl: 5,
                    });
                // }else if(form.value.siret == ''){
                //     store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                //         type: 'danger',
                //         message: 'Veuillez entrer SIRET',
                //         ttl: 5,
                //     });
                }else if(form.value.customerStatus == 0){
                    store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                        type: 'danger',
                        message: 'Veuillez sélectionner le statut',
                        ttl: 5,
                    });
                // }else if(form.value.customerCat == 0){
                //     store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                //         type: 'danger',
                //         message: 'Veuillez sélectionner la catégorie',
                //         ttl: 5,
                //     });
                }else if(form.value.customerPaiement == 0){
                    store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                        type: 'danger',
                        message: 'Veuillez sélectionner la MODE DE PAIEMENT',
                        ttl: 5,
                    });
                // }else if(form.value.naf == '' || form.value.naf.length != 5 || customerNafs.find((item)=>{ return item.code == form.value.naf }) == undefined){
                //     store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                //         type: 'danger',
                //         message: 'Veuillez entrer NAF',
                //         ttl: 5,
                //     });
                }else{
                    step.value = value;
                }
            }
            if( step.value == 'address' ){
                var error = false;
                form.value.addresses.forEach((address, index) => {
                    if(address.addressType == ''){
                        error = true;
                        store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                            type: 'danger',
                            message: 'Veuillez sélectionner le type d`adresse',
                            ttl: 5,
                        });
                    // }else if(address.address1 == ''){
                    //     error = true;
                    //     store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                    //         type: 'danger',
                    //         message: 'Veuillez entrer l`adresse1',
                    //         ttl: 5,
                    //     });
                    }else if(address.postCode == ''){
                        error = true;
                        store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                            type: 'danger',
                            message: 'Veuillez entrer le CODE POSTAL',
                            ttl: 5,
                        });
                    }else if(address.city == ''){
                        error = true;
                        store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                            type: 'danger',
                            message: 'Veuillez saisir VILLE',
                            ttl: 5,
                        });
                    }else if(address.firstName == ''){
                        error = true;
                        store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                            type: 'danger',
                            message: 'Please enter PRENOM / NOM BATIMENT',
                            ttl: 5,
                        });
                    }
                });
                if(!error){
                    step.value = value;
                }
            }
            if(step.value == 'contact'){
                step.value = value;
            }

        }
        const cancel = ()=>{

        }
        const nextStep = ()=>{
            if(step.value == 'client-detail'){
                if(form.value.raisonsociale == ''){
                    store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                        type: 'danger',
                        message: 'Veuillez entrer RAISON SOCIALE',
                        ttl: 5,
                    });
                // }else if(form.value.siret == ''){
                //     store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                //         type: 'danger',
                //         message: 'Veuillez entrer SIRET',
                //         ttl: 5,
                //     });
                }else if(form.value.customerStatus == 0){
                    store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                        type: 'danger',
                        message: 'Veuillez sélectionner le statut',
                        ttl: 5,
                    });
                // }else if(form.value.customerCat == 0){
                //     store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                //         type: 'danger',
                //         message: 'Veuillez sélectionner la catégorie',
                //         ttl: 5,
                //     });
                }else if(form.value.customerPaiement == 0){
                    store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                        type: 'danger',
                        message: 'Veuillez sélectionner la MODE DE PAIEMENT',
                        ttl: 5,
                    });
                // }else if(form.value.naf == '' || form.value.naf.length != 5 || customerNafs.find((item)=>{ return item.code == form.value.naf }) == undefined){
                //     store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                //         type: 'danger',
                //         message: 'Veuillez entrer NAF',
                //         ttl: 5,
                //     });
                }else{
                    step.value = 'address';
                }
            }else if( step.value == 'address' ){
                var error = false;
                form.value.addresses.forEach(address => {
                    if(address.addressType == ''){
                        error = true;
                        store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                            type: 'danger',
                            message: 'Veuillez sélectionner le type d`adresse',
                            ttl: 5,
                        });
                    // }else if(address.address1 == ''){
                    //     error = true;
                    //     store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                    //         type: 'danger',
                    //         message: 'Veuillez entrer l`adresse1',
                    //         ttl: 5,
                    //     });
                    }else if(address.postCode == ''){
                        error = true;
                        store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                            type: 'danger',
                            message: 'Veuillez entrer le CODE POSTAL',
                            ttl: 5,
                        });
                    }else if(address.city == ''){
                        error = true;
                        store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                            type: 'danger',
                            message: 'Veuillez saisir VILLE',
                            ttl: 5,
                        });
                    }else if(address.firstName == ''){
                        error = true;
                        store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                            type: 'danger',
                            message: 'Please enter PRENOM / NOM BATIMENT',
                            ttl: 5,
                        });
                    }
                });
                if(!error){
                    step.value = 'contact';
                }
            }else{

            }
        }
        const phoneCodesSorted = [...new Map(phoneCodes.map(item =>
            [item.value, item])).values()].sort((a, b)=>{
            return parseInt(a.value.replace(/\D/g, '')) - parseInt(b.value.replace(/\D/g, ''));
        });
        const addAddress = ()=>{
            form.value.addresses.push({
                id: '',
                addressType: '',
                firstName: '',
                nom: '',
                address1: '',
                address2: '',
                address3: '',
                postCode: '',
                city: '',
                state: '',
                receiptEmail: '',
                phoneCode: '',
                phoneNumber: '',
                latitude: 48.85560142492883,
                longitude: 2.3491914978706396,
                pente: '',
                surfacetoiture: '',
                materiau: '',
                presenceamiante: '',
                presenceepc: '',
                accesexterieur: '',
                presenceapportlumiere: '',
                etattoiture: '',
                accesinterieur: '',
                hauteurbatiment: '',
                typebatiment: '',
                infoNote: '',
            });
        }
        const addContact = ()=>{
            form.value.contacts.push({
                id: 0,
                id: 1,
                type: '',
                actif: true,
                qualite: '',
                gender: 'M',
                firstName: '',
                address: '',
                profilLinedin: '',
                name: '',
                email: '',
                note: '',
                numGx: '',
                phoneCountryCode1: '+33',
                phoneNumber1: '',
                phoneCountryCode2: '+33',
                phoneNumber2: '',
                acceptSMS: true,
                acceptmarketing: true,
                acceptcourrier: true,
            });
        }
        const removeAddress = (selectedIndex)=>{
            Swal.fire({
                title: 'Etes-vous sûr?',
                text: "Vous ne pourrez pas revenir en arrière !",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#42A71E',
                // cancelButtonColor: '#E8581B',
                cancelButtonColor: 'var(--lcdtOrange)',
                cancelButtonText: 'Annuler',
                confirmButtonText: `Oui, s'il vous plaît.`
            }).then((result) => {
                if (result.isConfirmed) {
                    form.value.addresses = form.value.addresses.filter((item, index)=>{
                        return index != selectedIndex
                    });
                }
            });
        }

        const removeContact = (selectedIndex)=>{
            Swal.fire({
                title: 'Etes-vous sûr?',
                text: "Vous ne pourrez pas revenir en arrière !",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#42A71E',
                // cancelButtonColor: '#E8581B',
                cancelButtonColor: 'var(--lcdtOrange)',
                cancelButtonText: 'Annuler',
                confirmButtonText: `Oui, s'il vous plaît.`
            }).then((result) => {
                if (result.isConfirmed) {
                    form.value.contacts = form.value.contacts.filter((item, index)=>{
                        return index != selectedIndex
                    });
                }
            });
        }
        watch(() => form.value.naf, (curVal, preVal)=>{
            var selectedNaf = customerNafs.filter((item)=>{
                return item.code == curVal;
            })[0];

            if(selectedNaf != undefined){
                form.value.segmentation = selectedNaf.selection;
                form.value.nomNaf = selectedNaf.name;
            }else{
                form.value.segmentation = '';
                form.value.nomNaf = '';
            }
        })
        uniqueEmail.value.status = false;
        const validationUniqueEmail = (event, tableName, contactId)=>{
            axios.post('/check-email-exists', { table: tableName, email:  event.target.value, id: contactId })
            .then((res)=>{
                if( !res.data.success ){
                    uniqueEmail.value.status = false;
                    Object.values(res.data.errors).forEach(item => {
                        uniqueEmail.value.msg = item[0];
                        store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                            type: 'danger',
                            message: item[0],
                            ttl: 5,
                        });
                    });
                }else{
                    uniqueEmail.value.status = true;
                    uniqueEmail.value.msg = '';
                }
            }).catch((error)=>{
                console.log(error);
            })
        }
        const submit = ()=>{
            if(uniqueEmail.value.status == false){
                store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                    type: 'danger',
                    message: uniqueEmail.value.msg == '' ? 'Validating email' : uniqueEmail.value.msg,
                    ttl: 5,
                });
            }
            let error = false;
            form.value.contacts.forEach((contact)=>{
                if(contact.type != '' || contact.firstName != '' || contact.email != '' || contact.name != ''){
                    if(contact.firstName == ''){
                        error = true;
                        store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                            type: 'danger',
                            message: 'Veuillez entrer PRENOM',
                            ttl: 5,
                        });
                    }else if(contact.email == ''){
                        error = true;
                        store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                            type: 'danger',
                            message: 'Veuillez saisir un e-mail',
                            ttl: 5,
                        });
                    }else if(contact.name == ''){
                        error = true;
                        store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                            type: 'danger',
                            message: 'Veuillez entrer NOM',
                            ttl: 5,
                        });
                    }else if(contact.type == ''){
                        error = true;
                        store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                            type: 'danger',
                            message: 'Veuillez sélectionner le type d`adresse',
                            ttl: 5,
                        });
                    }
                }
            })
            if( error == false ){
                store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`, [true, 'Mise à jour du client ...']);
                axios.post('/update-customer', form.value).then((res)=>{
                    if(res.data.success){
                        router.push({ name: 'entite-details', params: { id: route.params.id } });
                    }else{
                        Object.values(res.data.errors).forEach(item => {
                            if(item[0] == 'The siret has already been taken.'){
                                form.value.siretValidation = false;
                            }
                            store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                                type: 'danger',
                                message: item[0],
                                ttl: 5,
                            });
                        });
                    }
                }).catch((errors)=>{
                }).finally(()=>{
                    store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`);
                })
            }
        }
        const formatPhone = (phoneNumber)=>{
            if(phoneNumber == null){
                return ['+33', ''];
            }
            if(phoneNumber.split('|').length == 1){
                return ['+33', phoneNumber];
            }else{
                return phoneNumber.split('|');
            }
        }
        onMounted(()=>{
            axios.post('/get-customer/'+ route.params.id).then((res)=>{
                useGoogleService.value  = res.data.useGoogleService;
                customerOrigins.value  = res.data.customerOrigins;
                customerStatuses.value  = res.data.status;
                customerTaxes.value    = res.data.taxes;
                customerNafs    = res.data.nafs;
                customerCats.value   = res.data.customerCats;
                customerPentes.value   = res.data.customerPentes;
                addressTypes.value    = res.data.addressTypes;
                contactTypes.value    = res.data.contactTypes;
                customerQualites.value    = res.data.customerQualites;
                contactQualites.value    = res.data.contactQualites;
                customerTypeBatiments.value    = res.data.customerTypeBatiments;
                customerMateriaus.value    = res.data.customerMateriaus;
                customerAddresses.value = res.data.customerAddresses;
                customerPaiements.value = res.data.customerPaiements;
                var customer = res.data.customer;
                var phone = formatPhone(customer.telephone);
                customer.phoneCountryCode = phone[0];
                customer.phoneNumber = phone[1];
                for (let index = 0; index < customer.addresses.length; index++) {
                    if(customer.addresses[index].latitude != null){
                        customer.addresses[index].latitude = parseFloat(customer.addresses[index].latitude.replace(/[a-zA-Z()]/g, ""));
                    }else{
                        customer.addresses[index].latitude = 48.85560142492883;
                    }
                    if(customer.addresses[index].longitude != null){
                        customer.addresses[index].longitude = parseFloat(customer.addresses[index].longitude.replace(/[a-zA-Z()]/g, ""));
                    }else{
                        customer.addresses[index].longitude = 2.3491914978706396;
                    }
                    let phone = formatPhone(customer.addresses[index].phoneNumber);
                    customer.addresses[index].phoneCode = phone[0];
                    customer.addresses[index].phoneNumber = phone[1];
                }
                for (let index = 0; index < customer.contacts.length; index++) {
                    var phone = formatPhone(customer.contacts[index].mobile);
                    customer.contacts[index].phoneCountryCode1 = phone[0];
                    customer.contacts[index].phoneNumber1 = phone[1];
                    phone = formatPhone(customer.contacts[index].telephone);
                    customer.contacts[index].phoneCountryCode2 = phone[0];
                    customer.contacts[index].phoneNumber2 = phone[1];
                    if(customer.contacts[index].actif == 1){
                        customer.contacts[index].actif = true;
                    }else{
                        customer.contacts[index].actif = false;
                    }
                }
                if(customer.active)
                    customer.active = true;
                else
                    customer.active = false;
                if(customer.descision)
                    customer.descision = true;
                else
                    customer.descision = false;
                if(customer.litige)
                    customer.litige = true;
                else
                    customer.litige = false;
                customer.siretValidation = true;
                form.value = customer;
                // if(form.value.addresses.length == 0)
                    // addAddress();
                // if(form.value.contacts.length == 0)
                    // addContact();
            }).catch((errors)=>{
            }).finally(()=>{

            })
        })
        return {
            form,
            useGoogleService,
            step,
            customerOrigins,
            customerStatuses,
            customerTaxes,
            customerNafs,
            customerCats,
            customerPentes,
            addressTypes,
            contactTypes,
            customerQualites,
            contactQualites,
            customerMateriaus,
            customerTypeBatiments,
            customerPaiements,
            phoneCodesSorted,
            customerAddresses,
            dateFormat,
            checkSiret,
            addAddress,
            addContact,
            removeAddress,
            removeContact,
            selectNav,
            cancel,
            nextStep,
            updateAddressInfo,
            validationUniqueEmail,
            submit
        }
  },
}
</script>
<style lang="scss" scoped>
  .main-view{
      padding: 0;
      h1{
          padding: 60px 10px 0 80px;
      }
  }
.full-nav{
    margin-top: 28px;
  height: 70px;
  border-top: 1px solid #C3C3C3;
  .full-nav-item{
      cursor: pointer;
      position: relative;
      .icon{
          margin-right: 30px;
      }
      &::after{
          content: "";
          position: absolute;
          bottom: 0;
          width: 100%;
          height: 1px;
          background: #C3C3C3;
      }
      &.active,
      &:hover{
          background: rgba(217, 237, 210, 0.2);
          transition: background .3s ease-in-out;
      }
      &.active::after,
      &:hover::after{
          height: 4px;
          background: #42A71E;
          transition: background .3s ease-in-out;
      }
  }
  .border-right{
      border-right: 1px solid #C3C3C3;
  }
}
.cust-page-content{
  width: 1000px;
  margin-top: 3.125rem;
  .page-section{
    padding: 1.875rem 5rem 1.875rem;
    background: #FFFFFF;
    box-shadow: 0px 0px 4px rgba(80, 80, 80, 0.2);
    border-radius: 4px;
    margin-bottom: 30px;
    input[type="text"]:focus,
    input[type="tel"]:focus,
    input[type="email"]:focus{
        outline: 2px #000000 solid;
        border-color: #000000;
        box-shadow: none;
    }
  }
}
.custom-btn{
    padding: 0 1rem;
    height: 40px;
    font-family: 'Almarai Bold';
    font-style: normal;
    font-weight: 700;
    font-size: 16px;
    line-height: 140%;
    border-radius: 4px;
    text-align: center;
    border: 1px solid #47454B;
    cursor: pointer;
}
.btn-cancel{
    color: rgba(0, 0, 0, 0.2);
}
.btn-ok{
    background: #A1FA9F;
    color: #3E9A4D;
}
.btn-danger{
    background: rgba(255, 0, 0, 0.1);
    color: #E8581B;
}
.address-map{
    width: 270px;
    height: 170px;
}
</style>