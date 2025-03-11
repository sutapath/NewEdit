<template>

    <Head title="Edit Scholarship docSend" />
    <form @submit.prevent="handleSubmit" enctype="multipart/form-data">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 w-full">
            <div>
                <InputLabel for="result" value="ผลการตรวจ" />
                <select id="result" v-model="form.result" class="mt-1 block w-full bg-white rounded-xl border-gray-300">
                    <option value="0">ผ่าน</option>
                    <option value="1">แก้ไข</option>
                    <!-- <option value="2">รอการตรวจ</option> -->
                    <option value="3">ดำเนินการแก้ไข</option>
                </select>
                <span v-if="form.errors.result" class="text-red-500 text-sm">{{ form.errors.result }}</span>
            </div>
            <div class="mb-4">
                <InputLabel for="suggestions" value="ข้อเสนอแนะ" />
                <TextInput id="suggestions" type="text" class="mt-1 block w-full" v-model="form.suggestions" />
            </div>
            <div class="mb-4">
                <InputLabel for="check_date" value="วันที่" />
                <TextInput id="check_date" type="date" class="mt-1 block w-full" v-model="form.check_date" />
                <span v-if="form.errors.check_date" class="text-red-500 text-sm">{{ form.errors.check_date }}</span>
            </div>
            <div class="mb-4 mt-7">
                <Link   @click.prevent="handleCancel" class="custom-button-danger mr-3">
                ยกเลิก</Link>
                <PrimaryButton @click="handleSubmit" :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing" class="custom-button-success">
                    <span v-if="form.processing">กำลังโหลด...</span>
                    <span v-else> บันทึก</span>
                </PrimaryButton>
            </div>
        </div>
    </form>
</template>

<script setup>
import { ref } from 'vue';
import { usePage, useForm } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import { Head, Link } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const convertToThaiBuddhistYear = (date) => {
    const year = date.getFullYear() + 543;
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
};

// ตั้งค่า currentDate ก่อน
const currentDate = new Date();
// ตั้งค่า check_date ให้เป็นวันที่ปัจจุบันในรูปแบบ พ.ศ.
const checkDate = convertToThaiBuddhistYear(currentDate);

const { props } = usePage();
const form = useForm({
    result: props.docSend.result || '',
    suggestions: props.docSend.suggestions || '',
    check_date: checkDate,
    title: props.docSend.title || '',
    fname: props.docSend.fname || '',
    lname: props.docSend.lname || '',
});

const handleSubmit = () => {
    form.clearErrors(); // ล้างข้อผิดพลาดก่อนตรวจสอบ

    // ตรวจสอบค่าที่ป้อน
    if (!form.result) {
        form.setError('result', 'กรุณาเลือกผลการตรวจ');
    }
    if (!form.check_date) {
        form.setError('check_date', 'กรุณาเลือกวันที่');
    }

    // ถ้ามีข้อผิดพลาดให้หยุดการส่งฟอร์ม
    if (form.errors.result || form.errors.check_date) {
        return;
    }

    // ถ้าทุกอย่างถูกต้อง ให้ส่งข้อมูล
    form.put(route('docs_sends.check', { id: props.docSend.id }), {
        onSuccess: () => {
            Swal.fire({
                icon: 'success',
                title: 'บันทึกสำเร็จ',
                text: 'ข้อมูลได้ถูกบันทึกเรียบร้อยแล้ว',
                confirmButtonColor: '#3085d6',
                customClass: {
                    confirmButton: 'custom-confirm-button'
                }
            });

            let titleText;
            switch (form.title) {
                case '0':
                    titleText = 'นาย';
                    break;
                case '1':
                    titleText = 'นาง';
                    break;
                case '2':
                    titleText = 'นางสาว';
                    break;
                default:
                    titleText = '';
                    break;
            }

            let message;
            switch (form.result) {
                case '0':
                    message = `✅ **ผลการตรวจเอกสาร** (วันที่: ${form.check_date})\n\n📌 **ผู้สมัคร**: ${titleText} ${form.fname} ${form.lname}\n\n🟢 **สถานะ**: เอกสารผ่านการตรวจเรียบร้อย`;
                    break;
                case '1':
                    message = `📋 **ผลการตรวจเอกสาร** (วันที่: ${form.check_date})\n\n📌 **ผู้สมัคร**: ${titleText} ${form.fname} ${form.lname}\n\n⚠️ **คำแนะเพิ่มเติม**: ${form.suggestions}`;
                    break;
                case '2':
                    message = `⏳ **ผลการตรวจเอกสาร** (วันที่: ${form.check_date})\n\n📌 **ผู้สมัคร**: ${titleText} ${form.fname} ${form.lname}\n\n🔄 **สถานะ**: รอการตรวจ`;
                    break;
                case '3':
                    message = `🔧 **ผลการตรวจเอกสาร** (วันที่: ${form.check_date})\n\n📌 **ผู้สมัคร**: ${titleText} ${form.fname} ${form.lname}\n\n🛠 **สถานะ**: ดำเนินการแก้ไข`;
                    break;
                default:
                    message = `❌ **ผลการตรวจเอกสาร**\n\n📌 **ผู้สมัคร**: ${titleText} ${form.fname} ${form.lname}\n\n🚫 **สถานะ**: ผลการตรวจเอกสารไม่ถูกต้อง`;
                    break;
            }

            sendLineNotification(props.docSend.user_id, message);
        },
        onError: (error) => {
            Swal.fire({
                icon: 'error',
                title: 'บันทึกไม่สำเร็จ',
                text: 'เกิดข้อผิดพลาดในการบันทึกข้อมูล กรุณาลองใหม่อีกครั้ง',
                confirmButtonColor: '#d33',
                customClass: {
                    confirmButton: 'custom-confirm-button'
                }
            });

            console.error('Error submitting form:', error);
        },

    });
};

const handleCancel = () => {
    Swal.fire({
        title: 'ยกเลิกการแก้ไข?',
        text: "คุณต้องการยกเลิกการแก้ไขข้อมูลหรือไม่?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'ใช่, ยกเลิก',
        cancelButtonText: 'ไม่, ยกเลิก',
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = route('docs_sends.index');  // หรือเปลี่ยน URL ตามที่ต้องการ
        }
    });
};

const sendLineNotification = (userId, message) => {
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    if (!token) {
        console.error('CSRF token not found');
        return;
    }

    fetch(route('line.notify', { userId }), {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token,
        },
        body: JSON.stringify({
            message: message,
        }),
    })
        .then(response => response.json())
        .then(data => {
            console.log('Line Notify Response:', data);
        })
        .catch(error => {
            console.error('Error:', error);
        });
};
</script>

<style scoped>
.text-red-500 {
    color: red;
    /* ปรับแต่งตามต้องการ */
}
</style>
