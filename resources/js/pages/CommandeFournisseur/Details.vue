<template>

    <item-detail-panel :showloader="showloader">

        <div class="row mt-2" v-if="show">
            
            <div class="col-4 d-flex align-items-center gap-4">
                
                <page-title 
                    icon="pdf" 
                    :name="`N° ${details.id}`" 
                    class="almarai_extrabold_normal_normal"
                />

                <a 
                    href="#" 
                    class="text-editer"
                >
                    Editer
                </a>

            </div>

            <div class="col-8 d-flex justify-content-end align-items-center gap-5" style="padding-right: 5rem;">

                <span 
                    class="tag"
                    :style="{ 'background': details.state?.color }"
                >
                    {{ details.state?.name }}
                </span>

                <span class="d-flex align-items-center gap-2">
                    <span>Actif:</span> <Icon name="check" color="green" v-if="details.supplier?.actif" />
                    <Icon name="times" color="red" v-else />
                </span>

            </div>

        </div>
    

        <div class="responsable-section d-flex justify-content-between align-items-center mt-3" v-if="show">

            <div class="d-flex flex-column gap-1">
                <span class="title-label">Contact</span>
                <span class="detail">{{ contact || '--/--' }}</span>
            </div>

            <div class="d-flex flex-column gap-1">
                <span class="title-label">Date créaction:</span>
                <span class="detail">
                    {{ 
                        details.created_at != null 
                        ? moment(details.created_at).format('Y/MM/DD H:m')
                        : '--/--' 
                    }}
                </span>
            </div>
    
            <div class="d-flex flex-column gap-1">
                <span class="title-label">Type</span>
                <span class="detail">{{ details?.supplier?.type?.name || '--/--' }}</span>
            </div>
    
        </div>

        <supplier-order-details 
            v-if="show" 
            :supplierOrderId="id" 
            @trigger="triggerNewSupplierOrderDetail" 
        />
    
        <div class="od_actions my-3" v-if="show">
            <button 
                class="btn btn-outline-dark almarai_700_normal" 
                @click="goto()"
            >
                Editer
            </button>
            <button 
                class="btn btn-outline-secondary almarai_700_normal"  
            >
                Fermer
            </button>   
        </div>
    
    
    </item-detail-panel>
    
    <simple-modal-popup 
        v-model="modal.show" 
        :title="modal.title" 
        @modalconfirm="commitAction" 
        @modalclose="closeModal"
    >
    
        <template v-if="modal.type == 'supplierOrder'">
    
            <div class="container">

                <div class="d-flex justify-content-end">
                    <a href="#" class="text-editer" @click.prevent="$router.push({ name: 'create-product' })">
                        Produit Vide
                    </a>
                </div>
    
                <div class="row mb-3">

                    <div class="col-8">
                        <label>NOM*</label>
                        <SearchProduct
                            name="search" 
                            @selected="selectedProduct" 
                            :droppos="{ top: 'auto', right: 'auto', bottom: 'auto', left: '0', transformOrigin: 'top right' }" 
                        ></SearchProduct>  
                    </div>

                    <div class="col-2">
                        <label for="">Prix</label>
                        <input type="text" class="form-control" v-model="supplierOrder.price">
                    </div> 
    
    
                    <div class="col-2">
    
                        <label for="">Quantite</label>
                        <input type="number" class="form-control" v-model="supplierOrder.qty">
                    
                    </div>

                </div>
    
                <div class="row mb-3">
    
                    <div class="col-6">

                        <label for="">TYPE</label>
                        <select-box
                            placeholder="TYPE"
                            :options="supplierOrderTypes" 
                            name="productsType" 
                            v-model="supplierOrder.type"
                        />    

                    </div> 

                    <div class="col-6">

                        <label for="">UNITE</label>
                        <select-box
                            placeholder="UNITE"
                            :options="productUnits" 
                            name="productUnits" 
                            v-model="supplierOrder.unit"
                        />    

                    </div> 
                    
    
                </div>
    
            </div>
    
        </template>
         
    </simple-modal-popup>
    
</template>
    
<script>

    import moment from 'moment'
    import { computed, onMounted, ref, unref, h, watch, reactive } from 'vue';
    import { useRoute, useRouter } from 'vue-router'
    import { useStore } from 'vuex';
    import ItemDetailPanel from '../../components/miscellaneous/ItemListTable/ItemDetailPanel.vue'
    import { 

        TOASTER_CLEAR_TOASTS, 
        TOASTER_MESSAGE, 
        TOASTER_MODULE, 
        LOADER_MODULE,
        HIDE_LOADER,
        DISPLAY_LOADER,
        COMMANDE_FOURNISSEUR_LIST_MODULE,
        COMMANDE_FOURNISSEUR_GET_SUPPLIER_ORDER,
        GET_PRODUCT_UNITS,
        FOURNISSEUR_COMMANDE_CREATE_SUPPLIER_ORDER
    
    } from '../../store/types/types'

    import { formatPrice, formatDate, br, isFloat } from '../../components/helpers/helpers';
    import Swal from 'sweetalert2';
    import DatePicker from '../../components/miscellaneous/DatePicker.vue';
    import { Money3Component } from 'v-money3';
    import { mask } from 'vue-the-mask';
    import Icon from '../../components/miscellaneous/Icon.vue'
    import SupplierOrderDetails from '../../components/CommandeFournisseur/supplierOrderDetails.vue'
    import SearchProduct from '../../components/CommandeFournisseur/SearchProduct'

    
    export default {

        name: "CommandeFournisseurDetail",

        components: { 
            ItemDetailPanel, 
            DatePicker, 
            money3: Money3Component, 
            Icon, 
            SupplierOrderDetails,
            SearchProduct
        },
        
        directives: { mask },

        props: {
            id: {
                required: true,
                type: [String, Number]
            }
        },

        setup(props) {

            const route=useRoute()
            const router=useRouter()
            const store=useStore()
            const show=ref(false)
            const showloader=ref(false)

            const moneyconfig = ref({
                masked: false,
                prefix: '',
                suffix: '€',
                thousands: ',',
                decimal: '.',
                precision: 2,
                disableNegative: false,
                disabled: false,
                min: null,
                max: null,
                allowBlank: false,
                minimumNumberOfCharacters: 0,
            })

            const modal = reactive({
                show: false,
                title: '',
                type: 'facturation'
            })
            
            const supplierOrder = reactive({
                name: '',
                product: '',
                price: '',
                unit: 0,
                qty: 0,
                type: 0,
            })

            const supplierOrderTypes = computed(() => {
                return [
                    { value: 'produit', display: 'PRODUIT' },
                    { value: 'mo', display: 'MO' }
                ]
            })

            const details = computed(() => store.getters[`${COMMANDE_FOURNISSEUR_LIST_MODULE}details`])

            const contact = computed(() => {
                return details.value?.supplier?.contactname || '' 
                + ' ' + details.value?.supplier?.phone || '' 
                + ' ' + details.value?.supplier?.email
            })

            const selectedProduct = (product) => {
                supplierOrder.product = product.id
                supplierOrder.name = product.name
            }

            const productUnits = computed(() => store.getters[`${COMMANDE_FOURNISSEUR_LIST_MODULE}productUnits`])

            const getProductUnits = () => {
                try {
                    store.dispatch(`${COMMANDE_FOURNISSEUR_LIST_MODULE}${GET_PRODUCT_UNITS}`)
                }
                catch(e) {
                    throw e
                }
            }

            const triggerNewSupplierOrderDetail = () => {
                
                try {
                    
                    modal.title = ''
                    modal.show = true
                    modal.type = 'supplierOrder'
                    getProductUnits()

                }
                catch(e) {
                    throw e
                }
                
            }

            const commitAction = () => {

                if(modal.type == 'supplierOrder') {
                    createNewSupplierOrder()
                }

            }

            const closeModal = () => {
                modal.show = false
            }

            const goto = () => {}


            const createNewSupplierOrder = async () => {

                try {
                    
                    let valid = true

                    if(supplierOrder.product == '') {
                        store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                            type: 'danger',
                            message: 'Veuillez ajouter une nom.',
                            ttl: 8,
                        });
                        valid = false
                    }

                    if(valid == false) return


                    showloader.value = true
                    store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`, [true, 'Loading...'])
                    
                    await store.dispatch(`${COMMANDE_FOURNISSEUR_LIST_MODULE}${FOURNISSEUR_COMMANDE_CREATE_SUPPLIER_ORDER}`, {
                        supplierOrder: unref(supplierOrder),
                        orderId: props.id,
                    })

                    modal.show = false

                    supplierOrder.product =  ''
                    supplierOrder.price =  ''
                    supplierOrder.unit =  0
                    supplierOrder.qty =  0
                    supplierOrder.type =  0
                    supplierOrder.name = ''

                }
                catch(e) {
                    store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                        type: 'danger',
                        message: 'Something went wrong',
                        ttl: 8,
                    })
                    throw e
                }
                finally {
                    store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`)
                    showloader.value = false
                }

            }

            const getCommandeFournisseur = async (id) => {

                try {
                    show.value = false
                    showloader.value = true
                    await store.dispatch(`${COMMANDE_FOURNISSEUR_LIST_MODULE}${COMMANDE_FOURNISSEUR_GET_SUPPLIER_ORDER}`, id)
                    show.value = true
                    showloader.value = false
                }

                catch(e) {
                    showloader.value = false
                    throw e
                }

            }

            onMounted(()=>{
                getCommandeFournisseur(props.id)
            })

            return {

                supplierOrderTypes,
                supplierOrder,
                productUnits,
                selectedProduct,
                triggerNewSupplierOrderDetail,
                contact,
                details,
                commitAction,
                modal,
                moment,
                show,
                showloader,
                formatPrice,
                formatDate,
                br,
                router,
                goto,
                moneyconfig,
                closeModal,
                isFloat,

            }

        }
    }

</script>
    
<style scoped lang="scss">

    .responsable-section {
        .title-label {
            font-family: 'Almarai Regular';
            font-style: normal;
            font-weight: 400;
            font-size: 14px;
            line-height: 140%;
            display: flex;
            align-items: flex-end;
            color: #C3C3C3;
        }
    }

    h1 {
        line-height: 56px !important; 
        margin: 0;
    }
    .text-editer {
        font-family: 'Almarai Light';
        font-style: normal;
        font-weight: 400;
        font-size: 14px;
        line-height: 140%;
        display: flex;
        align-items: flex-end;
        color: #3E9A4D;
    }    

    .title-bold {
        font-family: 'Almarai Regular';
        font-style: normal;
        font-weight: 700;
        font-size: 14px;
        line-height: 140%;
        display: flex;
        align-items: flex-end;
        color: #000000;
    }
    
    .tag {
        text-transform: capitalize;
        background: #DDD;
        border-radius: 70px;
        text-align: center;
        font-size: 12px;
        height: 24px;
        position: relative;
        display: inline-block;
        vertical-align: middle;
        line-height: 24px;
        transition: all 0.5s ease-in;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        padding: 0 10px;
    }
    
    .facture_action{
        color:#E8581B;
        cursor: pointer;
        }
    h2{
        font-size: 16px;
        line-height: 20px;
        margin-bottom: 8px;
    }
    h3{
        font-size: 14px;
        line-height: 14px;
        margin-bottom: 8px;
    }
    
    hr{
        margin:25px -20px;
        background-color: #E0E0E0;
    
    }
    .subtitle{
        color:#C3C3C3;
        font-size:14px;
        font-weight: 400!important;
    }
    
    .lcdtgrey{
        color:var(--lcdtGrey);
    }
    .od_catname{
        position: relative;
        padding-left: 54px;
    }
    .od_catname::after{
        content:'';
        display: block;
        width: 13px;
        height: 13px;
        border-radius: 50%;
        background-color: var(--lcdtOrange);
        position: absolute;
        top:50%;
        left: 18px;
        transform: translateY(-50%);
    
    }
    .od_actions{
        display: flex;
        justify-content: space-between;
    }
    
    .noselect {
      -webkit-touch-callout: none; /* iOS Safari */
        -webkit-user-select: none; /* Safari */
         -khtml-user-select: none; /* Konqueror HTML */
           -moz-user-select: none; /* Old versions of Firefox */
            -ms-user-select: none; /* Internet Explorer/Edge */
                user-select: none; /* Non-prefixed version, currently
                                      supported by Chrome, Edge, Opera and Firefox */
    }
    .invoiceline{
        padding-left: 48px;
        position: relative;
    }
    
    .invoiceline::before{
        content: "";
        width: 13px;
        height: 13px;
        background-color:white;
        border-radius: 50%;
        display: block;
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        transition: background-color ease-in-out 0.3s;
    }
    .reste_a_facturer .invoiceline::before{
        background-color: var(--lcdtOrange);
    }
    .facturer .invoiceline::before{
        background-color: #78DC70;
    }
    
    .remise .invoiceline::before,.avoir .invoiceline::before{
        background-color: rgba(255, 0, 0, 0.7);
    }
    .dangerred{
        color:rgba(255, 0, 0, 0.7);
    }
    
      .list-enter-from{
            opacity: 0;
            transform: scale(0.6);
        }
        .list-enter-to{
            opacity: 1;
            transform: scale(1);
        }
        .list-enter-active{
            transition: all 1s ease;
        }
    
        .list-leave-from{
            transform-origin: right center;
            opacity: 1;
            transform: scale(1);
       
        }
        .list-leave-to{
            transform-origin: right center;
            opacity: 0;
            transform: scale(0.6);
        }
        .list-leave-active{
                   transition: all 1s ease;
             transform-origin: right center;
            position:absolute;     
            width: 100%;
        }
        .list-move{
            transition:all 0.3s ease;
        }
    </style>