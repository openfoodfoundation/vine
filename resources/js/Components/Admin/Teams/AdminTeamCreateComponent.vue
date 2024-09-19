<script setup>
import {onMounted, ref} from "vue";
import TextInput from "@/Components/TextInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import Swal from "sweetalert2";

const $props = defineProps({
    searchStr: {
        default: null
    }
})

const countries = ref({})
const team = ref({
    name: '',
    country_id: ''
})

const emit = defineEmits([
        'teamCreated'
    ]
);

onMounted(() => {
    if ($props.searchStr !== null) {
        team.value.name = $props.searchStr
    }

    getCountries();
})

function createNewTeam() {
    axios.post('/admin/teams', team.value).then(response => {
        Swal.fire({
            title: 'Success!',
            icon: 'success',
            timer: 1000
        }).then(() => {
            let team = response.data.data

            emit('teamCreated', team)
            team.value = {}
        })
    }).catch(error => {
        Swal.fire({
            icon: 'error',
            title: 'Oops!',
            text: error.response.data.message,
        });
    })
}


function getCountries() {
    axios.get('/countries?limit=300').then(response => {
        countries.value = response.data.data;
    }).catch(error => {
        console.log(error)
    })
}

</script>

<template>
    <form @submit.prevent="createNewTeam()">
        <div>
            <div class="flex justify-start items-center mt-4">
                <label class="w-full font-bold" for="name">
                    Team Name:
                    <TextInput
                        id="name"
                        v-model="team.name"
                        class="mt-1 block w-full font-normal"
                        type="text"/>
                </label>
            </div>

            <div class="flex justify-start items-center mt-4">
                <label class="w-full font-bold" for="country">
                    Country:
                    <select id="country" v-model="team.country_id" class="mt-1 block w-full font-normal">
                        <option :value="''">Select a country</option>
                        <option v-for="country in countries.data" :key="country.id" :value="country.id">
                            {{ country.name }}
                        </option>
                    </select>
                </label>
            </div>
        </div>
        <div class="flex items-center justify-end mt-4">
            <PrimaryButton :class="{ 'opacity-25': !team.name || !team.country_id }" :disabled="!team.name || !team.country_id"
                           class="ms-4 hover:cursor-pointer"
                           @click="">
                Submit
            </PrimaryButton>
        </div>
    </form>
</template>
