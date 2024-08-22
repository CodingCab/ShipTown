<template>
    <b-modal body-class="ml-0 mr-0 pl-1 pr-1" :id="modalId" size="xl" scrollable no-fade
             hide-header>
        <search-and-option-bar>
            <barcode-input-field
                :input_id="'customer_search_input'"
                placeholder="Search"
                ref="barcode"
                :showKeyboardOnFocus="true"
                @barcodeScanned="findText"
            />
            <template v-slot:buttons>
                <button @click="showNewAddressModal" type="button" class="btn btn-primary ml-2">
                    <font-awesome-icon icon="plus" class="fa-lg"></font-awesome-icon>
                </button>
            </template>
        </search-and-option-bar>
        <div class="customers">
            <div v-for="customer in customers" class="customers__item"
                 :class="{'customers__item--selected': selectedShippingAddress === customer.id || selectedBillingAddress === customer.id}">
                <p><strong>Company: </strong>{{ customer?.company ?? '-' }}</p>
                <p><strong>Address 1: </strong>{{ customer?.address1 ?? '-' }}</p>
                <p><strong>Address 2: </strong>{{ customer?.address2 ?? '-' }}</p>
                <p><strong>City: </strong>{{ customer?.city ?? '-' }}</p>
                <p><strong>Post Code: </strong>{{ customer?.postcode ?? '-' }}</p>
                <div class="customers__itemButtons d-flex">
                    <button class="customers__itemButton"
                            :class="{'customers__itemButton--clicked': selectedBillingAddress === customer.id}"
                            @click="selectBillingAddress(customer.id)">
                        <template v-if="selectedBillingAddress === customer.id">Selected as Billing Address</template>
                        <template v-else>Select as Billing Address</template>
                    </button>
                    <button class="customers__itemButton"
                            :class="{'customers__itemButton--clicked': selectedShippingAddress === customer.id}"
                            @click="selectShippingAddress(customer.id)">
                        <template v-if="selectedShippingAddress === customer.id">Selected as Shipping Address</template>
                        <template v-else>Select as Shipping Address</template>
                    </button>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <div ref="loadingContainerOverride" style="height: 32px"></div>
            </div>
        </div>
        <template #modal-footer>
            <b-button variant="secondary" class="float-right" @click="() => closeModal(false)">
                Cancel
            </b-button>
            <b-button variant="primary" class="float-right" @click="() => closeModal(true)">
                OK
            </b-button>
        </template>
    </b-modal>
</template>

<script>
import api from "../mixins/api.vue";
import url from "../mixins/url.vue";
import loadingOverlay from "../mixins/loading-overlay";
import Modals from "../plugins/Modals";

export default {
    components: {},

    mixins: [loadingOverlay, api, url],

    beforeMount() {
        Modals.EventBus.$on(`show::modal::${this.modalId}`, () => {
            this.$bvModal.show(this.modalId);
        })
    },

    data() {
        return {
            callback: null,
            modalId: 'find-customer-modal',
            customers: [],
            selectedBillingAddress: null,
            selectedShippingAddress: null,
        }
    },

    methods: {
        selectBillingAddress(customerId) {
            this.selectedBillingAddress = customerId;
        },

        selectShippingAddress(customerId) {
            this.selectedShippingAddress = customerId;
        },

        findText(searchText) {
            this.searchText = searchText;
            this.findCustomersContainingSearchText();
        },

        findCustomersContainingSearchText() {
            this.showLoading();

            const params = {};
            params['filter[search]'] = this.searchText;

            this.apiGetCustomers(params)
                .then(({data}) => {
                    this.customers = data.data;
                })
                .catch((error) => {
                    this.displayApiCallError(error);
                })
                .finally(() => {
                    this.hideLoading();
                });
            return this;
        },

        closeModal(saveChanges = false) {
            this.$bvModal.hide(this.modalId);

            Modals.EventBus.$emit(`hide::modal::${this.modalId}`, {
                billingAddress: this.selectedBillingAddress,
                shippingAddress: this.selectedShippingAddress,
                saveChanges: saveChanges
            });
        },

        showNewAddressModal() {
            this.$modal.showAddNewAddressModal();
        }
    }
};

</script>

<style lang="scss" scoped>
.customers {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;

    &__item {
        flex: 0 0 calc(33.33333% - 20px + (20px / 3));
        max-width: calc(33.33333% - 20px + (20px / 3));
        padding: 10px;
        border: 1px solid #ced4da;
        border-radius: 4px;

        &--selected {
            border-color: #227dc7;
        }
    }

    &__itemButtons {
        gap: 10px;
    }

    &__itemButton {
        padding: 5px 10px;
        border: 1px solid #ced4da;
        border-radius: 4px;
        background-color: #f8f9fa;
        cursor: pointer;
        transition: background-color 0.3s, color 0.3s;

        &--clicked {
            background-color: #227dc7;
            color: white;
        }
    }

    @media all and (max-width: 576px) {
        &__item {
            flex: 0 0 calc(50% - 20px + (20px / 2));
            max-width: calc(50% - 20px + (20px / 2));
        }
    }
}
</style>
