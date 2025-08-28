<script setup>

import {Link} from "@inertiajs/vue3";
import {ref, watch} from "vue";
import swal from "sweetalert2";

const searchTerm = ref('');
const searchResults = ref({
    users: [],
    teams: [],
    vouchers: [],
    voucherSets: [],
});
const showResults = ref(false);
const showNoResults = ref(false);

watch(searchTerm, () => {
    if (searchTerm.value.length >= 3) {
        getSearchData();
    } else {
        showResults.value = false;
        showNoResults.value = false;
    }
})

watch(searchResults, () => {
    showResults.value = false;
    showNoResults.value = false;

    if (searchTerm.value.length >= 3) {
        if (
            searchResults.value.users.length ||
            searchResults.value.teams.length ||
            searchResults.value.vouchers.length ||
            searchResults.value.voucherSets.length
        ) {
            showResults.value = true;
        } else {
            showNoResults.value = true;
        }
    }
})

function getSearchData() {
    if (searchTerm.value && searchTerm.value.length >= 3) {
        axios.get('/admin/search?cached=false&query=' + searchTerm.value).then(response => {
            searchResults.value = response.data.data;
        }).catch(error => {
            swal.fire({
                icon: 'error',
                title: 'Oops!',
                text: error.response.data.message,
            });
        });
    }
}

function highlightMatchingText(text) {
    /**
     * Matches all occurrences of the search string
     * in a case-insensitive manner
     */
    let reggie = new RegExp(searchTerm.value, "ig");
    let matches = text.matchAll(reggie);

    if (matches.length === 0) {
        return text
    }

    /**
     * For tracking the length change of 'text' variable when portions
     * are replaced with the highlighted text
     */
    let offset = 0;

    /**
     * Each match is an iterable object. The matched character or string is at index 0
     * {
     *     valueMatched,
     *     index: index of valueMatched in input,
     *     input: text that was searched
     * }
     */
    [...matches].forEach(match => {
        const start = text.slice(0, match.index + offset);
        const end = text.slice(match.index + offset + match[0].length);
        const replacement = '<b class="text-blue-500">' + match[0] + '</b>'
        text = start + replacement + end
        offset += replacement.length - match[0].length;
    });

    return text
}

</script>

<template>


    <h2>
        Admin Dashboard
    </h2>


    <div class="flex justify-between flex-wrap">
        <div
            class="w-full md:mt-4 md:w-2/3 md:grow lg:flex lg:justify-start lg:items-start lg:gap-x-4 grid grid-cols-3 font-normal text-base">
            <Link :href="route('admin.home')">
                Admin Home
            </Link>
            <Link :href="route('admin.users')">
                Users
            </Link>
            <Link :href="route('admin.teams')">
                Teams
            </Link>

            <Link :href="route('admin.voucher-sets')">
                Voucher Sets
            </Link>

            <Link :href="route('admin.vouchers')">
                Vouchers
            </Link>

            <Link :href="route('admin.voucher-redemptions')">
                Redemptions
            </Link>

            <Link :href="route('admin.api-access-tokens')">
                API Access Tokens
            </Link>
        </div>

        <div class="w-full md:w-1/3">
            <div class="py-1 h-full w-full pt-4 md:pt-0">
                <input id="admin-search-box"
                       v-model="searchTerm"
                       autofocus
                       class="border-gray-200 placeholder:text-gray-400 rounded p-3 w-full text-blue"
                       placeholder="Search.."
                       type="search"
                >
                <div class="text-xs mt-1 text-gray italic">Min. 3 chars</div>
            </div>
        </div>
    </div>

    <div class="h-full">
        <div v-if="showResults" class="absolute top-48 left-0 w-full h-full min-h-full min-w-full">
            <div class="bg-black fixed top-18 w-full h-screen opacity-60 z-10" @click="showResults = false"></div>
            <div class="bg-white border rounded-lg p-4 mt-6 w-3/4 mx-auto z-20 relative overflow-y-scroll max-h-screen">
                <div class="mb-4 text-2xl">Search Results for "{{ searchTerm }}"</div>

                <div v-if="searchResults.users.length > 0" class="mb-8">
                    <div class="font-bold text-gray-300 mb-1">
                        Users
                    </div>
                    <div>
                        <div v-for="user in searchResults.users">
                            <a :href="'/admin/user/' + user.id"
                               class="flex justify-between w-full py-2 border-b"
                               tabindex="0">
                                <div class="flex space-x-4">
                                    <div v-html="highlightMatchingText(user.name)"></div>
                                    <div v-html="highlightMatchingText(user.email)"></div>
                                </div>
                                <i class="fa fa-chevron-right flex items-center"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <div v-if="searchResults.teams.length > 0" class="my-8">
                    <div class="font-bold text-gray-300 mb-1">
                        Teams
                    </div>
                    <div>
                        <div v-for="team in searchResults.teams">
                            <a :href="'/admin/team/' + team.id"
                               class="flex justify-between w-full py-2 border-b"
                               tabindex="0">
                                <div class="flex">
                                    <div v-html="highlightMatchingText(team.name)"></div>
                                </div>
                                <i class="fa fa-chevron-right flex items-center"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <div v-if="searchResults.voucherSets.length > 0" class="my-8">
                    <div class="font-bold text-gray-300 mb-1">
                        Voucher Sets
                    </div>
                    <div>
                        <div v-for="voucherSet in searchResults.voucherSets">
                            <a :href="'/admin/voucher-set/' + voucherSet.id"
                               class="flex justify-between w-full py-2 border-b"
                               tabindex="0">
                                <div class="">
                                    <div v-html="highlightMatchingText(voucherSet.id)"></div>
                                    <div v-html="highlightMatchingText(voucherSet.name)" class="font-normal"></div>
                                </div>
                                <i class="fa fa-chevron-right flex items-center"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <div v-if="searchResults.vouchers.length > 0" class="my-8">
                    <div class="font-bold text-gray-300 mb-1">
                        Vouchers
                    </div>
                    <div>
                        <div v-for="voucher in searchResults.vouchers">
                            <a :href="'/admin/voucher/' + voucher.id"
                               class="flex justify-between w-full py-2 border-b"
                               tabindex="0">
                                <div class="flex">
                                    <div v-html="highlightMatchingText(voucher.id)"></div>
                                    <div class="ml-2">
                                        <div v-html="highlightMatchingText('(' + voucher.voucher_short_code + ')')"></div>
                                    </div>
                                </div>
                                <i class="fa fa-chevron-right flex items-center"></i>
                            </a>
                        </div>
                    </div>
                </div>


            </div>
        </div>


        <div v-if="showNoResults" class="absolute top-48 left-0 w-full h-full min-h-full min-w-full">
            <div class="bg-black fixed top-18 w-full h-screen opacity-60 z-10" @click="showNoResults = false"></div>
            <div class="bg-white border rounded-lg p-4 mt-6 w-3/4 mx-auto z-20 relative overflow-y-scroll max-h-screen">
                <div class="text-2xl">No Search Results for "{{ searchTerm }}"</div>
            </div>
        </div>

    </div>
</template>

