<template>
    <div>
        <div class="d-flex pl-1 pr-1">
            <div class="d-inline font-weight-bold text-uppercase small text-secondary align-content-center">
                REPORTS > {{ reportName }}
            </div>
            <div class="flex-grow-1">
                <div class="filter-container d-none d-lg-flex" ref="filterContainer">
                    <p class="text-primary small" v-for="filter in filters" :key="filter.id">
                        <span v-html="filterToHumanString(filter)"></span>
                        <button @click="removeFilter(filter, $event)" class="btn btn-link p-0 ml-1 mb-1">x</button>
                    </p>
                </div>
                <a href="#" @click.prevent="showFilters = !showFilters" class="float-right d-lg-none small">
                    {{ showFilters ? 'HIDE' : 'FILTERS' }} <span v-show="!showFilters">({{ filters.length }})</span>
                </a>
            </div>
        </div>

        <div class="filter-container d-flex d-lg-none" ref="filterContainer">
            <p v-if="showFilters" class="text-primary small" v-for="filter in filters" :key="filter.id">
                {{ filter.displayName }} <span v-html="filterToHumanString(filter)"></span><!--
            --><button @click="removeFilter(filter, $event)" class="btn btn-link p-0 ml-1 mb-1">x</button>
            </p>
        </div>
    </div>
</template>

<script>
export default {

    props: {
        reportName: String,
        filters: Array,
    },

    data() {
        return {
            showFilters: false,
        }
    },

    methods: {
        removeFilter(filter, event) {
            this.$emit('remove-filter', filter);
        },

        filterToHumanString(filter) {
            if (filter.selectedOperator === 'btwn') {
                return `${filter.displayName} between ${filter.value} <b>&</b> ${filter.valueBetween}`;
            } else {
                return `${filter.displayName} ${filter.selectedOperator} "${filter.value}"`;
            }
        },
    }
}
</script>

<style scoped>

.filter-container {
    flex-direction: row-reverse;
    flex-wrap: wrap;
    p {
        flex: 0 0 auto;
        margin: 0 0 0 10px;
        -webkit-user-select: none;
    }
}

.btn-link {
    outline: none;
    box-shadow: none;
    border-color: transparent;
}

</style>
