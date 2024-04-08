<template>
    <container>
        <top-nav-bar>
            <input id="custom-label"
                   placeholder="custom label text"
                   type=text
                   class="form-control"
                   autocomplete="off"
                   autocapitalize="off"
                   v-model.trim="customLabelText"
            />
        </top-nav-bar>
        <div class="row pl-2 p-1">
            <div class="col-12 col-sm-6">
                <header-upper>TOOLS > SHELF LABEL PRINTER</header-upper>
            </div>
            <div class="col-12 pt-1 pt-sm-0 col-sm-6">
                <div class="d-flex justify-content-sm-end">
                    <div class="small">
                        FROM:
                    </div>
                    <input type="text" v-model="fromLetter" class="mx-1 inline-input px-1 text-center"/>
                    <input type="text" v-model.number="fromNumber" class="mx-1 inline-input px-1 text-center"/>
                    <div class="small">
                        TO
                    </div>
                    <input type="text" v-model="toLetter" class="mx-1 inline-input px-1 text-center"/>
                    <input type="text" v-model.number="toNumber" class="mx-1 inline-input px-1 text-center"/>
                </div>
            </div>
        </div>
        <card class="mt-2">
            <div>
                <header-upper class="text-center d-none d-md-block mb-4">PREVIEW</header-upper>
                <div class="d-flex justify-content-center">
                    <iframe
                        id="shelf_label_preview"
                        title="Shelf Label Preview"
                        :src="pdfUrl"
                    >
                    </iframe>
                </div>
            </div>

            <div class="row mt-2">
                <div class="col-6">
                    <div class="d-flex">
                        <div class="small">
                            <p style="margin-top: 4.5px">Labels Per Page:</p>
                        </div>
                        <button
                            v-for="num in [1,2,3]"
                            class="btn mx-1"
                            :class="labelsPerPage === num ? 'btn-primary' : 'btn-outline-primary'"
                            style="height: 30px; padding: 0 6px 0 6px"
                            @click="labelsPerPage = num"
                        >
                            {{ num }}
                        </button>
                    </div>
                </div>
                <div class="col-6 d-flex justify-content-end">
                    <div class="d-flex">
                        <div class="small">
                            <p style="margin-top: 4.5px">NUM OF COPIES:</p>
                        </div>
                        <input v-model.number="numOfCopies" style="width: 30px; height: 30px" type="text" class="mx-1 px-1 text-center">
                        <button class="btn btn-primary" style="height: 30px; padding: 0 6px 0 6px">Print</button>
                    </div>
                </div>
            </div>
        </card>
    </container>
</template>

<script>

import url from "../../mixins/url.vue";

export default {
    mixins: [url],
    data() {
        return {
            customLabelText: '',
            fromLetter: 'A',
            fromNumber: 1,
            toLetter: 'A',
            toNumber: 2,
            numOfCopies: 1,
            labelsPerPage: 1,
            labelFilename: 'label_preview',
            pdfUrl: '',
        }
    },
    onMounted() {
        this.loadPdfIntoIframe();
    },
    methods: {

        loadPdfIntoIframe() {

            let templateTypes = {1: 'full', 2: 'half', 3: 'third'};

            let data = {
                labels: this.labelStingArray,
                templateType: templateTypes[this.labelsPerPage],
            };

            axios.post('/api/preview/shelf-label-pdf', data, { responseType: 'arraybuffer' })
                .then(response => {
                    let blob = new Blob([response.data], { type: 'application/pdf' });
                    this.pdfUrl = URL.createObjectURL(blob)+"#toolbar=0";
                })
                .catch(error => {
                    console.log(error);
                });
        },
    },
    computed: {

        labelStingArray() {

            let labels = [];

            if(this.customLabelText) {
                labels.push(this.customLabelText);
                return labels;
            }

            if(!this.allNumbersAndLettersFilled || this.numOfCopies < 1) {
                return labels;
            }

            let fromLetter = this.fromLetter.charCodeAt(0);
            let toLetter = this.toLetter.charCodeAt(0);
            let fromNumber = this.fromNumber;
            let toNumber = this.toNumber;
            for (let i = fromLetter; i <= toLetter; i++) {
                for (let j = fromNumber; j <= toNumber; j++) {
                    labels.push(String.fromCharCode(i) + j);
                }
            }

            if(this.numOfCopies > 1) {
                let temp = [];
                for(let i = 0; i < this.numOfCopies; i++) {
                    temp = temp.concat(labels);
                }
                labels = temp;
            }

            return labels;
        },

        allNumbersAndLettersFilled() {
            return this.fromLetter && this.fromNumber && this.toLetter && this.toNumber;
        }
    },

    watch: {

        labelStingArray(labels) {
            if(labels.length > 0) {
                this.loadPdfIntoIframe();
            }
        },

        labelsPerPage() {
            if(this.labelStingArray.length > 0) {
                this.loadPdfIntoIframe();
            }
        },
    },
}
</script>

<style scoped>
.inline-input{
    max-width: 30px;
    height: 19px;
}

#shelf_label_preview {
    width: 220px;
    height: 330px;
}
@media (min-width: 576px) {
    #shelf_label_preview {
        width: 320px;
        height: 480px;
    }
}

@media (min-width: 992px) {
    #shelf_label_preview {
        width: 400px;
        height: 600px;
    }
}
</style>
