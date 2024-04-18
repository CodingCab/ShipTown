<template>
    <div ref="stickyDiv" class="sticky-container row mb-2 pl-1 pr-1 py-2 bg-light d-flex flex-nowrap" :class="{ 'sticky-top': isSticky }" v-if="!getUrlParameter('hide_nav_bar', false)">
        <div class="flex-fill">
            <slot/>
        </div>
        <slot name="buttons"></slot>
        <button v-if="showStickyButton && isSticky" type="button" class="btn btn-primary ml-1 md:ml-2" @click="scrollToTop">
            <font-awesome-icon icon="angles-up" class="fa-lg"></font-awesome-icon>
        </button>
    </div>
</template>

<script>
import url from "../../mixins/url";

export default {
    mixins: [url],
    props: {
        isSticky: {
            type: Boolean,
            default: false
        }
    },
    data() {
        return {
            showStickyButton: false
        }
    },
    mounted() {
        this.$eventBus.$on('observer-status', (isVisible) => {
            this.showStickyButton = !isVisible;
        });
    },
    methods: {
        scrollToTop() {
            window.scrollTo(0, 0);
        }
    },
}
</script>
