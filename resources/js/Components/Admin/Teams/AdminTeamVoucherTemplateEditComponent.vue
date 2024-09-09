<script setup>
import {onMounted, ref} from "vue";
import TextInput from "@/Components/TextInput.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import Swal from "sweetalert2";
import AdminUserDetailsComponent from "@/Components/Admin/AdminUserDetailsComponent.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import {Link} from "@inertiajs/vue3";
import DangerButton from "@/Components/DangerButton.vue";
import FileUploaderComponent from "@/Components/Admin/FileUploads/FileUploaderComponent.vue";
import Checkbox from "@/Components/Checkbox.vue";

const $props = defineProps({
    id: {
        type: Number,
        required: false
    }
})

const item = ref({
    team_id: '',
    voucher_template_path: '',
    voucher_example_template_path: '',
    overlay_font_path: 'fonts/OpenSans_Condensed-Bold.ttf',
    voucher_qr_size_px: 200,
    voucher_qr_x: 100,
    voucher_qr_y: 200,
    voucher_code_size_px: 32,
    voucher_code_x: 100,
    voucher_code_y: 160,
    voucher_code_prefix: '',
    voucher_expiry_size_px: 32,
    voucher_expiry_x: 100,
    voucher_expiry_y: 220,
    voucher_expiry_prefix: '',
    voucher_value_size_px: 32,
    voucher_value_x: 100,
    voucher_value_y: 280,
    voucher_value_prefix: '',
});

const searchStr = ref('');
const teamsFound = ref({});
const selectedTeam = ref({});
const archived = ref(false);

function searchTeam() {
    axios.get('/admin/teams?where[]=name,like,*' + searchStr.value + '*&limit=100').then(response => {
        teamsFound.value = response.data.data;
    }).catch(error => {
        console.log(error)
    });
}

function teamSelected(team) {
    item.value.team_id = team.id;
    selectedTeam.value = team;
}

function saveTemplate() {

    let verb = 'post';
    let url = '/admin/team-voucher-templates'

    if ($props.id) {
        verb = 'put';
        url = '/admin/team-voucher-templates/' + $props.id
    }

    item.value.archive = archived.value;

    axios[verb](url, item.value).then(response => {
        Swal.fire({
            title: 'Success!',
            icon: 'success',
            timer: 1000
        }).then(() => {
            window.location.href = '/admin/team-voucher-template/' + response.data.data.id
        });
    }).catch(error => {
        console.log(error)
    })
}

function getTemplate() {

    axios.get('/admin/team-voucher-templates/' + $props.id + '?cached=false&relations=team').then(response => {
        item.value = response.data.data;

        selectedTeam.value = item.value.team;
        getArchivedStatus();
    }).catch(error => {
        console.log(error)
    })
}

function getArchivedStatus(){
    if(item.value.archived_at)
    {
        archived.value = true
    }
    else {
        archived.value = false;
    }
}

function preSelectIdIfInUrlParams() {
    const params = new URLSearchParams(window.location.search);
    if (params.has("teamId")) {
        const teamId = params.get("teamId");
        axios.get('/admin/teams/' + teamId).then(response => {
            selectedTeam.value = response.data.data;
            item.value.team_id = response.data.data.id;

            getArchivedStatus();

        }).catch(error => {
            console.log(error)

            Swal.fire({
                title: 'Oops..!',
                icon: 'error',
                text: 'I couldn\'t locate team with ID '+teamId+'. Please start from scratch.',
            });
        });
    }
}

onMounted(fn => {
    if ($props.id) {
        getTemplate();
    }
    else {
        preSelectIdIfInUrlParams();
    }
});

function filesWereUploaded(fileDetails) {
    item.value.voucher_template_path = fileDetails[0];
}

</script>

<template>

    <div class="grid gap-4 container mx-auto" :class="{'grid-cols-3': item.example_template_image_url}">
        <div class="card" :class="{'col-span-2': item.example_template_image_url}">

            <div class="card-header flex justify-between items-center">
                <div class="text-lg font-bold flex justify-start items-center">
                    <div v-if="item?.id">Edit</div>
                    <div v-else>New</div>

                    <div class="ml-1">
                        Voucher Template
                    </div>
                </div>

                <div v-if="item.archived_at">
                    <div class="border-4 rounded-lg border-dashed p-2 px-8 border-red-300 text-red-300 text-xl font-bold">
                        Archived
                    </div>
                </div>
            </div>


            <div v-if="!item.team_id">
                <div class="border-b border-dotted py-4">
                    <div>
                        <InputLabel for="name" value="First, Select A Team.."/>

                        <TextInput
                            @keyup="searchTeam()"
                            v-model="searchStr"
                            class="mt-1 block w-full"
                            placeholder="Search team by name.."
                            type="search"
                        ></TextInput>
                    </div>

                    <div v-if="searchStr.length > 0 && teamsFound.total > 0" class="mt-4">

                        <div class="grid grid-cols-4 gap-2">
                            <div v-for="teamFound in teamsFound.data">
                                <SecondaryButton class="truncate w-full py-2 flex justify-center"
                                                 @click="teamSelected(teamFound)">
                                    {{ teamFound?.name }}
                                </SecondaryButton>
                            </div>
                        </div>

                    </div>

                    <div v-if="searchStr.length > 0 && teamsFound.total === 0">
                        <div class="text-red-500 text-sm mt-4 cursor-pointer hover:underline">
                            No teams found.
                        </div>
                    </div>
                </div>
            </div>
            <div v-else>

                <div class="border-b border-dotted py-4">
                    <div class="text-sm font-bold">
                        Selected Team
                    </div>
                    <div class="flex justify-start items-center ">

                       <Link :href="'/admin/team/' + item.team_id">
                           {{ selectedTeam?.name }}
                       </Link>

                        <DangerButton class="ml-2 !p-1 !px-2" @click.prevent="item.team_id = ''">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                 stroke="currentColor" class="h-4">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/>
                            </svg>
                        </DangerButton>
                    </div>
                </div>


                <div>
                    <div class="border-b border-dotted py-4" v-if="!item.voucher_template_path">

                        <div>
                            Next, Upload a template (PNG only):
                        </div>

                        <FileUploaderComponent
                            @filesWereUploaded="filesWereUploaded"
                            :folder="'/teams/'+item.team_id+'/voucher-templates'"
                            allowed-file-types="image/png">
                        </FileUploaderComponent>

                    </div>
                    <div v-else>
                        <div class="border-b border-dotted py-4">
                            <div class="text-sm font-bold">
                                Selected Template
                            </div>
                            <div class="flex justify-start items-center ">

                                {{ item?.voucher_template_path }}
                                <DangerButton class="ml-2 !p-1 !px-2" @click.prevent="item.voucher_template_path = ''">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                         stroke-width="1.5" stroke="currentColor" class="h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/>
                                    </svg>
                                </DangerButton>
                            </div>
                            <div class="text-xs italic" v-if="!item.voucher_example_template_path">
                                Example image will show after we have saved/re-saved.
                            </div>
                        </div>


                    </div>
                </div>


                <div class="py-4 border-b" v-if="item.voucher_example_template_path">


                    <div class="text-sm font-bold mb-4">
                        Template Positions
                    </div>


                    <div class="grid grid-cols-4 gap-4">
                        <div>
                            <InputLabel>
                                QR Size
                            </InputLabel>
                            <input
                                type="number"
                                v-model.number="item.voucher_qr_size_px"></input>
                        </div>

                        <div>
                            <InputLabel>
                                QR Coords: X Position
                            </InputLabel>
                            <input
                                type="number"
                                v-model.number="item.voucher_qr_x"/>
                        </div>
                        <div>
                            <InputLabel>
                                QR Coords: Y Position
                            </InputLabel>
                            <input
                                type="number"
                                v-model.number="item.voucher_qr_y"/>
                        </div>

                        <div></div>
                        <div>
                            <InputLabel>
                                Voucher Code: Size (px)
                            </InputLabel>
                            <input
                                type="number"
                                v-model.number="item.voucher_code_size_px"/>
                        </div>
                        <div>
                            <InputLabel>
                                Code Coords: X Pos
                            </InputLabel>
                            <input
                                type="number"
                                v-model.number="item.voucher_code_x"/>
                        </div>
                        <div>
                            <InputLabel>
                                Code Coords: Y Pos
                            </InputLabel>
                            <input
                                type="number"
                                v-model.number="item.voucher_code_y"/>
                        </div>
                        <div>
                            <InputLabel>
                                Code Prefix:
                            </InputLabel>
                            <input
                                type="text"
                                v-model="item.voucher_code_prefix"/>
                        </div>
                        <div>
                            <InputLabel>
                                Voucher Expiry: Size (px)
                            </InputLabel>
                            <input
                                type="number"
                                v-model.number="item.voucher_expiry_size_px"/>
                        </div>
                        <div>
                            <InputLabel>
                                Expiry Coords: X Pos
                            </InputLabel>
                            <input
                                type="number"
                                v-model.number="item.voucher_expiry_x"/>
                        </div>
                        <div>
                            <InputLabel>
                                Expiry Coords: Y Pos
                            </InputLabel>
                            <input
                                type="number"
                                v-model.number="item.voucher_expiry_y"/>
                        </div>
                        <div>
                            <InputLabel>
                                Expiry Prefix:
                            </InputLabel>
                            <input
                                type="text"
                                v-model="item.voucher_expiry_prefix"/>
                        </div>
                        <div>
                            <InputLabel>
                                Voucher Value: Size (px)
                            </InputLabel>
                            <input
                                type="number"
                                v-model.number="item.voucher_value_size_px"/>
                        </div>
                        <div>
                            <InputLabel>
                                Value Coords: X Pos
                            </InputLabel>
                            <input
                                type="number"
                                v-model.number="item.voucher_value_x"/>
                        </div>
                        <div>
                            <InputLabel>
                                Value Coords: Y Pos
                            </InputLabel>
                            <input
                                type="number"
                                v-model.number="item.voucher_value_y"/>
                        </div>
                        <div>
                            <InputLabel>
                                Value Prefix:
                            </InputLabel>
                            <input
                                type="text"
                                v-model="item.voucher_value_prefix"/>
                        </div>
                    </div>

                </div>


            </div>

            <div class="py-4 border-b">
                <InputLabel for="archived" value="Archive / Unarchive this template">

                </InputLabel>

                <label for="archived">
                    <Checkbox id="archived" :checked="archived" v-model="archived">

                    </Checkbox>
                    <span class="ml-2">
                        Archived
                    </span>
                    <span v-if="item?.archived_at">@ {{item.archived_at}}</span>
                </label>

            </div>


            <div class="flex items-center justify-start mt-4">
                <PrimaryButton
                    @click.prevent="saveTemplate()"
                    class="" :class="{ 'opacity-25': !item.voucher_template_path }"
                    :disabled="!item.voucher_template_path">
                    Submit
                </PrimaryButton>
            </div>

        </div>
        <div class="card" v-if="item.example_template_image_url">

            <div class="card-header ">
                <div class="text-lg font-bold">
                    Preview
                </div>
            </div>

            <img :src="item.example_template_image_url" alt="" class="border rounded w-full">

        </div>
    </div>


</template>
