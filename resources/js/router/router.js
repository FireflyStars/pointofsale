import {
    createRouter,
    createWebHistory,
} from  'vue-router';
import NotFound from '../components/NotFound';
import AuthenticationMiddleware from './middleware/authentication';

const router = createRouter({
    history:createWebHistory(),
    routes:[
        {
            path:'',
            name:'LandingPage',
            component:()=> import('../pages/Home/Index.vue'),
            meta:{
                authenticated:true
            },

        },

        {
            path:'/devis',
            name:'Devis',
            component:()=> import('../pages/devis/Devis.vue'),//import('../Pages/Index'),
            children:[
                {
                    path:'/devis/detail/:id',
                    name:'DevisDetail',
                    component:()=> import('../pages/devis/DevisDetail.vue'),
                    meta:{
                        authenticated:false
                    }
                },
            ],
            meta:{
                authenticated:true
            },

        },

        {
            path: '/parameters',
            name: 'parameters',
            component: () => import('../pages/Parameters/Index.vue'),
            meta:{
                authenticated: true
            },
        },

        {
            path: '/ouvrage',
            name: 'ouvrage',
            props: true,
            component: () => import('../pages/Ouvrage/Index.vue'),
            meta:{
                authenticated: true
            },
            children: [
                {
                    path: '/ouvrage/details/:id',
                    name: 'ouvrage-details',
                    props: true,
                    component: () => import('../pages/Ouvrage/Details'),
                    meta: {
                        authenticated: true
                    }
                }
            ]
        },

        {
            path: '/articles',
            name: 'articles',
            component: () => import('../pages/Articles/Index.vue'),
            children: [
                {
                    path:'/articles/details/:id',
                    name: 'articles-details',
                    props: true,
                    component: () => import('../pages/Articles/Details.vue'),
                    meta: {
                        authenticated: true
                    }
                },
            ],
            meta: {
                authenticated: true
            },
        },

        {
            path: '/ged',
            name: 'ged',
            component: () => import('../pages/Ged/Index.vue'),
            meta:{
                authenticated: true
            },
        },

        {
            path: '/statistique',
            name: 'statistique',
            component: () => import('../pages/Statistique/Index.vue'),
            meta:{
                authenticated: true
            },
        },

        {
            path: '/intervention',
            name: 'intervention',
            component: () => import('../pages/Intervention/Index.vue'),
            meta:{
                authenticated: true
            },
        },

        {
            path: '/pointage',
            name: 'pointage',
            component: () => import('../pages/Pointage/Index.vue'),
            meta:{
                authenticated: true
            },
        },

        {
            path: '/paiement',
            name: 'paiement',
            component: () => import('../pages/Paiement/Index.vue'),
            meta:{
                authenticated: true
            },
            children:[
                {
                    path:'/paiement/detail/:id',
                    name: 'paiement-details',
                    props: true,
                    component: () => import('../pages/Paiement/Details'),
                    meta: {
                        authenticated: true
                    }
                },
            ],
        },

        {
            path: '/commande',
            name: 'commande',
            component: () => import('../pages/Commande/Index.vue'),
            meta:{
                authenticated: true
            },
            children:[
                {
                    path:'/commande/detail/:id',
                    props: true,
                    name: 'commande-details',
                    component: () => import('../pages/Commande/Details'),
                    meta: {
                        authenticated: true
                    }
                },
            ],
        },

        {
            path:'/facture',
            name:'FacturePage',
            component:()=> import('../pages/facture/Facture.vue'),//import('../Pages/Index'),
            children:[
                {
                    path:'/facture/detail/:id',
                    name:'FactureDetail',
                    component:()=> import('../pages/facture/FactureDetail.vue'),
                    meta:{
                        authenticated:false
                    }
                },
            ],
            meta:{
                authenticated:true
            },

        },
        {
            path:'/devis/create',
            name:'CreateDevis',
            component: () => import('../pages/devis/Create'),
            meta:{
                authenticated: true
            },

        },
        {
            path:'/devis/edit/:id',
            name:'EditDevis',
            component: () => import('../pages/devis/Edit'),
            meta: {
                authenticated: true
            },
        },
        {
            path:'/entite/create',
            name:'CreateCustomer',
            component: () => import('../pages/Customer/Create'),
            meta: {
                authenticated: true
            }
        },
        {
            path:'/entite/edit/:id',
            name:'EditCustomer',
            component: () => import('../pages/Customer/Edit'),
            meta: {
                authenticated: true
            },
        },        
        {
            path:'/ComponentsTest',
            name:'ComponentsTest',
            component:()=> import('../pages/ComponentsTest'),
            meta: {
                authenticated:true
            },
        },
        {
            path: '/reports',
            name: 'reports',
            component: () => import('../pages/Reports/Index'),
            props: true,
            meta: {
                authenticated: true
            },
        },
        {
            path: '/report/create/:id',
            name: 'create-report-page',
            component: () => import('../pages/Reports/Create'),
            props: true,
            meta: {
                authenticated: true
            },
        },
        {
            path: '/order/:orderId/report/edit/:id',
            name: 'edit-report-page',
            component: () => import('../pages/Reports/Edit'),
            props: true,
            meta: {
                authenticated: true
            },
        },
        {
            path: '/templates',
            name: 'templates',
            component: () => import('../pages/templates/index'),
            meta: {
                authenticated: true
            }
        },
        {
            path: '/templates/add',
            name: 'templates-add',
            component: () => import('../pages/templates/add'),
            meta: {
                authenticated: true
            },
        },
        {
            path: '/templates/:id',
            name: 'templates-edit',
            component: () => import('../pages/templates/edit'),
            props: true,
            meta: {
                authenticated: true
            },
        },

        {
            path: '/personnel',
            name: 'personnel',
            component: () => import('../pages/Personnel/Index'),
            meta: {
                authenticated: true
            },
            children: [{
                path: '/personnel/details/:id',
                name: 'personnel-details',
                component: () => import('../pages/Personnel/Details'),
                props: true,
                meta: {
                    authenticated: true
                }
            }]
        },
        {
            path: '/personnel/create',
            name: 'create-personnel',
            component: () => import('../pages/Personnel/Create'),
            meta: {
                authenticated: true
            },
        },
        {
            path: '/personnel/edit/:id',
            name: 'edit-personnel',
            component: () => import('../pages/Personnel/Edit'),
            meta: {
                authenticated: true
            },
        },

        {
            path: '/contact',
            name: 'contact',
            component: () => import('../pages/contact/Index'),
            meta: {
                authenticated: true
            },
            children: [{
                path: '/contact/detail/:id',
                name: 'contact-details',
                component: () => import('../pages/contact/Details'),
                props: true,
                meta: {
                    authenticated: true
                }
            }]
        },

        {
            path: '/entite',
            name: 'entite',
            component: () => import('../pages/entite/index'),
            meta: {
                authenticated: true
            },
            children: [{
                path: '/entite/detail/:id',
                name: 'entite-details',
                component: () => import('../pages/Entite/Details'),
                props: true,
                meta: {
                    authenticated: true
                }
            }]
        },

        {
            path: '/action-commercial',
            name: 'action-commercial',
            component: () => import('../pages/ActionCommercial/Index'),
            meta: {
                authenticated: true
            },
            children: [{
                path: '/action-commercial/detail/:id',
                name: 'action-commercial-details',
                component: () => import('../pages/ActionCommercial/Details'),
                props: true,
                meta: {
                    authenticated: true
                }
            }]
        },
        {
            path:'/action-commercial/create',
            name:'CreateAction',
            component: () => import('../pages/ActionCommercial/Create'),
            meta: {
                authenticated: true
            }
        },        
        {
            path:'/action-commercial/edit/:id',
            name:'EditAction',
            component: () => import('../pages/ActionCommercial/Edit'),
            meta: {
                authenticated: true
            }
        },
        {
            path:'/action-commercial/calendar',
            name:'ActionCalendarView',
            component: () => import('../pages/ActionCommercial/Calendar'),
            meta: {
                authenticated: true
            }
        },
        {
            path:'/contact/create',
            name:'CreateContact',
            component: () => import('../pages/Contact/Create'),
            meta: {
                authenticated: true
            }
        },        
        {
            path:'/contact/edit/:id',
            name:'EditContact',
            component: () => import('../pages/Contact/Edit'),
            meta: {
                authenticated: true
            }
        },

        {
            path:'/auth/',
            name:'AuthPage',
            component:()=> import('../components/auth/AuthPage'),
            meta:{
                authenticated:false
            },
            children:[
                {
                    path:'/auth/login/',
                    name:'Login',
                    component:()=> import('../components/auth/Login'),
                    meta:{
                        authenticated:false
                    }
                },
                {
                    path:'/auth/forgot-password/',
                    name:'ForgotPassword',
                    component:()=> import('../components/auth/ForgotPassword'),
                    meta:{
                        authenticated:false
                    }
                },
                {
                    path:'/auth/new-password/:recovery_token',
                    name:'NewPassword',
                    component:()=> import('../components/auth/NewPassword'),
                    meta:{
                        authenticated:false
                    }
                }
            ]
        },

        {
            path: "/marketing/producta/:categoryId",
            name: "marketing-producta",
            props: true,
            component: () => import("../components/marketing/ProductA.vue"),
            meta: {
                authenticated: true,
            },
        },

        {
            path: "/marketing/campagnes-list",
            name: "marketing-list",
            props: true,
            component: () => import("../components/marketing/List.vue"),
            meta: {
                authenticated: true,
            },
            children: [{
                path: "/marketing/campagnes-detail/:id",
                name: "marketing-campagne-details",
                props: true,
                component: () => import("../components/marketing/ListDetail.vue"),
                meta: {
                    authenticated: true,
                },
            }]
        },
        
        {
            path: "/marketing/product-card",
            name: "marketing-card",
            props: true,
            component: () => import("../components/marketing/cardPage.vue"),
            meta: {
                authenticated: true,
            },
        },

        {
            path: "/emailing/",
            name: "emailing",
            props: true,
            component: () => import("../components/emailing/EmailingPage.vue"),
            meta: {
                authenticated: true,
            },
        },

        {
            path: "/emailing/emailingprestations/:id",
            name: "emailingprestations",
            props: true,
            component: () =>
                import("../components/emailing/EmailingPrestations.vue"),
            meta: {
                authenticated: true,
            },
        },
        {
            path: "/emailing/cible/:type/:categ_id/:data",
            name: "cible",
            props: true,
            component: () => import("../components/emailing/Cible.vue"),
            meta: {
                authenticated: true,
            },
        },
        {
            path: "/emailing/cible/:type/:cible_id/:element_old",
            name: "cibleSeg",
            props: true,
            component: () => import("../components/emailing/Cible.vue"),
            meta: {
                authenticated: true,
            },
        },
        {
            path: "/emailing/segmentation/:type/:cible_id/:element_old",
            name: "segmentation",
            props: true,
            component: () =>
                import("../components/emailing/EmailingSegmentations.vue"),
            meta: {
                authenticated: true,
            },
        },
        {
            path: "/emailing/content/:type/:cible_id",
            name: "content",
            props: true,
            component: () =>
                import("../components/emailing/EmailingContent.vue"),
            meta: {
                authenticated: true,
            },
        },
        {
            path: "/emailing/mailing/:type/:cible_id",
            name: "mailingContenu",
            props: true,
            component: () =>
                import("../components/emailing/MailingContenu.vue"),
            meta: {
                authenticated: true,
            },
        },
        {
            path: "/emailing/display/:type/:cible_id/:show",
            name: "mailingInterface",
            props: true,
            component: () =>
                import("../components/emailing/MailingInterface.vue"),
            meta: {
                authenticated: true,
            },
        },
        {
            path: "/emailing/display/:type/:cible_id/:show",
            name: "personnaliserflyer",
            props: true,
            component: () =>
                import("../components/emailing/EmailingContent.vue"),
            meta: {
                authenticated: true,
            },
        },
        {
            path: "/emailing/envoi/:type/:cible_id",
            name: "envoi",
            props: true,
            component: () => import("../components/emailing/EmailingEnvoi.vue"),
            meta: {
                authenticated: true,
            },
        },

        {
            path: "/emailing/content/:type",
            name: "mailing",
            props: true,
            component: () =>
                import("../components/emailing/MailingContenu.vue"),
            meta: {
                authenticated: false,
            },
        },
        {
            path: "/emailing/display",
            name: "mailingcontent",
            props: true,
            component: () =>
                import("../components/emailing/MailingInterface.vue"),
            meta: {
                authenticated: false,
            },
        },
        {
            path: "/template",
            name: "LettreAccompagnement",
            component: () =>
                import("../components/templates/LettreAccompagnement.vue"),
            props: true,
            meta: {
                authenticated: false,
            },
        },
        {
            path: '/:pathMatch(.*)',
            name: 'not-found',
            component:NotFound
        },

    ]
});

router.beforeEach(AuthenticationMiddleware);

export default router;
