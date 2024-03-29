<template>
    <input :id="inputId"
           :placeholder="placeholder"
           type=text
           class="form-control barcode-input"
           autocomplete="off"
           autocapitalize="off"
           enterkeyhint="done"
           v-model.trim="searchInput"
           @keyup.enter="$emit('search', searchInput)"
    />

    <template v-if="getUrlParameter('hide_nav_bar', false) === false">
        <div class="row mb-3 pl-1 pr-1">
            <div class="flex-fill">
                <barcode-input-field placeholder="Search activity"
                                     ref="barcode"
                                     @refreshRequest="reloadProducts"
                                     @barcodeScanned="findText"
                />
            </div>

            <button disabled type="button" class="btn btn-primary ml-2" data-toggle="modal" data-target="#filterConfigurationModal"><font-awesome-icon icon="cog" class="fa-lg"></font-awesome-icon></button>
        </div>
    </template>
</template>

<script>
export default {
    props: {
        inputId: {
            type: String,
            default: 'search-filter'
        },
        placeholder: {
            type: String,
            default: 'Search filter...'
        }
    },
    data() {
        return {
            searchInput: ''
        }
    },
}
</script>
