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
            path:'/customer/create',
            name:'CreateCustomer',
            component: () => import('../pages/Customer/Create'),
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
            path:'/reports',
            name:'reports',
            component: () => import('../pages/Reports/Index'),
            props: true,
            meta: {
                authenticated: true
            },

        },
        {
            path:'/reports/:id',
            name:'report-page',
            component: () => import('../pages/Reports/Show'),
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
            path:'/templates/add',
            name:'templates-add',
            component: () => import('../pages/templates/add'),
            meta: {
                authenticated: true
            },
        },
        {
            path:'/templates/:id',
            name:'templates-edit',
            component: () => import('../pages/templates/edit'),
            props: true,
            meta: {
                authenticated: true
            },
        },

        {
            path: '/entite',
            name: 'entite',
            component: () => import('../pages/entite/index'),
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
