<script setup>
import DangerButton from '@/Components/DangerButton.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
 import { Head, Link, useForm } from '@inertiajs/vue3';
import { nextTick, ref } from 'vue';
import { usePermission } from "@/composables/permissions";

const { hasRole, hasPermission } = usePermission();

const confirmingUserDeletion = ref(false);
const passwordInput = ref(null);

const form = useForm({
    password: '',
});

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;

    nextTick(() => passwordInput.value.focus());
};

const deleteUser = () => {
    form.delete(route('profile.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value.focus(),
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    confirmingUserDeletion.value = false;

    form.reset();
};
</script>

<template>
    <section>

        <div class="flex justify-end sm:px-6 lg:px-8">
            <Link v-if="!hasRole('manager')" :href="route('user_line_notify.index')" class="custom-button-success mr-3 px-1 py-1">
                เพิ่มช่องทางรับการแจ้งเตือน
                    </Link>
            <DangerButton @click="confirmUserDeletion" class="custom-button-danger mr-3 px-1 py-1">ลบบัญชีผู้ใช้</DangerButton>
            <Modal :show="confirmingUserDeletion" @close="closeModal">
                <div class="p-6">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                        คุณแน่ใจหรือไม่ที่ต้องการลบบัญชีผู้ใช้ของคุณ?
                    </h2>

                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        เมื่อลบบัญชีของคุณแล้ว ข้อมูลและทรัพยากรทั้งหมดจะถูกลบอย่างถาวร
                        โปรดป้อนรหัสผ่านเพื่อยืนยันว่าคุณต้องการลบบัญชีของคุณอย่างถาวร
                    </p>

                    <div class="mt-6">
                        <InputLabel for="password" value="รหัสผ่าน" class="sr-only" />

                        <TextInput id="password" ref="passwordInput" v-model="form.password" type="password"
                            class="mt-1 block w-3/4" placeholder="รหัสผ่าน" @keyup.enter="deleteUser" />

                        <InputError :message="form.errors.password" class="mt-2" />
                    </div>

                    <div class="mt-6 flex justify-end">
                        <SecondaryButton @click="closeModal">ยกเลิก</SecondaryButton>
                        <DangerButton class="ml-3" :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing" @click="deleteUser">
                            ลบบัญชีผู้ใช้
                        </DangerButton>
                    </div>
                </div>

            </Modal>
        </div>
    </section>
</template>
