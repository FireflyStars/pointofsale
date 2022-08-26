<template>
  <transition enter-active-class="animate__animated animate__fadeIn">
    <div class="container-fluid h-100 bg-color" id="container">
      <main-header />
      <div class="row d-flex align-content-stretch align-items-stretch flex-row hmax main-view-wrap reports-page" style="z-index:100" >
        <side-bar />
        <div class="col main-view container">
          <div class="d-flex">
            <div class="col-6 d-flex align-items-center">
              <page-title icon="star" :name="'Devis :' + route.params.id" class="almarai_extrabold_normal_normal m-0"/>
              <span class="font-14 text-center almarai-bold p-1 rounded-pill ms-4" :style="'width: 120px; background-color: ' + form.orderStatus.color + ';' + 'color: ' + form.orderStatus.fontcolor">{{ form.orderStatus.name }}</span>
            </div>
            <div class="col-6 d-flex" v-if="devisCreateStep == 'create_devis'">
              <button class="btn btn-save me-3 text-white" v-if="form.orderStatus.type != 'COMMANDE'" @click="updateDevis">Mise à jour</button>
              <button class="btn btn-save me-3 text-white" v-else>VOIR LISTE</button>
              <button class="btn btn-pdf text-white rounded-3" @click="PDFDevis">PDF</button>
            </div>
          </div>
          <ul class="m-0 p-0 breadcrumb mt-3" v-if="breadcrumbs.length">
            <li class="breadcrumb-item almarai-extrabold font-18 cursor-pointer" 
              v-for="(breadcrumb, index) in breadcrumbs" 
              @click="goToStep(index)"
              :key="index">{{ breadcrumb }}</li>
          </ul>
          <div class="choose-customer-panel d-flex mt-3" v-if="devisCreateStep == 'choose_customer'">
            <div class="col-5 bg-white p-3 rounded">
              <h2 class="almarai-extrabold font-22">Détail Client <span @click="addNewCustomer" class="ms-3 almarai-bold font-16 cursor-pointer text-decoration-underline text-custom-success">Nouveau</span></h2>
              <SearchCustomer name="search" @selected="selectedCustomer" :droppos="{top:'auto',right:'auto',bottom:'auto',left:'0',transformOrigin:'top right'}" label="Rechercher client" hint="disabled till 2021-09-10" ></SearchCustomer>
            </div>
          </div>
          <div class="choose-address-panel mt-3" v-if="devisCreateStep == 'choose_address'">
            <div class="col-5 p-3 bg-white rounded">
              <div class="d-flex">
                <div class="col-6">
                  <h2 class="almarai-extrabold font-22">{{ form.customer.company }}</h2>
                  <p class="text-gray font-16 almarai-bold">{{ form.customer.raisonsocial }}</p>
                </div>
                <div class="col-6 d-flex align-items-center justify-content-end">
                  <p @click="chooseOtherCustomer" class="text-custom-success font-16 almarai-bold text-decoration-underline cursor-pointer">Autre client</p>
                </div>
              </div>
              <div class="d-flex mt-3">
                <div class="col-4">
                  <label for="" class="text-gray font-16 almarai-bold">GROUPE</label>
                  <p class="font-16 almarai-bold">{{ form.customer.group }}</p>
                </div>
                <div class="col-4">
                  <label for="" class="text-gray font-16 almarai-bold">CONTACT</label>
                  <p class="font-16 almarai-bold">{{ form.customer.contact }}</p>
                </div>
                <div class="col-4">
                  <label for="" class="text-gray font-16 almarai-bold">TELEPHONE</label>
                  <p class="font-16 almarai-bold">{{ form.customer.telephone }}</p>
                </div>
              </div>
              <div class="d-flex mt-3">
                <div class="col-4">
                  <label for="" class="text-gray font-16 almarai-bold">TVA</label>
                  <p class="font-16 almarai-bold">{{ form.customer.tax }}</p>
                </div>
                <div class="col-4">
                  <label for="" class="text-gray font-16 almarai-bold">NAF</label>
                  <p class="font-16 almarai-bold">{{ form.customer.naf }}</p>
                </div>
                <div class="col-4">
                  <label for="" class="text-gray font-16 almarai-bold">SIRET</label>
                  <p class="font-16 almarai-bold">{{ form.customer.siret }}</p>
                </div>
              </div>
            </div>
            <div class="col-5 p-3 mt-3 rounded bg-white">
              <h2 class="almarai-extrabold font-22">Choix adresse Chantier <span @click="addNewAddress" class="ms-3 almarai-bold font-16 cursor-pointer text-decoration-underline text-custom-success">Nouvelle adresse</span></h2>
              <div class="mt-3 customer-addresses">
                <div class="px-4 py-3 bg-gray mt-2 address-item rounded cursor-pointer" 
                  @click="chooseCustomerAddress(address)" v-for="(address, index) in customerAddresses" :key="index">
                  <div class="d-flex">
                    <div class="col-7">
                      <h3 class="almarai-bold font-16">{{ address.name }}</h3>
                    </div>
                    <div class="col-5">
                      <p class="almarai-bold font-16 text-gray">{{ address.addressType }}</p>
                    </div>
                  </div>
                  <div class="d-flex">
                      <div class="col-4 almarai-bold text-gray">{{ address.address1 }}</div>
                      <div class="col-4 almarai-bold text-gray">{{ address.address2 }}</div>
                      <div class="col-4 almarai-bold text-gray">{{ address.postcode }} {{ address.city }}</div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="devis-panel d-flex" v-if="devisCreateStep == 'create_devis'">
            <div class="left-panel">
              <div class="customer-section px-3 py-2 d-flex bg-white">
                <div class="col-7 d-flex">
                  <div class="customer-pic rounded-circle bg-primary">
                      
                  </div>
                  <div class="ms-3 customer-name d-flex flex-wrap">
                    <div class="small-title w-100">
                      Nom du client
                    </div>
                    <div class="mulish-extrabold font-16 w-100">
                      {{ form.customer.contact }}
                    </div>
                  </div>
                  <div class="ms-5 customer-edit d-flex align-items-center">
                    <!-- <span class="edit-icon me-3"></span> -->
                    <!-- <span class="call-external"></span> -->
                  </div>
                </div>
                <div class="col-5 me-3 price-section d-flex align-items-end">
                  <span class="col-4 fw-bold mulish-extra-bold font-16 text-black text-center text-nowrap">
                    {{ (form.totalHoursForInstall + form.totalHoursForSecurity + form.totalHoursForPrestation) }} hr
                  </span>
                  <span class="col-4 fw-bold mulish-extra-bold font-16 text-black text-center text-nowrap">
                    {{ (form.totalPriceForInstall + form.totalPriceForSecurity + form.totalPriceForPrestation).toFixed(2) }} €
                  </span>
                  <span class="col-4 fw-bold mulish-extra-bold font-16 text-black text-center text-nowrap">
                    {{ (form.totalPriceForInstall + form.totalPriceForSecurity + form.totalPriceForPrestation).toFixed(2) }} €
                  </span>
                </div>
              </div>
              <div class="zone-section" v-for="(zone, zoneIndex) in form.zones" :key="zoneIndex">
                <div class="ged-section bg-white px-3 py-2">
                  <div class="zone-header d-flex align-items-center">
                    <span class="home-icon"></span>
                    <div class="zone-name ms-2">
                      <input type="text" @blur="zone.edit = false" v-if="zone.edit" class="form-control form-control-sm" v-model="zone.name">
                      <span class="mulish-extrabold font-18 text-black" @dblclick="zone.edit = true" v-else>{{ zone.name }}</span>
                    </div>
                    <div v-if="zoneIndex == 0" @click="addZone(zoneIndex)" class="add-btn ms-3 d-flex align-items-center mulish-semibold font-14 custom-text-danger cursor-pointer">
                      <span class="plus-icon me-2"></span> AJOUTER
                    </div>
                    <div @click="removeZone(zoneIndex)" class="remove-btn ms-3 d-flex align-items-center mulish-semibold font-14 custom-text-danger cursor-pointer">
                      <span class="cancel-icon me-2"></span> RETIRER
                    </div>
                  </div>
                  <div class="d-flex px-4 mt-4">
                    <div class="col-6">
                      <div class="roof-access d-flex align-items-end mulish-semibold font-16 custom-text-danger">
                        <span class="roof-icon me-2"></span> Accès toiture
                      </div>
                      <div class="d-flex mt-3 ms-4">
                        <div class="col-6">
                          <SelectBox :label="''" 
                              v-model="zone.roofAccess" 
                              :options="roofAccesses"
                              :name="'roofAccess'+zoneIndex"
                              :classnames="'col-12'"
                              :placeholder="'Accessible'"
                          />
                        </div>
                        <div class="col-1"></div>
                        <div class="col-5">
                          <input type="text" v-model="zone.height" class="form-control" placeholder="Hauteur m">
                        </div>
                      </div>
                    </div>
                    <div class="col-6 ps-2 d-flex">
                      <div class="col-5 pe-2">
                        <p class="m-0 almarai-bold font-14 text-gray">Adresse du chantier</p>
                        <p class="m-0 almarai-light font-14">{{ form.address.address1 }} {{ form.address.postCode }} {{ form.address.city }}</p>
                      </div>
                      <div class="col-7">
                        <GoogleMap v-model:latitude="form.address.lat" v-model:longitude="form.address.lon"></GoogleMap>
                      </div>
                    </div>
                  </div>
                  <div class="d-flex px-4 mt-4 flex-wrap">
                    <div class="col-6 mb-3" v-for="(gedCat, catIndex) in zone.gedCats" :key="catIndex">
                      <div class="ged-cat-header d-flex align-items-end mulish-semibold font-16 custom-text-danger">
                        <span class="camera-icon me-2"></span> {{ gedCat[0].name }} 
                        <div class="add-btn ms-3 d-flex align-items-center mulish-semibold font-14 custom-text-danger cursor-pointer"
                        @click="addFileToGed(zoneIndex, gedCat[0].id)">
                          <span class="plus-icon me-2"></span> AJOUTER
                        </div>
                      </div>
                      <div class="ged-cat-content mt-3 mb-3 d-flex flex-wrap">
                        <div class="img ms-2" v-for="(gedDetail, index) in gedCat[0].items" :key="index">
                          <div class="rounded border border-1 ged-image"
                            :style="{ 'background-image': `url(${gedDetail.url != '' ? gedDetail.url : gedDetail.base64data})`}"
                            v-if="gedDetail.type == 'png'">
                            <div class="w-100 h-100 image-overlayer d-flex justify-content-around align-items-center">
                              <span class="eye-icon" @click="viewGedMedia(gedDetail.url != '' ? gedDetail.url : gedDetail.base64data, gedDetail.type)"></span>
                              <span class="cancel-icon" @click="removeImage(zoneIndex, gedCat[0].id,index)"></span>
                            </div>
                          </div>
                          <div class="rounded border border-1 ged-image"
                            v-else-if="gedDetail.type == 'm4a'">
                            <div class="w-100 h-100 d-flex justify-content-around align-items-center">
                              <span class="music-play-icon" @click="viewGedMedia(gedDetail.url != '' ? gedDetail.url : gedDetail.base64data, gedDetail.type)"></span>
                            </div>
                          </div>
                          <div class="rounded border border-1 ged-image"
                            v-else>
                            <div class="w-100 h-100 d-flex justify-content-around align-items-center">
                              <span class="play-icon" @click="viewGedMedia(gedDetail.url != '' ? gedDetail.url : gedDetail.base64data, gedDetail.type)"></span>
                            </div>
                          </div>
                        </div>
                      </div>
                      <!-- <div v-if="gedCat[0].name == 'Vue exterieur'" class="get-cat-footer almarai-light font-14 mb-3">
                        Attention grosse poutre en haut
                      </div> -->
                    </div>
                  </div>
                </div>

                <div class="ouvrage-section installation-ouvrages bg-white px-4 py-3 mt-2 mb-2" :id="'zone-'+zoneIndex+'-installation-ouvrages'">
                  <div class="ouvrage-header d-flex">
                    <div class="col-7">
                      <div class="col-5 d-flex align-items-center cursor-pointer toggle-btn" @click="togglePanel('zone-'+zoneIndex+'-installation-ouvrages')">
                        <span class="installation-icon me-2"></span> 
                        <input type="text" @blur="zone.installOuvrage.edit = false" v-if="zone.installOuvrage.edit" class="form-control form-control-sm" v-model="zone.installOuvrage.name">
                        <span v-else class="me-4" @dblclick="zone.installOuvrage.edit = true">{{ zone.installOuvrage.name }}</span>
                        <span class="arrow-icon ms-auto"></span>
                      </div>
                      <div class="add-btn ms-5 ps-3 d-flex align-items-center mulish-semibold font-14 custom-text-danger cursor-pointer" 
                        @click="openInstallationModal(zoneIndex)">
                        <span class="plus-icon me-2"></span> AJOUTER UN OUVRAGE
                      </div>
                    </div>
                    <div class="col-5 d-flex align-items-center">
                      <div class="col-4 fw-bold mulish-extra-bold font-16 text-black text-center">{{ zone.installOuvrage.totalHour }} hr</div>
                      <div class="col-4 fw-bold mulish-extra-bold font-16 text-black text-center text-nowrap">{{ zone.installOuvrage.sumUnitPrice.toFixed(2) }} €</div>
                      <div class="col-4 fw-bold mulish-extra-bold font-16 text-black text-center text-nowrap">{{ zone.installOuvrage.totalPrice.toFixed(2) }} €</div>
                    </div>
                  </div>
                  <div class="ouvrage-body ps-3 mt-3">
                    <div class="ouvrage-list">
                      <div class="ouvrage-list-header">
                        <div class="d-flex">
                          <div class="col-4"></div>
                          <div class="col-8 d-flex">
                            <div class="col-2 d-flex align-items-center justify-content-center border border-1 fw-bold">Qte</div>
                            <div class="col-2 d-flex align-items-center justify-content-center border border-1 fw-bold">Unit ouv</div>
                            <div class="col-2 d-flex align-items-center justify-content-center border border-1 fw-bold">Total hr</div>
                            <div class="col-2 d-flex align-items-center justify-content-center border border-1 fw-bold">Total / Qte</div>
                            <div class="col-2 d-flex align-items-center justify-content-center border border-1 fw-bold">Total</div>
                            <div class="col-2"></div>
                          </div>
                        </div>
                      </div>
                      <div class="ouvrage-item" v-for="(ouvrage, ouvrageIndex) in zone.installOuvrage.ouvrages" :key="ouvrageIndex">
                        <div class="d-flex ouvrage-header">
                          <div class="col-4">
                            <a class="ouvrage custom-option d-flex align-items-center px-0 text-black" :class="{ 'active': ouvrageIndex == (zone.installOuvrage.ouvrages.length - 1)}" :data-id="'zone-' + zoneIndex +'-installation-ouvrage-'+ouvrageIndex" href="javascript:;"
                                @click="activeOuvrage"
                              >
                                <span class="option-icon me-2"><span class="option-icon-dot"></span></span> {{ ouvrage.name }}
                              </a>
                          </div>
                          <div class="col-8 d-flex">
                            <div class="col-2 d-flex align-items-center justify-content-center border border-1">
                              <input type="text" @keyup="updateAllValues" v-model.number="ouvrage.qty" class="w-100 form-control form-control-sm custom-text-danger">
                            </div>
                            <div class="col-2 d-flex align-items-center justify-content-center border border-1">
                              <select class="form-control form-control-sm custom-text-danger" v-model="ouvrage.unit">
                                <option v-for="(unit, unitIndex) in units" :value="unit.value" :key="unitIndex">{{ unit.display }}</option>
                              </select>
                            </div>
                            <div class="col-2 d-flex align-items-center justify-content-center border border-1">
                              {{ ouvrage.totalHour }} hr
                            </div>
                            <div class="col-2 d-flex align-items-center justify-content-center border border-1">
                              {{ (ouvrage.total / ouvrage.qty).toFixed(2) }}€
                            </div>
                            <div class="col-2 d-flex align-items-center justify-content-center border border-1">
                              {{ ouvrage.total.toFixed(2) }}€
                            </div>
                            <div class="col-2 d-flex align-items-center justify-content-center">
                              <svg class="cursor-pointer" @click="removeOuvrage(zoneIndex, 1, ouvrageIndex)" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                              <path d="M17 6H22V8H20V21C20 21.2652 19.8946 21.5196 19.7071 21.7071C19.5196 21.8946 19.2652 22 19 22H5C4.73478 22 4.48043 21.8946 4.29289 21.7071C4.10536 21.5196 4 21.2652 4 21V8H2V6H7V3C7 2.73478 7.10536 2.48043 7.29289 2.29289C7.48043 2.10536 7.73478 2 8 2H16C16.2652 2 16.5196 2.10536 16.7071 2.29289C16.8946 2.48043 17 2.73478 17 3V6ZM18 8H6V20H18V8ZM13.414 14L15.182 15.768L13.768 17.182L12 15.414L10.232 17.182L8.818 15.768L10.586 14L8.818 12.232L10.232 10.818L12 12.586L13.768 10.818L15.182 12.232L13.414 14ZM9 4V6H15V4H9Z" fill="black"/>
                              </svg>
                            </div>
                          </div>
                        </div>
                        <div class="tab-content">
                          <div class="tab-pane ps-3" :class="{ 'active': ouvrageIndex == 0}" :id="'zone-'+ zoneIndex +'-installation-ouvrage-'+ouvrageIndex">
                            <h3 class="mulish-light fw-light custom-text-danger font-14">TEXTE COMMENTAIRE TECHNIQUE</h3>
                            <!-- ouvrage description -->
                            <ul class="ps-3" v-if="ouvrage.textchargeaffaire !=''">
                              <li class="mulish-regular font-10">
                                {{ ouvrage.textchargeaffaire }}
                              </li>
                            </ul>
                            <h3 class="mt-3 mulish-light fw-light text-custom-success font-14">TEXTE POUR CLIENTS</h3>
                            <ul class="ps-3">
                              <li class="mulish-regular font-10 custom-text-danger">
                                <input style="text-align: left !important" type="text" v-model="ouvrage.customerText" class="w-100 form-control form-control-sm custom-text-danger">
                              </li>
                            </ul>
                            <!-- Ouvrage Task -->
                            <div class="ouvrage-task" v-for="(task, taskIndex) in ouvrage.tasks" :key="taskIndex">
                              <div class="task-header d-flex align-items-center custom-option cursor-pointer" :class="{ 'active': taskIndex == 0}" :data-id="'zone-'+ zoneIndex +'-installation-ouvrage-'+ouvrageIndex+'-task-'+taskIndex" @click="activeOuvrageTask">
                                <span class="option-icon me-2"><span class="option-icon-dot"></span></span> {{ task.name }}
                              </div>
                              <div class="task-body ps-3" :class="{ 'show': taskIndex == 0}" :id="'zone-'+ zoneIndex +'-installation-ouvrage-'+ouvrageIndex+'-task-'+taskIndex">
                                <h3 class="mulish-light fw-light custom-text-danger font-14">TEXTE COMMENTAIRE TECHNIQUE</h3>
                                <!-- task description -->
                                <ul class="ps-3" v-if="task.textchargeaffaire != ''">
                                  <li class="mulish-regular font-10">
                                    {{ task.textchargeaffaire }}
                                  </li>
                                </ul>
                                <h3 class="mt-3 mulish-light fw-light text-custom-success font-14">TEXTE POUR CLIENTS</h3>
                                <ul class="ps-3">
                                  <li class="mulish-regular font-10 custom-text-danger">
                                    <textarea style="text-align: left !important" v-model="task.customerText" class="w-100 form-control custom-text-danger" rows="2"></textarea>
                                  </li>
                                </ul>
                                <div class="w-100 ps-3">
                                  <table class="table w-100 details-table m-0">
                                    <thead>
                                      <tr>
                                        <td></td>
                                        <td valign="middle" class="text-center fw-bold">Qte ouvrage</td>
                                        <td valign="middle" class="text-center fw-bold">Qte</td>
                                        <td valign="middle" class="text-center fw-bold">Unité</td>
                                        <td valign="middle" class="text-center fw-bold">Hr</td>
                                        <td valign="middle" class="text-center fw-bold">Prix</td>
                                        <td valign="middle" class="text-center fw-bold">Marge</td>
                                        <td valign="middle" class="text-center fw-bold">Total</td>
                                        <td valign="middle" class="text-center fw-bold">Taxe</td>
                                        <td></td>
                                      </tr>
                                    </thead>                                    
                                    <tbody>
                                      <tr v-for="(detail, detailIndex) in task.details" :key="detailIndex">
                                        <td valign="middle">
                                          <div class="custom-option d-flex align-items-center">
                                            <span 
                                              :class="{ 'clock-icon': detail.type == 'MO' || detail.type == 'INTERIM'|| detail.type == 'Labor',  
                                                'book-icon' : detail.type == 'PRODUIT' || detail.type == 'COMMANDE FOURNISSEUR'
                                              }"
                                            ></span> {{ detail.name }}
                                          </div>
                                        </td>
                                        <td valign="middle" class="text-center">{{ detail.qtyOuvrage }}</td>
                                        <td valign="middle" class="text-center">
                                          <input v-if="detail.type != 'MO'" @keyup="updateAllValues" type="text" v-model.number="detail.qty" class="w-100 form-control form-control-sm custom-text-danger">
                                          <span v-else>{{ detail.qty }}</span>
                                        </td>
                                        <td valign="middle" text="text-center">{{ detail.unit }}</td>
                                        <td valign="middle" v-if="detail.type == 'MO' || detail.type == 'Labor'">
                                          <div class="d-flex align-items-center">
                                            <input @keyup="updateAllValues" type="text" v-model.number="detail.numberH" class="w-100 form-control form-control-sm custom-text-danger">hr
                                          </div>
                                        </td>                                      
                                        <td valign="middle" class="text-center supplier" v-else-if="detail.type == 'COMMANDE FOURNISSEUR'">
                                          <svg width="20" height="25" viewBox="0 0 20 25" fill="none" xmlns="http://www.w3.org/2000/svg" @click="openPdfModal(detail.base64)" class="cursor-pointer">
                                            <path d="M19.9981 5.41835C19.9972 5.4093 19.9956 5.40049 19.994 5.39168C19.9935 5.38875 19.9932 5.38571 19.9926 5.38278C19.9905 5.37226 19.9877 5.36199 19.9847 5.35186C19.9843 5.35064 19.9841 5.34932 19.9837 5.34809C19.9804 5.33767 19.9766 5.3275 19.9724 5.31751C19.972 5.31649 19.9717 5.31541 19.9713 5.31438C19.9672 5.30484 19.9626 5.2956 19.9578 5.2865C19.957 5.28498 19.9563 5.28341 19.9555 5.2819C19.9509 5.27343 19.9457 5.26531 19.9405 5.25724C19.9391 5.25519 19.9379 5.25303 19.9366 5.25103C19.9314 5.24349 19.9258 5.23635 19.92 5.22921C19.9182 5.22686 19.9165 5.22441 19.9146 5.22211C19.9075 5.21365 19.8998 5.20558 19.892 5.19775C19.8913 5.19702 19.8907 5.19623 19.8899 5.1955L14.6769 0.107436C14.676 0.106556 14.675 0.105822 14.674 0.104941C14.6662 0.097456 14.6581 0.0902153 14.6497 0.0834149C14.6469 0.0812133 14.644 0.0793542 14.6412 0.0772505C14.6343 0.0720157 14.6274 0.0667808 14.6201 0.0620352C14.6176 0.0603718 14.6149 0.059002 14.6123 0.0573875C14.6045 0.052544 14.5967 0.0477495 14.5885 0.0434932C14.5867 0.0425636 14.5848 0.0417808 14.583 0.0409002C14.5739 0.0363503 14.5647 0.0319472 14.5552 0.0280822C14.554 0.027593 14.5527 0.0272505 14.5515 0.0267613C14.5414 0.0227984 14.5311 0.0190802 14.5206 0.0159491C14.5192 0.0155577 14.5179 0.0153131 14.5165 0.0149217C14.5062 0.0119863 14.4957 0.0092955 14.4851 0.00719178C14.4819 0.00655577 14.4786 0.00631115 14.4754 0.00577299C14.4666 0.00425636 14.4577 0.00273973 14.4486 0.0018591C14.4361 0.000636008 14.4236 0 14.411 0H1.97995C0.88822 0 0 0.866928 0 1.93249V23.0675C0 24.1331 0.88822 25 1.97995 25H18.02C19.1118 25 20 24.1331 20 23.0675V5.45499C20 5.44271 19.9993 5.43048 19.9981 5.41835ZM14.787 1.25274L18.7165 5.08806H16.015C15.3379 5.08806 14.787 4.55039 14.787 3.88943V1.25274ZM18.02 24.2661H1.97995C1.30281 24.2661 0.75188 23.7285 0.75188 23.0675V1.93249C0.75188 1.27153 1.30281 0.733855 1.97995 0.733855H14.0351V3.88943C14.0351 4.95499 14.9233 5.82192 16.015 5.82192H19.2481V23.0675C19.2481 23.7285 18.6972 24.2661 18.02 24.2661Z" fill="black"/>
                                          </svg>
                                        </td>
                                        <td valign="middle" class="text-center" v-else></td>
                                        <td valign="middle">
                                          <div class="d-flex align-items-center">
                                            <input @keyup="updateAllValues" type="text" v-model="detail.unitPrice" class="w-100 form-control form-control-sm custom-text-danger">€
                                          </div>
                                        </td>
                                        <td valign="middle" v-if="detail.type == 'MO' || detail.type == 'Labor'">
                                        </td>
                                        <td v-else style="min-width: 40px" valign="middle">
                                          <div class="d-flex align-items-center">
                                            <input @keyup="updateAllValues" type="text" v-model.number="detail.marge" class="w-100 form-control form-control-sm custom-text-danger">%
                                          </div>                                        
                                        </td>
                                        <td valign="middle">{{ detail.totalPrice }}€</td>
                                        <td valign="middle">
                                          <select style="min-width: 40px" class="w-100 form-control form-control-sm custom-text-danger" v-model="detail.tax">
                                            <option :value="tax.value" v-for="(tax, taxIndex) in taxes" :key="taxIndex">{{ tax.display }} %</option>
                                          </select>                                          
                                        </td>
                                        <td valign="middle">
                                          <svg class="cursor-pointer" @click="removeOuvrageDetail(zoneIndex, 1, ouvrageIndex, taskIndex, detailIndex)" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                          <path d="M17 6H22V8H20V21C20 21.2652 19.8946 21.5196 19.7071 21.7071C19.5196 21.8946 19.2652 22 19 22H5C4.73478 22 4.48043 21.8946 4.29289 21.7071C4.10536 21.5196 4 21.2652 4 21V8H2V6H7V3C7 2.73478 7.10536 2.48043 7.29289 2.29289C7.48043 2.10536 7.73478 2 8 2H16C16.2652 2 16.5196 2.10536 16.7071 2.29289C16.8946 2.48043 17 2.73478 17 3V6ZM18 8H6V20H18V8ZM13.414 14L15.182 15.768L13.768 17.182L12 15.414L10.232 17.182L8.818 15.768L10.586 14L8.818 12.232L10.232 10.818L12 12.586L13.768 10.818L15.182 12.232L13.414 14ZM9 4V6H15V4H9Z" fill="black"/>
                                          </svg>
                                        </td>
                                      </tr>
                                    </tbody>
                                  </table>
                                </div>
                                <div class="btns d-flex mt-4">
                                  <div class="col-5">
                                    <div class="add-btn ms-3 mt-3 d-flex align-items-center mulish-semibold font-14 custom-text-danger cursor-pointer" @click="openProductModal(zoneIndex, 1,ouvrageIndex, taskIndex)">
                                      <span class="plus-icon me-2"></span> AJOUTER UN PRODUIT
                                    </div>
                                    <div class="add-btn ms-3 mt-3 d-flex align-items-center mulish-semibold font-14 custom-text-danger cursor-pointer" @click="openTaskModal(zoneIndex, 1, ouvrageIndex)">
                                      <span class="plus-icon me-2"></span> AJOUTER ACTION
                                    </div>
                                    <div class="add-btn ms-3 mt-3 d-flex align-items-center mulish-semibold font-14 custom-text-danger cursor-pointer" @click="openSupplierModal(zoneIndex, 1, ouvrageIndex, taskIndex)">
                                      <span class="plus-icon me-2"></span> COMMANDE FOURNISSEUR
                                    </div>
                                  </div>
                                  <div class="col-5">
                                    <div class="add-btn ms-3 mt-3 d-flex align-items-center mulish-semibold font-14 custom-text-danger cursor-pointer" @click="openLaborModal(zoneIndex, 1, ouvrageIndex, taskIndex)">
                                      <span class="plus-icon me-2"></span> AJOUTER UN MAIN D’ OEUVRES
                                    </div>
                                    <div class="add-btn ms-3 mt-3 d-flex align-items-center mulish-semibold font-14 custom-text-danger cursor-pointer" @click="openInterimModal(zoneIndex, 1, ouvrageIndex, taskIndex)">
                                      <span class="plus-icon me-2"></span> AJOUTER  INTERIM
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                            <div v-if="ouvrage.tasks.length == 0" class="add-btn ms-3 mt-3 d-flex align-items-center mulish-semibold font-14 custom-text-danger cursor-pointer" @click="openTaskModal(zoneIndex, 1, ouvrageIndex)">
                              <span class="plus-icon me-2"></span> AJOUTER ACTION
                            </div>                                                
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="ouvrage-section security-ouvrages bg-white px-4 py-3 mt-2 mb-2" :id="'zone-'+zoneIndex+'-security-ouvrages'">
                  <div class="ouvrage-header d-flex">
                    <div class="col-7">
                      <div class="col-5 d-flex align-items-center cursor-pointer toggle-btn" @click="togglePanel('zone-'+zoneIndex+'-security-ouvrages')">
                        <span class="securite-icon me-2"></span> 
                        <input type="text" @blur="zone.securityOuvrage.edit = false" v-if="zone.securityOuvrage.edit" class="form-control form-control-sm" v-model="zone.securityOuvrage.name">
                        <span v-else class="me-4" @dblclick="zone.securityOuvrage.edit = true">{{ zone.securityOuvrage.name }}</span>
                        <span class="arrow-icon ms-auto"></span>
                      </div>
                      <div class="add-btn ms-5 ps-3 d-flex align-items-center mulish-semibold font-14 custom-text-danger cursor-pointer" 
                        @click="openSecuriteModal(zoneIndex)">
                        <span class="plus-icon me-2"></span> AJOUTER UN OUVRAGE
                      </div>
                    </div>
                    <div class="col-5 d-flex align-items-center">
                      <div class="col-4 fw-bold mulish-extra-bold font-16 text-black text-center">{{ zone.securityOuvrage.totalHour }} hr</div>
                      <div class="col-4 fw-bold mulish-extra-bold font-16 text-black text-center text-nowrap">{{ zone.securityOuvrage.sumUnitPrice.toFixed(2) }} €</div>
                      <div class="col-4 fw-bold mulish-extra-bold font-16 text-black text-center text-nowrap">{{ zone.securityOuvrage.totalPrice.toFixed(2) }} €</div>
                    </div>
                  </div>
                  <div class="ouvrage-body ps-3 mt-3">
                    <div class="ouvrage-list">
                      <div class="ouvrage-list-header">
                        <div class="d-flex">
                          <div class="col-4"></div>
                          <div class="col-8 d-flex">
                            <div class="col-2 d-flex align-items-center justify-content-center border border-1 fw-bold">Qte</div>
                            <div class="col-2 d-flex align-items-center justify-content-center border border-1 fw-bold">Unit ouv</div>
                            <div class="col-2 d-flex align-items-center justify-content-center border border-1 fw-bold">Total hr</div>
                            <div class="col-2 d-flex align-items-center justify-content-center border border-1 fw-bold">Total / Qte</div>
                            <div class="col-2 d-flex align-items-center justify-content-center border border-1 fw-bold">Total</div>
                            <div class="col-2"></div>
                          </div>
                        </div>
                      </div>                      
                      <div class="ouvrage-item" v-for="(ouvrage, ouvrageIndex) in zone.securityOuvrage.ouvrages" :key="ouvrageIndex">
                        <div class="d-flex ouvrage-header">
                          <div class="col-4">
                            <a class="ouvrage custom-option d-flex align-items-center px-0 text-black" :class="{ 'active': ouvrageIndex == (zone.securityOuvrage.ouvrages.length -1) }" :data-id="'zone-' + zoneIndex +'-security-ouvrage-'+ouvrageIndex" href="javascript:;"
                                @click="activeOuvrage"
                              >
                                <span class="option-icon me-2"><span class="option-icon-dot"></span></span> {{ ouvrage.name }}
                              </a>
                          </div>
                          <div class="col-8 d-flex">
                            <div class="col-2 d-flex align-items-center justify-content-center border border-1">
                              <input @keyup="updateAllValues" type="text" v-model.number="ouvrage.qty" class="w-100 form-control form-control-sm custom-text-danger">
                            </div>
                            <div class="col-2 d-flex align-items-center justify-content-center border border-1">
                              <select class="form-control form-control-sm custom-text-danger" v-model="ouvrage.unit">
                                <option v-for="(unit, unitIndex) in units" :value="unit.value" :key="unitIndex">{{ unit.display }}</option>
                              </select>
                            </div>
                            <div class="col-2 d-flex align-items-center justify-content-center border border-1">
                              {{ ouvrage.totalHour }} hr
                            </div>
                            <div class="col-2 d-flex align-items-center justify-content-center border border-1">
                              {{ (ouvrage.total / ouvrage.qty).toFixed(2) }}€
                            </div>
                            <div class="col-2 d-flex align-items-center justify-content-center border border-1">
                              {{ ouvrage.total.toFixed(2) }}€
                            </div>
                            <div class="col-2 d-flex align-items-center justify-content-center">
                              <svg class="cursor-pointer" @click="removeOuvrage(zoneIndex, 2, ouvrageIndex)" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                              <path d="M17 6H22V8H20V21C20 21.2652 19.8946 21.5196 19.7071 21.7071C19.5196 21.8946 19.2652 22 19 22H5C4.73478 22 4.48043 21.8946 4.29289 21.7071C4.10536 21.5196 4 21.2652 4 21V8H2V6H7V3C7 2.73478 7.10536 2.48043 7.29289 2.29289C7.48043 2.10536 7.73478 2 8 2H16C16.2652 2 16.5196 2.10536 16.7071 2.29289C16.8946 2.48043 17 2.73478 17 3V6ZM18 8H6V20H18V8ZM13.414 14L15.182 15.768L13.768 17.182L12 15.414L10.232 17.182L8.818 15.768L10.586 14L8.818 12.232L10.232 10.818L12 12.586L13.768 10.818L15.182 12.232L13.414 14ZM9 4V6H15V4H9Z" fill="black"/>
                              </svg>
                            </div>
                          </div>
                        </div>
                        <div class="tab-content">
                          <div class="tab-pane ps-3" :class="{ 'active': ouvrageIndex == 0}" :id="'zone-'+ zoneIndex +'-security-ouvrage-'+ouvrageIndex">
                            <h3 class="mulish-light fw-light custom-text-danger font-14">TEXTE COMMENTAIRE TECHNIQUE</h3>
                            <!-- ouvrage description -->
                            <ul class="ps-3" v-if="ouvrage.textchargeaffaire !=''">
                              <li class="mulish-regular font-10">
                                {{ ouvrage.textchargeaffaire }}
                              </li>
                            </ul>
                            <h3 class="mt-3 mulish-light fw-light text-custom-success font-14">TEXTE POUR CLIENTS</h3>
                            <ul class="ps-3">
                              <li class="mulish-regular font-10 custom-text-danger">
                                <input style="text-align: left !important" type="text" v-model="ouvrage.customerText" class="w-100 form-control form-control-sm custom-text-danger">
                              </li>
                            </ul>
                            <!-- Ouvrage Task -->
                            <div class="ouvrage-task" v-for="(task, taskIndex) in ouvrage.tasks" :key="taskIndex">
                              <div class="task-header d-flex align-items-center custom-option cursor-pointer" :class="{ 'active': taskIndex == 0}" :data-id="'zone-'+ zoneIndex +'-security-ouvrage-'+ouvrageIndex+'-task-'+taskIndex" @click="activeOuvrageTask">
                                <span class="option-icon me-2"><span class="option-icon-dot"></span></span> {{ task.name }}
                              </div>
                              <div class="task-body ps-3" :class="{ 'show': taskIndex == 0}" :id="'zone-'+ zoneIndex +'-security-ouvrage-'+ouvrageIndex+'-task-'+taskIndex">
                                <h3 class="mulish-light fw-light custom-text-danger font-14">TEXTE COMMENTAIRE TECHNIQUE</h3>
                                <!-- task description -->
                                <ul class="ps-3" v-if="task.textchargeaffaire != ''">
                                  <li class="mulish-regular font-10">
                                    {{ task.textchargeaffaire }}
                                  </li>
                                </ul>
                                <h3 class="mt-3 mulish-light fw-light text-custom-success font-14">TEXTE POUR CLIENTS</h3>
                                <ul class="ps-3">
                                  <li class="mulish-regular font-10 custom-text-danger">
                                    <textarea style="text-align: left !important" v-model="task.customerText" class="w-100 form-control custom-text-danger" rows="2"></textarea>
                                  </li>
                                </ul>
                                <div class="w-100 ps-3">
                                  <table class="table w-100 details-table m-0">
                                    <thead>
                                      <tr>
                                        <td></td>
                                        <td valign="middle" class="text-center fw-bold">Qte ouvrage</td>
                                        <td valign="middle" class="text-center fw-bold">Qte</td>
                                        <td valign="middle" class="text-center fw-bold">Unité</td>
                                        <td valign="middle" class="text-center fw-bold">Hr</td>
                                        <td valign="middle" class="text-center fw-bold">Prix</td>
                                        <td valign="middle" class="text-center fw-bold">Marge</td>
                                        <td valign="middle" class="text-center fw-bold">Total</td>
                                        <td valign="middle" class="text-center fw-bold">Taxe</td>
                                        <td></td>
                                      </tr>
                                    </thead>
                                    <tbody>
                                      <tr v-for="(detail, detailIndex) in task.details" :key="detailIndex">
                                        <td valign="middle">
                                          <div class="custom-option d-flex align-items-center">
                                            <span 
                                              :class="{ 'clock-icon': detail.type == 'MO' || detail.type == 'INTERIM'|| detail.type == 'Labor',  
                                                'book-icon' : detail.type == 'PRODUIT' || detail.type == 'COMMANDE FOURNISSEUR'
                                              }"
                                            ></span> {{ detail.name }}
                                          </div>
                                        </td>
                                       <td valign="middle" class="text-center">{{ detail.qtyOuvrage }}</td>
                                        <td valign="middle" class="text-center">
                                          <input v-if="detail.type != 'MO'" @keyup="updateAllValues" type="text" v-model.number="detail.qty" class="w-100 form-control form-control-sm custom-text-danger">
                                          <span v-else>{{ detail.qty }}</span>
                                        </td>
                                        <td valign="middle" text="text-center">{{ detail.unit }}</td>
                                        <td valign="middle" v-if="detail.type == 'MO' || detail.type == 'Labor'">
                                          <div class="d-flex align-items-center">
                                            <input @keyup="updateAllValues" type="text" v-model.number="detail.numberH" class="w-100 form-control form-control-sm custom-text-danger">hr
                                          </div>
                                        </td>                                      
                                        <td valign="middle" class="text-center supplier" v-else-if="detail.type == 'COMMANDE FOURNISSEUR'">
                                          <svg width="20" height="25" viewBox="0 0 20 25" fill="none" xmlns="http://www.w3.org/2000/svg" @click="openPdfModal(detail.base64)" class="cursor-pointer">
                                            <path d="M19.9981 5.41835C19.9972 5.4093 19.9956 5.40049 19.994 5.39168C19.9935 5.38875 19.9932 5.38571 19.9926 5.38278C19.9905 5.37226 19.9877 5.36199 19.9847 5.35186C19.9843 5.35064 19.9841 5.34932 19.9837 5.34809C19.9804 5.33767 19.9766 5.3275 19.9724 5.31751C19.972 5.31649 19.9717 5.31541 19.9713 5.31438C19.9672 5.30484 19.9626 5.2956 19.9578 5.2865C19.957 5.28498 19.9563 5.28341 19.9555 5.2819C19.9509 5.27343 19.9457 5.26531 19.9405 5.25724C19.9391 5.25519 19.9379 5.25303 19.9366 5.25103C19.9314 5.24349 19.9258 5.23635 19.92 5.22921C19.9182 5.22686 19.9165 5.22441 19.9146 5.22211C19.9075 5.21365 19.8998 5.20558 19.892 5.19775C19.8913 5.19702 19.8907 5.19623 19.8899 5.1955L14.6769 0.107436C14.676 0.106556 14.675 0.105822 14.674 0.104941C14.6662 0.097456 14.6581 0.0902153 14.6497 0.0834149C14.6469 0.0812133 14.644 0.0793542 14.6412 0.0772505C14.6343 0.0720157 14.6274 0.0667808 14.6201 0.0620352C14.6176 0.0603718 14.6149 0.059002 14.6123 0.0573875C14.6045 0.052544 14.5967 0.0477495 14.5885 0.0434932C14.5867 0.0425636 14.5848 0.0417808 14.583 0.0409002C14.5739 0.0363503 14.5647 0.0319472 14.5552 0.0280822C14.554 0.027593 14.5527 0.0272505 14.5515 0.0267613C14.5414 0.0227984 14.5311 0.0190802 14.5206 0.0159491C14.5192 0.0155577 14.5179 0.0153131 14.5165 0.0149217C14.5062 0.0119863 14.4957 0.0092955 14.4851 0.00719178C14.4819 0.00655577 14.4786 0.00631115 14.4754 0.00577299C14.4666 0.00425636 14.4577 0.00273973 14.4486 0.0018591C14.4361 0.000636008 14.4236 0 14.411 0H1.97995C0.88822 0 0 0.866928 0 1.93249V23.0675C0 24.1331 0.88822 25 1.97995 25H18.02C19.1118 25 20 24.1331 20 23.0675V5.45499C20 5.44271 19.9993 5.43048 19.9981 5.41835ZM14.787 1.25274L18.7165 5.08806H16.015C15.3379 5.08806 14.787 4.55039 14.787 3.88943V1.25274ZM18.02 24.2661H1.97995C1.30281 24.2661 0.75188 23.7285 0.75188 23.0675V1.93249C0.75188 1.27153 1.30281 0.733855 1.97995 0.733855H14.0351V3.88943C14.0351 4.95499 14.9233 5.82192 16.015 5.82192H19.2481V23.0675C19.2481 23.7285 18.6972 24.2661 18.02 24.2661Z" fill="black"/>
                                          </svg>
                                        </td>
                                        <td valign="middle" class="text-center" v-else></td>
                                        <td valign="middle">
                                          <div class="d-flex align-items-center">
                                            <input @keyup="updateAllValues" type="text" v-model="detail.unitPrice" class="w-100 form-control form-control-sm custom-text-danger">€
                                          </div>
                                        </td>
                                        <td valign="middle" v-if="detail.type == 'MO' || detail.type == 'Labor'">
                                        </td>
                                        <td v-else style="min-width: 40px" valign="middle">
                                          <div class="d-flex align-items-center">
                                            <input @keyup="updateAllValues" type="text" v-model.number="detail.marge" class="w-100 form-control form-control-sm custom-text-danger">%
                                          </div>                                        
                                        </td>
                                        <td valign="middle">{{ detail.totalPrice }}€</td>
                                        <td valign="middle">
                                          <select style="min-width: 40px" class="w-100 form-control form-control-sm custom-text-danger" v-model="detail.tax">
                                            <option :value="tax.value" v-for="(tax, taxIndex) in taxes" :key="taxIndex">{{ tax.display }} %</option>
                                          </select>                                          
                                        </td>
                                        <td valign="middle">
                                          <svg class="cursor-pointer" @click="removeOuvrageDetail(zoneIndex, 2, ouvrageIndex, taskIndex, detailIndex)" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                          <path d="M17 6H22V8H20V21C20 21.2652 19.8946 21.5196 19.7071 21.7071C19.5196 21.8946 19.2652 22 19 22H5C4.73478 22 4.48043 21.8946 4.29289 21.7071C4.10536 21.5196 4 21.2652 4 21V8H2V6H7V3C7 2.73478 7.10536 2.48043 7.29289 2.29289C7.48043 2.10536 7.73478 2 8 2H16C16.2652 2 16.5196 2.10536 16.7071 2.29289C16.8946 2.48043 17 2.73478 17 3V6ZM18 8H6V20H18V8ZM13.414 14L15.182 15.768L13.768 17.182L12 15.414L10.232 17.182L8.818 15.768L10.586 14L8.818 12.232L10.232 10.818L12 12.586L13.768 10.818L15.182 12.232L13.414 14ZM9 4V6H15V4H9Z" fill="black"/>
                                          </svg>
                                        </td>
                                      </tr>
                                    </tbody>
                                  </table>
                                </div>
                                <div class="btns d-flex mt-4">
                                  <div class="col-5">
                                    <div class="add-btn ms-3 mt-3 d-flex align-items-center mulish-semibold font-14 custom-text-danger cursor-pointer" @click="openProductModal(zoneIndex, 2,ouvrageIndex, taskIndex)">
                                      <span class="plus-icon me-2"></span> AJOUTER UN PRODUIT
                                    </div>
                                    <div class="add-btn ms-3 mt-3 d-flex align-items-center mulish-semibold font-14 custom-text-danger cursor-pointer" @click="openTaskModal(zoneIndex, 2, ouvrageIndex)">
                                      <span class="plus-icon me-2"></span> AJOUTER ACTION
                                    </div>
                                    <div class="add-btn ms-3 mt-3 d-flex align-items-center mulish-semibold font-14 custom-text-danger cursor-pointer" @click="openSupplierModal(zoneIndex, 2, ouvrageIndex, taskIndex)">
                                      <span class="plus-icon me-2"></span> COMMANDE FOURNISSEUR
                                    </div>
                                  </div>
                                  <div class="col-5">
                                    <div class="add-btn ms-3 mt-3 d-flex align-items-center mulish-semibold font-14 custom-text-danger cursor-pointer" @click="openLaborModal(zoneIndex, 2, ouvrageIndex, taskIndex)">
                                      <span class="plus-icon me-2"></span> AJOUTER UN MAIN D’ OEUVRES
                                    </div>
                                    <div class="add-btn ms-3 mt-3 d-flex align-items-center mulish-semibold font-14 custom-text-danger cursor-pointer" @click="openInterimModal(zoneIndex, 2, ouvrageIndex, taskIndex)">
                                      <span class="plus-icon me-2"></span> AJOUTER  INTERIM
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                            <div v-if="ouvrage.tasks.length == 0" class="add-btn ms-3 mt-3 d-flex align-items-center mulish-semibold font-14 custom-text-danger cursor-pointer" @click="openTaskModal(zoneIndex, 2, ouvrageIndex)">
                              <span class="plus-icon me-2"></span> AJOUTER ACTION
                            </div>                                                
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="ouvrage-section prestation-ouvrages bg-white px-4 py-3 mt-2 mb-2" :id="'zone-'+zoneIndex+'-prestation-ouvrages'">
                  <div class="ouvrage-header d-flex">
                    <div class="col-7">
                      <div class="col-5 d-flex align-items-center cursor-pointer toggle-btn" @click="togglePanel('zone-'+zoneIndex+'-prestation-ouvrages')">
                        <span class="prestation-icon me-2"></span> 
                        <input type="text" @blur="zone.prestationOuvrage.edit = false" v-if="zone.prestationOuvrage.edit" class="form-control form-control-sm" v-model="zone.prestationOuvrage.name">
                        <span v-else class="me-4" @dblclick="zone.prestationOuvrage.edit = true">{{ zone.prestationOuvrage.name }}</span>                          
                        <span class="arrow-icon ms-auto"></span>
                      </div>
                      <div class="add-btn ms-5 ps-3 d-flex align-items-center mulish-semibold font-14 custom-text-danger cursor-pointer" 
                      @click="openPrestationModal(zoneIndex)">
                        <span class="plus-icon me-2"></span> AJOUTER UN OUVRAGE
                      </div>
                    </div>
                    <div class="col-5 d-flex align-items-center">
                      <div class="col-4 fw-bold mulish-extra-bold font-16 text-black text-center">{{ zone.prestationOuvrage.totalHour }} hr</div>
                      <div class="col-4 fw-bold mulish-extra-bold font-16 text-black text-center text-nowrap">{{ zone.prestationOuvrage.sumUnitPrice.toFixed(2) }} €</div>
                      <div class="col-4 fw-bold mulish-extra-bold font-16 text-black text-center text-nowrap">{{ zone.prestationOuvrage.totalPrice.toFixed(2) }} €</div>
                    </div>
                  </div>
                  <div class="ouvrage-body ps-3 mt-3">
                    <div class="ouvrage-list">
                      <div class="ouvrage-list-header">
                        <div class="d-flex">
                          <div class="col-4"></div>
                          <div class="col-8 d-flex">
                            <div class="col-2 d-flex align-items-center justify-content-center border border-1 fw-bold">Qte</div>
                            <div class="col-2 d-flex align-items-center justify-content-center border border-1 fw-bold">Unit ouv</div>
                            <div class="col-2 d-flex align-items-center justify-content-center border border-1 fw-bold">Total hr</div>
                            <div class="col-2 d-flex align-items-center justify-content-center border border-1 fw-bold">Total / Qte</div>
                            <div class="col-2 d-flex align-items-center justify-content-center border border-1 fw-bold">Total</div>
                            <div class="col-2"></div>
                          </div>
                        </div>
                      </div>                      
                      <div class="ouvrage-item" v-for="(ouvrage, ouvrageIndex) in zone.prestationOuvrage.ouvrages" :key="ouvrageIndex">
                        <div class="d-flex ouvrage-header">
                          <div class="col-4">
                            <a class="ouvrage custom-option d-flex align-items-center px-0 text-black" :class="{ 'active': ouvrageIndex == (zone.prestationOuvrage.ouvrages.length - 1 )}" :data-id="'zone-' + zoneIndex +'-prestation-ouvrage-'+ouvrageIndex" href="javascript:;"
                                @click="activeOuvrage"
                              >
                                <span class="option-icon me-2"><span class="option-icon-dot"></span></span> {{ ouvrage.name }}
                              </a>
                          </div>
                          <div class="col-8 d-flex">
                            <div class="col-2 d-flex align-items-center justify-content-center border border-1">
                              <input @keyup="updateAllValues" type="text" v-model.number="ouvrage.qty" class="w-100 form-control form-control-sm custom-text-danger">
                            </div>
                            <div class="col-2 d-flex align-items-center justify-content-center border border-1">
                              <select class="form-control form-control-sm custom-text-danger" v-model="ouvrage.unit">
                                <option v-for="(unit, unitIndex) in units" :value="unit.value" :key="unitIndex">{{ unit.display }}</option>
                              </select>
                            </div>
                            <div class="col-2 d-flex align-items-center justify-content-center border border-1">
                              {{ ouvrage.totalHour }} hr
                            </div>
                            <div class="col-2 d-flex align-items-center justify-content-center border border-1">
                              {{ (ouvrage.total / ouvrage.qty).toFixed(2) }}€
                            </div>
                            <div class="col-2 d-flex align-items-center justify-content-center border border-1">
                              {{ ouvrage.total.toFixed(2) }}€
                            </div>
                            <div class="col-2 d-flex align-items-center justify-content-center">
                              <svg class="cursor-pointer" @click="removeOuvrage(zoneIndex, 3, ouvrageIndex)" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                              <path d="M17 6H22V8H20V21C20 21.2652 19.8946 21.5196 19.7071 21.7071C19.5196 21.8946 19.2652 22 19 22H5C4.73478 22 4.48043 21.8946 4.29289 21.7071C4.10536 21.5196 4 21.2652 4 21V8H2V6H7V3C7 2.73478 7.10536 2.48043 7.29289 2.29289C7.48043 2.10536 7.73478 2 8 2H16C16.2652 2 16.5196 2.10536 16.7071 2.29289C16.8946 2.48043 17 2.73478 17 3V6ZM18 8H6V20H18V8ZM13.414 14L15.182 15.768L13.768 17.182L12 15.414L10.232 17.182L8.818 15.768L10.586 14L8.818 12.232L10.232 10.818L12 12.586L13.768 10.818L15.182 12.232L13.414 14ZM9 4V6H15V4H9Z" fill="black"/>
                              </svg>
                            </div>
                          </div>
                        </div>
                        <div class="tab-content">
                          <div class="tab-pane ps-3" :class="{ 'active': ouvrageIndex == 0}" :id="'zone-'+ zoneIndex +'-prestation-ouvrage-'+ouvrageIndex">
                            <h3 class="mulish-light fw-light custom-text-danger font-14">TEXTE COMMENTAIRE TECHNIQUE</h3>
                            <!-- ouvrage description -->
                            <ul class="ps-3" v-if="ouvrage.textchargeaffaire !=''">
                              <li class="mulish-regular font-10">
                                {{ ouvrage.textchargeaffaire }}
                              </li>
                            </ul>
                            <h3 class="mt-3 mulish-light fw-light text-custom-success font-14">TEXTE POUR CLIENTS</h3>
                            <ul class="ps-3">
                              <li class="mulish-regular font-10 custom-text-danger">
                                <input  style="text-align: left !important" type="text" v-model="ouvrage.customerText" class="w-100 form-control form-control-sm custom-text-danger">
                              </li>
                            </ul>
                            <!-- Ouvrage Task -->
                            <div class="ouvrage-task" v-for="(task, taskIndex) in ouvrage.tasks" :key="taskIndex">
                              <div class="task-header d-flex align-items-center custom-option cursor-pointer" :class="{ 'active': taskIndex == 0}" :data-id="'zone-'+ zoneIndex +'-prestation-ouvrage-'+ouvrageIndex+'-task-'+taskIndex" @click="activeOuvrageTask">
                                <span class="option-icon me-2"><span class="option-icon-dot"></span></span> {{ task.name }}
                              </div>
                              <div class="task-body ps-3" :class="{ 'show': taskIndex == 0}" :id="'zone-'+ zoneIndex +'-prestation-ouvrage-'+ouvrageIndex+'-task-'+taskIndex">
                                <h3 class="mulish-light fw-light custom-text-danger font-14">TEXTE COMMENTAIRE TECHNIQUE</h3>
                                <!-- task description -->
                                <ul class="ps-3" v-if="task.textchargeaffaire != ''">
                                  <li class="mulish-regular font-10">
                                    {{ task.textchargeaffaire }}
                                  </li>
                                </ul>
                                <h3 class="mt-3 mulish-light fw-light text-custom-success font-14">TEXTE POUR CLIENTS</h3>
                                <ul class="ps-3">
                                  <li class="mulish-regular font-10 custom-text-danger">
                                    <textarea  style="text-align: left !important" v-model="task.customerText" class="w-100 form-control custom-text-danger" rows="2"></textarea>
                                  </li>
                                </ul>
                                <div class="w-100 ps-3">
                                  <table class="table w-100 details-table m-0">
                                    <thead>
                                      <tr>
                                        <td></td>
                                        <td valign="middle" class="text-center fw-bold">Qte ouvrage</td>
                                        <td valign="middle" class="text-center fw-bold">Qte</td>
                                        <td valign="middle" class="text-center fw-bold">Unité</td>
                                        <td valign="middle" class="text-center fw-bold">Hr</td>
                                        <td valign="middle" class="text-center fw-bold">Prix</td>
                                        <td valign="middle" class="text-center fw-bold">Marge</td>
                                        <td valign="middle" class="text-center fw-bold">Total</td>
                                        <td valign="middle" class="text-center fw-bold">Taxe</td>
                                        <td></td>
                                      </tr>
                                    </thead>                                    
                                    <tbody>
                                      <tr v-for="(detail, detailIndex) in task.details" :key="detailIndex">
                                        <td valign="middle">
                                          <div class="custom-option d-flex align-items-center">
                                            <span 
                                              :class="{ 'clock-icon': detail.type == 'MO' || detail.type == 'INTERIM'|| detail.type == 'Labor',  
                                                'book-icon' : detail.type == 'PRODUIT' || detail.type == 'COMMANDE FOURNISSEUR'
                                              }"
                                            ></span> {{ detail.name }}
                                          </div>
                                        </td>
                                       <td valign="middle" class="text-center">{{ detail.qtyOuvrage }}</td>
                                        <td valign="middle" class="text-center">
                                          <input v-if="detail.type != 'MO'" @keyup="updateAllValues" type="text" v-model.number="detail.qty" class="w-100 form-control form-control-sm custom-text-danger">
                                          <span v-else>{{ detail.qty }}</span>
                                        </td>
                                        <td valign="middle" text="text-center">{{ detail.unit }}</td>
                                        <td valign="middle" v-if="detail.type == 'MO' || detail.type == 'Labor'">
                                          <div class="d-flex align-items-center">
                                            <input @keyup="updateAllValues" type="text" v-model="detail.numberH" class="w-100 form-control form-control-sm custom-text-danger">hr
                                          </div>
                                        </td>                                      
                                        <td valign="middle" class="text-center supplier" v-else-if="detail.type == 'COMMANDE FOURNISSEUR'">
                                          <svg width="20" height="25" viewBox="0 0 20 25" fill="none" xmlns="http://www.w3.org/2000/svg" @click="openPdfModal(detail.base64)" class="cursor-pointer">
                                            <path d="M19.9981 5.41835C19.9972 5.4093 19.9956 5.40049 19.994 5.39168C19.9935 5.38875 19.9932 5.38571 19.9926 5.38278C19.9905 5.37226 19.9877 5.36199 19.9847 5.35186C19.9843 5.35064 19.9841 5.34932 19.9837 5.34809C19.9804 5.33767 19.9766 5.3275 19.9724 5.31751C19.972 5.31649 19.9717 5.31541 19.9713 5.31438C19.9672 5.30484 19.9626 5.2956 19.9578 5.2865C19.957 5.28498 19.9563 5.28341 19.9555 5.2819C19.9509 5.27343 19.9457 5.26531 19.9405 5.25724C19.9391 5.25519 19.9379 5.25303 19.9366 5.25103C19.9314 5.24349 19.9258 5.23635 19.92 5.22921C19.9182 5.22686 19.9165 5.22441 19.9146 5.22211C19.9075 5.21365 19.8998 5.20558 19.892 5.19775C19.8913 5.19702 19.8907 5.19623 19.8899 5.1955L14.6769 0.107436C14.676 0.106556 14.675 0.105822 14.674 0.104941C14.6662 0.097456 14.6581 0.0902153 14.6497 0.0834149C14.6469 0.0812133 14.644 0.0793542 14.6412 0.0772505C14.6343 0.0720157 14.6274 0.0667808 14.6201 0.0620352C14.6176 0.0603718 14.6149 0.059002 14.6123 0.0573875C14.6045 0.052544 14.5967 0.0477495 14.5885 0.0434932C14.5867 0.0425636 14.5848 0.0417808 14.583 0.0409002C14.5739 0.0363503 14.5647 0.0319472 14.5552 0.0280822C14.554 0.027593 14.5527 0.0272505 14.5515 0.0267613C14.5414 0.0227984 14.5311 0.0190802 14.5206 0.0159491C14.5192 0.0155577 14.5179 0.0153131 14.5165 0.0149217C14.5062 0.0119863 14.4957 0.0092955 14.4851 0.00719178C14.4819 0.00655577 14.4786 0.00631115 14.4754 0.00577299C14.4666 0.00425636 14.4577 0.00273973 14.4486 0.0018591C14.4361 0.000636008 14.4236 0 14.411 0H1.97995C0.88822 0 0 0.866928 0 1.93249V23.0675C0 24.1331 0.88822 25 1.97995 25H18.02C19.1118 25 20 24.1331 20 23.0675V5.45499C20 5.44271 19.9993 5.43048 19.9981 5.41835ZM14.787 1.25274L18.7165 5.08806H16.015C15.3379 5.08806 14.787 4.55039 14.787 3.88943V1.25274ZM18.02 24.2661H1.97995C1.30281 24.2661 0.75188 23.7285 0.75188 23.0675V1.93249C0.75188 1.27153 1.30281 0.733855 1.97995 0.733855H14.0351V3.88943C14.0351 4.95499 14.9233 5.82192 16.015 5.82192H19.2481V23.0675C19.2481 23.7285 18.6972 24.2661 18.02 24.2661Z" fill="black"/>
                                          </svg>
                                        </td>
                                        <td valign="middle" class="text-center" v-else></td>
                                        <td valign="middle">
                                          <div class="d-flex align-items-center">
                                            <input @keyup="updateAllValues" type="text" v-model.number="detail.unitPrice" class="w-100 form-control form-control-sm custom-text-danger">€
                                          </div>
                                        </td>
                                        <td valign="middle" v-if="detail.type == 'MO' || detail.type == 'Labor'">
                                        </td>
                                        <td v-else style="min-width: 40px" valign="middle">
                                          <div class="d-flex align-items-center">
                                            <input @keyup="updateAllValues" type="text" v-model.number="detail.marge" class="w-100 form-control form-control-sm custom-text-danger">%
                                          </div>                                        
                                        </td>
                                        <td valign="middle">{{ detail.totalPrice }}€</td>
                                        <td valign="middle">
                                          <select style="min-width: 40px" class="w-100 form-control form-control-sm custom-text-danger" v-model="detail.tax">
                                            <option :value="tax.value" v-for="(tax, taxIndex) in taxes" :key="taxIndex">{{ tax.display }} %</option>
                                          </select>                                          
                                        </td>
                                        <td valign="middle">
                                          <svg class="cursor-pointer" @click="removeOuvrageDetail(zoneIndex, 3, ouvrageIndex, taskIndex, detailIndex)" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                          <path d="M17 6H22V8H20V21C20 21.2652 19.8946 21.5196 19.7071 21.7071C19.5196 21.8946 19.2652 22 19 22H5C4.73478 22 4.48043 21.8946 4.29289 21.7071C4.10536 21.5196 4 21.2652 4 21V8H2V6H7V3C7 2.73478 7.10536 2.48043 7.29289 2.29289C7.48043 2.10536 7.73478 2 8 2H16C16.2652 2 16.5196 2.10536 16.7071 2.29289C16.8946 2.48043 17 2.73478 17 3V6ZM18 8H6V20H18V8ZM13.414 14L15.182 15.768L13.768 17.182L12 15.414L10.232 17.182L8.818 15.768L10.586 14L8.818 12.232L10.232 10.818L12 12.586L13.768 10.818L15.182 12.232L13.414 14ZM9 4V6H15V4H9Z" fill="black"/>
                                          </svg>
                                        </td>
                                      </tr>
                                    </tbody>
                                  </table>
                                </div>
                                <div class="btns d-flex mt-4">
                                  <div class="col-5">
                                    <div class="add-btn ms-3 mt-3 d-flex align-items-center mulish-semibold font-14 custom-text-danger cursor-pointer" @click="openProductModal(zoneIndex, 3,ouvrageIndex, taskIndex)">
                                      <span class="plus-icon me-2"></span> AJOUTER UN PRODUIT
                                    </div>
                                    <div class="add-btn ms-3 mt-3 d-flex align-items-center mulish-semibold font-14 custom-text-danger cursor-pointer" @click="openTaskModal(zoneIndex, 3, ouvrageIndex)">
                                      <span class="plus-icon me-2"></span> AJOUTER ACTION
                                    </div>
                                    <div class="add-btn ms-3 mt-3 d-flex align-items-center mulish-semibold font-14 custom-text-danger cursor-pointer" @click="openSupplierModal(zoneIndex, 3, ouvrageIndex, taskIndex)">
                                      <span class="plus-icon me-2"></span> COMMANDE FOURNISSEUR
                                    </div>
                                  </div>
                                  <div class="col-5">
                                    <div class="add-btn ms-3 mt-3 d-flex align-items-center mulish-semibold font-14 custom-text-danger cursor-pointer" @click="openLaborModal(zoneIndex, 3, ouvrageIndex, taskIndex)">
                                      <span class="plus-icon me-2"></span> AJOUTER UN MAIN D’ OEUVRES
                                    </div>
                                    <div class="add-btn ms-3 mt-3 d-flex align-items-center mulish-semibold font-14 custom-text-danger cursor-pointer" @click="openInterimModal(zoneIndex, 3, ouvrageIndex, taskIndex)">
                                      <span class="plus-icon me-2"></span> AJOUTER  INTERIM
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                            <div v-if="ouvrage.tasks.length == 0" class="add-btn ms-3 mt-3 d-flex align-items-center mulish-semibold font-14 custom-text-danger cursor-pointer" @click="openTaskModal(zoneIndex, 3, ouvrageIndex)">
                              <span class="plus-icon me-2"></span> AJOUTER ACTION
                            </div>                                                
                          </div>
                        </div>
                      </div>                        
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="ms-2 right-panel">
              <div class="section bg-white">
                <div class="d-flex">
                  <div class="col-9 d-flex flex-wrap">
                    <p class="w-100 m-0 title">
                      Arrondir les heures
                    </p>
                    <p class="w-100 m-0 sub-title">
                      Les jours du Chantiers
                    </p>
                  </div>
                  <div class="col-3 d-flex align-items-center">
                    <p class="w-100 text-center font-16 mulish-extrabold d-flex align-items-center">
                      <input type="text" class="form-control form-control-sm me-2" v-model="form.totalDays" @change="adjustHours"> jours 
                    </p>                    
                  </div>
                </div>
                <div class="d-flex mt-2">
                  <div class="col-9 d-flex flex-wrap">
                    <p class="w-100 m-0 title">
                      Remise Euros :  
                    </p>
                    <p class="w-100 m-0 sub-title">
                      A convertir en heure et a soustraire au total d’heures
                    </p>
                  </div>
                  <div class="col-3 d-flex align-items-center">
                    <p class="w-100 text-center font-16 mulish-extrabold d-flex align-items-center">
                      <input type="text" class="form-control form-control-sm me-2" v-model="form.discount"> € 
                    </p>
                  </div>
                </div>
              </div>
              <div class="section mt-2 bg-white">
                <div class="d-flex">
                  <div class="d-flex flex-wrap col-8">
                    <p class="w-100 title m-0">
                      Synthese Chantier
                    </p>
                    <p class="w-100 m-0 sub-title">
                      Apud has gentes, quarum exordiens 
                    </p>
                  </div>
                  <div class="col-2 d-flex align-items-center justify-content-center fw-bold mulish-extra-bold font-16 text-black  text-nowrap">
                    {{ (form.totalHoursForInstall + form.totalHoursForSecurity + form.totalHoursForPrestation).toFixed(2) }} hr
                  </div>
                  <div class="col-2 d-flex align-items-center justify-content-center fw-bold mulish-extra-bold font-16 text-black  text-nowrap">
                    {{ (form.totalPriceForInstall + form.totalPriceForSecurity + form.totalPriceForPrestation - form.discount).toFixed(2) }} €
                  </div>
                </div>
                <div class="d-flex mt-4">
                  <div class="col-8 title">
                    Installation
                  </div>
                  <div class="col-2 border-bottom text-center border-end font-14 text-nowrap">{{ form.totalHoursForInstall }}hr</div>
                  <div class="col-2 border-bottom text-center font-14  text-nowrap">{{ form.totalPriceForInstall.toFixed(2) }} €</div>
                </div>
                <div class="d-flex mt-3">
                  <div class="col-8 title">
                    Mise en Securite
                  </div>
                  <div class="col-2 border-bottom text-center border-end font-14 text-nowrap">{{ form.totalHoursForSecurity }}hr</div>
                  <div class="col-2 border-bottom text-center font-14 text-nowrap">{{ form.totalPriceForSecurity.toFixed(2) }} €</div>
                </div>
                <div class="d-flex mt-3">
                  <div class="col-8 title">
                    Intervention
                  </div>
                  <div class="col-2 border-bottom text-center border-end font-14 text-nowrap">{{ form.totalHoursForPrestation }}hr</div>
                  <div class="col-2 border-bottom text-center font-14 text-nowrap">{{ form.totalPriceForPrestation.toFixed(2) }} €</div>
                </div>
                <div class="d-flex mt-4">
                  <div class="col-10 bold-title">
                    Total Jours Interim
                  </div>
                  <div class="col-2 font-14 fw-bold text-nowrap">{{form.totalHoursForInterim}} Jours</div>
                </div>
                <div class="d-flex mt-3">
                  <div class="col-10 bold-title">
                    Total M/V Fourniseurs
                  </div>
                  <div class="col-2 font-14 fw-bold text-nowrap">€ {{ parseFloat(form.totalPriceWithoutMarge).toFixed(2) }}</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <input type="file" @change="previewFile" accept="audio/*,video/*,image/*" ref="file" multiple class="d-none">
      <zoom-modal ref="zoomModal"></zoom-modal>
      <SecuriteModal ref="securiteModal" @selectedOuvrage="selectedOuvrage" @openEmptyOuvrageModal="openEmptyOuvrageModal"></SecuriteModal>
      <InstallationModal ref="installationModal" @selectedOuvrage="selectedOuvrage" @openEmptyOuvrageModal="openEmptyOuvrageModal"></InstallationModal>
      <PrestationModal ref="prestationModal" @selectedOuvrage="selectedOuvrage" @openEmptyOuvrageModal="openEmptyOuvrageModal"></PrestationModal>
      <ProductModal ref="productModal" @selectedProduct="selectedProduct" @openEmptyProductModal="openEmptyProductModal"></ProductModal>
      <SupplierModal ref="supplierModal" @selectedSupplier="selectedSupplier"></SupplierModal>
      <LaborModal ref="laborModal" @selectedLabor="selectedLabor"></LaborModal>
      <TaskModal ref="taskModal" @selectedTask="selectedTask"></TaskModal>
      <InterimModal ref="interimModal" @selectedInterim="selectedInterim"></InterimModal>
      <AddressModal ref="addressModal" @addedNewAddress="addedNewAddress"></AddressModal>
      <EmptyOuvrageModal ref="emptyOuvrageModal" @selectedEmptyOuvrage="selectedEmptyOuvrage"></EmptyOuvrageModal>
      <EmptyProductModal ref="emptyProductModal" @selectedEmptyProduct="selectedEmptyProduct"></EmptyProductModal>
      <PdfModal ref="pdfModal"></PdfModal>
    </div>
  </transition>
</template>
<script>
import { ref, onMounted, watch, watchEffect } from 'vue';
import SelectBox from '../../components/miscellaneous/SelectBox';
import SearchCustomer from '../../components/miscellaneous/SearchCustomer';
import ZoomModal from '../../components/miscellaneous/ZoomModal';
import SecuriteModal from '../../components/miscellaneous/SecuriteModal';
import InstallationModal from '../../components/miscellaneous/InstallationModal';
import PrestationModal from '../../components/miscellaneous/PrestationModal';
import SupplierModal from '../../components/miscellaneous/SupplierModal';
import InterimModal from '../../components/miscellaneous/InterimModal';
import LaborModal from '../../components/miscellaneous/LaborModal';
import ProductModal from '../../components/miscellaneous/ProductModal';
import AddressModal from '../../components/miscellaneous/AddressModal';
import TaskModal from '../../components/miscellaneous/TaskModal';
import EmptyOuvrageModal from '../../components/miscellaneous/EmptyOuvrageModal';
import EmptyProductModal from '../../components/miscellaneous/EmptyProductModal';
import PdfModal from '../../components/miscellaneous/PdfModal';
import GoogleMap from '../../components/miscellaneous/GoogleMap';
import Swal from 'sweetalert2';
import {     
  DISPLAY_LOADER,
  HIDE_LOADER,
  LOADER_MODULE, 
  TOASTER_MESSAGE, 
  TOASTER_MODULE
  } from '../../store/types/types';
import axios from 'axios';
import { useRouter, useRoute } from 'vue-router';
import { useStore } from 'vuex';
export default {
  components:{
    SearchCustomer,
    SelectBox,
    ZoomModal,
    SecuriteModal,
    InstallationModal,
    PrestationModal,
    SupplierModal,
    AddressModal,
    ProductModal,
    InterimModal,
    LaborModal,
    TaskModal,
    EmptyOuvrageModal,
    EmptyProductModal,
    PdfModal,
    GoogleMap
  },
  setup() {
    const store = useStore();
    const router = useRouter();
    const route = useRoute();
    const breadcrumbs = ref(['Choix client']);
    const devisCreateStep = ref('create_devis');
    watchEffect(()=>{
      if(devisCreateStep.value == 'choose_customer'){
        breadcrumbs.value = ['Choix client'];
      }else if(devisCreateStep.value == 'choose_address'){
        breadcrumbs.value = ['Choix client', 'Choix adresse chantier'];
      }else{
        breadcrumbs.value = [];
      }
    })
    const taxes = ref([]);
    const units = ref([]);
    const customerAddresses = ref([]);
    const zoneIndex = ref(0);
    const file = ref(null);
    const zoomModal = ref(null);
    const securiteModal = ref(null);
    const installationModal = ref(null);
    const prestationModal = ref(null);
    const supplierModal = ref(null);
    const laborModal = ref(null);
    const interimModal = ref(null);
    const productModal = ref(null);
    const addressModal = ref(null);
    const emptyOuvrageModal = ref(null);
    const emptyProductModal = ref(null);
    const pdfModal = ref(null);
    const taskModal = ref(null);
    const gedCatId = ref(0);
    const gedCats = ref([]);
    const roofAccesses = ref([]);
    const form = ref({
      orderStatus: {
        name: 'EN PREPA',
        type: 'DEVIS',
        fontcolor: '#42a71e',
        color: '#d9edd2',
      },
      customer: {
        id: 1,
        company: '',
        raisonsocial: '',
        group: '',
        contact: '',
        telephone: '',
        tax: '',
        taxId: 0,
        naf: '',
        siret: '',
      },
      address: {
        id: 1,
        name: '',
        address1: '',
        address2: '',
        postcode: '',
        city: '',
        addressType: '',
        lat: 36.846691982477985,
        lon: 10.197948495703532,
      },
      totalHoursForInstall: 0,
      totalPriceForInstall: 0,
      totalHoursForSecurity: 0,
      totalPriceForSecurity: 0,
      totalHoursForPrestation: 0,
      totalPriceForPrestation: 0,
      totalHoursForInterim: 0,
      totalPriceWithoutMarge: 0,
      totalUnitPrice: 0,
      discount: 0,
      totalDays: 0,
      zones: [
        {
          edit: false,
          name: 'Zone 1',
          roofAccess: 0,
          height: '',
          gedCats: [],
          installOuvrage: {
            name: 'Installation',
            edit: false,
            totalHour: 0,
            sumUnitPrice: 0,
            totalPrice: 0,
            ouvrages: [
            ]
          },
          securityOuvrage: {
            name: 'Sécurité',
            edit: false,
            totalHour: 0,
            sumUnitPrice: 0,
            totalPrice: 0,
            ouvrages: []
          },
          prestationOuvrage: {
            name: 'Prestations',
            edit: false,
            totalHour: 0,
            sumUnitPrice: 0,
            totalPrice: 0,
            ouvrages: []
          },          
        }
      ],
    });    
    const updateAllValues = ()=>{
      form.value.totalHoursForInstall = 0;
      form.value.totalPriceForInstall = 0;
      form.value.totalHoursForSecurity = 0;
      form.value.totalPriceForSecurity = 0;
      form.value.totalHoursForPrestation = 0;
      form.value.totalPriceForPrestation = 0;
      form.value.totalHoursForInterim = 0;
      form.value.totalPriceWithoutMarge = 0;
      form.value.totalUnitPrice = 0;
      form.value.zones.forEach(zone=>{
        // install ouvrages
        zone.installOuvrage.totalHour = 0;
        zone.installOuvrage.totalPrice = 0;
        zone.installOuvrage.sumUnitPrice = 0;
        zone.installOuvrage.ouvrages.forEach(ouvrage=>{
          ouvrage.total = 0;
          ouvrage.totalHour = 0;
          ouvrage.qtyOuvrage = ouvrage.qty;
          ouvrage.totalWithoutMarge = 0;
          ouvrage.tasks.forEach(task=>{
            task.details.forEach(detail=>{
              zone.installOuvrage.sumUnitPrice += parseInt(detail.unitPrice);
              if(detail.original){
                if(detail.qtyOuvrage != ouvrage.qty){
                  detail.qtyOuvrage = ouvrage.qty;
                  detail.qty = parseInt(ouvrage.qty)*parseInt(detail.originalDetailQty);
                  detail.numberH = parseFloat(detail.originalNumberH)* parseInt(detail.qty);
                }
              }           
              if(detail.type == 'MO'){
                if(detail.original){
                  detail.totalPrice = (parseFloat(detail.numberH == '' ? '0' : detail.numberH) * parseInt(detail.unitPrice)).toFixed(2);
                  detail.totalPriceWithoutMarge = (parseFloat(detail.numberH == '' ? '0' : detail.numberH) * parseFloat(detail.unitPrice));
                }else{
                  detail.totalPrice = (parseFloat(detail.numberH == '' ? '0' : detail.numberH) * parseInt(detail.unitPrice)* parseInt(detail.qty)).toFixed(2);
                  detail.totalPriceWithoutMarge = (parseFloat(detail.numberH == '' ? '0' : detail.numberH) * parseFloat(detail.unitPrice)* parseInt(detail.qty));
                }
              }else if( detail.type == 'Labor' ){
                detail.totalPrice = (parseFloat(detail.numberH == '' ? '0' : detail.numberH) * parseInt(detail.unitPrice) * parseInt(detail.qty)).toFixed(2);
                detail.totalPriceWithoutMarge = (parseFloat(detail.numberH == '' ? '0' : detail.numberH) * parseFloat(detail.unitPrice));
              }else if( detail.type == 'INTERIM'){
                detail.totalPrice = (parseFloat(detail.qty) * parseInt(detail.unitPrice)*(1+ parseInt(detail.marge)/100)).toFixed(2);
                detail.totalPriceWithoutMarge = (parseFloat(detail.numberH == '' ? '0' : detail.numberH) * parseFloat(detail.unitPrice));
              }
              else{
                detail.totalPrice = (parseInt(detail.qty) * parseFloat(detail.unitPrice) * (parseInt(detail.marge)/100 + 1)).toFixed(2);
                detail.totalPriceWithoutMarge = (parseInt(detail.qty) * parseFloat(detail.unitPrice));
              }     
              if(detail.type == 'INTERIM'){
                form.value.totalHoursForInterim += parseFloat(detail.qty)
              }
              ouvrage.total += parseFloat(detail.totalPrice);    
              ouvrage.totalWithoutMarge += parseFloat(detail.totalPriceWithoutMarge);
              ouvrage.totalHour += parseFloat(detail.numberH == '' ? '0' : detail.numberH);
            })
          })
          zone.installOuvrage.totalHour += ouvrage.totalHour;
          zone.installOuvrage.totalPrice += ouvrage.total;
          form.value.totalPriceWithoutMarge += ouvrage.totalWithoutMarge;
        })
        form.value.totalUnitPrice += zone.installOuvrage.sumUnitPrice;
        form.value.totalHoursForInstall += zone.installOuvrage.totalHour;
        form.value.totalPriceForInstall += zone.installOuvrage.totalPrice;
        // securite ouvrages
        zone.securityOuvrage.totalHour = 0;
        zone.securityOuvrage.totalPrice = 0;
        zone.securityOuvrage.sumUnitPrice = 0;
        zone.securityOuvrage.ouvrages.forEach(ouvrage=>{
          ouvrage.total = 0;
          ouvrage.totalHour = 0;
          ouvrage.qtyOuvrage = ouvrage.qty;
          ouvrage.totalWithoutMarge = 0;
          ouvrage.tasks.forEach(task=>{
            task.details.forEach(detail=>{
              zone.securityOuvrage.sumUnitPrice += parseInt(detail.unitPrice);
              if(detail.original){
                if(detail.qtyOuvrage != ouvrage.qty){
                  detail.qtyOuvrage = ouvrage.qty;
                  detail.qty = parseInt(ouvrage.qty)*parseInt(detail.originalDetailQty);
                  detail.numberH = parseFloat(detail.originalNumberH)* parseInt(detail.qty);
                }
              }           
              if(detail.type == 'MO'){
                if(detail.original){
                  detail.totalPrice = (parseFloat(detail.numberH == '' ? '0' : detail.numberH) * parseInt(detail.unitPrice)).toFixed(2);
                  detail.totalPriceWithoutMarge = (parseFloat(detail.numberH == '' ? '0' : detail.numberH) * parseFloat(detail.unitPrice));
                }else{
                  detail.totalPrice = (parseFloat(detail.numberH == '' ? '0' : detail.numberH) * parseInt(detail.unitPrice)* parseInt(detail.qty)).toFixed(2);
                  detail.totalPriceWithoutMarge = (parseFloat(detail.numberH == '' ? '0' : detail.numberH) * parseFloat(detail.unitPrice)* parseInt(detail.qty));
                }
              }else if( detail.type == 'Labor' ){
                detail.totalPrice = (parseFloat(detail.numberH == '' ? '0' : detail.numberH) * parseInt(detail.unitPrice) * parseInt(detail.qty)).toFixed(2);
                detail.totalPriceWithoutMarge = (parseFloat(detail.numberH == '' ? '0' : detail.numberH) * parseFloat(detail.unitPrice));
              }else if( detail.type == 'INTERIM'){
                detail.totalPrice = (parseFloat(detail.qty) * parseInt(detail.unitPrice)*(1+ parseInt(detail.marge)/100)).toFixed(2);
                detail.totalPriceWithoutMarge = (parseFloat(detail.numberH == '' ? '0' : detail.numberH) * parseFloat(detail.unitPrice));
              }
              else{
                detail.totalPrice = (parseInt(detail.qty) * parseFloat(detail.unitPrice) * (parseInt(detail.marge)/100 + 1)).toFixed(2);
                detail.totalPriceWithoutMarge = (parseInt(detail.qty) * parseFloat(detail.unitPrice));
              }     
              if(detail.type == 'INTERIM')     
                form.value.totalHoursForInterim += parseFloat(detail.qty)
              ouvrage.total += parseFloat(detail.totalPrice);    
              ouvrage.totalWithoutMarge += parseFloat(detail.totalPriceWithoutMarge);
              ouvrage.totalHour += parseFloat(detail.numberH == '' ? '0' : detail.numberH);
            })
          })
          zone.securityOuvrage.totalHour += ouvrage.totalHour;
          zone.securityOuvrage.totalPrice += ouvrage.total;          
          form.value.totalPriceWithoutMarge += ouvrage.totalWithoutMarge;
        })
        form.value.totalUnitPrice += zone.securityOuvrage.sumUnitPrice;
        form.value.totalHoursForSecurity += zone.securityOuvrage.totalHour;
        form.value.totalPriceForSecurity += zone.securityOuvrage.totalPrice;        
        // prestation ouvrages
        zone.prestationOuvrage.totalHour = 0;
        zone.prestationOuvrage.totalPrice = 0;
        zone.prestationOuvrage.sumUnitPrice = 0;                
        zone.prestationOuvrage.ouvrages.forEach(ouvrage=>{
          ouvrage.total = 0;
          ouvrage.totalHour = 0;
          ouvrage.qtyOuvrage = ouvrage.qty;
          ouvrage.totalWithoutMarge = 0;
          ouvrage.tasks.forEach(task=>{
            task.details.forEach(detail=>{
              detail.sumUnitPrice = parseInt(detail.unitPrice);
              zone.prestationOuvrage.sumUnitPrice += parseInt(detail.unitPrice);
              if(detail.original){
                if(detail.qtyOuvrage != ouvrage.qty){
                  detail.qtyOuvrage = ouvrage.qty;
                  detail.qty = parseInt(ouvrage.qty)*parseInt(detail.originalDetailQty);
                  detail.numberH = parseFloat(detail.originalNumberH)* parseInt(detail.qty);
                }
              }           
              if(detail.type == 'MO'){
                if(detail.original){
                  detail.totalPrice = (parseFloat(detail.numberH == '' ? '0' : detail.numberH) * parseInt(detail.unitPrice)).toFixed(2);
                  detail.totalPriceWithoutMarge = (parseFloat(detail.numberH == '' ? '0' : detail.numberH) * parseFloat(detail.unitPrice));
                }else{
                  detail.totalPrice = (parseFloat(detail.numberH == '' ? '0' : detail.numberH) * parseInt(detail.unitPrice)* parseInt(detail.qty)).toFixed(2);
                  detail.totalPriceWithoutMarge = (parseFloat(detail.numberH == '' ? '0' : detail.numberH) * parseFloat(detail.unitPrice)* parseInt(detail.qty));
                }                
              }else if( detail.type == 'Labor' ){
                detail.totalPrice = (parseFloat(detail.numberH == '' ? '0' : detail.numberH) * parseInt(detail.unitPrice) * parseInt(detail.qty)).toFixed(2);
                detail.totalPriceWithoutMarge = (parseFloat(detail.numberH == '' ? '0' : detail.numberH) * parseFloat(detail.unitPrice));
              }else if( detail.type == 'INTERIM'){
                detail.totalPrice = (parseFloat(detail.qty) * parseInt(detail.unitPrice)*(1+ parseInt(detail.marge)/100)).toFixed(2);
                detail.totalPriceWithoutMarge = (parseFloat(detail.numberH == '' ? '0' : detail.numberH) * parseFloat(detail.unitPrice));
              }
              else{
                detail.totalPrice = (parseInt(detail.qty) * parseFloat(detail.unitPrice) * (parseInt(detail.marge)/100 + 1)).toFixed(2);
                detail.totalPriceWithoutMarge = (parseInt(detail.qty) * parseFloat(detail.unitPrice));
              }     
              if(detail.type == 'INTERIM')     
                form.value.totalHoursForInterim += parseFloat(detail.qty)
              ouvrage.total += parseFloat(detail.totalPrice);    
              ouvrage.totalWithoutMarge += parseFloat(detail.totalPriceWithoutMarge);
              ouvrage.totalHour += parseFloat(detail.numberH == '' ? '0' : detail.numberH);
            })
          })
          zone.prestationOuvrage.totalHour += ouvrage.totalHour;
          zone.prestationOuvrage.totalPrice += ouvrage.total;            
          form.value.totalPriceWithoutMarge += ouvrage.totalWithoutMarge;          
        })
        form.value.totalUnitPrice += zone.prestationOuvrage.sumUnitPrice;
        form.value.totalHoursForPrestation += zone.prestationOuvrage.totalHour;
        form.value.totalPriceForPrestation += zone.prestationOuvrage.totalPrice;    
        form.value.totalDays = (form.value.totalHoursForPrestation + form.value.totalHoursForSecurity +  form.value.totalHoursForInstall)/8;
      })
    };
    const adjustHours = ()=>{
      let currentTotalHours = form.value.totalDays*8;
      let previousTotalHours = form.value.totalHoursForPrestation + form.value.totalHoursForSecurity +  form.value.totalHoursForInstall;
      if(parseFloat(currentTotalHours) != parseFloat(previousTotalHours)){
        let MOCount = 0;
        let deltaHours = currentTotalHours - previousTotalHours;
        console.log(deltaHours);
        // get MO Product count
        form.value.zones.forEach(zone=>{
          zone.prestationOuvrage.ouvrages.forEach(ouvrage=>{
            ouvrage.tasks.forEach(task=>{
              task.details.forEach(detail=>{
                if(detail.type == 'MO'){
                  MOCount++;
                }
              })
            })
          })
        })
        // adjusting hours
        form.value.zones.forEach(zone=>{
          zone.prestationOuvrage.ouvrages.forEach(ouvrage=>{
            ouvrage.tasks.forEach(task=>{
              task.details.forEach(detail=>{
                if(detail.type == 'MO'){
                  detail.numberH = parseFloat(detail.numberH) + (deltaHours/MOCount);
                }
              })
            })
          })
        })
        updateAllValues();
      }
    }
    onMounted(()=>{
      axios.post('/get-devis/'+route.params.id).then((res)=>{
        taxes.value = res.data.taxes;
        units.value = res.data.units;
        roofAccesses.value = res.data.roofAccesses;
        gedCats.value = res.data.gedCats
        let devisData = res.data.devis
        if(devisData.address.lat != null){
            devisData.address.lat = parseFloat(devisData.address.lat.replace(/[a-zA-Z()]/g, ""));
        }else{
            devisData.address.lat = 48.85560142492883;
        }
        if(devisData.address.lon != null){
            devisData.address.lon = parseFloat(devisData.address.lon.replace(/[a-zA-Z()]/g, ""));
        }else{
            devisData.address.lon = 2.3491914978706396;
        }   
        form.value = devisData;
        updateAllValues();
      }).catch((error)=>{
        console.log(error);
      })
    })
    const addFileToGed = (zone, catId)=>{
      zoneIndex.value = zone;
      gedCatId.value = catId;
      file.value.click();
    }
    const previewFile = ()=>{
      let images = file.value.files;
      for (let i = 0; i < images.length; i++) {
        let type = images[i].type.split("/")[0];
        if(type == 'image'){
          type = 'png';
        }else if(type == 'audio'){
          type = 'm4a';
        }else{
          type = 'm4v';
        }        
        let reader = new FileReader();
        reader.onload = (e) => {
          form.value.zones[zoneIndex.value].gedCats[gedCatId.value][0].items.push({
            url: '',
            base64data: reader.result,
            fileName: images[i].name,
            url: reader.result,
            id: '',
            type: type
          })
        };
        reader.readAsDataURL(images[i]);
      }      
    }
    const removeImage = (zIndex, id, index)=>{
      if(form.value.orderStatus.type  != 'COMMANDE'){
        form.value.zones[zIndex].gedCats[id][0].items =  form.value.zones[zIndex].gedCats[id][0].items.filter((item, i)=>{
          return i != index;
        })
      }else{
        store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
          type: 'danger',
          message: 'you can not edit COMMANDE',
          ttl: 5,
        });
      }
    }
    const viewGedMedia = (content, type)=>{
      zoomModal.value.openModal(content, type);
    }
    const togglePanel = (eleID)=>{
      document.getElementById(eleID).classList.toggle('open');
    }
    const openSecuriteModal = (zIndex)=>{
      if(form.value.orderStatus.type  != 'COMMANDE'){
        securiteModal.value.openModal(zIndex);
      }else{
        store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
          type: 'danger',
          message: 'you can not edit COMMANDE',
          ttl: 5,
        });
      }      
     
    }
    const openInstallationModal = (zIndex)=>{
      if(form.value.orderStatus.type  != 'COMMANDE'){
        installationModal.value.openModal(zIndex);
      }else{
        store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
          type: 'danger',
          message: 'you can not edit COMMANDE',
          ttl: 5,
        });
      }       
    }
    const openPrestationModal = (zIndex)=>{
      if(form.value.orderStatus.type  != 'COMMANDE'){
        prestationModal.value.openModal(zIndex);
      }else{
        store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
          type: 'danger',
          message: 'you can not edit COMMANDE',
          ttl: 5,
        });
      } 
    }
    const openSupplierModal = (zIndex, ouvrageType, ouvrageId, taskId)=>{
      if(form.value.orderStatus.type  != 'COMMANDE'){
        supplierModal.value.openModal(zIndex, ouvrageType, ouvrageId, taskId);
      }else{
        store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
          type: 'danger',
          message: 'you can not edit COMMANDE',
          ttl: 5,
        });
      }       
    }
    const openProductModal = (zIndex, ouvrageType, ouvrageId, taskId)=>{
      if(form.value.orderStatus.type  != 'COMMANDE'){
        productModal.value.openModal(zIndex, ouvrageType, ouvrageId, taskId);
      }else{
        store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
          type: 'danger',
          message: 'you can not edit COMMANDE',
          ttl: 5,
        });
      }       
    }
    
    const openInterimModal = (zIndex, ouvrageType, ouvrageId, taskId)=>{
      if(form.value.orderStatus.type  != 'COMMANDE'){
        interimModal.value.openModal(zIndex, ouvrageType, ouvrageId, taskId);
      }else{
        store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
          type: 'danger',
          message: 'you can not edit COMMANDE',
          ttl: 5,
        });
      }       
    }

    const openLaborModal = (zIndex, ouvrageType, ouvrageId, taskId)=>{
      if(form.value.orderStatus.type  != 'COMMANDE'){
        laborModal.value.openModal(zIndex, ouvrageType, ouvrageId, taskId);
      }else{
        store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
          type: 'danger',
          message: 'you can not edit COMMANDE',
          ttl: 5,
        });
      }       
    }
    const openTaskModal = (zIndex, ouvrageType, ouvrageId)=>{
      if(form.value.orderStatus.type  != 'COMMANDE'){
        taskModal.value.openModal(zIndex, ouvrageType, ouvrageId);
      }else{
        store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
          type: 'danger',
          message: 'you can not edit COMMANDE',
          ttl: 5,
        });
      }       
    }
    const openPdfModal = (data)=>{
      pdfModal.value.openModal(data);
    }
        
    const addNewCustomer = ()=>{
      router.push({
        name: "CreateCustomer"
      })
    }

    const addNewAddress = ()=>{
      addressModal.value.openModal(form.value.customer.id)
    }

    const addedNewAddress = (data)=>{
      customerAddresses.value.push(data);
    }
    
    const selectedCustomer = (data)=>{
      // move on to "addess choose step"
      devisCreateStep.value = 'choose_address';
      // set customer value to devis form
      form.value.customer = data;

      // loading customer addresses
      store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`, [true, 'Chargement des adresses des clients..']);
      axios.post('/get-customer-addresses', { customer_id: data.id }).then((res)=>{
        customerAddresses.value = res.data;
      }).catch((error)=>{
        console.log(error);
      }).finally(()=>{
        store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`);
      })
    }
    // handler to choose other customer
    const chooseOtherCustomer = ()=>{
      devisCreateStep.value = 'choose_customer';
    }
    // handler to choose a customer address
    const chooseCustomerAddress = (data)=>{
      devisCreateStep.value = 'create_devis';
      if(data.lat != null){
          data.lat = parseFloat(data.lat.replace(/[a-zA-Z()]/g, ""));
      }else{
          data.lat = 48.85560142492883;
      }
      if(data.lon != null){
          data.lon = parseFloat(data.lon.replace(/[a-zA-Z()]/g, ""));
      }else{
          data.lon = 2.3491914978706396;
      }         
      form.value.address = data;
    }

    const goToStep = (index)=>{
      if(index == 0){
        devisCreateStep.value = 'choose_customer';
      }else{
        devisCreateStep.value = 'choose_address';
      }
    }
    // get details for a ouvrage selected
    const selectedOuvrage = (data)=>{
      store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`, [true, 'Ajout de l`Ouvrage sélectionné..']);
      axios.post('/get-ouvrage', { id: data.ouvrageId, qtyOuvrage: data.qtyOuvrage, customerId: form.value.customer.id }).then((res)=>{
        if( data.type == 'installation' ){
          form.value.zones[data.zoneIndex].installOuvrage.ouvrages.push(res.data);
          document.querySelector('.installation-ouvrages').classList.add('open');
        }
        if( data.type == 'security' ){
          form.value.zones[data.zoneIndex].securityOuvrage.ouvrages.push(res.data);
          document.querySelector('.security-ouvrages').classList.add('open');
        }
        if(data.type == 'prestation'){
          form.value.zones[data.zoneIndex].prestationOuvrage.ouvrages.push(res.data);
          document.querySelector('.prestation-ouvrages').classList.add('open');
        }        
        updateAllValues();
      }).catch((error)=>{
        console.log(error);
      }).finally(()=>{
        store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`);
      });
    }

    // listen selecedProduct event
    const selectedProduct = (product)=>{
      if(product.ouvrageType == 1){
        form.value.zones[product.zoneIndex].installOuvrage.ouvrages[product.ouvrageId].tasks[product.taskId].details.push({
          qty: 1,
          tax: form.value.customer.taxId,
          unitPrice: parseFloat(product.unitPrice).toFixed(2),
          marge: 8,
          type: product.type,
          name: product.name,
          unit: product.unit,
          qtyOuvrage: '',
          totalPrice: product.unitPrice * parseInt(product.unit),
          numberH: 0,
          productId: product.productId,
          unit_id: product.unitId,
        });
      }
      if(product.ouvrageType == 2){
        form.value.zones[product.zoneIndex].securityOuvrage.ouvrages[product.ouvrageId].tasks[product.taskId].details.push({
          qty: 1,
          tax: form.value.customer.taxId,
          unitPrice: parseFloat(product.unitPrice).toFixed(2),
          marge: 8,
          type: product.type,
          name: product.name,
          unit: product.unit,
          qtyOuvrage: '',
          totalPrice: product.unitPrice * parseInt(product.unit),
          numberH: 0,
          productId: product.productId,
          unit_id: product.unitId,
        });
      }
      if(product.ouvrageType == 3){
        form.value.zones[product.zoneIndex].prestationOuvrage.ouvrages[product.ouvrageId].tasks[product.taskId].details.push({
          qty: 1,
          tax: form.value.customer.taxId,
          unitPrice: parseFloat(product.unitPrice).toFixed(2),
          marge: 8,
          type: product.type,
          name: product.name,
          unit: product.unit,
          qtyOuvrage: '',
          totalPrice: product.unitPrice,
          numberH: 0,
          productId: product.productId,
          unit_id: product.unitId,
        });
      }
      updateAllValues();
    }

    // listen selecedSupplier event
    const selectedSupplier = (supplier)=>{
      if(supplier.ouvrageType == 1){
        form.value.zones[supplier.zoneIndex].installOuvrage.ouvrages[supplier.ouvrageId].tasks[supplier.taskId].details.push({
          qty: 1,
          tax: form.value.customer.taxId,
          unitPrice: supplier.unitPrice,
          marge: 8,
          type: supplier.type,
          supplierId: supplier.supplier_id,
          datesupplier: supplier.datesupplier,
          name: supplier.name,
          base64: supplier.base64,
          unit: 'UN',
          qtyOuvrage: supplier.qtyOuvrage,
          totalPrice: 0,
          numberH: 0,
          productId: 0,
          unit_id: 0,
        });
      }
      if(supplier.ouvrageType == 2){
        form.value.zones[supplier.zoneIndex].securityOuvrage.ouvrages[supplier.ouvrageId].tasks[supplier.taskId].details.push({
          qty: 1,
          tax: form.value.customer.taxId,
          unitPrice: supplier.unitPrice,
          marge: 8,
          supplierId: supplier.supplier_id,
          datesupplier: supplier.datesupplier,
          type: supplier.type,
          name: supplier.name,
          base64: supplier.base64,
          unit: 'UN',
          qtyOuvrage: supplier.qtyOuvrage,
          totalPrice: 0,
          numberH: 0,
          productId: 0,
          unit_id: 0,
        });
      }
      if(supplier.ouvrageType == 3){
        form.value.zones[supplier.zoneIndex].prestationOuvrage.ouvrages[supplier.ouvrageId].tasks[supplier.taskId].details.push({
          qty: 1,
          tax: form.value.customer.taxId,
          unitPrice: supplier.unitPrice,
          marge: 8,
          supplierId: supplier.supplier_id,
          datesupplier: supplier.datesupplier,
          type: supplier.type,
          name: supplier.name,
          base64: supplier.base64,
          unit: 'UN',
          qtyOuvrage: supplier.qtyOuvrage,
          totalPrice: 0,
          numberH: 0,
          productId: 0,
          unit_id: 0,
        });
      }
      updateAllValues();
    }

    const selectedLabor = (labor)=>{
      if(labor.ouvrageType == 1){
        form.value.zones[labor.zoneIndex].installOuvrage.ouvrages[labor.ouvrageId].tasks[labor.taskId].details.push({
          qty: 1,
          tax: form.value.customer.taxId,
          unitPrice: parseFloat(labor.price).toFixed(2),
          marge: 8,
          type: 'Labor',
          name: 'main d\'œuvre ouvrier chantier',
          unit: 'HR',
          qtyOuvrage: labor.qtyOuvrage,
          totalPrice: labor.total,
          numberH: labor.numberH,
          productId: labor.productId,
          unit_id: labor.unitId,
        });
      }
      if(labor.ouvrageType == 2){
        form.value.zones[labor.zoneIndex].securityOuvrage.ouvrages[labor.ouvrageId].tasks[labor.taskId].details.push({
          qty: 1,
          tax: form.value.customer.taxId,
          unitPrice: parseFloat(labor.price).toFixed(2),
          marge: 8,
          type: 'Labor',
          name: 'main d\'œuvre ouvrier chantier',
          unit: 'HR',
          qtyOuvrage: labor.qtyOuvrage,
          totalPrice: labor.total,
          numberH: labor.numberH,
          productId: labor.productId,
          unit_id: labor.unitId,
        });
      }
      if(labor.ouvrageType == 3){
        form.value.zones[labor.zoneIndex].prestationOuvrage.ouvrages[labor.ouvrageId].tasks[labor.taskId].details.push({
          qty: 1,
          tax: form.value.customer.taxId,
          unitPrice: parseFloat(labor.price).toFixed(2),
          marge: '',
          type: 'Labor',
          name: 'main d\'œuvre ouvrier chantier',
          unit: 'HR',
          qtyOuvrage: labor.qtyOuvrage,
          totalPrice: labor.total,
          numberH: labor.numberH,
          productId: labor.productId,
          unit_id: labor.unitId,
        });
      }
      updateAllValues();
    }

    const selectedInterim = (interim)=>{
      if(interim.ouvrageType == 1){
        form.value.zones[interim.zoneIndex].installOuvrage.ouvrages[interim.ouvrageId].tasks[interim.taskId].details.push({
          qty: interim.numberH,
          tax: form.value.customer.taxId,
          societe: interim.societe,
          unitPrice: parseFloat(interim.price).toFixed(2),
          marge: 8,
          type: 'INTERIM',
          name: 'INTERIM',
          unit: 'HR',
          qtyOuvrage: interim.qtyOuvrage,
          totalPrice: interim.total,
          numberH: 0,
          productId: interim.productId,
          unit_id: interim.unitId,
        });
      }
      if(interim.ouvrageType == 2){
        form.value.zones[interim.zoneIndex].securityOuvrage.ouvrages[interim.ouvrageId].tasks[interim.taskId].details.push({
          qty: interim.numberH,
          tax: form.value.customer.taxId,
          societe: interim.societe,
          unitPrice: parseFloat(interim.price).toFixed(2),
          marge: 8,
          type: 'INTERIM',
          name: 'INTERIM',
          unit: 'HR',
          qtyOuvrage: interim.qtyOuvrage,
          totalPrice: interim.total,
          numberH: 0,
          productId: interim.productId,
          unit_id: interim.unitId,
        });
      }
      if(interim.ouvrageType == 3){
        form.value.zones[interim.zoneIndex].prestationOuvrage.ouvrages[interim.ouvrageId].tasks[interim.taskId].details.push({
          qty: interim.numberH,
          tax: form.value.customer.taxId,
          societe: interim.societe,
          unitPrice: parseFloat(interim.price).toFixed(2),
          marge: 8,
          type: 'INTERIM',
          name: 'INTERIM',
          unit: 'HR',
          qtyOuvrage: interim.qtyOuvrage,
          totalPrice: interim.total,
          numberH: 0,
          productId: interim.productId,
          unit_id: interim.unitId,
        });
      }
      updateAllValues();
    }
    // listen selecedTask event
    const selectedTask = (task)=>{
      if(task.ouvrageType == 1){
        form.value.zones[task.zoneIndex].installOuvrage.ouvrages[task.ouvrageId].tasks.push({
          name: task.name,
          customerText: task.customerText,
          textchargeaffaire: task.textchargeaffaire,
          unit_id: task.unit_id,
          qty: task.qty,
          details: []
        });
      }
      if(task.ouvrageType == 2){
        form.value.zones[task.zoneIndex].securityOuvrage.ouvrages[task.ouvrageId].tasks.push({
          name: task.name,
          customerText: task.customerText,
          textchargeaffaire: task.textchargeaffaire,
          unit_id: task.unit_id,
          qty: task.qty,
          details: []
        });        
      }
      if(task.ouvrageType == 3){
        form.value.zones[task.zoneIndex].prestationOuvrage.ouvrages[task.ouvrageId].tasks.push({
          name: task.name,
          customerText: task.customerText,
          textchargeaffaire: task.textchargeaffaire,
          unit_id: task.unit_id,
          qty: task.qty,
          details: []
        });        
      }
      updateAllValues();
    }
    // add a zone
    const addZone = (index)=>{
      if(form.value.orderStatus.type  != 'COMMANDE'){
        form.value.zones.push(
          {
            name: 'zone '+ (form.value.zones.length+1),
            roofAccess: 0,
            height: '',
            edit: false,
            gedCats: gedCats.value,
            installOuvrage: {
              name: 'Installation',
              edit: false,
              totalHour: 0,
              unitPrice: 0,
              totalPrice: 0,
              ouvrages: []
            },
            securityOuvrage: {
              name: 'Sécurité',
              edit: false,
              totalHour: 0,
              unitPrice: 0,
              totalPrice: 0,
              ouvrages: []
            },
            prestationOuvrage: {
              name: 'Prestations',
              edit: false,
              totalHour: 0,
              unitPrice: 0,
              totalPrice: 0,
              ouvrages: []
            },
          } 
        );
      }else{
        store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
          type: 'danger',
          message: 'you can not edit COMMANDE',
          ttl: 5,
        });
      }       

    }

    // remove a zone
    const removeZone = (selectedIndex)=>{
      if(form.value.orderStatus.type  != 'COMMANDE'){
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
            form.value.zones = form.value.zones.filter((item, index)=>{
              return index != selectedIndex;
            });
            Swal.fire(
              'Supprimé!',
              "Louvrage a été supprimé.",
              'success'
            )          
            updateAllValues();
          }
        });      
      }else{
        store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
          type: 'danger',
          message: 'you can not edit COMMANDE',
          ttl: 5,
        });
      }       
    }

    // activate the ouvrage
    const activeOuvrage = (event)=>{
      event.target.classList.toggle('active');
      document.getElementById(event.target.getAttribute('data-id')).classList.toggle('active');
    }
    // activate the task
    const activeOuvrageTask = (event)=>{
      event.target.classList.toggle('active');
      document.getElementById(event.target.getAttribute('data-id')).classList.toggle('show');
    }
    // remove ouvrage from ouvrages
    const removeOuvrage = (zoneIndex, ouvrageType, ouvrageIndex)=>{
      if(form.value.orderStatus.type  != 'COMMANDE'){
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
            if(ouvrageType == 1){
              form.value.zones[zoneIndex].installOuvrage.ouvrages = form.value.zones[zoneIndex].installOuvrage.ouvrages.filter((item, index)=>{
                return ouvrageIndex != index;
              });
            }
            if(ouvrageType == 2){
              form.value.zones[zoneIndex].securityOuvrage.ouvrages = form.value.zones[zoneIndex].securityOuvrage.ouvrages.filter((item, index)=>{
                return ouvrageIndex != index;
              });
            }
            if(ouvrageType == 3){
              form.value.zones[zoneIndex].prestationOuvrage.ouvrages = form.value.zones[zoneIndex].prestationOuvrage.ouvrages.filter((item, index)=>{
                return ouvrageIndex != index;
              });            
            }
            Swal.fire(
              'Supprimé!',
              "Louvrage a été supprimé.",
              'success'
            )          
            updateAllValues();
          }
        });
      }else{
        store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
          type: 'danger',
          message: 'you can not edit COMMANDE',
          ttl: 5,
        });
      }       
    }
    // remove detail from task
    const removeOuvrageDetail = (zoneIndex, ouvrageType, ouvrageIndex, taskIndex, detailIndex)=>{
      if(form.value.orderStatus.type  != 'COMMANDE'){
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
            if(ouvrageType == 1){
              form.value.zones[zoneIndex].installOuvrage.ouvrages[ouvrageIndex].tasks[taskIndex].details = form.value.zones[zoneIndex].installOuvrage.ouvrages[ouvrageIndex].tasks[taskIndex].details.filter((item, index)=>{
                return detailIndex != index;
              })
            }
            if(ouvrageType == 2){
              form.value.zones[zoneIndex].securityOuvrage.ouvrages[ouvrageIndex].tasks[taskIndex].details = form.value.zones[zoneIndex].securityOuvrage.ouvrages[ouvrageIndex].tasks[taskIndex].details.filter((item, index)=>{
                return detailIndex != index;
              })
            }
            if(ouvrageType == 3){
              form.value.zones[zoneIndex].prestationOuvrage.ouvrages[ouvrageIndex].tasks[taskIndex].details = form.value.zones[zoneIndex].prestationOuvrage.ouvrages[ouvrageIndex].tasks[taskIndex].details.filter((item, index)=>{
                return detailIndex != index;
              })            
            }
            Swal.fire(
              'Supprimé!',
              'Le détail a été supprimé.',
              'success'
            )        
            updateAllValues();
          }
        })        
      }else{
        store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
          type: 'danger',
          message: 'you can not edit COMMANDE',
          ttl: 5,
        });
      }       
    }
    // update Devis
    const updateDevis = ()=>{
      store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`, [true, 'Mettez à jour le devis....']);
      axios.post('/update-devis/'+route.params.id, form.value).then((res)=>{
        console.log(res.data);
        if(res.data.success){
          Swal.fire({
            title: 'Success',
            text: "DEVIS updated successfully!",
            icon: 'success',
          });
          router.push({
            name: 'DevisDetail',
            params: { id: route.params.id }
          });          
        }
      }).catch((error)=>{
        console.log(error);
      }).finally(()=>{
        store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`);
      })
    }
    // Generate PDF for DEVIS
    const PDFDevis = ()=>{

    }
    // open empty ouvrage modal
    const openEmptyOuvrageModal = (data)=>{
      emptyOuvrageModal.value.openModal(data);
    }
    // emits for selected ouvrage
    const selectedEmptyOuvrage = (type, zIndex, emptyOuvrage)=>{
      if( type == 1 ){
        form.value.zones[zIndex].installOuvrage.ouvrages.push(emptyOuvrage);
        document.querySelector('.installation-ouvrages').classList.add('open');
      }
      if( type == 2 ){
        form.value.zones[zIndex].securityOuvrage.ouvrages.push(emptyOuvrage);
        document.querySelector('.security-ouvrages').classList.add('open');
      }
      if(type == 3){
        form.value.zones[zIndex].prestationOuvrage.ouvrages.push(emptyOuvrage);
        document.querySelector('.prestation-ouvrages').classList.add('open');
      }  
      updateAllValues();     
    }
    // open empty product modal
    const openEmptyProductModal = (data)=>{
      emptyProductModal.value.openModal(data, units.value);
    }
    // emits for selected ouvrage
    const selectedEmptyProduct = (data, emptyProduct)=>{

      if(data.ouvrageType == 1){
        form.value.zones[data.zoneIndex].installOuvrage.ouvrages[data.ouvrageId].tasks[data.taskId].details.push({
          qty: parseFloat(emptyProduct.qty),
          tax: 0,
          unitPrice: parseFloat(emptyProduct.productPrice),
          marge: 8,
          type: emptyProduct.type,
          name: emptyProduct.name,
          unit: emptyProduct.unit,
          qtyOuvrage: '',
          totalPrice: parseFloat(emptyProduct.productPrice)*parseFloat(emptyProduct.qty)* 1.08,
          numberH: 0,
          productId: 0,
          unit_id: 0,
        });
      }
      if(data.ouvrageType == 2){
        form.value.zones[data.zoneIndex].securityOuvrage.ouvrages[data.ouvrageId].tasks[data.taskId].details.push({
          qty: parseFloat(emptyProduct.qty),
          tax: 0,
          unitPrice: parseFloat(emptyProduct.productPrice),
          marge: 8,
          type: emptyProduct.type,
          name: emptyProduct.name,
          unit: emptyProduct.unit,
          qtyOuvrage: '',
          totalPrice: parseFloat(emptyProduct.productPrice)*parseFloat(emptyProduct.qty)* 1.08,
          numberH: 0,
          productId: 0,
          unit_id: 0,
        });
      }
      if(data.ouvrageType == 3){
        form.value.zones[data.zoneIndex].prestationOuvrage.ouvrages[data.ouvrageId].tasks[data.taskId].details.push({
          qty: parseFloat(emptyProduct.qty),
          tax: 0,
          unitPrice: parseFloat(emptyProduct.productPrice),
          marge: 8,
          type: emptyProduct.type,
          name: emptyProduct.name,
          unit: emptyProduct.unit,
          qtyOuvrage: '',
          totalPrice: parseFloat(emptyProduct.productPrice)*parseFloat(emptyProduct.qty)* 1.08,
          numberH: 0,
          productId: 0,
          unit_id: 0,
        });
      }      
      updateAllValues();
    }
    return {
      route,
      breadcrumbs,
      taxes,
      units,
      roofAccesses,
      customerAddresses,
      devisCreateStep,
      form,
      file,
      zoomModal,
      securiteModal,
      installationModal,
      prestationModal,
      supplierModal,
      productModal,
      addressModal,
      laborModal,
      interimModal,
      taskModal,
      emptyOuvrageModal,
      emptyProductModal,
      pdfModal,
      addFileToGed,
      previewFile,
      removeImage,
      viewGedMedia,
      togglePanel,
      openSecuriteModal,
      openInstallationModal,
      openPrestationModal,
      openSupplierModal,
      openInterimModal,
      openLaborModal,
      openProductModal,
      openTaskModal,
      openPdfModal,
      adjustHours,
      goToStep,
      addNewCustomer,
      selectedCustomer,
      chooseOtherCustomer, // handler to choose other customer
      chooseCustomerAddress, // handler to choose customer addess
      addNewAddress, // handler to add a new address
      addedNewAddress, // address modal emits
      selectedOuvrage, // Ouvrage was choosed
      selectedTask,
      selectedProduct,
      selectedInterim,
      selectedLabor,
      selectedSupplier,
      addZone,
      removeZone,
      activeOuvrage,
      activeOuvrageTask,
      removeOuvrage,
      removeOuvrageDetail,
      updateDevis,
      PDFDevis,
      openEmptyOuvrageModal,
      selectedEmptyOuvrage,
      openEmptyProductModal,
      selectedEmptyProduct,
      updateAllValues
    }
  },
}
</script>
<style lang="scss" scoped>
  /* width */
  ::-webkit-scrollbar {
    width: 9px;
  }
  /* Track */
  ::-webkit-scrollbar-track {
    background: #E0E0E0; 
  }
  /* Handle */
  ::-webkit-scrollbar-thumb {
    background: #47454B; 
    border-radius: 6px;
  }
  .bold-title{
    font-family: "Mulish ExtraBold";
    font-weight: 800;
    font-size: 20px;
    line-height: 18px;
  }
  .title{
    font-family: "Mulish SemiBold";
    font-weight: 600;
    font-size: 20px;
    line-height: 18px;
  }
  .sub-title{
    font-family: "Mulish Regular";
    font-weight: 400;
    font-size: 15px;
    color: #324A59;
    line-height: 194.62%;
  }
  .small-title{
    font-family: "Mulish SemiBold";
    font-size: 10px;
    color: rgba(0, 24, 51, 0.22);
    line-height: 12.55px;    
  }
  .customer-addresses{
    height: 400px;
    overflow-y: auto;
  }

  .btn-save{
    width: 190px;
    background: #3E9A4D;
    border-radius: 10px;
    border: none;
    outline: none;
  }
  .btn-pdf{
    width: 190px;
    background: rgba(255, 0, 0, 0.7);    
    border-radius: 10px;
    border: none;
    outline: none;
  }
  .table > :not(:first-child){
    border-top: 0 !important;
  }
  .ouvrage-section{
    .form-control{
      border: none;
      outline: none;
      box-shadow: none;
      background: transparent;
      text-align: center;
      padding: 0;
      &:focus{
        outline: none;
        box-shadow: none;        
      }
    }
  }
  .custom-option{
    .option-icon{
      display: block;
      width: 13px;
      height: 13px;
      padding: 3px;
      border-radius: 50%;
      border: solid 1px #E8581B;
      .option-icon-dot{
        display: block;
        height: 100%;
        border-radius: 50%;
        background-color: transparent;
      }
    }
    &.active{
      .option-icon-dot{
        background-color: #E8581B;
      }      
    }
    *{
      pointer-events: none;
    }
  }
  .task-body{
    display: none;
    &.show{
      display: block;
    }
  }
  .main-view{
    padding: 60px 10px 0 0;
    .devis-panel{
      margin-top: 28px;
      .left-panel{
        // width: 1019px;
        width: 60%;
        height: fit-content;
        .customer-section{
          .customer-pic{
            width: 42px;
            height: 42px;
          }
        }
        .zone-section{
          .ged-image{
            background-size: contain;
            width: 55px;
            height: 55px;
            .image-overlayer{
              visibility: hidden;
            }
            &:hover{
              .image-overlayer{
                visibility: visible;
                background: rgba(0, 0, 0, .2);
                transition: background ease-out .3s;
              }
            }
          }
        }
        .ouvrage-section{
          .ouvrage-body{
            display: none;
            .details-table{
              td{
                border: solid 1px #C4C4C4;
              }
              td:first-child{
                border: none;
              }
              td:last-child{
                border: none;
              }
            }
          }
        }
        .ouvrage-section.open{
          .ouvrage-body{
            display: block;
            transition: all ease-in .3s;
          }
        }
      }
      .right-panel{
        // width: 704px;
        width: 40%;
        height: fit-content;
        .section{
          padding: 15px 25px;
        }
      }
    }
  }
</style>