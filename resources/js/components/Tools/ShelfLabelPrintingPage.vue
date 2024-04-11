<template>
    <container>
        <top-nav-bar>
            <barcode-input-field :input_id="'barcode-input'"  :url_param_name="'search'" @barcodeScanned="findText" placeholder="Search orders using number, sku, alias or command" ref="barcode"/>
        </top-nav-bar>
        <div class="grid-col-12 pl-2 p-1">
            <div class="col-span-12 xs:col-span-6 md:col-span-4 xl:col-span-6 mb-2 mb-sm-0">
                <header-upper><span class="xs:sd-none sm:sd-inline-block">TOOLS >&nbsp;</span>SHELF LABEL PRINTER</header-upper>
            </div>
            <div class="col-span-12 xs:col-span-6 md:col-span-4 xl:col-span-4">
                <div class="col-span-8 d-flex justify-content-end justify-content-md-center justify-content-xl-end">
                    <div class="small xs:sd-none sm:sd-block">FROM:</div>
                    <input type="text" v-model="fromLetter" @keyup="changeNonSearchValue" @focus="$selectAllInputText" class="mx-1 inline-input-sm px-1 text-center"/>
                    <input type="text" v-model.number="fromNumber" @keyup="changeNonSearchValue" @focus="$selectAllInputText" class="mx-1 inline-input-sm px-1 text-center"/>
                    <div class="small">TO</div>
                    <input type="text" v-model="toLetter" @keyup="changeNonSearchValue" @focus="$selectAllInputText" class="mx-1 inline-input-sm px-1 text-center"/>
                    <input type="text" v-model.number="toNumber" @keyup="changeNonSearchValue" @focus="$selectAllInputText" class="mx-1 inline-input-sm px-1 text-center"/>
                </div>
            </div>
            <div class="col-span-12 xs:col-span-12 md:col-span-4 xl:col-span-2 d-flex justify-content-end">
                <array-dropdown-select class="ml-0 ml-sm-2 mt-1 mt-md-0" :items="templates" :item-selected.sync="templateSelected" :align-menu-right="true"/>
            </div>
        </div>
        <card class="mt-sm-2 bg-dark">
<!--            <vue-pdf-embed ref="pdfRef" :source="pdfUrl" :page="null"/>-->
        </card>
        <b-modal id="quick-actions-modal" no-fade hide-header @hidden="setFocusElementById('barcode-input')">
            <div class="row mt-2">
                <div class="col-6">
                    <div class="dropdown">
                        <button class="btn btn-sm dropdown-toggle text-primary font-weight-bold" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            {{ templateSelected }}
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownTemplates">
                            <a class="dropdown-item" v-for="templateOption in templates" @click.prevent="templateSelected = templateOption">
                                {{ templateOption }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- added br so dropdown does not overflow the modal -->
            <br><br>
            <template #modal-footer>
                <b-button variant="secondary" class="float-right" @click="$bvModal.hide('quick-actions-modal');">
                    Cancel
                </b-button>
                <b-button variant="primary" class="float-right" @click="printPDF">
                    Print
                </b-button>
            </template>
        </b-modal>
    </container>
</template>

<script>

import url from "../../mixins/url.vue";
import helpers  from "../../helpers";
import VuePdfEmbed from 'vue-pdf-embed/dist/vue2-pdf-embed'

export default {
    mixins: [url],
    components: {
        VuePdfEmbed
    },
    data() {
        return {
            customLabelText: this.getUrlParameter('search', ''),
            fromLetter: this.getUrlParameter('from-letter', 'A'),
            fromNumber: this.getUrlParameter('from-number', 1),
            toLetter: this.getUrlParameter('to-letter', 'A'),
            toNumber: this.getUrlParameter('to-number', 3),
            templates:[
                'shelf-labels/6x4-1-per-page',
                'shelf-labels/4x6-2-per-page',
                'shelf-labels/6x4-3-per-page',
            ],
            templateSelected: this.getUrlParameter('template-selected', helpers.getCookie('templateSelected', 'shelf-labels/6x4-3-per-page') ),
            pdfUrl: '',
        }
    },
    mounted() {
        this.loadPdfIntoIframe();
    },
    methods: {
        findText(text) {
            this.customLabelText = text;
        },
        printPDF() {
            // window.print();
        },

        changeNonSearchValue() {
            this.customLabelText = '';
            this.loadPdfIntoIframe();
        },

        loadPdfIntoIframe() {

            let data = {
                labels: this.getLabelArray(),
                template: this.templateSelected,
            };

            axios.post('/api/preview/shelf-label-pdf', data, { responseType: 'arraybuffer' })
                .then(response => {
                    let blob = new Blob([response.data], { type: 'application/pdf' });
                    this.pdfUrl = URL.createObjectURL(blob);
                })
                .catch(error => {
                    console.log(error);
                });
        },

        buildUrl() {

            if(this.customLabelText) {
                this.updateUrl({'search': this.customLabelText});
                return;
            }

            this.updateUrl({
                'from-letter': this.fromLetter,
                'from-number': this.fromNumber,
                'to-letter': this.toLetter,
                'to-number': this.toNumber,
                'template-selected': this.templateSelected,
            });
        },

        getLabelArray() {

            if(!this.allNumbersAndLettersFilled) {
                return [];
            }

            if(this.customLabelText) {
                return [this.customLabelText];
            }

            let labels = [];

            let fromLetter = this.fromLetter.toUpperCase().charCodeAt(0);
            let toLetter = this.toLetter.toUpperCase().charCodeAt(0);
            let fromNumber = this.fromNumber;
            let toNumber = this.toNumber;
            for (let i = fromLetter; i <= toLetter; i++) {
                for (let j = fromNumber; j <= toNumber; j++) {
                    labels.push(String.fromCharCode(i) + j);
                }
                // Reset the fromNumber to 1 after the first letter
                fromNumber = 1;
            }

            return labels;
        },
    },
    computed: {
        allNumbersAndLettersFilled() {
            return this.fromLetter && this.fromNumber && this.toLetter && this.toNumber;
        }
    },
    watch: {
        customLabelText() {
            this.loadPdfIntoIframe();
        },

        templateSelected() {
            helpers.setCookie('templateSelected', this.templateSelected);
            this.loadPdfIntoIframe();
        },

        pdfUrl() {
            this.buildUrl();
        }
    },
}
</script>

<style scoped>
.inline-input{
    max-width: 30px;
    height: 19px;
}

.vue-pdf-embed > div {
    margin-bottom: 8px;
    box-shadow: 0 2px 8px 4px rgba(0, 0, 0, 0.1);
}

</style>
