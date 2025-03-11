<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { useForm } from '@inertiajs/vue3';
import Swal from 'sweetalert2'; // นำเข้า Swal
import { ref } from 'vue';

const passwordInput = ref(null);
const currentPasswordInput = ref(null);

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const updatePassword = () => {
    form.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            Swal.fire({
                icon: 'success',
                title: 'สำเร็จ',
                text: 'รหัสผ่านของคุณถูกอัปเดตสำเร็จ!',
                confirmButtonText: 'ปิด',
                customClass: {
                    confirmButton: 'bg-green-500 text-white hover:bg-green-600'
                }
            });
        },
        onError: () => {
            // กำหนดข้อความข้อผิดพลาดเอง
            const customMessages = {
                current_password: 'รหัสผ่านปัจจุบันไม่ถูกต้อง! กรุณาตรวจสอบอีกครั้ง.',
                password: 'รหัสผ่านใหม่ต้องมีอย่างน้อย 8 ตัวอักษร.',
                password_confirmation: 'การยืนยันรหัสผ่านใหม่ไม่ตรงกัน.'
            };

            // ใช้ข้อความข้อผิดพลาดที่กำหนดเอง
            if (form.errors.password) {
                form.reset('password', 'password_confirmation');
                passwordInput.value.focus();
            }
            if (form.errors.current_password) {
                form.reset('current_password');
                currentPasswordInput.value.focus();
            }

            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาด',
                text: 'การเปลี่ยนรหัสผ่านล้มเหลว! กรุณาตรวจสอบข้อมูลและลองใหม่อีกครั้ง.',
                confirmButtonText: 'ปิด',
                customClass: {
                    confirmButton: 'bg-red-500 text-white hover:bg-red-600'
                }
            });

            // สามารถสร้างข้อความข้อผิดพลาดใหม่ได้โดยใช้ข้อความที่กำหนดเอง
            form.errors.current_password = customMessages.current_password;
            form.errors.password = customMessages.password;
            form.errors.password_confirmation = customMessages.password_confirmation;
        },
    });
};
</script>

<template>
    <section>
        <div class="sm:col-span-1 sm:px-6 ">
            <div
                class="bg-gray-700 dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg border border-gray-500 dark:border-gray-600">
                <div class="py-2 px-4 text-white dark:text-gray-100 flex justify-between items-center">
                    <div class="px-4">เปลี่ยนรหัสผ่าน</div>
                    <PrimaryButton @click="updatePassword" :disabled="form.processing" class="custom-button-success">
                        บันทึก
                    </PrimaryButton>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto">
            <div class="sm:px-6  flex flex-col items-start">
                <form @submit.prevent="updatePassword" class="mt-6 space-y-6 mb-4 w-full text-left">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <InputLabel for="current_password" value="รหัสผ่านปัจจุบัน" />
                            <TextInput id="current_password" ref="currentPasswordInput" v-model="form.current_password"
                                type="password" class="mt-1 block w-full" autocomplete="current-password" />
                            <InputError :message="form.errors.current_password" class="mt-2" />
                        </div>

                        <div>
                            <InputLabel for="password" value="รหัสผ่านใหม่" />
                            <TextInput id="password" ref="passwordInput" v-model="form.password" type="password"
                                class="mt-1 block w-full" autocomplete="new-password" />
                            <InputError :message="form.errors.password" class="mt-2" />
                        </div>

                        <div>
                            <InputLabel for="password_confirmation" value="ยืนยันรหัสผ่านใหม่" />
                            <TextInput id="password_confirmation" v-model="form.password_confirmation" type="password"
                                class="mt-1 block w-full" autocomplete="new-password" />
                            <InputError :message="form.errors.password_confirmation" class="mt-2" />
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
</template>
