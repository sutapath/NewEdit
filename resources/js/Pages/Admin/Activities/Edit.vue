<template>
  <AuthenticatedLayout>

    <Head title="Edit Activity" />
    <div class="py-2">
      <div class="max-w-7xl mx-auto mb-3 py-4 mt-20 px-10">
        <div
          class="bg-gray-700 dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg border border-gray-500 dark:border-gray-600">
          <div class="py-2 px-4 text-white dark:text-gray-100 flex justify-between items-center">
            <div class="px-4">แก้ไขข้อมูลกิจกรรม</div>
            <div class="flex items-center space-x-4">
              <Link @click.prevent="confirmReturn" class="custom-button-danger">กลับ</Link>
              <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                @click="handleSubmit" class="custom-button-success">
                <span v-if="form.processing">กำลังโหลด...</span>
                <span v-else> บันทึก</span>
              </PrimaryButton>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="max-w-7xl px-6 mx-auto">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <form @submit.prevent="handleSubmit" class="grid grid-cols-1 md:grid-cols-2 gap-6 w-full">
          <div class="mb-4">
            <InputLabel for="activity_name" value="ชื่อกิจกรรม" />
            <TextInput id="activity_name" type="text" class="mt-1 block w-full" v-model="form.activity_name" required
              autofocus />
            <InputError class="mt-2" :message="form.errors.activity_name" />
          </div>

          <div class="mb-4">
            <InputLabel for="start_date" value="วันที่จัดกิจกรรม" />
            <TextInput id="start_date" type="date" class="mt-1 block w-full" v-model="form.start_date" required />
            <InputError class="mt-2" :message="form.errors.start_date" />
          </div>

          <div class="mb-4">
            <InputLabel for="end_date" value="วันที่สิ้นสุด" />
            <TextInput id="end_date" type="date" class="mt-1 block w-full" v-model="form.end_date" required />
            <InputError class="mt-2" :message="form.errors.end_date" />
          </div>

          <div class="mb-4">
            <InputLabel for="location" value="สถานที่จัดกิจกรรม" />
            <TextInput id="location" type="text" class="mt-1 block w-full" v-model="form.location" required />
            <InputError class="mt-2" :message="form.errors.location" />
          </div>

          <div class="mb-4">
            <InputLabel for="organizer" value="ผู้จัดกิจกรรม" />
            <TextInput id="organizer" type="text" class="mt-1 block w-full" v-model="form.organizer" required />
            <InputError class="mt-2" :message="form.errors.organizer" />
          </div>

          <div class="mb-4">
            <InputLabel for="description" value="คำอธิบาย" />
            <TextInput id="description" type="text" class="mt-1 block w-full" v-model="form.description" />
            <InputError class="mt-2" :message="form.errors.description" />
          </div>

          <div class="mb-4">
            <InputLabel for="limits" value="จำนวนที่จำกัด" />
            <TextInput id="limits" type="number" class="mt-1 block w-full" v-model="form.limits" required />
            <InputError class="mt-2" :message="form.errors.limits" />
          </div>

          <div class="mb-4">
            <InputLabel for="regis_due" value="วันที่หมดเขตลงทะเบียน" />
            <TextInput id="regis_due" type="date" class="mt-1 block w-full" v-model="form.regis_due" required />
            <InputError class="mt-2" :message="form.errors.regis_due" />
          </div>

          <div class="mb-4">
            <InputLabel for="link" value="ลิงค์" />
            <TextInput id="link" type="text" class="mt-1 block w-full" v-model="form.link" />
            <InputError class="mt-2" :message="form.errors.link" />
          </div>

        </form>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { useForm, usePage } from '@inertiajs/vue3';
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

import Swal from 'sweetalert2';

const props = defineProps(['auth', 'activity']);

const form = useForm({
  activity_name: props.activity.activity_name,
  start_date: props.activity.start_date,
  end_date: props.activity.end_date,
  location: props.activity.location,
  organizer: props.activity.organizer,
  description: props.activity.description,
  limits: props.activity.limits,
  regis_due: props.activity.regis_due,
  link: props.activity.link,
  hours: props.activity.hours,
  user_id: props.auth.user.id,
});

const handleSubmit = () => {
  form.put(route('activities.update', { activity: props.activity.id }), {
    onFinish: () => {
      Swal.fire({
        title: 'บันทึกสำเร็จ!',
        text: 'ข้อมูลกิจกรรมได้รับการอัพเดทแล้ว',
        icon: 'success',
        confirmButtonText: 'เสร็จสิ้น',
        customClass: {
          confirmButton: 'bg-green-500 hover:bg-green-600 text-white',
        },
      }).then(() => {
        window.location.href = route('activities.index');
      });
    },
    onError: () => {
      Swal.fire({
        title: 'เกิดข้อผิดพลาด!',
        text: 'ไม่สามารถบันทึกข้อมูลได้',
        icon: 'error',
        confirmButtonText: 'ตกลง',
        customClass: {
          confirmButton: 'bg-red-500 hover:bg-red-600 text-white',
        },
      });
    },
  });
};

// ฟังก์ชันยืนยันการกลับ
const confirmReturn = () => {
  Swal.fire({
    title: 'คุณแน่ใจหรือไม่?',
    text: 'หากคุณกลับข้อมูลจะหาย',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'กลับไป',
    cancelButtonText: 'ยกเลิก',
    reverseButtons: true,
    customClass: {
      confirmButton: 'bg-red-500 text-white',
      cancelButton: 'bg-gray-300 text-black',
    },
  }).then((result) => {
    if (result.isConfirmed) {
      window.location.href = route('activities.index');
    }
  });
};
</script>
