<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const form = useForm({
    name: '',
    password: '',
    remember: false,
});

const submit = async () => {
 form.post(route('login'), {
            onFinish: () => form.reset('password'),
 
        });
    
};

const nameInput = ref(null);
const showPassword = ref(false); // ใช้สำหรับสลับการแสดงรหัสผ่าน

onMounted(() => {
    nameInput.value.focus();
});
</script>

<template>
    <Head title="Login" />

    <form @submit.prevent="submit" class="mt-10 mb-5">
        <div class="text-center mb-10">
            <h1 class="font-bold text-3xl text-gray-900">เข้าสู่ระบบ</h1>
            <p class="mt-2">กรอกข้อมูลเพื่อเข้าใช้งานในระบบ</p>
        </div>

        <!-- Error Message
        <div v-if="Object.keys(form.errors).length" class="text-red-500 text-center mb-4">
            <p>มีข้อผิดพลาดในการกรอกข้อมูล กรุณาตรวจสอบอีกครั้ง</p>
        </div> -->

        <div>
            <InputLabel for="name" value="Username" />
            <TextInput
                id="name"
                type="text"
                class="mt-1 block w-full"
                v-model="form.name"
                required
                ref="nameInput"
                autocomplete="name"
            />
            <InputError class="mt-2" :message="form.errors.name" />
        </div>

        <div class="mt-4 relative">
            <InputLabel for="password" value="Password" />
            <TextInput
                id="password"
                :type="showPassword ? 'text' : 'password'" 
                class="mt-1 block w-full"
                v-model="form.password"
                required
                autocomplete="current-password"
            />
            <span
                class="absolute right-3 top-8 cursor-pointer mdi"
                :class="showPassword ? 'mdi-eye-off' : 'mdi-eye'"  
                @click="showPassword = !showPassword"  
            ></span>
            <InputError class="mt-2" :message="form.errors.password" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <PrimaryButton 
                class="custom-button-gray ml-4"
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
            >
                <span v-if="form.processing">กำลังโหลด...</span>
                <span v-else>Log in</span>
            </PrimaryButton>
        </div>
    </form>
</template>

<style>
@import url('https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/5.3.45/css/materialdesignicons.min.css');
</style>
