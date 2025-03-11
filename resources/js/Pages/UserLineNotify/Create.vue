<template>
  <section>
    <form @submit.prevent="submitForm" class="flex items-center space-x-1 w-full px-5 py-5 max-w-md mx-auto">
      <div class="flex flex-col flex-grow space-y-1 mr-5">
        <div class="relative w-full">
          <TextInput id="line_notify_token" type="text" class="block text-gray-800 w-full"
            v-model="form.line_notify_token" required placeholder="กรุณากรอก token ของคุณ" />
        </div>
      </div>
      <PrimaryButton class="custom-button-success" :disabled="form.processing">
        ยืนยัน
      </PrimaryButton>
    </form>
  </section>
</template>

<script setup>
import { useForm, usePage } from '@inertiajs/vue3';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Swal from 'sweetalert2';

const { props } = usePage();

const form = useForm({
  user_id: props.user_id,
  line_notify_token: '',
});
const submitForm = async () => {
  if (!form.line_notify_token.trim()) {
    Swal.fire({
      title: 'เกิดข้อผิดพลาด!',
      text: 'กรุณากรอก Token ของคุณ',
      icon: 'warning',
      confirmButtonText: 'ตกลง',
      customClass: {
        confirmButton: 'bg-yellow-500 text-white hover:bg-yellow-600',
      },
    });
    return;
  }

  try {
    const response = await axios.post(route('user_line_notify.store'), form);

    if (response.data.success) {
      Swal.fire({
        title: 'สำเร็จ!',
        text: response.data.message,
        icon: 'success',
        confirmButtonText: 'ตกลง',
        customClass: {
          confirmButton: 'bg-green-500 text-white hover:bg-green-600',
        },
      }).then(() => {
        // เปลี่ยนเส้นทางหลังจากการแสดง Swal
        window.location.href = route('user_line_notify.index');  // เปลี่ยน URL ไปยังหน้า /user_line_notify
      });  

    } else {
      Swal.fire({
        title: 'เกิดข้อผิดพลาด!',
        text: response.data.error_message,
        icon: 'error',
        confirmButtonText: 'ตกลง',
        customClass: {
          confirmButton: 'bg-red-500 text-white hover:bg-red-600',
        },
      });
    }

  } catch (error) {
    Swal.fire({
      title: 'เกิดข้อผิดพลาด!',
      text: 'ไม่สามารถสมัครรับการแจ้งเตือนได้ กรุณาลองใหม่อีกครั้ง',
      icon: 'error',
      confirmButtonText: 'ตกลง',
      customClass: {
        confirmButton: 'bg-red-500 text-white hover:bg-red-600',
      },
    });
  }
};

</script>
