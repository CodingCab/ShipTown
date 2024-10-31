<template>
    <b-modal body-class="ml-0 mr-0 pl-1 pr-1" :id="modalId" @hidden="emitNotification" size="xl" scrollable no-fade>
        <template #modal-header>
            <span>{{ modalTitle }}</span>
        </template>

        <div class="container">
            <input id="vatRateSku" type="text" v-model="vatRate.code"
                   class="form-control mb-2" placeholder="Vat Rate - Code">
            <input id="vatRateName" type="number" v-model="vatRate.rate" class="form-control mb-2"
                   placeholder="Vat Rate - Rate" step="1">
        </div>
        <template #modal-footer>
            <b-button variant="secondary" class="float-right" @click="$bvModal.hide(modalId);">Cancel</b-button>
            <b-button variant="primary" class="float-right" @click="saveVatRate">{{ buttonText }}</b-button>
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
            if (typeof data.id !== 'undefined' && data.id) {
                this.vatRate = {
                    id: data.id,
                    code: data.vatRate.code,
                    rate: data.vatRate.rate,
                };
                this.update = true;
            }

            this.$bvModal.show(this.modalId);
        })
    },

    data() {
        return {
            vatRate: {
                id: 0,
                code: '',
                rate: null,
            },
            saved: false,
            update: false,
            modalId: 'module-vat-rates-create-update-vat-rate-modal',
        }
    },

    computed: {
        modalTitle() {
            return this.update ? 'Update Vat Rate' : 'Create Vat Rate';
        },
        buttonText() {
            return this.update ? 'Update' : 'Create';
        }
    },

    methods: {
        saveVatRate() {
            if (this.update) {
                this.apiPutVatRate(this.vatRate)
                    .then(response => {
                        if (response.status === 200) {
                            this.saved = true;
                            this.$bvModal.hide(this.modalId);
                        }
                    })
                    .catch(error => {
                        this.displayApiCallError(error);
                    })
            } else {
                this.apiPostVatRate(this.vatRate)
                    .then(response => {
                        if (response.status === 201) {
                            this.saved = true;
                            this.$bvModal.hide(this.modalId);
                        }
                    })
                    .catch(error => {
                        this.displayApiCallError(error);
                    })
            }
        },

        emitNotification() {
            Modals.EventBus.$emit('hide::modal::' + this.modalId, {
                vatRate: this.vatRate,
                saved: this.saved
            });

            this.saved = false;
            this.update = false;
            this.vatRate = {
                id: 0,
                code: '',
                rate: null,
            };
        }
    }
};

</script>
