<template>
    <router-view >
        
        <transition
            enter-active-class="animate__animated animate__fadeIn"
        >
            <div class="container-fluid h-100 bg-color" v-if="showcontainer">
                <main-header />

                <div class="row d-flex align-content-stretch align-items-stretch flex-row hmax main-view-wrap" style="z-index:100" >
                    
                    <side-bar />

                    <div class="col main-view container">
                        <page-title icon="edit" name="Developer Component Library" class="almarai_extrabold_normal_normal"/>

                        <div class="">
                            <div class="row m-0">
                                
                                <div class="col m-2">

                                    <select-box
                                        v-model="select1" 
                                        placeholder="Choose a number" 
                                        :options="selectBoxOptions" 
                                        name="select2" 
                                        :valid="true"
                                        label="Positive" 
                                    />

                                </div>

                                <div class="col m-2">
                                    <multi-select 
                                        id="multiSelect"
                                        width="116px"
                                        tagBackground="orange"
                                        tagColor="white"
                                        :numtag="1"
                                        classes="almarai_700_normal"
                                        :styles="{}" 
                                        dropdownClasses="almarai_700_normal"
                                        :dropdownStyles="{ width: '167px' }"
                                        v-model:options="options"
                                        transformOrigin="top center"
                                        :selectedOptions="selectedMultiSelectOptions"
                                    />
                                </div>
                                
                                <div class="col m-2">
                                    <h4 class="text-center">App Filters</h4>
                                    <table-filter
                                        :checkboxOptions="filterCheckboxOptions"
                                        :selectOptions="filterSelectOptions"
                                        activeBackground="orange"
                                        activeColor="white"
                                        transformOrigin="top"
                                        dropdownClasses=""
                                        :dropdownStyles="{}"
                                        classes=""
                                        :styles="{}"
                                        @onChange="validateFilters"
                                    />
                     
                                </div>

                            </div>

                            <div class="row m-0 my-5">
                                <div class="col">
                                    <h4>Dropdown Item</h4>
                                    <div class="position-relative">
                                        <base-button 
                                            class="mt-1"
                                            title="Dropdown Trigger"
                                            @click="toggleActiveItem('demoDropdown')"
                                        />
                                        <dropdown 
                                            id="demoDropdown"
                                            height="200px"
                                            background="transparent"
                                            transformOrigin="bottom"
                                        >
                                            <ul class="list-group w-100">
                                                <li class="list-group-item list-group-item-action">Item 1</li>
                                                <li class="list-group-item list-group-item-action">Item 2</li>
                                                <li class="list-group-item list-group-item-action">Item 3</li>
                                                <li class="list-group-item list-group-item-action">Item 4</li>
                                            </ul>
                                        </dropdown>
                                    </div>
                                </div>
                                <div class="col">
                                    <h4>Checkbox</h4>
                                    <check-box 
                                        id="samplecheckbox"
                                        title="Custom attributes checkbox"
                                        name="customCheckbox"
                                        background="blue"
                                        color="orange"
                                        :checked="false"
                                        @change="checkboxUpdated"
                                    />
                                </div>
                                
                            </div>

                            <div class="row m-0 mt-5">
                                <div class="col">
                                    
                                    <h4>Buttons</h4>
                                    
                                    <div class="d-flex align-items-center justify-content-between">
                                        <base-button title="Danger" htmlTitle="kind danger" kind="danger" class="text-capitalize" />
                                        <base-button title="Primary" htmlTitle="kind primary" kind="primary" class="text-capitalize" />
                                        <base-button title="success" htmlTitle="kind success" kind="success" class="text-capitalize" />
                                        <base-button title="Green" htmlTitle="kind green" kind="green" class="text-capitalize" />
                                        <base-button title="Light Green" htmlTitle="kind light-green" kind="light-green" class="text-capitalize" />
                                        <base-button title="Warning" htmlTitle="kind warning" kind="warning" class="text-capitalize" />
                                        <base-button title="Light warning" htmlTitle="kind light-warning" kind="light-warning" class="text-capitalize" />

                                        <base-button 
                                            title="Light warning Icon"
                                            htmlTitle="kind light-warning"
                                            kind="light-warning"
                                            class="text-capitalize"
                                            prepend
                                        >
                                            <icon name="plus-circle" />
                                        </base-button>

                                        <base-button 
                                            title="Warning Icon"
                                            htmlTitle="kind warning"
                                            kind="warning"
                                            class="text-capitalize"
                                        >
                                            <icon name="clipboard" />
                                        </base-button>

                                    </div>


                                </div>
                            </div>

                        </div>

                        <div class="row m-0 mt-5">
                            <div class="col">
                                <tab-pane :tabs="{first:'First tab',second:'Second tab',third:'Third tab'}" class="almarai_700_normal">
                                    <template v-slot:first>
                                        1st
                                    </template>
                                    <template v-slot:second>
                                        2nd
                                    </template>
                                <template v-slot:third>
                                        3rd
                                    </template>
                                </tab-pane>
                            </div>

                        </div>
                    </div>

                </div>

            </div>
        </transition>
    </router-view>
</template>

<script>

import { ref, reactive, onMounted, nextTick } from "vue";
import TableFilter from "../components/miscellaneous/TableFilter.vue"

import useToggler from '../composables/useToggler'
import TabPane from '../components/miscellaneous/TabPane.vue';




export default {

    components: {
        TableFilter,
        TabPane
    },

    setup() {
        
        const { open, toggleActiveItem } = useToggler()

        const select1 = ref(0)
        const customCheckbox = ref(false)
        const showcontainer = ref(false)
        const selectedMultiSelectOptions = ref([1, 2])

        onMounted(() => {
            nextTick(() => {
                showcontainer.value=true
            })
        })

        const selectBoxOptions = ref([
            { value:'1', display: 'One' },
            { value:'2', display:'Two' }
        ])

        const options = ref([
            { id: 1, value: 'Perdu', check: false },
            { id: 2, value: 'Gagne', check: false},
            { id: 3, value: 'Abondonne', check: false},
            { id: 4, value: 'Laravel', check: false},
        ]);

        const filterCheckboxOptions =  ref([
            {
                name: 'Champs',
                options: [
                    { id: 'nodevis', value: 'no devis', check: false },
                    { id: 2, value: 'client', check: false },
                    { id: 3, value: 'contact', check: false },
                    { id: 4, value: 'chantier', check: false },
                    { id: 5, value: 'date creation', check: false },
                    { id: 6, value: 'responsable', check: false },
                    { id: 7, value: 'status', check: false },
                ],
            },
        ])

        const filterSelectOptions = ref([
            {
                label: 'Grouper par',
                options: [
                    { value: 1, display: 'One' },
                    { value: 2, display: 'Two' },
                ],
            },
        ])

        const validateFilters = (val) => {
            console.log(val);
            console.log('validated')
        }
        const checkboxUpdated = (val) => {
            console.log(val);
        }
        return { 
            showcontainer,
            open,
            select1,
            options,
            customCheckbox,
            validateFilters, 
            toggleActiveItem,
            selectBoxOptions,
            filterSelectOptions,
            filterCheckboxOptions,
            checkboxUpdated,
            selectedMultiSelectOptions
        }
    },
}

</script>

<style lang="scss"></style>
