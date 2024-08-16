<script setup>
import {onMounted, ref} from "vue";
import TextInput from "@/Components/TextInput.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import Swal from "sweetalert2";

const $props = defineProps({
    searchStr: {
        default: null
    }
})

const team = ref({name: ''})

const emit = defineEmits([
        'teamCreated'
    ]
);

onMounted(() => {
    if ($props.searchStr !== null) {
        team.value.name = $props.searchStr
    }
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
        console.log(error)
    })
}

</script>

<template>
    <form @submit.prevent="createNewTeam()">
        <div>
            <InputLabel for="name" value="Name"/>
            <TextInput
                id="name"
                type="text"
                class="mt-1 block w-full"
                v-model="team.name"
                required
            />
        </div>
        <div class="flex items-center justify-end mt-4">
            <PrimaryButton class="ms-4" :class="{ 'opacity-25': !team.name }" :disabled="!team.name">
                Submit
            </PrimaryButton>
        </div>
    </form>
</template>
