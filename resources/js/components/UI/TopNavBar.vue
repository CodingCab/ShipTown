<template>
    <div class="row mb-2 pl-1 pr-1 bg-light d-flex flex-nowrap" :class="isSticky ? 'sticky-top' : ''" v-if="!getUrlParameter('hide_nav_bar', false)">
        <div class="flex-fill">
            <slot/>
        </div>
        <slot name="buttons"></slot>
        <button v-if="isSticky" type="button" class="btn btn-primary ml-1 md:ml-2" @click="scrollToTop"><font-awesome-icon icon="caret-up" class="fa-lg"></font-awesome-icon></button>
    </div>
</template>

<script>
import url from "../../mixins/url";
export default {
    mixins: [url],
    props: {
        isSticky: {
            default: false
        }
    },
    methods: {
        scrollToTop() {
            window.scrollTo(0, 0);
        }
    }
}
</script>

