import { ref } from "vue";
import axios from "axios";
import { useRouter } from "vue-router";
import { stubFalse } from "lodash";

export default function useCompanies() {
    const status = ref([]);
    const industries = ref([]);
    const indus_length = ref([]);
    const stat_length = ref([]);
    const count = ref([]);
    const category = ref([]);
    const template = ref([]);
    const campagnedata = ref([]);
    const id = ref([]);
    const string = ref([]);
    const errors = ref("");
    const cibleSum = ref([]);
    const emails_selected = ref([]);
    const datacible = ref("");
    const datacibleCount = ref("");
    const subdata = ref("");
    const mylist = ref("");
    const my_ids = ref("");
    const selected_id = ref("");
    const item = ref("");
    const selectedcibles = ref([]);
    const router = useRouter();
    const title = ref("");
    const req = ref("");
    const myvar = ref(true);
    const myvar2 = ref(true);
    const mytitle1 = ref("");
    const mytitle = ref("");
    const company = ref([]);
    const type = ref("");
    const courrier = ref([]);
    const check_name = ref([]);
    const courrier_lettre = ref([]);
    const courrier_autre = ref([]);
    const history_compagne = ref([]);
    const count_cible = ref([]);
    const my_name = ref([]);
    const idtemplate = ref([]);
    const vaal = ref([]);
    const idcategory = ref([]);
    const field = ref([]);
    const dataimage1 = ref([]);
    const dataimage2 = ref([]);
    const dataimage3 = ref([]);
    const old_campagne_emails = ref([]);

    const getCible = async () => {
        let response = await axios.get("/getCustomerStatut");
        status.value = response.data.customerStatut;
        industries.value = response.data.codeNaf;
        indus_length.value = industries.value.length; //champ selection
        stat_length.value = status.value.length;
        count.value = response.data.count;
        emails_selected.value = 0;

        let data = await axios.get("/getOldCompagne");
        history_compagne.value = data.data.oldCompagne;
        old_campagne_emails.value = data.data.old_cibles;
    };

    const getCompgneCible = async (id) => {
        console.log("comp_cibl_id", id);
        let response = await axios.get("/getCompgneCibleSelected" + id);
        selectedcibles.value = response.data.cibles;
    };

    const getCampagneCampagneCategory = async (campagne_id)=>{
        let response = await axios.get("/getCampagneCampagneCategory/"+campagne_id)
        return response;
    }

    const getCategory = async () => {
        let response = await axios.get("/getCampagneCategory");
        category.value = response.data.campagnes;
    };

    const getTemplates = async (id, redirect = undefined) => {

        let response = await axios.get("/getTemplates/" + id);
        template.value = response.data.template;
        type.value = response.data.type;
        campagnedata.value = response.data.campagnesCategory;
        console.log("campagnedata", response.data.campagnesCategory[0].ciblage);
        my_ids.value = response.data.data_id;
        selected_id.value = response.data.id;
        mytitle1.value = response.data.myvar;

        if ((mytitle1.value = true)) {
            myvar.value = true;
        }

        if (my_ids.value.length == 0) {
            console.log("empty");
        } 

        else {

            console.log(my_ids.value, selected_id.value, my_ids.value.includes(selected_id.value))

            if (my_ids.value.includes(`${selected_id.value}`)) {
                req.value = router.push({
                    name: "emailingprestations",
                    params: { id: `${selected_id.value}` },
                })
            } 
            else {
                // if(redirect == true || redirect == 'true') return

                let d
                if (response.data.campagnesCategory[0].ciblage) {
                    d = response.data.campagnesCategory[0].ciblage
                } 
                else {
                    d = "[]"
                }
                router.push({
                    name: "cible",
                    params: {
                        type: `${type.value}`,
                        data: d,
                        categ_id: `${selected_id.value}`,
                    },
                })

            }
        }
    };

    const storeCible = async (data) => {
        console.log("eeeeeeee", data);
        errors.value = "";
        try {
            cibleSum = await axios.post("/ciblesum", data);
        } catch (e) {
            if (e.response.status === 422) {
                console.log("errooor");
            }
        }
    };

    const getCible_data = async (cible_id) => {
        console.log(cible_id);
        let response = await axios.get("/getCibleByCampagne/" + cible_id);
        datacible.value = response.data.cibles;
        datacibleCount.value = response.data.count;
        console.log(datacible);
    };

    const getCount_cible = async (cible_id) => {
        console.log(cible_id);
        let response = await axios.get("/getCount_cible/" + cible_id);
        count_cible.value = response.data.count_cible;
    };

    const SubCategory = async (id, name = '') => {
        // id = 4
        let response = await axios.get("/SubCategory/" + id);
        title.value = response.data.campagnesCategory;
        template.value = response.data.data;
        mylist.value = response.data.mylist;
        my_ids.value = response.data.mylist;
        selected_id.value = response.data.id;
        mytitle.value = response.data.myvar;
        type.value=response.data.type[0];
        
        if ((mytitle.value = true)) {
            myvar2.value = true;
            myvar.value = false;
        }

        if (my_ids.value.length == 0) {
            console.log("empty");
        } 
        else {
            console.log("in subcategory", my_ids.value, selected_id.value, my_ids.value.includes(`${selected_id.value}`))
            if (my_ids.value.includes(`${selected_id.value}`)) {
                myvar.value = true;
                router.push({
                    name: "emailingprestations",
                    params: { id: `${selected_id.value}` },
                });
            } 
            else {
               
                if (template.value.length == 0) {

                    if(type.value.toLowerCase() == 'produit') {
                        router.push({
                            name: 'marketing-producta',
                            params: {
                                categoryId: `${selected_id.value}`,
                            }
                        })
                        return
                    }

                    router.push({
                        name: "cible",
                        params: {
                            type: `${type.value}`,
                            data: "[]",
                            categ_id: `${selected_id.value}`,
                        },
                    });

                    myvar.value = false;
                }
            }
        }
    };

    const contentform = async (id) => {
        await axios.put("/contentform/" + id, company.value);
    };

    const getCourrier = async () => {
        let response = await axios.get("/getCourrier");
        courrier.value = response.data.data;
        courrier_autre.value = response.data.data2;
        courrier_lettre.value = response.data.data1;

        for (let i = 0; i < courrier.value.length; i++) {
            check_name.value.push(response.data.data[i].name);
            // console.log(check_name.value.push(response.data.data[i].name));
        }
        console.log("_____", check_name.value);
    };

    const getTempname = async (ca_id) => {
        console.log(ca_id);
        let response = await axios.get("/getTempname/" + ca_id);
        my_name.value = response.data;

        console.log(my_name);
    };

    const getImgUrl = async () => {
        idtemplate.value = localStorage.getItem("imagetemplate");
        console.log(idtemplate.value);
        let response = await axios.get("/getContentImage/" + idtemplate.value);
        vaal.value = response.data;
        console.log(vaal);
    };

    const getimagefield = async () => {
        idcategory.value = localStorage.getItem("id_category");
        console.log(idcategory.value);
        let response = await axios.get("/imagefield/" + idcategory.value);
        dataimage1.value = response.data.data1;
        dataimage2.value = response.data.data2;
        dataimage3.value = response.data.data3;
        console.log(dataimage1.value);
        console.log(dataimage1);
    };

    return {
        status,
        industries,
        stat_length,
        indus_length,
        category,
        template,
        campagnedata,
        string,
        datacible,
        datacibleCount,
        contentform,

        subdata,
        mylist,
        my_ids,
        selected_id,
        selectedcibles,

        storeCible,
        cibleSum,
        getCible,
        emails_selected,
        getCategory,
        getTemplates,
        getCible_data,
        SubCategory,
        myvar,
        router,
        item,
        myvar2,
        item,
        title,
        mytitle,
        mytitle1,
        req,
        getCompgneCible,
        company,
        type,
        courrier,
        check_name,
        courrier_lettre,
        courrier_autre,
        history_compagne,
        getCount_cible,
        count_cible,
        getTempname,
        my_name,

        getImgUrl,
        idtemplate,
        vaal,
        getimagefield,
        idcategory,
        field,
        dataimage1,
        dataimage2,
        dataimage3,
        old_campagne_emails,
        getCampagneCampagneCategory
    };
}
