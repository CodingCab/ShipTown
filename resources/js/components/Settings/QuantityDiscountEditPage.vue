<template>
    <div v-if="discount">
        <swiping-card>
            <template v-slot:content>
                <div class="row setting-list">
                    <div class="col-sm-12 col-lg-4">
                        <div class="text-primary">{{ discount['name'] }}</div>
                        <div class="text-secondary small">
                            {{ formatDateTime(discount['created_at'], 'dddd - MMM D HH:mm') }}
                        </div>
                        <div class="text-secondary small">{{ discountTypes[discount['type']] }}</div>
                    </div>
                    <div class="col-sm-12 col-lg-4">
                        <div class="text-primary">Configuration</div>
                        <template v-if="discount.type === 'BUY_X_GET_Y_FOR_Z_PRICE'">
                            <div class="text-secondary small">
                                Quantity full price:
                                {{ dashIfEmpty(discount['configuration']?.quantity_full_price ?? '') }}
                            </div>
                            <div class="text-secondary small">
                                Quantity discounted:
                                {{ dashIfEmpty(discount['configuration']?.quantity_discounted ?? '') }}
                            </div>
                            <div class="text-secondary small">
                                Discounted price: {{ dashIfEmpty(discount['configuration']?.discounted_price ?? '') }}
                            </div>
                        </template>
                        <template v-else-if="discount.type === 'BUY_X_GET_Y_FOR_Z_PERCENT_DISCOUNT'">
                            <div class="text-secondary small">
                                Quantity full price:
                                {{ dashIfEmpty(discount['configuration']?.quantity_full_price ?? '') }}
                            </div>
                            <div class="text-secondary small">
                                Quantity discounted:
                                {{ dashIfEmpty(discount['configuration']?.quantity_discounted ?? '') }}
                            </div>
                            <div class="text-secondary small">
                                Discount percent: {{ dashIfEmpty(discount['configuration']?.discount_percent ?? '') }}
                            </div>
                        </template>
                        <template v-else-if="discount.type === 'BUY_X_GET_Y_PRICE'">
                            <div class="text-secondary small">
                                Quantity required: {{ dashIfEmpty(discount['configuration']?.quantity_required ?? '') }}
                            </div>
                            <div class="text-secondary small">
                                Discounted unit price:
                                {{ dashIfEmpty(discount['configuration']?.discounted_unit_price ?? '') }}
                            </div>
                        </template>
                        <template v-else>
                            <div class="text-secondary small">
                                Quantity required: {{ dashIfEmpty(discount['configuration']?.quantity_required ?? '') }}
                            </div>
                            <div class="text-secondary small">
                                Discount percent: {{ dashIfEmpty(discount['configuration']?.discount_percent ?? '') }}
                            </div>
                        </template>
                    </div>
                    <div class="col-sm-12 col-lg-4 text-right">
                        <top-nav-button v-b-modal="'configuration-modal'" icon="edit"/>
                    </div>
                </div>
            </template>
        </swiping-card>

        <search-and-option-bar-observer/>
        <search-and-option-bar :isStickable="true">
            <div class="d-flex flex-nowrap">
                <div class="flex-fill">
                    <barcode-input-field :input_id="'barcode_input'"
                                         placeholder="Enter SKU"
                                         ref="barcode"
                                         :url_param_name="'search'"
                                         @barcodeScanned="addProductToDiscount">
                    </barcode-input-field>
                </div>
            </div>
        </search-and-option-bar>

        <div v-if="(products !== null) && (products.length === 0)" class="text-secondary small text-center mt-3">
            No products found<br>
            Scan or type in SKU to add products to this discount<br>
        </div>

        <template v-for="product in products">
            <swiping-card :disable-swipe-right="true" :disable-swipe-left="true">
                <template v-slot:content>
                    <div class="row">
                        <div class="col-12 col-md-4">
                            <product-info-card :product="product"></product-info-card>
                        </div>
                        <div class="col-12 col-md-3 text-left small">
                            <div>Price: <strong>{{ dashIfZero(Number(product['price'])) }}</strong></div>
                            <div>Sale price: <strong>{{ dashIfZero(Number(product['sale_price'])) }}</strong></div>
                        </div>
                        <div class="col-12 col-md-5 d-flex align-items-center justify-content-end">
                            <button class="remove-product d-inline-flex align-items-center justify-content-center"
                                    @click="removeProductFromDiscount(product['discount_product_id'])">
                                <font-awesome-icon icon="trash" class="fa-lg"></font-awesome-icon>
                            </button>
                        </div>
                    </div>
                </template>
            </swiping-card>
        </template>

        <b-modal id="configuration-modal" no-fade hide-header @hidden="setFocusElementById('barcode_input')">
            <form @submit.prevent="saveDiscountConfiguration">
                <div class="form-group">
                    <label class="form-label" for="base_url">Base URL</label>
                    <input type="text" class="form-control">
                </div>
            </form>
            <hr>
            <template #modal-footer>
                <b-button variant="secondary" class="float-right" @click="$bvModal.hide('configuration-modal');">
                    Cancel
                </b-button>
                <b-button variant="primary" class="float-right" @click="$bvModal.hide('configuration-modal');">
                    OK
                </b-button>
            </template>
        </b-modal>
    </div>
</template>

<script>
import api from "../../mixins/api.vue";
import helpers from "../../mixins/helpers";
import SwipingCard from "../SharedComponents/SwipingCard.vue";
import loadingOverlay from "../../mixins/loading-overlay";
import beep from "../../mixins/beep";
import url from "../../mixins/url.vue";

export default {
    mixins: [loadingOverlay, beep, url, api, helpers],

    components: {
        SwipingCard,
    },

    props: {
        initialDiscount: {
            type: String,
            default: null
        },
        initialProducts: {
            type: String,
            default: null
        }
    },

    data() {
        return {
            discount: null,
            products: null,
            configuration: {
                quantity_full_price: null,
                quantity_discounted: null,
                discounted_price: null,
                discount_percent: null,
                quantity_required: null,
                discounted_unit_price: null
            },
            discountTypes: {
                'BUY_X_GET_Y_FOR_Z_PRICE': 'Buy X, get Y for Z price',
                'BUY_X_GET_Y_FOR_Z_PERCENT_DISCOUNT': 'Buy X, get Y for Z percent discount',
                'BUY_X_GET_Y_PRICE': 'Buy X for Y price',
                'BUY_X_FOR_Y_PERCENT_DISCOUNT': 'Buy X for Y percent discount'
            },
        }
    },

    mounted() {
        if (this.initialDiscount) {
            this.discount = JSON.parse(this.initialDiscount);
        }
        if (this.initialProducts) {
            this.products = JSON.parse(this.initialProducts);
        }
    },

    methods: {
        addProductToDiscount(barcode) {
            if (barcode.trim() === '') {
                return;
            }

            this.apiGetProducts({'filter[sku_or_alias]': barcode, 'include': 'prices'})
                .then(response => {
                    if (response.data.data.length === 0) {
                        this.notifyError('Product "' + barcode + '" not found.');
                        return;
                    }

                    this.beep();

                    const product = response.data.data[0];

                    if (this.products === null) {
                        this.products = [];
                    }

                    if (!this.products.some(p => p.id === product.id)) {
                        this.apiPostQuantityDiscountProduct({
                            quantity_discount_id: this.discount.id,
                            product_id: product.id
                        })
                            .then(response => {
                                if (typeof response.data !== 'undefined' && typeof response.data !== 'undefined') {
                                    this.products = response.data;
                                }
                            })
                            .catch(error => {
                                this.displayApiCallError(error);
                            });
                    } else {
                        this.notifyError(`Product "${barcode}" is already added to this discount.`);
                    }
                })
                .catch(error => {
                    this.displayApiCallError(error);
                });
        },

        removeProductFromDiscount(discountProductId) {
            this.apiRemoveQuantityDiscountProduct(discountProductId)
                .then(response => {
                    if (typeof response.data !== 'undefined' && typeof response.data !== 'undefined') {
                        this.products = response.data;
                        this.notifySuccess('Product removed from discount.');
                    }
                })
                .catch(error => {
                    this.displayApiCallError(error);
                });
        },

        saveDiscountConfiguration() {
        },
    }
}
</script>

<style lang="scss" scoped>
.setting-list {
    width: 100%;
    color: #495057;
    display: flex;
    align-items: flex-start;
    margin-bottom: 5px;
}

.remove-product {
    cursor: pointer;
    width: 40px;
    height: 40px;
    border: 1px solid #dc3545;
    color: #dc3545;
    background-color: transparent;
    transition: background-color 0.3s, color 0.3s;

    &:hover {
        background-color: #dc3545;
        color: white;
    }
}
</style>