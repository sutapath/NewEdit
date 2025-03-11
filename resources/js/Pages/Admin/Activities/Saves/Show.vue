<template>
  <AuthenticatedLayout>

    <Head title="Create Activity" />

    <div class="max-w-7xl mx-auto py-4 mt-20 px-4 sm:px-10">
      <div class="px-4 text-white dark:text-gray-100 flex flex-col md:flex-row justify-between items-center">
        <div class="py-2 text-gray-800 dark:text-gray-100 rounded-t-lg text-xl font-bold">
          ข้อมูลกิจกรรม
        </div>
        <div class="flex-1 mr-0 ml-0 md:mr-3 md:ml-3 w-full md:w-auto">
          <hr class="border-t border-gray-300" />
        </div>
        <div class="flex justify-end space-x-4 mt-4 md:mt-0">
          <Link :href="route('activity_saves.edit', save.id)"
            v-if="(hasRole('scholar') || hasRole('intlscholar')) && save.result !== '1'" class="custom-button-warning">
          แก้ไข</Link>
          <button @click="showCheckActivity(save.id)" class="custom-button-warning"
            v-if="hasRole('admin') || hasRole('officer')">
            ตรวจสอบ
          </button>

          <Link :href="route('activity_registrations.index')" class="custom-button-danger">
          กลับ
          </Link>
        </div>
      </div>
    </div>




    <div v-if="showCheck" class="max-w-6xl mx-auto mt-3 px-4 sm:px-6 lg:px-8">
      <div v-if="form.successMessage" class="bg-green-500 text-white p-4 rounded-lg mb-4">
        <p>{{ form.successMessage }}</p>
      </div>
      <div v-if="form.errorMessage" class="bg-red-500 text-white p-4 rounded-lg mb-4">
        <p>{{ form.errorMessage }}</p>
      </div>
      <form @submit.prevent="handleSubmit" enctype="multipart/form-data">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 lg:grid-cols-4 xl:grid-cols-5 gap-6 w-full">
          <!-- ปรับ grid ตามขนาดหน้าจอ -->

          <!-- Result field -->
          <div>
            <InputLabel for="result" value="ผลการตรวจ" />
            <select id="result" v-model="form.result" class="mt-1 block w-full bg-white rounded-xl border-gray-300">
              <option value="1">ผ่าน</option>
              <option value="4">แก้ไข</option>
              <!-- <option value="2">รอการตรวจ</option> -->
              <option value="3">ไม่ผ่าน</option>
            </select>
            <InputError class="mt-2" :message="form.errors.result" />
          </div>

          <!-- Suggestions field -->
          <div class="mb-4">
            <InputLabel for="suggestions" value="ข้อเสนอแนะ" />
            <TextInput id="suggestions" type="text" class="mt-1 block w-full" v-model="form.suggestions"
              placeholder="กรุณาระบุข้อเสนอแนะ" />
            <InputError class="mt-2" :message="form.errors.suggestions" />
          </div>

          <!-- Hours field -->
          <div class="mb-4">
            <InputLabel for="hours" value="จำนวนชั่วโมง" />
            <TextInput id="hours" type="number" class="mt-1 block w-full" v-model="form.hours"
              placeholder="กรุณาระบุจำนวนชั่วโมง" />
            <InputError class="mt-2" :message="form.errors.hours" />
          </div>

          <!-- <div class="mb-4">
            <InputLabel for="check_date" value="วันที่" />
            <TextInput id="check_date" type="date" class="mt-1 block w-full" v-model="form.check_date" />
            <InputError class="mt-2" :message="form.errors.check_date" />
          </div> -->

          <!-- Submit Button -->
          <div class="mb-4 mt-6">
            <PrimaryButton class="custom-button-success" @click="handleSubmit">
              บันทึก
            </PrimaryButton>
            <PrimaryButton @click="handleCancel" class="mx-2 custom-button-danger" >
              ยกเลิก
            </PrimaryButton>
          </div>

        </div>
      </form>
    </div>

    <div class="max-w-6xl mx-auto py-6 px-4 sm:px-6">
      <div v-if="form"
        class="bg-white dark:bg-gray-800 shadow-md sm:rounded-lg border border-gray-300 dark:border-gray-600">
        <div class="py-4 px-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6 w-full">
            <!-- Image Section -->
            <div class="flex items-center justify-center">
              <div v-if="form.fileUrl" class="w-full">
                <img :src="form.fileUrl" alt="Activity file" class="w-full h-auto object-cover rounded-md" />
              </div>
            </div>

            <!-- Content Section -->
            <div class="flex flex-col">
              <h2 class="text-2xl font-semibold mb-3 mt-3">{{ save.activity_name }}</h2>
              <hr />
              <h2 class="text-gray-600 text-xl font-semibold mb-3 mt-3">
                <span v-if="save.title == 0">นาย</span>
                <span v-else-if="save.title == 1">นาง</span>
                <span v-else-if="save.title == 2">นางสาว</span>
                {{ save.fname }} {{ save.lname }}
              </h2>
              <p class="text-gray-600 dark:text-gray-300 mb-3 text-xl">สถานที่ : <span class="font-semibold">{{
                  save.location }}</span></p>
              <p class="text-gray-600 dark:text-gray-300 mb-3 text-xl">วันที่ : <span class="font-semibold">{{
                  formatDate(save.date) }}</span> ( {{ formatTime(save.launch) }} - {{ formatTime(save.end) }} )</p>
              <p class="text-gray-600 dark:text-gray-300 mb-3 text-xl">{{ save.description }}</p>

              <div v-if="(save.suggestions && (save.result === '0' || save.result === '4'))"
                class="mt-3 bg-red-500 rounded-lg">
                <p class="text-white mb-1 px-2 py-2">ข้อเสนอแนะ: {{ save.suggestions }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
<script setup>
import { ref } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { usePermission } from '@/composables/permissions';
import Check from "@/Pages/Admin/Activities/Saves/Check.vue";
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from "@/Components/PrimaryButton.vue";

const { hasRole } = usePermission();
const props = defineProps({
  save: Object, // รับข้อมูลเอกสารจาก backend
});

// ใช้ useForm แทน formData
const save = props.save || {}; // Default to empty object if undefined
const form = useForm({
  title: save.title || '',
  fname: save.fname || '',
  lname: save.lname || '',
  date: save.date || '',
  user_id: save.user_id || props.auth.user.id,
  description: save.description || '',
  file: null,
  fileUrl: save.file ? `/storage/files/${save.file}` : '', // Set URL for preview
  institution: save.institution || '',
  launch: save.launch || '',
  end: save.end || '',
  location: save.location || '',
  result: save.result || 2,
});

const showCheck = ref(false);

const formatTime = (time) => {
  if (!time) return "Invalid time";
  return time.slice(0, 5);
};
console.log('Received props:', props);
const handleCancel = () => {
  Swal.fire({
    title: 'คุณแน่ใจหรือไม่?',
    text: 'หากคุณยกเลิก ข้อมูลจะไม่ถูกบันทึก',
    icon: 'warning',
    showCancelButton: true, // แสดงปุ่มยกเลิก
    confirmButtonText: 'ยืนยัน',
    cancelButtonText: 'ยกเลิก',
    confirmButtonColor: '#3085d6', // สีของปุ่มยืนยัน
    cancelButtonColor: '#d33', // สีของปุ่มยกเลิก
  }).then((result) => {
    if (result.isConfirmed) {
      // กรณียืนยัน
      Swal.fire({
        icon: 'success',
        title: 'ยกเลิกเรียบร้อย',
        text: 'ข้อมูลของคุณไม่ได้ถูกบันทึก',
        timer: 1500,
        showConfirmButton: false,
      });
      // ทำการกระทำอื่น ๆ ที่ต้องการ
    } else {
      // กรณียกเลิก
      Swal.fire({
        icon: 'info',
        title: 'การยกเลิกการแก้ไข',
        text: 'ข้อมูลของคุณยังคงอยู่',
        timer: 1500,
        showConfirmButton: false,
      });
    }
  });
};

// Date formatting function
const formatDate = (date) => {
  const months = [
    'มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน',
    'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'
  ];

  const d = new Date(date);
  const day = d.getDate().toString().padStart(2, '0');
  const month = months[d.getMonth()];
  const year = d.getFullYear().toString();

  return `${day} ${month} ${year}`;
};

// ฟังก์ชันที่ใช้เมื่อกดปุ่มตรวจสอบ
const showCheckActivity = (id) => {
  showCheck.value = !showCheck.value;
  console.log("Activity ID:", id); // แสดง id หรือทำอย่างอื่นตามที่ต้องการ
};

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

// ใช้ form จาก useForm แทน formData
const formData = ref({
  result: save.result,
  suggestions: save.suggestions,
  check_date: checkDate,
  title: save.title || '',
  hours: save.hours,
  fname: save.fname || '',
  lname: save.lname || '',
});
import Swal from 'sweetalert2';

const handleSubmit = () => {
  if (!form.hours && form.result != 4) {
    Swal.fire({
      icon: 'warning',
      title: 'กรุณาระบุจำนวนชั่วโมง',
      showConfirmButton: true,
      confirmButtonText: 'ตกลง', 
    });
    showCheck.value = false;
    return;
  }

  if (form.result && form.result == 4 && form.suggestions != 4) {
    Swal.fire({
      icon: 'warning',
      title: 'กรุณาระบุข้อเสนอแนะ',
      showConfirmButton: true,
      confirmButtonText: 'ตกลง',
      confirmButtonColor: '#FF6347',
    }).then(() => {
      showCheck.value = false; // เปลี่ยนค่า showCheck.value ที่นี่
    });
    return;
  }


  form.put(route('activity_saves.updatecheck', { save: props.save.id }), {
    onSuccess: () => {
      Swal.fire({
        icon: 'success',
        title: 'ข้อมูลได้ถูกอัปเดตเรียบร้อยแล้ว!',
        showConfirmButton: true,
        confirmButtonText: 'ตกลง',
        confirmButtonColor: '#3085d6', // กำหนดสีของปุ่ม
      });
      showCheck.value = false;

      let titleText;
      switch (form.title) {
        case '0': titleText = 'นาย'; break;
        case '1': titleText = 'นาง'; break;
        case '2': titleText = 'นางสาว'; break;
        default: titleText = ''; break;
      }

      let message;
      switch (form.result) {
        case '1':
          message = `📋 **ผลการตรวจข้อมูลกิจกรรม**\n
          **วันที่ตรวจ**: ${form.check_date}\n
          **ชื่อผู้เข้าร่วม**: ${titleText} ${form.fname} ${form.lname}\n
          **สถานะ**: ✅ ข้อมูลกิจกรรมผ่านการตรวจเรียบร้อย\n
          **จำนวนชั่วโมง**: ${form.hours} ชั่วโมง\n\n
          ขอบคุณสำหรับการเข้าร่วมกิจกรรมของเรา! 🎉`;
          break;
        case '4':
          message = `📋 **ผลการตรวจข้อมูลกิจกรรม**\n
          **วันที่ตรวจ**: ${form.check_date}\n
          **ชื่อผู้เข้าร่วม**: ${titleText} ${form.fname} ${form.lname}\n
          **สถานะ**: ⚠️ ต้องแก้ไขเพิ่มเติม\n
          **คำแนะนำเพิ่มเติม**: ${form.suggestions}\n\n
          กรุณาดำเนินการตามคำแนะนำและส่งข้อมูลใหม่อีกครั้ง.`;
          break;
        case '2':
          message = `📋 **ผลการตรวจข้อมูลกิจกรรม**\n
          **วันที่ตรวจ**: ${form.check_date}\n
          **ชื่อผู้เข้าร่วม**: ${titleText} ${form.fname} ${form.lname}\n
          **สถานะ**: ⏳ รอการตรวจ\n\n
          ขอบคุณที่รอการตรวจสอบจากทีมงาน.`;
          break;
        case '3':
          message = `📋 **ผลการตรวจข้อมูลกิจกรรม**\n
          **วันที่ตรวจ**: ${form.check_date}\n
          **ชื่อผู้เข้าร่วม**: ${titleText} ${form.fname} ${form.lname}\n
          **สถานะ**: 🚫 ไม่ผ่านกิจกรรม\n\n
          โปรดติดต่อเจ้าหน้าที่หากต้องการรายละเอียดเพิ่มเติม.`;
          break;
        default:
          message = `📋 **ผลการตรวจข้อมูลกิจกรรม**\n
          **ชื่อผู้เข้าร่วม**: ${titleText} ${form.fname} ${form.lname}\n
          **สถานะ**: 🚫 ผลการตรวจไม่ถูกต้อง\n\n
          กรุณาตรวจสอบข้อมูลที่ส่งเข้ามาอีกครั้ง.`;
          break;
      }

      sendLineNotification(props.save.user_id, message);
    },
    onError: (errors) => {
      Swal.fire({
        icon: 'error',
        title: 'เกิดข้อผิดพลาดในการอัปเดตข้อมูล',
        text: 'โปรดลองใหม่อีกครั้ง',
        showConfirmButton: true, // แสดงปุ่ม OK
        confirmButtonText: 'ตกลง', // ข้อความปุ่ม
      });
      console.error('Form submission error:', errors);
    },
  });
};


// ฟังก์ชันเรียกไปยัง LineNotifyController
const sendLineNotification = (userId, message) => {
  const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
  console.log('CSRF Token:', token);  // ตรวจสอบว่า token ถูกโหลดมาแล้วหรือไม่

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
