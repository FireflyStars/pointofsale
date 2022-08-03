<template>
    <div class="container-fluid h-100 bg-color">
        <main-header />
        <div class="row d-flex align-content-stretch align-items-stretch flex-row hmax main-view-wrap" style="z-index:100">
            <side-bar />
            <transition>
                <div class="col main-view container">
                    <div class="row m-0 ml-5 mr-5">
                        <div class="col p-0 d-flex mt-5">
                            <div class="home-news-pannel bg-white rounded-3 p-3">
                                home news
                            </div>
                            <div class="stats-pannel">
                                <div class="breif-total p-3 bg-white rounded-3">
                                    <h4 class="font-24 mulish_normal_600">En bref cette semaine semaine</h4>
                                    <div class="mt-3 action-commercial px-3">
                                        <h4 class="font-16 mulish_normal_600">Mes actions commerciales</h4>
                                        <div class="mt-3 bg-dark-gray rounded-3 p-3">
                                            actions
                                        </div>
                                    </div>
                                    <div class="mt-3 marketing px-3">
                                        <h4 class="font-16 mulish_normal_600">Marketing</h4>
                                        <div class="mt-3 bg-dark-gray rounded-3 p-3">
                                            Marketing
                                        </div>
                                    </div>                                    
                                </div>
                                <div class="month-sales p-3 bg-white rounded-3 mt-3">
                                    <div class="d-flex justify-content-between">
                                        <h4 class="font-24 mulish_normal_600">Vente par mois</h4>
                                        <div class="me-3">
                                            <TotalPercent :amount="230" :pastAmount="380"></TotalPercent>
                                            <TotalPercent :amount="360" :symbol="'Hr'" :pastAmount="200"></TotalPercent>
                                        </div>
                                    </div>
                                    <div class="d-flex mt-3 px-3">
                                        <div class="col-5">
                                            <h4 class="font-16 mulish_600_normal">Devis</h4>
                                            <div class="d-flex flex-wrap">
                                                <div class="avg-sale-block py-3 px-2 mt-2 me-2 d-flex flex-wrap">
                                                    <p class="w-100 text-center font-12">Devis A faire</p>
                                                    <p class="w-100 text-center font-20 mulish_600_normal align-self-end">3,3 K€</p>
                                                </div>
                                                <div class="avg-sale-block py-3 px-2 mt-2 d-flex flex-wrap">
                                                    <p class="w-100 text-center font-12">Devis Attente client</p>
                                                    <p class="w-100 text-center font-20 mulish_600_normal align-self-end">5.3 K€</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-7">
                                            <h4 class="font-16 mulish_600_normal">Finance</h4>
                                            <div class="d-flex flex-wrap">
                                                <div class="avg-sale-block py-3 px-2 mt-2 me-2 d-flex flex-wrap">
                                                    <p class="w-100 text-center font-12">Paiement</p>
                                                    <p class="w-100 text-center font-20 mulish_600_normal align-self-end">3,3 K€</p>
                                                </div>
                                                <div class="avg-sale-block py-3 px-2 me-2 mt-2 d-flex flex-wrap">
                                                    <p class="w-100 text-center font-12">Commande Facturé</p>
                                                    <p class="w-100 text-center font-20 mulish_600_normal align-self-end">5.3 K€</p>
                                                </div>
                                                <div class="avg-sale-block py-3 px-2 mt-2 d-flex flex-wrap">
                                                    <p class="w-100 text-center font-12">Rentabilité</p>
                                                    <p class="w-100 text-center font-20 mulish_600_normal align-self-end">5.3 K€</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>                                    
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </transition>    
        </div>
    </div>    
</template>
<script>
import TotalPercent from '../../components/miscellaneous/TotalPercent';
export default {
    components:{
        TotalPercent
    }
}
</script>
<style>
.main-view{
    margin: 64px 60px 0;
}
</style>
<style lang="scss" scoped>
.home-news-pannel{
    width: calc(100% - 620px);
}
.bg-dark-gray{
    background-color: #E0E0E0 !important;
}
.stats-pannel{
    margin-left: 60px;
    width: 560px;
    .avg-sale-block{
        width: 85px;
        height: 100px;
        background: #E0E0E0;
        border-radius: 10px;
    }    
}
</style>