<template>

    <Head title="Update Scholar Information" />
    <AuthenticatedLayout>
        <div class="max-w-7xl mx-auto mb-3 py-4 mt-20 px-10">
            <div class="max-w-7xl mx-auto py-2">
                <div
                    class="bg-gray-700 dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg border border-gray-500 dark:border-gray-600 rounded-lg">
                    <div class="py-2 px-4 text-white dark:text-gray-100 flex items-center justify-between">
                        <div class="flex-1 text-left px-4">
                            แก้ไขข้อมูลทุนการศึกษา
                        </div>
                        <div class="flex space-x-2">
                            <Link :href="route('scholarships.index')" class="custom-button-danger">
                            กลับ
                            </Link>
                            <PrimaryButton @click="submit" class="custom-button-success ml-4"
                                :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                <span v-if="form.processing">กำลังโหลด...</span>
                                <span v-else> บันทึก</span>
                            </PrimaryButton>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 bg-white dark:bg-gray-800 sm:rounded-xl shadow-md">
                <form @submit.prevent="submit" class="grid grid-cols-1 gap-4 p-4 sm:p-6 lg:p-8"
                    enctype="multipart/form-data">
                    <input type="hidden" name="_method" value="PUT">

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <InputLabel for="scholar_name" value="ชื่อทุนการศึกษา" />
                            <TextInput id="scholar_name" type="text" class="mt-1 block w-full"
                                v-model="form.scholar_name" autofocus />
                            <span v-if="errorMessages.scholar_name" class="text-red-600">{{ errorMessages.scholar_name
                            }}</span>
                        </div>
                        <div>
                            <InputLabel for="type" value="ประเภททุนการศึกษา" />
                            <select id="type"
                                class="mt-1 block w-full border rounded-md border-gray-300 focus:border-gray-500 focus:ring focus:ring-gray-200 focus:ring-opacity-50"
                                v-model="form.type">
                                <option value="0">ทุนเพชรอินทนิล</option>
                                <option value="1">ทุนงดเว้นค่าธรรมเนียมการศึกษา</option>
                            </select>
                            <span v-if="errorMessages.type" class="text-red-600">{{ errorMessages.type }}</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <InputLabel for="apply_date" value="วันที่สมัคร" />
                            <TextInput id="apply_date" type="date" class="mt-1 block w-full"
                                v-model="form.apply_date" />
                            <span v-if="errorMessages.apply_date" class="text-red-600">{{ errorMessages.apply_date
                            }}</span>
                        </div>
                        <div>
                            <InputLabel for="due_date" value="วันสิ้นสุด" />
                            <TextInput id="due_date" type="date" class="mt-1 block w-full" v-model="form.due_date" />
                            <span v-if="errorMessages.due_date" class="text-red-600">{{ errorMessages.due_date }}</span>
                        </div>
                        <div>
                            <InputLabel for="status" value="สถานะ" />
                            <select id="status"
                                class="mt-1 block w-full border rounded-md border-gray-300 focus:border-gray-500 focus:ring focus:ring-gray-200 focus:ring-opacity-50"
                                v-model="form.status">
                                <option value="0">ปิด</option>
                                <option value="1">เปิด</option>
                            </select>
                            <span v-if="errorMessages.status" class="text-red-600">{{ errorMessages.status }}</span>
                        </div>
                    </div>

                    <div>
                        <InputLabel for="description" value="คำอธิบาย" />
                        <textarea id="description"
                            class="mt-1 block w-full border rounded-md border-gray-300 focus:border-gray-500 focus:ring focus:ring-gray-200 focus:ring-opacity-50"
                            v-model="form.description"></textarea>
                        <span v-if="errorMessages.description" class="text-red-600">{{ errorMessages.description
                        }}</span>
                    </div>

                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <InputLabel for="link" value="ลิงค์" />
                            <TextInput id="link" type="text" class="mt-1 block w-full" v-model="form.link" />
                            <span v-if="errorMessages.link" class="text-red-600">{{ errorMessages.link }}</span>
                        </div>
                        <div>
                            <InputLabel for="file" value="ไฟล์" />
                            <TextInput id="file" type="file" class="mt-1 block w-full py-1 px-2"
                                @change="handleFileChange($event, 'file')" />
                            <span v-if="errorMessages.file" class="text-red-600">{{ errorMessages.file }}</span>
                        </div>
                        <div>
                            <InputLabel for="limits" value="จำนวนที่เปิดรับสมัคร" />
                            <TextInput id="limits" type="text" class="mt-1 block w-full" v-model="form.limits" />
                            <span v-if="errorMessages.limits" class="text-red-600">{{ errorMessages.limits }}</span>
                        </div>
                    </div>

                    <div>
                        <p class="font-semibold">ไฟล์ปัจจุบัน:</p>
                        <template v-if="props.scholarship.file">
                            <div v-if="isImage(props.scholarship.file)" class="mt-2">
                                <img :src="`/storage/files/${props.scholarship.file}`" alt="Current File"
                                    class="w-64 h-64 object-cover" />
                            </div>
                            <div v-else-if="isPDF(props.scholarship.file)" class="mt-2">
                                <iframe :src="`/storage/files/${props.scholarship.file}`"
                                    class="w-full h-64 border-none" frameborder="0"></iframe>
                            </div>
                            <div v-else class="mt-2">
                                <p>ไฟล์ประเภทอื่น: {{ props.scholarship.file }}</p>
                            </div>
                        </template>
                        <template v-else>
                            <p>ไม่มีไฟล์ที่อัพโหลด</p>
                        </template>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link } from '@inertiajs/vue3';
import Swal from "sweetalert2";

const props = defineProps({
    scholarship: Object
});

const errorMessages = ref({
    scholar_name: '',
    type: '',
    apply_date: '',
    due_date: '',
    status: '',
    description: '',
    link: '',
    limits: '',
    file: '',
});

const form = useForm({
    scholar_name: props.scholarship.scholar_name || '',
    apply_date: props.scholarship.apply_date || '',
    due_date: props.scholarship.due_date || '',
    status: props.scholarship.status,
    description: props.scholarship.description || '',
    link: props.scholarship.link || '',
    type: props.scholarship.type || 0,
    limits: props.scholarship.limits || 0,
    file: null,
}); 
const handleFileChange = (event, field) => {
    const file = event.target.files[0];
    form[field] = file;
};
const submit = () => {
    validateForm();
    if (Object.values(errorMessages.value).some(error => error)) {
        return;
    }
    const formData = new FormData();
    formData.append('scholar_name', form.scholar_name);
    formData.append('apply_date', form.apply_date);
    formData.append('due_date', form.due_date);
    formData.append('status', form.status);
    formData.append('description', form.description);
    formData.append('link', form.link);
    formData.append('type', form.type);
    formData.append('limits', form.type);
    if (form.file) {
        formData.append('file', form.file);
    }
    form.post(route("scholarships.update", { id: props.scholarship.id }), {
        data: formData,
        headers: {
            'Content-Type': 'multipart/form-data'
        },
        onSuccess: () => {
            Swal.fire({
                title: 'บันทึกสำเร็จ!',
                text: 'ข้อมูลกิจกรรมได้รับการอัพเดทแล้ว',
                icon: 'success',
                confirmButtonText: 'เสร็จสิ้น',
                confirmButtonColor: '#4CAF50',
            });
        },
        onError: (errors) => {
            console.error(errors);
            Swal.fire({
                title: "ผิดพลาด!",
                text: "เกิดข้อผิดพลาดในการบันทึกข้อมูลทุนการศึกษา",
                icon: "error",
                confirmButtonText: "ตกลง",
                confirmButtonColor: '#f44336',
            });
        },
    });
};

const validateForm = () => {
    Object.keys(errorMessages.value).forEach(key => {
        errorMessages.value[key] = '';
    });

    if (!form.scholar_name) {
        errorMessages.value.scholar_name = 'กรุณากรอกชื่อทุนการศึกษา';
    }
    if (!form.type) {
        errorMessages.value.type = 'กรุณาเลือกประเภททุนการศึกษา';
    }
    if (!form.apply_date) {
        errorMessages.value.apply_date = 'กรุณากรอกวันที่สมัคร';
    }
    if (!form.due_date) {
        errorMessages.value.due_date = 'กรุณากรอกวันสิ้นสุด';
    }
    if (!form.status) {
        errorMessages.value.status = 'กรุณาเลือกสถานะ';
    }
    if (!form.description) {
        errorMessages.value.description = 'กรุณากรอกคำอธิบาย';
    }
    if (!form.link) {
        errorMessages.value.link = 'กรุณากรอกลิงค์';
    }
    if (!form.limits) {
        errorMessages.value.limits = 'กรุณากรอกจำนวนผู้สมัคร';
    }
};

const isImage = (filename) => /\.(jpg|jpeg|png|gif)$/i.test(filename);
const isPDF = (filename) => /\.pdf$/i.test(filename);
</script>
