<script setup>
import {Link, usePage} from '@inertiajs/vue3';
import {onMounted, ref} from "vue";
import TextInput from "@/Components/TextInput.vue";

const myTeam = ref({})
const newCountry = ref({})
const searchStr = ref('')

onMounted(() => {
    getMyTeam()
});

function getMyTeam() {
    axios.get('/my-team?cached=false&relations=country').then(response => {
        myTeam.value = response.data.data

        newCountry.value = myTeam.value.country
    }).catch(error => {
        console.log(error)
    })
}

function searchCountry() {
    if (newCountry.value.name.length > 2) {
        axios.get('/countries?cached=false&where[]=name,like,*' + searchStr.value + '*&')
    }
}

</script>

<template>
    <div class="card">
        <div class="card-header">
            Country / Currency
        </div>


        <div v-if="myTeam.country">
            <TextInput
                id="country"
                type="text"
                class="mt-1 block w-full"
                v-model="newCountry.name"
                required
                @keyup="searchCountry()"
            />

            <!--            {{myTeam.country.name}} ({{myTeam.country.currency_code}})-->
        </div>
    </div>
</template>
