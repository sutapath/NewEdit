<template>
  <AuthenticatedLayout>

    <Head title="Create Apply Scholar" />

    <div class="max-w-7xl mx-auto mb-3 py-4 mt-20 px-10">
      <div
        class="bg-gray-700 dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg border border-gray-500 dark:border-gray-600">
        <div class="py-2 px-4 text-white dark:text-gray-100 flex justify-between items-center">
          <div class="px-4">ลงทะเบียนกิจกรรม</div>
        </div>
      </div>
    </div>

    <div class="max-w-7xl mx-auto mb-10 bg-white">
      <div class="bg-white rounded-xl shadow-md p-6 sm:p-8 lg:p-10 mx-4 md:mx-6 lg:mx-8">
        <form @submit.prevent="handleSubmit">
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6 w-full">
            <div>
              <InputLabel for="title" value="คำนำหน้า" />
              <select id="title" v-model="form.title" class="mt-1 block w-full bg-gray-200 rounded-xl border-gray-300"
                disabled>
                <option value="0">นาย (Mr.)</option>
                <option value="1">นาง (Mrs.)</option>
                <option value="2">นางสาว (Miss)</option>
              </select>
              <InputError class="mt-2" :message="form.errors.title" />
            </div>
            <div>
              <InputLabel for="fname" value="ชื่อ" />
              <TextInput id="fname" type="text" class="mt-1 block w-full bg-gray-200" v-model="form.fname" disabled />
              <InputError class="mt-2" :message="form.errors.fname" />
            </div>
            <div>
              <InputLabel for="lname" value="นามสกุล" />
              <TextInput id="lname" type="text" class="mt-1 block w-full bg-gray-200" v-model="form.lname" disabled />
              <InputError class="mt-2" :message="form.errors.lname" />
            </div>
          </div>

          <div class="mt-4">
            <InputLabel for="regis_date" value="วันที่ลงทะเบียน" />
            <TextInput id="regis_date" type="date" class="mt-1 block w-full" v-model="form.regis_date" required />
            <InputError class="mt-2" :message="form.errors.regis_date" />
          </div>

          <div class="mt-6 d-flex align-items-center">
            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
              class="custom-button-success me-4">
              <span v-if="form.processing">กำลังโหลด...</span>
              <span v-else>บันทึก</span>
            </PrimaryButton>
            <!-- ปุ่มกลับที่มีการยืนยันก่อนกลับ -->
            <Link @click.prevent="confirmBack" class="custom-button-danger ml-3">
            กลับ
            </Link>
          </div>
        </form>
      </div>
    </div>

  </AuthenticatedLayout>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';
import { Head, Link } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
  auth: Object,
  activity_id: String
});

const today = new Date().toISOString().split('T')[0];

const form = useForm({
  regis_date: today,
  user_id: props.auth.user.id,
  activity_id: props.activity_id,
  title: props.auth.user.title || '',
  fname: props.auth.user.fname || '',
  lname: props.auth.user.lname || '',
});

const handleSubmit = () => {
  form.post(route('activity_registrations.store'), {
    onSuccess: () => {
      Swal.fire({
        title: "บันทึกสำเร็จ!",
        text: "การลงทะเบียนกิจกรรมของคุณถูกบันทึกแล้ว",
        icon: "success",
        confirmButtonText: "ตกลง",
        confirmButtonColor: "#4CAF50" // ปุ่มสีเขียว
      }).then(() => {
        window.location.href = route('activities.index');
      });
    },
    onError: () => {
      Swal.fire({
        title: "เกิดข้อผิดพลาด!",
        text: "กรุณาตรวจสอบข้อมูลและลองใหม่",
        icon: "error",
        confirmButtonText: "ตกลง",
        confirmButtonColor: "#d33" // ปุ่มสีแดง
      });
    }
  });
};

// ฟังก์ชันยืนยันก่อนกลับ
const confirmBack = () => {
  Swal.fire({
    title: "คุณแน่ใจหรือไม่?",
    text: "หากคุณกลับ, ข้อมูลที่ยังไม่ได้บันทึกจะหายไป",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "กลับ",
    cancelButtonText: "ยกเลิก",
    confirmButtonColor: "#d33", // ปุ่มสีแดง
    cancelButtonColor: "#aaa", // ปุ่มยกเลิกสีเทา
  }).then((result) => {
    if (result.isConfirmed) {
      window.location.href = route('activities.index'); // เปลี่ยนเส้นทางไปยังหน้าที่ต้องการ
    }
  });
};

</script>
