<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { Head, Link } from '@inertiajs/vue3';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Swal from 'sweetalert2'; // นำเข้า sweetalert2

const props = defineProps({
  student: Object,
});
console.log(props);
const form = useForm({
  citizen_id: props.student?.citizen_id || '',
  title: props.student?.title || '',
  fname: props.student?.fname || '',
  lname: props.student?.lname || '',
  code: props.student?.code || '',
});

function submit() {
  form.put(route("intlScholars.update", props.student.id), {
    onSuccess: () => {
      Swal.fire({
        icon: 'success',
        title: 'บันทึกสำเร็จแล้ว',
        showConfirmButton: true, // แสดงปุ่มยืนยัน
        confirmButtonText: 'ปิด', // เปลี่ยนข้อความปุ่มเป็น "ปิด"
        timer: 1500,
        customClass: {
          confirmButton: 'bg-green-500 text-white hover:bg-green-700', // เพิ่มสีให้ปุ่ม "ปิด"
        },
        willClose: () => {
          form.reset();
          setTimeout(() => {
            window.location.reload();
          }, 500);
        }
      });
    },
    onError: (errors) => {
      if (errors.response && errors.response.data.errors) {
        const errorData = errors.response.data.errors;
        Object.keys(errorData).forEach((field) => {
          form.setError(field, {
            message: errorData[field][0],
          });
        });
      } else {
        console.error("เกิดข้อผิดพลาดที่ไม่คาดคิด:", errors);
      }
    },
  });
}

function clearError(field) {
  form.setError(field, null);
}

function confirmCancel() {
  Swal.fire({
    title: 'คุณต้องการยกเลิกหรือไม่?',
    text: 'การเปลี่ยนแปลงของคุณจะไม่ถูกบันทึก!',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'ยืนยัน',
    cancelButtonText: 'ยกเลิก',
    confirmButtonColor: '#3085d6', // สีของปุ่มยืนยัน
    cancelButtonColor: '#d33', // สีของปุ่มยกเลิก
  }).then((result) => {
    if (result.isConfirmed) {
      window.location.href = route('intlScholars.index'); // กลับไปหน้าหลัก
    }
  });
}

</script>

<template>

  <Head title="แก้ไขข้อมูลนักศึกษา" />

  <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 bg-white sm:rounded-xl mb-20">
    <form @submit.prevent="submit">
      <div class="p-6 space-y-4">
        <div v-if="form.successMessage" class="bg-green-500 text-white text-sm mt-4 p-2 rounded">
          {{ form.successMessage }}
        </div>

        <div>
          <label for="citizen_id" class="block text-sm font-medium text-gray-700">หมายเลขบัตรประชาชน</label>
          <TextInput id="citizen_id" v-model="form.citizen_id" type="text" placeholder="กรุณาระบุหมายเลขบัตรประชาชน"
            class="mt-1 block w-full" @input="clearError('citizen_id')" />
          <span v-if="form.errors.citizen_id" class="text-red-500 text-sm">{{ form.errors.citizen_id }}</span>
        </div>

        <div>
          <label for="title" class="block text-sm font-medium text-gray-700">คำนำหน้า</label>
          <select id="title" v-model="form.title"
            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
            @change="clearError('title')">
            <option value="" selected disabled>เลือกคำนำหน้า</option>
            <option value="0">นาย</option>
            <option value="1">นาง</option>
            <option value="2">นางสาว</option>
          </select>
          <span v-if="form.errors.title" class="text-red-500 text-sm">{{ form.errors.title }}</span>
        </div>

        <div>
          <label for="fname" class="block text-sm font-medium text-gray-700">ชื่อ</label>
          <TextInput id="fname" v-model="form.fname" type="text" placeholder="กรุณาระบุชื่อ" class="mt-1 block w-full"
            @input="clearError('fname')" />
          <span v-if="form.errors.fname" class="text-red-500 text-sm">{{ form.errors.fname }}</span>
        </div>

        <div>
          <label for="lname" class="block text-sm font-medium text-gray-700">นามสกุล</label>
          <TextInput id="lname" v-model="form.lname" type="text" placeholder="กรุณาระบุนามสกุล"
            class="mt-1 block w-full" @input="clearError('lname')" />
          <span v-if="form.errors.lname" class="text-red-500 text-sm">{{ form.errors.lname }}</span>
        </div>

        <div>
          <label for="code" class="block text-sm font-medium text-gray-700">รหัสนักศึกษา</label>
          <TextInput id="code" v-model="form.code" type="text" placeholder="กรุณาระบุรหัสนักศึกษา"
            class="mt-1 block w-full" @input="clearError('code')" />
          <span v-if="form.errors.code" class="text-red-500 text-sm">{{ form.errors.code }}</span>
        </div>

        <div class="flex space-x-4">
          <PrimaryButton type="submit" class="custom-button-success px-1 py-1">บันทึก</PrimaryButton>
          <button type="button" @click="confirmCancel" class="custom-button-danger px-1 py-1">กลับ</button>
        </div>
      </div>
    </form>
  </div>
</template>
