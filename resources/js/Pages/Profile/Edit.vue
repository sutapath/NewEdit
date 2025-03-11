<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps({
    mustVerifyEmail: Boolean,
    status: String,
});

const activeForm = ref('profile');

const setActiveForm = (form) => {
    activeForm.value = form;
};
</script>

<template>
    <Head title="Profile" />

    <AuthenticatedLayout>
        <template #header>
    <div class="flex justify-between items-center mt-5">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">ข้อมูลบัญชีผู้ใช้</h2>
        <div>

            <DeleteUserForm />
        </div>
    </div>
</template>
 
            <div class="max-w-7xl mx-auto ">
                <div class="p-4 sm:p-8">

                    <UpdateProfileInformationForm
                        :must-verify-email="mustVerifyEmail"
                        :status="status"
                        class="max-w-7xl"
                    />
                    <hr class="mt-1 border-b-1 border-Gray-600 mb-5 mt-5">
                    <UpdatePasswordForm class="max-w-7xl mt-6" /> 
                </div> 
        </div>
    </AuthenticatedLayout>
</template>
