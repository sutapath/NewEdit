<template>

  <Head title="Edit Scholarship Application" />
  <form @submit.prevent="handleSubmit" enctype="multipart/form-data">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 w-full">
      <div>
        <InputLabel for="result" value="ผลการตรวจ" />
        <select id="result" v-model="form.result" class="mt-1 block w-full bg-white rounded-xl border-gray-300">
          <option value="0">ผ่าน</option>
          <option value="1">แก้ไข</option>
         <!--  <option value="2">รอการตรวจ</option> -->
          <option value="3">ไม่ผ่านการพิจารณา</option>
        </select>
        <InputError class="mt-2" :message="form.errors.result" />
      </div>
      <div class="mb-4">
        <InputLabel for="suggestions" value="ข้อเสนอแนะ" />
        <TextInput id="suggestions" type="text" class="mt-1 block w-full" v-model="form.suggestions" />
        <InputError class="mt-2" :message="form.errors.suggestions" />
      </div>
    </div>
    <div class="flex justify-end mt-4">
      <Link :href="route('scholarship_application.check', props.application.id)" class="custom-button-success"
        @click="handleSubmit">
      บันทึก
      </Link>
      <button type="button" @click="handleCancel" class="ml-2 custom-button-danger">
        ยกเลิก
      </button>
    </div>
  </form>
</template>

<script setup>
import { ref} from 'vue';
import { usePage, useForm,router } from '@inertiajs/vue3';
import { Head, Link } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Swal from 'sweetalert2'; // นำเข้า sweetalert2

// รับ props จาก Inertia.js
const { props } = usePage();
console.log(props.application.id)

// สร้างฟอร์มด้วย useForm
const form = useForm({
  result: props.application.result,
  suggestions: props.application.suggestions,
  title: props.application.title,
  fname: props.application.fname,
  lname: props.application.lname,
});

const handleSubmit = () => {
  form.put(route('scholarship_application.check', props.application.id), {
    onSuccess: () => {
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
          message = `✔️ **ผลการตรวจสอบ**: \n\n📌 **ชื่อผู้สมัคร**: ${titleText} ${form.fname} ${form.lname} \n✅ **สถานะ**: ผ่านการตรวจเรียบร้อย \n\n🎓 ขอบคุณสำหรับการสมัครทุนการศึกษา!`;
          break;
        case '1':
          message = `🔍 **ผลการตรวจสอบ**: \n\n📌 **ชื่อผู้สมัคร**: ${titleText} ${form.fname} ${form.lname} \n⚠️ **สถานะ**: ต้องแก้ไขเพิ่มเติม \n📄 **คำแนะเพิ่มเติม**: ${form.suggestions} \n\nกรุณาแก้ไขและส่งข้อมูลใหม่อีกครั้ง`;
          break;
        case '2':
          message = `⏳ **ผลการตรวจสอบ**: \n\n📌 **ชื่อผู้สมัคร**: ${titleText} ${form.fname} ${form.lname} \n🔄 **สถานะ**: กำลังรอการตรวจสอบ \n\nโปรดรอสักครู่ ทางทีมงานกำลังดำเนินการตรวจสอบ`;
          break;
        case '3':
          message = `❌ **ผลการตรวจสอบ**: \n\n📌 **ชื่อผู้สมัคร**: ${titleText} ${form.fname} ${form.lname} \n🚫 **สถานะ**: ไม่ผ่านกิจกรรม \n\nโปรดตรวจสอบและติดต่อเจ้าหน้าที่หากต้องการข้อมูลเพิ่มเติม`;
          break;
        default:
          message = `❌ **ผลการตรวจสอบ**: \n\n📌 **ชื่อผู้สมัคร**: ${titleText} ${form.fname} ${form.lname} \n🚫 **สถานะ**: ไม่ผ่านเกณฑ์การพิจารณา \n\nขออภัยในความไม่สะดวก`;
          break;
      }

      sendLineNotification(props.application.user_id, message);
 
      Swal.fire({
        title: 'บันทึกสำเร็จ!',
        text: 'ข้อมูลของคุณได้รับการบันทึกแล้ว และการแจ้งเตือน LINE ได้ส่งเรียบร้อยแล้ว',
        icon: 'success',
        confirmButtonText: 'ตกลง',
        customClass: {
          confirmButton: 'bg-blue-500 text-white hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 rounded-lg' // เพิ่มคลาส CSS ที่ต้องการให้กับปุ่ม
        },
      }).then(() => {  
        window.location.href = route('scholarship_applications.show', { application: props.application.id });
      }); 
    },
    onError: () => {
      // แสดง Swal แจ้งเตือนกรณีที่เกิดข้อผิดพลาด
      Swal.fire({
        title: 'เกิดข้อผิดพลาด!',
        text: 'ไม่สามารถบันทึกข้อมูลได้ กรุณาลองใหม่อีกครั้ง',
        icon: 'error',
        confirmButtonText: 'ตกลง',
        customClass: {
          confirmButton: 'bg-red-500 text-white hover:bg-red-700 focus:ring-2 focus:ring-red-500 rounded-lg' // เปลี่ยนสีปุ่ม
        },
      });

    }
  });
};
const handleCancel = () => { 
  window.location.href = route('scholarship_applications.show', { application: props.application.id });
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
