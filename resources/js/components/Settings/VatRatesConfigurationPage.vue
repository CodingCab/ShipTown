<template>
    <div>
        <search-and-option-bar-observer/>
        <search-and-option-bar :isStickable="true">
            <barcode-input-field
                :input_id="'barcode_input'"
                placeholder="Search"
                ref="barcode"
                :url_param_name="'search'"
                @refreshRequest="reloadVatRatesList"
                @barcodeScanned="findText"
            />
            <template v-slot:buttons>
                <button @click="showNewVatRateModal" type="button" class="btn btn-primary ml-2">
                    <font-awesome-icon icon="plus" class="fa-lg"></font-awesome-icon>
                </button>
                <top-nav-button v-b-modal="'quick-actions-modal'"/>
            </template>
        </search-and-option-bar>

        <div class="row pl-2 p-0">
            <breadcrumbs></breadcrumbs>
        </div>

        <template
            v-if="isLoading === false && vatRates !== null && Array.isArray(vatRates) && vatRates.length === 0">
            <div class="text-secondary small text-center mt-3">
                No records found<br>
                Click + to create one<br>
            </div>
        </template>

        <template v-if="vatRates" v-for="vatRate in vatRates">
            <swiping-card :disable-swipe-right="true" :disable-swipe-left="true">
                <template v-slot:content>
                    <div role="button" class="row">
                        <div class="col-12 col-md-9">
                            <div class="text-primary">{{ vatRate['code'] }}</div>
                            <div class="text-secondary small">Rate: {{ vatRate['rate'] }}%</div>
                        </div>
                        <div class="col-12 col-md-3 d-flex action-buttons align-items-center justify-content-end">
                            <button
                                class="action-button action-button--edit d-inline-flex align-items-center justify-content-center"
                                title="Edit"
                                @click="editVatRate(vatRate['id'])">
                                <font-awesome-icon icon="edit" class="fa-lg"></font-awesome-icon>
                            </button>
                            <button
                                class="action-button action-button--remove d-inline-flex align-items-center justify-content-center"
                                title="Delete"
                                @click="removeVatRate(vatRate['id'])">
                                <font-awesome-icon icon="trash" class="fa-lg"></font-awesome-icon>
                            </button>
                        </div>
                    </div>
                </template>
            </swiping-card>
        </template>

        <div class="row">
            <div class="col">
                <div ref="loadingContainerOverride" style="height: 32px"></div>
            </div>
        </div>

        <b-modal id="quick-actions-modal" no-fade hide-header @hidden="setFocusElementById('barcode_input')">
            <stocktake-input></stocktake-input>
            <template #modal-footer>
                <b-button variant="secondary" class="float-right" @click="$bvModal.hide('quick-actions-modal');">
                    Cancel
                </b-button>
                <b-button variant="primary" class="float-right" @click="$bvModal.hide('quick-actions-modal');">
                    OK
                </b-button>
            </template>
        </b-modal>

        <module-vat-rates-create-update-vat-rate-modal></module-vat-rates-create-update-vat-rate-modal>
    </div>
</template>

<script>
import loadingOverlay from "../../mixins/loading-overlay";
import url from "../../mixins/url.vue";
import api from "../../mixins/api.vue";
import helpers from "../../mixins/helpers";
import Breadcrumbs from "../Reports/Breadcrumbs.vue";
import BarcodeInputField from "../SharedComponents/BarcodeInputField.vue";
import SwipingCard from "../SharedComponents/SwipingCard.vue";
import Modals from "../../plugins/Modals";

export default {
    mixins: [loadingOverlay, url, api, helpers],

    components: {
        Breadcrumbs,
        BarcodeInputField,
        SwipingCard,
    },

    data: () => ({
        pagesLoadedCount: 1,
        reachedEnd: false,
        vatRates: null,
        perPage: 20,
        scrollPercentage: 70,
    }),

    mounted() {
        window.onscroll = () => this.loadMore();

        Modals.EventBus.$on('hide::modal::module-vat-rates-create-update-vat-rate-modal', (data) => {
            if (typeof data.saved !== 'undefined' && data.saved) {
                this.reloadVatRatesList();
            }
        });

        this.reloadVatRatesList();
    },

    methods: {
        findText(search) {
            this.setUrlParameter('search', search);
            this.reloadVatRatesList();
        },

        reloadVatRatesList() {
            this.vatRates = null;

            this.findVatRates();
        },

        findVatRates(page = 1) {
            this.showLoading();

            const params = {...this.$router.currentRoute.query};
            params['filter[search]'] = this.getUrlParameter('search') ?? '';
            params['per_page'] = this.perPage;
            params['page'] = page;

            this.apiGetVatRates(params)
                .then(({data}) => {
                    this.vatRates = this.vatRates ? this.vatRates.concat(data.data) : data.data
                    this.reachedEnd = data.data.length === 0;
                    this.pagesLoadedCount = page;

                    this.scrollPercentage = (1 - this.perPage / this.vatRates.length) * 100;
                    this.scrollPercentage = Math.max(this.scrollPercentage, 70);
                })
                .catch((error) => {
                    this.displayApiCallError(error);
                })
                .finally(() => {
                    this.hideLoading();
                });

            return this;
        },

        loadMore() {
            if (this.isMoreThanPercentageScrolled(this.scrollPercentage) && this.hasMorePagesToLoad() && !this.isLoading) {
                this.findVatRates(++this.pagesLoadedCount);
            }
        },

        hasMorePagesToLoad() {
            return this.reachedEnd === false;
        },

        showNewVatRateModal() {
            this.$modal.showCreateUpdateVatRateModal();
        },

        editVatRate(id) {
            const vatRate = this.vatRates.find(vatRate => vatRate.id === id);

            if (typeof vatRate === 'undefined') {
                return;
            }

            this.$modal.showCreateUpdateVatRateModal(id, vatRate);
        },

        removeVatRate(id) {
            this.showLoading();

            this.apiDeleteVatRate(id)
                .then(() => {
                    this.reloadVatRatesList();
                })
                .catch((error) => {
                    this.displayApiCallError(error);
                })
                .finally(() => {
                    this.hideLoading();
                });
        },
    },
}
</script>

<style lang="scss" scoped>
.action-buttons {
    gap: 10px;
}

.action-button {
    cursor: pointer;
    width: 40px;
    height: 40px;
    background-color: transparent;
    border: 1px solid #3490dc;
    color: #3490dc;
    transition: background-color 0.3s, color 0.3s;

    &:hover {
        background-color: #3490dc;
        color: white;
    }

    &--remove {
        border-color: #dc3545;
        color: #dc3545;

        &:hover {
            background-color: #dc3545;
            color: white;
        }
    }
}
</style>
