<script setup>
import {Link, usePage} from '@inertiajs/vue3';
import {onMounted, ref} from "vue";
import TextInput from "@/Components/TextInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import Swal from "sweetalert2";

const countries = ref({})
const myTeam = ref({})
const newCountry = ref({})
const searchStr = ref('')
const updatingCountry = ref(false)

onMounted(() => {
    getMyTeam()
});

function cancelUpdatingCountry() {
    updatingCountry.value = false
    searchStr.value = ''
    countries.value = {}
}

function getMyTeam() {
    axios.get('/my-team?cached=false&relations=country').then(response => {
        myTeam.value = response.data.data

        newCountry.value = myTeam.value.country
    }).catch(error => {
        console.log(error)
    })
}

function newCountrySelected(country) {
    newCountry.value = country
    countries.value = {}
    updatingCountry.value = false
}

function saveNewCountry() {
    let payload = {
        country_id: newCountry.value.id
    }
    axios.put('/my-team/' + usePage().props.auth.user.current_team_id, payload).then(response => {
        Swal.fire({
            title: 'Success!',
            icon: 'success',
            timer: 1000
        }).then(() => {
            getMyTeam()
        })
    }).catch(error => {
        Swal.fire({
            icon: 'error',
            title: 'Oops!',
            text: error.response.data.message
        });
    })
}

function searchCountry() {
    if (searchStr.value.length > 2) {
        axios.get('/countries?cached=false&where[]=name,like,*' + searchStr.value + '*&').then(response => {
            countries.value = response.data.data
        }).catch(error => {
            console.log(error)
        })
    }
}

</script>

<template>
    <div v-if="myTeam.country" class="card">
        <div class="card-header">
            Country / Currency
        </div>

        <div v-if="updatingCountry">
            <div class="flex justify-between items-center">
                <div class="flex-grow mr-2">
                    <TextInput
                        id="country"
                        type="text"
                        class="mt-1 block w-full"
                        v-model="searchStr"
                        required
                        @keyup="searchCountry()"
                    />
                </div>
                <div class="ml-2">
                    <PrimaryButton v-if="searchStr.length === 0 || newCountry.id === myTeam.country_id" @click="cancelUpdatingCountry()">Cancel</PrimaryButton>
                </div>
            </div>

            <div v-if="countries.data && countries.data.length" class="p-2 text-sm">
                <div v-for="country in countries.data" class="py-1 border-b cursor-pointer hover:opacity-50" @click="newCountrySelected(country)">
                    {{ country.name }}
                </div>
            </div>
        </div>

        <div v-else>
            <div class="flex justify-between items-center">
                <div @click="updatingCountry = true" class="cursor-pointer flex-grow mr-2">
                    {{ newCountry.name }} ({{ newCountry.currency_code }})
                </div>
                <div class="ml-2" v-if="searchStr.length > 0 && newCountry.id !== myTeam.country_id">
                    <PrimaryButton @click="saveNewCountry()">Save</PrimaryButton>
                </div>
            </div>
            <div v-if="searchStr.length > 0 && newCountry.id !== myTeam.country_id" class="text-red-500 text-xs font-bold mt-2">
                *Swapping a team's country will NOT update the selected currency for existing voucher sets
            </div>
        </div>
    </div>
</template>
