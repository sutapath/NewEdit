<template>

  <Head title="Check Activity" />

  <div class="max-w-6xl mx-auto mt-3 px-4 sm:px-6 lg:px-8"> <!-- เพิ่ม padding สำหรับแต่ละขนาดหน้าจอ -->

      <form @submit.prevent="handleSubmit" enctype="multipart/form-data">
          <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 lg:grid-cols-4 xl:grid-cols-5 gap-6 w-full"> <!-- ปรับ grid ตามขนาดหน้าจอ -->

              <!-- Result field -->
              <div>
                  <InputLabel for="result" value="ผลการตรวจ" />
                  <select id="result" v-model="form.result"
                      class="mt-1 block w-full bg-white rounded-xl border-gray-300">
                      <option value="1">ผ่าน</option>
                      <option value="4">แก้ไข</option>
                      <option value="2">รอการตรวจ</option>
                      <option value="3">ไม่ผ่าน</option>
                  </select>
                  <InputError class="mt-2" :message="form.errors.result" />
              </div>

              <!-- Suggestions field -->
              <div class="mb-4">
                  <InputLabel for="suggestions" value="ข้อเสนอแนะ" />
                  <TextInput id="suggestions" type="text" class="mt-1 block w-full" v-model="form.suggestions" />
                  <InputError class="mt-2" :message="form.errors.suggestions" />
              </div>

              <!-- Hours field -->
              <div class="mb-4">
                  <InputLabel for="hours" value="จำนวนชั่วโมง" />
                  <TextInput id="hours" type="number" class="mt-1 block w-full" v-model="form.hours" />
                  <InputError class="mt-2" :message="form.errors.hours" />
              </div>

              <!-- Check Date field -->
              <div class="mb-4">
                  <InputLabel for="check_date" value="วันที่" />
                  <TextInput id="check_date" type="date" class="mt-1 block w-full" v-model="form.check_date" />
                  <InputError class="mt-2" :message="form.errors.check_date" />
              </div>

              <!-- Submit Button -->
              <div class="mb-4 mt-6">
                  <PrimaryButton class="custom-button-success" @click="handleSubmit">
                      บันทึก
                  </PrimaryButton>
              </div>

          </div>
      </form>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { usePage, useForm } from '@inertiajs/vue3';
import { Head, Link } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from "@/Components/PrimaryButton.vue";

const { props } = usePage();
// ฟังก์ชันแปลงปี ค.ศ. เป็นปี พ.ศ.
const convertToThaiBuddhistYear = (date) => {
  const year = date.getFullYear() + 543;
  const month = String(date.getMonth() + 1).padStart(2, '0');
  const day = String(date.getDate()).padStart(2, '0');
  return `${year}-${month}-${day}`;
};

// ตั้งค่า check_date ให้เป็นวันที่ปัจจุบันในรูปแบบ พ.ศ.
const currentDate = new Date();
const checkDate = convertToThaiBuddhistYear(currentDate);

// Refactor form to formData
const formData = ref({
  result: props.save.result,
  suggestions: props.save.suggestions,
  check_date: checkDate,
  title: props.save.title || '',
  hours: props.save.hours,
  fname: props.save.fname || '',
  lname: props.save.lname || '',
});

// handleSubmit function
const handleSubmit = () => {
  formData.value.put(route('activity_saves.updatecheck', { save: props.save.id }), {
    onSuccess: () => {
      let titleText;
      switch (formData.value.title) {
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
      switch (formData.value.result) {
        case '1':
          message = `📋 **ผลการตรวจข้อมูลกิจกรรม**\n
          **วันที่ตรวจ**: ${formData.value.check_date}\n
          **ชื่อผู้เข้าร่วม**: ${titleText} ${formData.value.fname} ${formData.value.lname}\n
          **สถานะ**: ✅ ข้อมูลกิจกรรมผ่านการตรวจเรียบร้อย\n
          **จำนวนชั่วโมง**: ${formData.value.hours} ชั่วโมง\n\n
          ขอบคุณสำหรับการเข้าร่วมกิจกรรมของเรา! 🎉`;
          break;
        case '4':
          message = `📋 **ผลการตรวจข้อมูลกิจกรรม**\n
          **วันที่ตรวจ**: ${formData.value.check_date}\n
          **ชื่อผู้เข้าร่วม**: ${titleText} ${formData.value.fname} ${formData.value.lname}\n
          **สถานะ**: ⚠️ ต้องแก้ไขเพิ่มเติม\n
          **คำแนะนำเพิ่มเติม**: ${formData.value.suggestions}\n\n
          กรุณาดำเนินการตามคำแนะนำและส่งข้อมูลใหม่อีกครั้ง.`;
          break;
        case '2':
          message = `📋 **ผลการตรวจข้อมูลกิจกรรม**\n
          **วันที่ตรวจ**: ${formData.value.check_date}\n
          **ชื่อผู้เข้าร่วม**: ${titleText} ${formData.value.fname} ${formData.value.lname}\n
          **สถานะ**: ⏳ รอการตรวจ\n\n
          ขอบคุณที่รอการตรวจสอบจากทีมงาน.`;
          break;
        case '3':
          message = `📋 **ผลการตรวจข้อมูลกิจกรรม**\n
          **วันที่ตรวจ**: ${formData.value.check_date}\n
          **ชื่อผู้เข้าร่วม**: ${titleText} ${formData.value.fname} ${formData.value.lname}\n
          **สถานะ**: 🚫 ไม่ผ่านกิจกรรม\n\n
          โปรดติดต่อเจ้าหน้าที่หากต้องการรายละเอียดเพิ่มเติม.`;
          break;
        default:
          message = `📋 **ผลการตรวจข้อมูลกิจกรรม**\n
          **ชื่อผู้เข้าร่วม**: ${titleText} ${formData.value.fname} ${formData.value.lname}\n
          **สถานะ**: 🚫 ผลการตรวจไม่ถูกต้อง\n\n
          กรุณาตรวจสอบข้อมูลที่ส่งเข้ามาอีกครั้ง.`;
          break;
      }

      sendLineNotification(props.save.user_id, message);
    },
  });
};

// ฟังก์ชันเรียกไปยัง LineNotifyController
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
