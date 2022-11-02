<template>
    <div class="attach-wrapper">
        <attach-header></attach-header>
        <div class="attach-body">
            <table class="table table-borderless">
                <thead>
                    <tr v-if="files.length">
                        <th></th>
                        <th>Nom</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(file, index) in files" :key="index">
                        <td>
                            <svg width="13" height="13" viewBox="0 0 13 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="6.5" cy="6.5" r="6.5" fill="#C4C4C4"/>
                            </svg>
                        </td>
                        <td>{{ file.fileName }}</td>
                        <td class="d-flex align-items-center">
                            <svg  class="cursor-pointer" width="14" @click="viewFile(file.url)" height="16" viewBox="0 0 14 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M13.9987 3.46774C13.998 3.46195 13.9969 3.45631 13.9958 3.45068C13.9954 3.4488 13.9953 3.44686 13.9948 3.44498C13.9933 3.43825 13.9914 3.43167 13.9893 3.42519C13.989 3.42441 13.9888 3.42356 13.9886 3.42278C13.9863 3.41611 13.9836 3.4096 13.9807 3.40321C13.9804 3.40255 13.9802 3.40186 13.9799 3.40121C13.9771 3.3951 13.9738 3.38918 13.9705 3.38336C13.9699 3.38239 13.9694 3.38139 13.9689 3.38041C13.9656 3.375 13.962 3.3698 13.9583 3.36463C13.9574 3.36332 13.9566 3.36194 13.9556 3.36066C13.952 3.35584 13.948 3.35126 13.944 3.34669C13.9427 3.34519 13.9416 3.34362 13.9402 3.34215C13.9352 3.33674 13.9299 3.33157 13.9244 3.32656C13.9239 3.32609 13.9235 3.32559 13.9229 3.32512L10.2738 0.0687593C10.2732 0.0681957 10.2725 0.067726 10.2718 0.0671624C10.2664 0.0623718 10.2607 0.0577378 10.2548 0.0533855C10.2528 0.0519765 10.2508 0.0507867 10.2488 0.0494403C10.244 0.04609 10.2392 0.0427397 10.2341 0.0397025C10.2323 0.038638 10.2304 0.0377613 10.2286 0.036728C10.2232 0.0336282 10.2177 0.0305597 10.212 0.0278356C10.2107 0.0272407 10.2094 0.0267397 10.2081 0.0261761C10.2017 0.0232642 10.1953 0.0204462 10.1886 0.0179726C10.1878 0.0176595 10.1869 0.0174403 10.186 0.0171272C10.1789 0.014591 10.1718 0.0122114 10.1644 0.0102074C10.1635 0.00995695 10.1625 0.00980039 10.1616 0.0095499C10.1544 0.00767123 10.147 0.00594912 10.1395 0.00460274C10.1373 0.00419569 10.1351 0.00403914 10.1328 0.00369472C10.1266 0.00272407 10.1204 0.00175342 10.114 0.00118982C10.1053 0.000407045 10.0965 0 10.0877 0H1.38596C0.621754 0 0 0.554834 0 1.23679V14.7632C0 15.4452 0.621754 16 1.38596 16H12.614C13.3782 16 14 15.4452 14 14.7632V3.49119C14 3.48333 13.9995 3.47551 13.9987 3.46774ZM10.3509 0.801753L13.1015 3.25636H11.2105C10.7365 3.25636 10.3509 2.91225 10.3509 2.48924V0.801753ZM12.614 15.5303H1.38596C0.911965 15.5303 0.526316 15.1862 0.526316 14.7632V1.23679C0.526316 0.813777 0.911965 0.469667 1.38596 0.469667H9.82456V2.48924C9.82456 3.17119 10.4463 3.72603 11.2105 3.72603H13.4737V14.7632C13.4737 15.1862 13.088 15.5303 12.614 15.5303Z" fill="black"/>
                            </svg>
                            <span class="mx-2 download-icon cursor-pointer" @click="downloadFile(index)"></span>
                            <svg class="cursor-pointer" width="24" @click="removeDocument(index)" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M17 6H22V8H20V21C20 21.2652 19.8946 21.5196 19.7071 21.7071C19.5196 21.8946 19.2652 22 19 22H5C4.73478 22 4.48043 21.8946 4.29289 21.7071C4.10536 21.5196 4 21.2652 4 21V8H2V6H7V3C7 2.73478 7.10536 2.48043 7.29289 2.29289C7.48043 2.10536 7.73478 2 8 2H16C16.2652 2 16.5196 2.10536 16.7071 2.29289C16.8946 2.48043 17 2.73478 17 3V6ZM18 8H6V20H18V8ZM13.414 14L15.182 15.768L13.768 17.182L12 15.414L10.232 17.182L8.818 15.768L10.586 14L8.818 12.232L10.232 10.818L12 12.586L13.768 10.818L15.182 12.232L13.414 14ZM9 4V6H15V4H9Z" fill="black"/>
                            </svg>
                        </td>
                    </tr>                
                </tbody>
            </table>
        </div>
        <div class="attach-footer lcdtOrange cursor-pointer" @click="openFileDialog">
            <span class="me-3 plus-icon"></span> AJOUTER
        </div>    
        <input type="file" @change="addToDocumentList" accept="application/pdf" ref="doc" multiple class="d-none">
        <PdfModal ref="pdfModal"></PdfModal>
    </div>
</template>
<script>
    import { ref, watch } from 'vue';
    import AttachHeader from './AttachHeader.vue';
    import PdfModal from '../../components/miscellaneous/PdfModal';
    export default {
        components:{
            AttachHeader,
            PdfModal
        },
        props:{
            documents:{
                type: Array,
                required: true
            }
        },
        emits: ['updateDocumentList'],
        setup(props, { emit }){
            const files = ref(props.documents);
            const doc = ref(null);
            const pdfModal = ref(null);
            watch(()=> props.documents, (cur_val, pre_val)=>{
                files.value = cur_val;
            })
            const addToDocumentList = ()=>{
                let documents = doc.value.files;
                for (let i = 0; i < documents.length; i++) {
                    let reader = new FileReader();
                    reader.onload = (e) => {
                        files.value.push({
                            url: reader.result,
                            base64data: reader.result,
                            fileName: documents[i].name,
                            type: 'pdf',
                            id: '',
                        })
                    };
                    reader.readAsDataURL(documents[i]);
                }
                emit('updateDocumentList', files.value);
            }
            const openFileDialog = ()=>{
                doc.value.click();
            }
            const viewFile = (data)=>{
                pdfModal.value.openModal(data);
            }
            const downloadFile = (fileIndex)=>{
                const file = files.value.find((item, index)=>{
                    return index == fileIndex;
                });
                if(file.url != ''){
                    const link = document.createElement('a');
                    link.setAttribute('download', file.fileName);
                    link.href = file.url;
                    document.body.appendChild(link);
                    link.click();                    
                    document.body.removeChild(link);
                }
            }
            const removeDocument = (documentIndex)=>{
                files.value = files.value.filter((item, index)=>{
                    return index != documentIndex;
                });
                emit('updateDocumentList', files.value);
            }
            return{
                doc,
                files,
                pdfModal,
                openFileDialog,
                addToDocumentList,
                viewFile,
                downloadFile,
                removeDocument,
            }
        }
    }
</script>
<style scoped>
.attach-wrapper{
    background: white;
    width: 356px;
    min-height: 467px;
    padding: 20px 20px 10px 20px;
}
.attach-footer{
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>