<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { Head, Link } from '@inertiajs/vue3';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Swal from 'sweetalert2'; // นำเข้า sweetalert2

const props = defineProps({
  officer: Object,  // Change "user" to "officer"
});

const form = useForm({
  citizen_id: props.officer?.citizen_id || '',
  title: props.officer?.title || '',
  fname: props.officer?.fname || '',
  lname: props.officer?.lname || '',
  code: props.officer?.code || '',
});

function submit() {
  form.put(route("officers.update", props.officer.id), { // Update route to "officers"
    onSuccess: () => {
      Swal.fire({
        icon: 'success',
        title: 'บันทึกสำเร็จแล้ว',
        showConfirmButton: true,
        confirmButtonText: 'ปิด',
        timer: 1500,
        customClass: {
          confirmButton: 'bg-green-500 text-white hover:bg-green-700',
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
        console.error("Unexpected error:", errors);
      }
    },
  });
}

function clearError(field) {
  form.setError(field, null);
}
// ฟังก์ชันสำหรับยืนยันการยกเลิกการกลับไปหน้าอื่น
function confirmCancel() {
  Swal.fire({
    title: 'คุณต้องการยกเลิกหรือไม่?',
    text: 'การเปลี่ยนแปลงของคุณจะไม่ถูกบันทึก!',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'ยืนยัน',
    cancelButtonText: 'ยกเลิก',
    confirmButtonColor: '#008000',  // สีแดงสำหรับปุ่ม 'ยืนยัน'
    cancelButtonColor: '#FF0000', // สีน้ำเงินสำหรับปุ่ม 'ยกเลิก'
  }).then((result) => {
    if (result.isConfirmed) {
      window.location.href = route('officers.index'); // เปลี่ยนเส้นทางไปยัง officers.index
    }
  });
}

</script>

<template>

  <Head title="Edit Officer" /> <!-- Change title to Officer -->

  <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 bg-white sm:rounded-xl mb-20">
    <form @submit.prevent="submit">
      <div class="p-6 space-y-4">
        <div v-if="form.successMessage" class="bg-green-500 text-white text-sm mt-4 p-2 rounded">
          {{ form.successMessage }}
        </div>

        <div>
          <label for="citizen_id" class="block text-sm font-medium text-gray-700">Citizen ID</label>
          <TextInput id="citizen_id" v-model="form.citizen_id" type="text" placeholder="ระบุหมายเลขบัตรประชาชน"
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
          <label for="fname" class="block text-sm font-medium text-gray-700">First Name</label>
          <TextInput id="fname" v-model="form.fname" type="text" placeholder="ชื่อ" class="mt-1 block w-full"
            @input="clearError('fname')" />
          <span v-if="form.errors.fname" class="text-red-500 text-sm">{{ form.errors.fname }}</span>
        </div>

        <div>
          <label for="lname" class="block text-sm font-medium text-gray-700">Last Name</label>
          <TextInput id="lname" v-model="form.lname" type="text" placeholder="นามสกุล" class="mt-1 block w-full"
            @input="clearError('lname')" />
          <span v-if="form.errors.lname" class="text-red-500 text-sm">{{ form.errors.lname }}</span>
        </div>

        <div>
          <label for="code" class="block text-sm font-medium text-gray-700">Code</label>
          <TextInput id="code" v-model="form.code" type="text" placeholder="รหัสเจ้าหน้าที่" class="mt-1 block w-full"
            @input="clearError('code')" />
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
