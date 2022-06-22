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
                  <span class="ms-3 font-22 almarai_extrabold_normal_normal">CREATION / EDITION CLIENT</span>
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
                                <div class="col-3">
                                    <p class="m-0 mulish-light font-14 text-gray">N {{ form.id }}</p>
                                </div>
                                <div class="col-9 d-flex px-2">
                                    <div class="col-4">
                                    </div>
                                    <div class="col-4"></div>
                                    <div class="col-4">
                                        <p class="m-0 mulish-light font-14 text-gray text-nowrap">Date Création :</p>
                                        <p class="m-0 mulish-light font-14 text-gray text-nowrap">Date Modification :</p>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex mt-3">
                                <div class="col-7 d-flex">
                                    <div class="col-4">
                                        <div class="form-group">
                                            <label class="mulish-medium font-16 text-nowrap">RAISON SOCIALE *</label>
                                            <input type="text" v-model="form.raisonsociale" placeholder="Raison sociale" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-1"></div>
                                    <div class="col-7">
                                        <div class="form-group">
                                            <label class="mulish-medium font-16 text-nowrap">NOM COMMERCIAL</label>
                                            <input type="text" v-model="form.raisonsociale2" placeholder="Raison2 sociale" class="form-control">
                                        </div>                                        
                                    </div>
                                </div>
                            </div>    
                            <div class="d-flex mt-3">
                                <div class="col-7 d-flex">
                                    <div class="col-8">
                                        <div class="form-group">
                                            <label>SIRET *</label>
                                            <input type="text" v-model="form.siret" class="form-control">
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
                                <div class="col-4">
                                    <select-box v-model="form.customerTax" :options="customerTaxes" :label="'TVA'" :name="'customerTax'"></select-box>
                                </div>
                            </div>                                           
                        </div>
                        <div class="page-section mt-3">
                            <h3 class="m-0 mulish-extrabold font-22">SITUATION</h3>
                            <div class="d-flex">
                                <div class="col-9"></div>
                                <div class="col-3">
                                    <CheckBox v-model="form.litige" class="ms-5" :title="'LITIGE'"></CheckBox>
                                    <CheckBox v-model="form.actif" :checked="true" class="ms-5 mt-1" :title="'ACTIF'"></CheckBox>
                                </div>
                            </div>
                            <div class="d-flex">
                                <div class="col-4 ">
                                    <select-box v-model="form.customerOrigin" 
                                        :options="customerOrigins" 
                                        :name="'customerOrigin'"
                                        :label="'ORIGINE ENTITE'"
                                        ></select-box>
                                </div>
                                <div class="col-1"></div>
                                <div class="col-4">
                                    <select-box v-model="form.customerStatus" 
                                        :options="customerStatuses" 
                                        :name="'customerStatus'"
                                        :label="'STATUT'"
                                        ></select-box>
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
                                        <label>NAF *</label>
                                        <input v-model="form.naf" type="text" class="form-control" v-mask="'####A'">
                                    </div>
                                </div>
                                <div class="col-1"></div>
                                <div class="col-5 form-group">
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
                                        <input type="date" v-model="form.dateCreated" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex mt-3">
                                <div class="col-7">
                                    <div class="form-group">
                                        <select-box v-model="form.customerCat" :options="customerCats" :name="'customerCat'" :label="'CATEGORIE JURIDIQUE'"></select-box>
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
                                        <input type="text" placeholder="Adresse1" v-model="address.address1" class="form-control">
                                    </div>
                                </div>
                                <div class="col-6 ps-3">
                                    <div class="form-group">
                                        <label class="text-nowrap">ADRESSE 3 (Lieu-dit, bâtiment, boîte postale…)</label>
                                        <input type="text" placeholder="Adresse1" v-model="address.address3" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex mt-3">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label class="text-nowrap">ADRESSE 2 (ZI, ZA…)</label>
                                        <input type="text" v-model="address.address2" placeholder="Address1" class="form-control">
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
                                    <CheckBox v-model="contact.actif" :checked="true" :title="'ACTIF'"></CheckBox>
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
                                                { value: 'M', display: 'M' },
                                                { value: 'Mme', display: 'Mme' },
                                                { value: 'Mlle', display: 'Mlle' },
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
                                        :options="customerQualites" 
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
                                        :options="[
                                            { value: 'M', display: 'M' },
                                            { value: 'Mme', display: 'Mme' },
                                            { value: 'Mlle', display: 'Mlle' },
                                        ]" 
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
                                        <label class="mulish-medium font-16">EMAIL</label>
                                        <input type="text" v-model="contact.email" placeholder="email" class="form-control">
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
                                            <CheckBox v-model="contact.acceptSMS" :checked="true" :title="'SMS Marketing'"></CheckBox>
                                        </div>
                                        <div class="col-4">
                                            <CheckBox v-model="contact.acceptmarketing" :checked="true" :title="'Email Marketing'"></CheckBox>
                                        </div>
                                        <div class="col-4">
                                            <CheckBox v-model="contact.acceptcourrier" :checked="true" :title="'Courrier Marketing'"></CheckBox>
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
                            <button class="custom-btn btn-ok text-uppercase" @click="submit">VALIDATUON</button>
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
import { phoneCountryCode as phoneCodes } from '../../static/PhoneCountryCodes';
import {     
  DISPLAY_LOADER,
  HIDE_LOADER,
  LOADER_MODULE, 
  TOASTER_MESSAGE, 
  TOASTER_MODULE
  } from '../../store/types/types';
  
import axios from 'axios';
import { useStore } from 'vuex';
import { useRouter } from 'vue-router';
import { mask } from 'vue-the-mask';

export default {
    directives: {
        mask
    },
    components:{
        SelectBox,
        CheckBox,
        GoogleMap
    },
    setup() {
        const store = useStore();
        const router = useRouter();
        const step = ref('client-detail');
        // const step = ref('address');
        const customerStatuses  = ref([]);
        const customerOrigins  = ref([]);
        const customerTaxes    = ref([]);
        var customerNafs    = [];
        const customerCats   = ref([]);
        const customerPentes   = ref([]);
        const customerQualites   = ref([]);
        const customerTypeBatiments   = ref([]);
        const customerMateriaus   = ref([]);
        const addressTypes     = ref([]);
        const contactTypes     = ref([]);
        const form = ref({
            id: '',
            raisonsociale: '',
            raisonsociale2: '',
            siret: '',
            numLCDT: '',
            company: '',
            customerOrigin: 0,
            customerStatus: 0,
            segmentation: '',
            customerCat: '',
            naf: '',
            nomNaf: '',
            gender: 'M',
            firstName: '',
            lastName: '',
            lastName: '',
            phoneCountryCode: '+33',
            phoneNumber: '',
            email: '',
            customerTax: 0,
            litige: false,
            actif: true,
            linkedin: '',
            website: '',
            note: '',
            trancheEffectif: '',
            trancheCA: '',
            trancheCommune: '',
            statusEtablissement: '',
            environment: '',
            zpe: '',
            // address tab
            addresses: [{
                addressType: '',
                firstName: '',
                Nom: '',
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
            }],            
        });

        const selectNav = (value)=>{
            step.value = value;
        }
        const cancel = ()=>{

        }
        const nextStep = ()=>{
            if(step.value == 'client-detail'){
                if(form.value.raisonsociale == ''){
                    store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                        type: 'danger',
                        message: 'Please enter RAISON SOCIALE',
                        ttl: 5,
                    });
                }else if(form.value.siret == ''){
                    store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                        type: 'danger',
                        message: 'Please enter SIRET',
                        ttl: 5,
                    });                    
                }else if(form.value.naf == ''){
                    store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                        type: 'danger',
                        message: 'Please enter NAF',
                        ttl: 5,
                    });                    
                }else{
                    step.value = 'address';
                }
            }else if( step.value == 'address' ){
                var flag = false;
                form.value.addresses.forEach(address => {
                    if(address.addressType == ''){
                        flag = true;
                        store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                            type: 'danger',
                            message: 'Please select address type',
                            ttl: 5,
                        });  
                    }else if(address.address1 == ''){
                        flag = true;
                        store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                            type: 'danger',
                            message: 'Please fill in address1',
                            ttl: 5,
                        });                          
                    }else if(address.postCode == ''){
                        flag = true;
                        store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                            type: 'danger',
                            message: 'Please enter CODE POSTAL',
                            ttl: 5,
                        });                          
                    }else if(address.city == ''){
                        flag = true;
                        store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                            type: 'danger',
                            message: 'Please enter CODE VILLE *',
                            ttl: 5,
                        });                                                  
                    }else if(address.firstName == ''){
                        flag = true;
                        store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                            type: 'danger',
                            message: 'Please enter PRENOM / NOM BATIMENT',
                            ttl: 5,
                        });
                    }
                });
                if(!flag){
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
                addressType: '',
                firstName: '',
                Nom: '',
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
                type: '',
                actif: true,
                quantite: '',
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
            form.value.addresses = form.value.addresses.filter((item, index)=>{
                return index != selectedIndex
            });            
        }

        const removeContact = (selectedIndex)=>{
            form.value.contacts = form.value.contacts.filter((item, index)=>{
                return index != selectedIndex
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
        const submit = ()=>{
            store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`, [true, 'Creating a new customer ...']);
            axios.post('/add-customer', form.value).then((res)=>{
                if(res.data.success){
                    router.push({ name: 'LandingPage' });
                }else{
                    Object.values(res.data.errors).forEach(item => {
                        store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                            type: 'danger',
                            message: item[0],
                            ttl: 5,
                        });
                    });
                }
            }).catch((errors)=>{
                console.log(errors);
            }).finally(()=>{
                store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`);
            })
        }
        onMounted(()=>{
            axios.post('/get-list-info-for-customer').then((res)=>{
                customerOrigins.value  = res.data.customerOrigins;
                customerStatuses.value  = res.data.status;
                customerTaxes.value    = res.data.taxes;
                customerNafs    = res.data.nafs;
                customerCats.value   = res.data.customerCats;
                customerPentes.value   = res.data.customerPentes;
                addressTypes.value    = res.data.addressTypes;
                contactTypes.value    = res.data.contactTypes;
                customerQualites.value    = res.data.customerQualites;
                customerTypeBatiments.value    = res.data.customerTypeBatiments;
                customerMateriaus.value    = res.data.customerMateriaus;
                form.value.customerOrigin = 1;
                form.value.customerStatus = 1;
                form.value.customerTax = 3;
            }).catch((errors)=>{
                console.log(errors);
            }).finally(()=>{

            })
        })
        return {
            form,
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
            customerMateriaus,
            customerTypeBatiments,
            phoneCodesSorted,
            addAddress,
            addContact,
            removeAddress,
            removeContact,
            selectNav,
            cancel,
            nextStep,
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