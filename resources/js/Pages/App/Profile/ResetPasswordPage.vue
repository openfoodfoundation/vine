<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, usePage} from '@inertiajs/vue3';
import {onMounted, ref} from "vue";
import ApplicationLogo from '@/Components/ApplicationLogo.vue';

import TextInput from "@/Components/TextInput.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import Swal from "sweetalert2";


const newPassword = ref('')

function updateMyPassword(){
    console.log('updating')

    let payload = {
        password: newPassword.value,
    }

    axios.put('/my-profile/1234', payload).then(response => {

        Swal.fire({
            icon: 'success',
            title: 'Updated. Redirecting..',
            text: response.data.meta.message,
        });


        setTimeout(fn => {
            window.location.href = '/dashboard';
        }, 1000)

    }).catch(error => {
        console.log(error);
        Swal.fire({
            icon: 'error',
            title: 'Oops!',
            text: error.response.data.meta.message,
        });
    })
}


onMounted(() => {

})



</script>

<template>
    <Head title="Set / Reset Password"/>

    <AuthenticatedLayout>


        <div class="grid grid-cols-1 lg:grid-cols-3 pt-32">
            <div></div>
            <div>
                <ApplicationLogo class="h-16 fill-current w-full" />

                <div class="p-4 lg:p-6 bg-white shadow-sm sm:rounded-lg my-4">
                    <div class="">

                        Welcome, {{usePage().props.auth.user.name.split(' ')[0]}}! Please set your password.

                        <div class="mt-4">
                            <InputLabel for="password" value="Password" />

                            <TextInput type="password"  id="password" v-model="newPassword" class="w-full"/>

                            <div class="flex justify-end mt-4">
                                <PrimaryButton @click="updateMyPassword()">
                                    Set Password & Continue >
                                </PrimaryButton>
                            </div>
                        </div>


                        <div class="text-xs mt-4">
                            <span class="font-bold">Password requirements:</span> must have mixed-case letters, numbers, and symbols.
                        </div>
                    </div>
                </div>
            </div>
            <div></div>
        </div>



    </AuthenticatedLayout>
</template>
