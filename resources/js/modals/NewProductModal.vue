<template>
    <b-modal body-class="ml-0 mr-0 pl-1 pr-1" :id="modalId" @hidden="emitNotification" size="md" scrollable no-fade>
        <template #modal-header>
            <span>New Product</span>
        </template>

        <div class="container">
            <input id="newProductSku" type="text" :disabled="! isCreatingProduct" v-model="newProduct.sku"
                   class="form-control mb-2" placeholder="Product SKU">
            <input id="newProductName" type="text" v-model="newProduct.name" class="form-control mb-2"
                   placeholder="Product Name">
            <input id="newProductPrice" type="number" :disabled="! isCreatingProduct" v-model="newProduct.price"
                   class="form-control" placeholder="Product Price">
        </div>
        <template #modal-footer>
            <div class="d-flex justify-content-between w-100 m-0">
                <div>
                    <b-button variant="primary" class="float-right" @click="openImportProductsModal">
                        Import CSV File
                    </b-button>
                </div>
                <div class="d-flex">
                    <b-button variant="secondary" class="float-right mr-2" @click="$bvModal.hide(modalId);">
                        Cancel
                    </b-button>
                    <b-button variant="primary" class="float-right" @click="createNewProduct">
                        Create
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

export default {
    components: {ProductCard},
    mixins: [api],

    beforeMount() {
        Modals.EventBus.$on('show::modal::' + this.modalId, (data) => {
            this.product = data['product'];

            this.newProduct = {
                sku: '',
                name: '',
                price: '0.00',
            };

            if (this.product) {
                this.newProduct.sku = this.product.sku;
                this.newProduct.name = this.product.name;
                this.newProduct.price = this.product.price;
            }

            this.$bvModal.show(this.modalId);
        })
    },

    data() {
        return {
            newProduct: {
                sku: '',
                name: '',
                price: '',
            },
            modalId: 'new-product-modal',
            product: undefined
        }
    },

    computed: {
        isCreatingProduct() {
            return this.product === null || (this.product === undefined);
        }
    },

    methods: {
        createNewProduct() {
            this.apiPostProducts(this.newProduct)
                .then(() => {
                    this.$bvModal.hide(this.modalId);
                })
                .catch(error => {
                    this.displayApiCallError(error);
                })
        },

        openImportProductsModal() {
            this.$bvModal.hide(this.modalId);
            this.$modal.showImportProductsModal();
        },

        emitNotification() {
            Modals.EventBus.$emit('hide::modal::' + this.modalId, this.newProduct);
        }
    }
};

</script>
