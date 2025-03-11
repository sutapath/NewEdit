<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    title: '',
    fname: '',
    lname: '',
    name: '',
    email: '',
    password: '',
    phone: '',
    id_card: '',
    password_confirmation: '',
    terms: false,
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>

    <Head title="Register" />
    <form @submit.prevent="submit">
        <div class="text-center mb-10">
            <h1 class="font-bold text-3xl text-gray-900">สมัครสมาชิก</h1>
            <p class="mt-2">กรอกข้อมูลเพื่อสมัครเข้าใช้งานในระบบ</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Title -->
            <div class="col-span-1 sm:col-span-1">
                <InputLabel for="title" value="คำนำหน้าชื่อ" />
                <select id="title" v-model="form.title" class="mt-1 block w-full bg-white rounded-xl border-gray-300"
                    required>
                    <option value="">Select Title</option>
                    <option value="0">นาย (Mr.)</option>
                    <option value="1">นาง (Mrs.)</option>
                    <option value="2">นางสาว (Miss)</option>
                </select>
                <InputError class="mt-2" :message="form.errors.title" />
            </div>

            <div class="col-span-1 md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- First Name -->
                <div class="col-span-1">
                    <InputLabel for="fname" value="ชื่อจริง" />
                    <TextInput id="fname" type="text" v-model="form.fname" class="mt-1 block w-full"
                        placeholder="Enter your first name" required />
                    <InputError class="mt-2" :message="form.errors.fname" />
                </div>

                <!-- Last Name -->
                <div class="col-span-1">
                    <InputLabel for="lname" value="นามสกุล" />
                    <TextInput id="lname" type="text" v-model="form.lname" class="mt-1 block w-full"
                        placeholder="Enter your last name" required />
                    <InputError class="mt-2" :message="form.errors.lname" />
                </div>
            </div>

            <!-- ID Card -->
            <div class="col-span-2">
                <InputLabel for="id_card" value="หมายเลขบัตรประชาชน" />
                <TextInput id="id_card" type="text" v-model="form.id_card" class="mt-1 block w-full"
                    placeholder="Enter your ID card number" required />
                <InputError class="mt-2" :message="form.errors.id_card" />
            </div>
            <!-- ID Card -->
            <div class="col-span-2">
                <InputLabel for="phone" value="หมายเลขโทรศัพท์" />
                <TextInput id="phone" type="text" v-model="form.phone" class="mt-1 block w-full"
                    placeholder="Enter your ID card number" required />
                <InputError class="mt-2" :message="form.errors.phone" />
            </div>
            <!-- Username -->
            <div class="col-span-1">
                <InputLabel for="name" value="Username" />
                <TextInput id="name" type="text" v-model="form.name" class="mt-1 block w-full"
                    placeholder="Choose a username" required />
                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <!-- Email -->
            <div class="col-span-1">
                <InputLabel for="email" value="Email" />
                <TextInput id="email" type="email" v-model="form.email" class="mt-1 block w-full"
                    placeholder="Enter your email address" required />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <!-- Password -->
            <div class="col-span-1">
                <InputLabel for="password" value="รหัสผ่าน (มากกว่า 8 อักขระ)" />
                <TextInput id="password" type="password" v-model="form.password" class="mt-1 block w-full"
                    placeholder="Create a password" required autocomplete="new-password" />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <!-- Confirm Password -->
            <div class="col-span-1">
                <InputLabel for="password_confirmation" value="ยืนยันรหัสผ่าน" />
                <TextInput id="password_confirmation" type="password" v-model="form.password_confirmation"
                    class="mt-1 block w-full" placeholder="Confirm your password" required
                    autocomplete="new-password" />
                <InputError class="mt-2" :message="form.errors.password_confirmation" />
            </div>
        </div>

        <div class="flex items-center justify-end mt-4">
 
            <PrimaryButton class="custom-button-dark ml-5" :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing">
                <template v-if="form.processing">
                    กำลังโหลด...
                </template>
                <template v-else>
                    Register
                </template>
            </PrimaryButton>

        </div>
    </form>
</template>

<style>
@import url('https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/5.3.45/css/materialdesignicons.min.css');
</style>
