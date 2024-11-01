<template>
    <b-modal body-class="ml-0 mr-0 pl-1 pr-1" :id="modalId" @hidden="emitNotification" size="md" scrollable no-fade>
        <template #modal-header>
            <span>Import Products</span>
        </template>

        <vue-csv-import v-model="csv"
                        headers
                        canIgnore
                        autoMatchFields
                        loadBtnText="Load"
                        :map-fields="['sku', 'name', 'department', 'category', 'price', 'sale_price', 'sale_price_start_date', 'sale_price_end_date', 'commodity_code', 'supplier']">
            <template slot="hasHeaders" slot-scope="{headers, toggle}">
                <label>
                    <input type="checkbox" id="hasHeaders" :value="headers" @change="toggle">
                    Headers?
                </label>
            </template>

            <template slot="error">
                File type is invalid
            </template>

            <template slot="thead">
                <tr>
                    <th>My Fields</th>
                    <th>Column</th>
                </tr>
            </template>

            <template slot="submit" slot-scope="{submit}">
                <button @click.prevent="submit">Import</button>
            </template>
        </vue-csv-import>

        <button v-if="csv" type="button" @click.prevent="importProducts" class="col btn mb-1 btn-primary">Import
            Products
        </button>

        <template #modal-footer>
            <div class="d-flex justify-content-between w-100 m-0">
                <div>
                    <b-button variant="primary" class="float-left mr-2" href="/templates/product_import_template.csv" target="_blank">
                        Download Template
                    </b-button>
                </div>
                <div class="d-flex">
                    <b-button variant="secondary" class="float-right mr-2" @click="$bvModal.hide(modalId);">
                        Cancel
                    </b-button>
                </div>
            </div>
        </template>
    </b-modal>
</template>

<script>

import ProductCard from "../components/Products/ProductCard.vue";
import api from "../mixins/api.vue";
import Modals from "../plugins/Modals";
import {VueCsvImport} from 'vue-csv-import';

export default {
    components: {ProductCard, VueCsvImport},
    mixins: [api],

    beforeMount() {
        Modals.EventBus.$on('show::modal::' + this.modalId, () => {
            this.$bvModal.show(this.modalId);
        })
    },

    data() {
        return {
            modalId: 'import-products-modal',
            csv: null,
            refreshList: false
        }
    },

    methods: {
        importProducts() {
            const data = this.csv.map(record => ({
                'sku': record.sku,
                'name': record.name,
                'department': record.department,
                'category': record.category,
                'price': record.price,
                'sale_price': record.sale_price,
                'sale_price_start_date': record.sale_price_start_date,
                'sale_price_end_date': record.sale_price_end_date,
                'commodity_code': record.commodity_code,
                'supplier': record.supplier
            }));

            data.shift();

            this.apiPostCsvImportProducts({data})
                .then(response => {
                    if (
                        typeof response.data !== 'undefined' &&
                        typeof response.data.data !== 'undefined' &&
                        typeof response.data.data.success !== 'undefined' &&
                        response.data.data.success
                    ) {
                        this.notifySuccess('Products imported successfully');
                        this.refreshList = true;
                        this.csv = null;
                        this.$bvModal.hide(this.modalId);
                    }
                })
                .catch(error => {
                    this.displayApiCallError(error);
                })
        },

        emitNotification() {
            Modals.EventBus.$emit('hide::modal::' + this.modalId, {refreshList: this.refreshList});
        }
    }
};

</script>
