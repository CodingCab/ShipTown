<template>
    <div class="row mb-2 pl-1 pr-1" v-if="getUrlParameter('hide_nav_bar', false) === false">
        <div class="flex-fill">
            <slot/>
        </div>
        <slot name="buttons"></slot>
    </div>
</template>

<script>

import url from "../../mixins/url";
export default {
    mixins: [url],
}
</script>


