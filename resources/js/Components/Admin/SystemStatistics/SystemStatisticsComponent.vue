<script setup>
import {ref} from "vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import {Link} from "@inertiajs/vue3";
import Swal from "sweetalert2";
import AjaxLoadingIndicator from "@/Components/AjaxLoadingIndicator.vue";

const loading = ref(false);

const latestSystemStatistic = ref({
    'num_users': 0,
    'num_teams': 0,
    'num_voucher_sets': 0,
    'num_vouchers': 0,
    'num_voucher_redemptions': 0,
    'sum_voucher_value_total': 0,
    'sum_voucher_value_redeemed': 0,
    'sum_voucher_value_remaining': 0,
});

function getData() {
    loading.value = true;

    axios.get('/admin/system-statistics?cached=false&limit=1&orderBy=id,desc').then(response => {

        if(response.data.data?.data[0])
        {
            latestSystemStatistic.value = response.data.data?.data[0];
        }

        loading.value = false;

    }).catch(error => {

        Swal.fire({
            icon: 'error',
            title: 'Oops!',
            text: error.response.data.meta.message
        });

        loading.value = false;
    });
}



function formatStatisticNumber(number){
    let formatter = Intl.NumberFormat('en', { notation: 'compact' });

    return formatter.format(number)
}

getData();


</script>

<template>
    <div class="card">
        <AjaxLoadingIndicator :loading="loading"></AjaxLoadingIndicator>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

                <SecondaryButton>

                    <div class="w-full">
                        <Link :href="route('admin.users')" class="hover:no-underline">
                            <div class="flex justify-center text-3xl">
                                {{ formatStatisticNumber(latestSystemStatistic.num_users) }}
                            </div>
                            <div class="text-xs">
                                # Users
                            </div>
                        </Link>
                    </div>

                </SecondaryButton>


            <SecondaryButton>

                <div class="w-full">
                    <Link :href="route('admin.teams')" class="hover:no-underline">
                        <div class="flex justify-center text-3xl">
                            {{ formatStatisticNumber(latestSystemStatistic.num_teams) }}
                        </div>
                        <div class="text-xs">
                            # Teams
                        </div>
                    </Link>
                </div>

            </SecondaryButton>

            <div class="hidden lg:inline"></div>
            <div class="hidden lg:inline"></div>


            <SecondaryButton>

                <div class="w-full">
                    <Link :href="route('admin.teams')" class="hover:no-underline">
                        <div class="flex justify-center text-3xl">
                            {{ formatStatisticNumber(latestSystemStatistic.num_voucher_sets) }}
                        </div>
                        <div class="text-xs">
                            # Voucher Sets (x)
                        </div>
                    </Link>
                </div>

            </SecondaryButton>

            <SecondaryButton>

                <div class="w-full">
                    <Link :href="route('admin.teams')" class="hover:no-underline">
                        <div class="flex justify-center text-3xl">
                            {{ formatStatisticNumber(latestSystemStatistic.num_vouchers) }}
                        </div>
                        <div class="text-xs">
                            # Vouchers (x)
                        </div>
                    </Link>
                </div>

            </SecondaryButton>


            <SecondaryButton>

                <div class="w-full">
                    <Link :href="route('admin.teams')" class="hover:no-underline">
                        <div class="flex justify-center text-3xl">
                            {{ formatStatisticNumber(latestSystemStatistic.sum_voucher_value_total) }}
                        </div>
                        <div class="text-xs">
                            $ Voucher (Original) (x)
                        </div>
                    </Link>
                </div>

            </SecondaryButton>

            <SecondaryButton>

                <div class="w-full">
                    <Link :href="route('admin.teams')" class="hover:no-underline">
                        <div class="flex justify-center text-3xl">
                            {{ formatStatisticNumber(latestSystemStatistic.sum_voucher_value_remaining) }}
                        </div>
                        <div class="text-xs">
                            $ Vouchers Remaining (x)
                        </div>
                    </Link>
                </div>

            </SecondaryButton>



            <SecondaryButton>

                <div class="w-full">
                    <Link :href="route('admin.teams')" class="hover:no-underline">
                        <div class="flex justify-center text-3xl">
                            {{ formatStatisticNumber(latestSystemStatistic.num_voucher_redemptions) }}
                        </div>
                        <div class="text-xs">
                            # Redemptions (x)
                        </div>
                    </Link>
                </div>

            </SecondaryButton>

            <SecondaryButton>

                <div class="w-full">
                    <Link :href="route('admin.teams')" class="hover:no-underline">
                        <div class="flex justify-center text-3xl">
                            {{ formatStatisticNumber(latestSystemStatistic.sum_voucher_value_redeemed) }}
                        </div>
                        <div class="text-xs">
                            $ Redemptions (x)
                        </div>
                    </Link>
                </div>

            </SecondaryButton>




            <div>
                X = Yet to be linked
            </div>

        </div>
    </div>
</template>

