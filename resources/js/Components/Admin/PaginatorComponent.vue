<template>
    <div v-if="(paginationData.total > paginationData.per_page)">

        <div class="grid grid-cols-2 gap-2 text-center">
            <div>
                <SecondaryButton class="px-8 w-full flex justify-center" @click="paginatePrevious()">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                    </svg>

                </SecondaryButton>
            </div>
            <div>
                <SecondaryButton class="px-8 w-full flex justify-center"
                        @click="paginateNext()">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                    </svg>
                </SecondaryButton>
            </div>
        </div>

        <div class="w-full text-center text-sm" v-if="showSummary">
            Showing {{paginationData.from}} - {{ paginationData.to }} of {{ paginationData.total }}
        </div>

    </div>


</template>

<script>

import SecondaryButton from "@/Components/SecondaryButton.vue";

export default {
    components: {SecondaryButton},
    props: {
        paginationData: {
            type: Object,
            required: true
        },
        showSummary: {
            type: Boolean,
            required: false,
            default: true
        }
    },
    mounted() {

    },
    created() {


    },
    methods: {
        paginatePrevious() {
            if (this.paginationData.current_page > 1) {

                if(this.component)
                {
                    this.$emit('setDataPageForComponent', {
                        page: (this.paginationData.current_page - 1),
                        component: this.component,
                    });
                }
                else
                {
                    this.$emit('setDataPage', (this.paginationData.current_page - 1));
                }
            }
        },
        paginateNext() {

            if (this.paginationData.total > this.paginationData.to) {

                if(this.component)
                {
                    this.$emit('setDataPageForComponent', {
                        page: (this.paginationData.current_page + 1),
                        component: this.component,
                    });
                }
                else
                {

                    this.$emit('setDataPage', (this.paginationData.current_page + 1));
                }
            }
        }
    }
}
</script>
