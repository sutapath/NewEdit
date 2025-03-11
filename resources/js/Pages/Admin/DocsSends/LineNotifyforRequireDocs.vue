<template>
  <AuthenticatedLayout class="mtb">
    <div class="max-w-7xl mx-auto py-4 mt-20 px-10">
      <div class="px-4 text-white dark:text-gray-100 flex justify-between items-center">
        <div class="py-2 text-gray-800 rounded-t-lg text-xl font-bold">
          แจ้งเตือนการส่งเอกสาร
        </div>
        <div class="flex-1 mr-3 ml-3">
          <hr class="border-t border-gray-300" />
        </div>
        <div class="flex justify-end space-x-4">
          <Link :href="route('docs_sends.index')" class="custom-button-danger">กลับ</Link>
        </div>
      </div>
    </div>

    <div class="w-full max-w-7xl p-6 mx-auto">
      <form @submit.prevent="handleSubmit" class="flex flex-col">
        <div class="mt-4 flex items-center space-x-4">
          <div class="flex-1">
            <InputLabel for="docType" class="block text-gray-700 dark:text-gray-200 text-md font-semibold mb-2">
              ประเภทเอกสาร:
            </InputLabel>
            <select v-model="form.document_type" id="docType"
              class="mt-1 block w-full p-2 border border-gray-300 rounded-md text-md">
              <option value="transcripts">เอกสารแสดงผลการเรียน</option>
              <option value="operat_docs">แบบบันทึกการดำเนินงาน</option>
            </select>
          </div>

          <div class="flex-1">
            <InputLabel for="sendDate" class="block text-gray-700 dark:text-gray-200 text-md font-semibold mb-2">
              วันที่ (กำหนดส่ง):
            </InputLabel>
            <TextInput v-model="form.send_date" id="sendDate" type="date"
              class="mt-1 block w-full p-2 border border-gray-300 rounded-md text-md"></TextInput>
          </div>
        </div>


        <div class="mt-6">
          <InputLabel class="block text-gray-700 dark:text-gray-200 text-md font-semibold mb-2">ข้อความตัวอย่าง:
          </InputLabel>
          <textarea readonly
            class="mt-1 block w-full p-4 border border-gray-300 rounded-md text-md bg-gray-100 resize-none" rows="10">
            {{ previewMessage }}
          </textarea>
        </div>

        <div class="mt-6 flex justify-end space-x-4">
          <button type="submit" class="custom-button-success">ส่งข้อความ</button>
        </div>
      </form>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import moment from 'moment';
import 'moment/locale/th'; // โหลด locale ภาษาไทย
import { Link, useForm, usePage } from '@inertiajs/vue3';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Swal from 'sweetalert2'; // นำเข้า SweetAlert

// ตั้งค่า moment ให้ใช้ภาษาไทย
moment.locale('th');

const { props } = usePage();

const form = useForm({
  user_id: props.auth.user.id,
  document_type: 'transcripts',
  send_date: '',
  type:'1'
}); 
const documentTypeText = computed(() => {
  return form.document_type === 'transcripts' ? 'เอกสารแสดงผลการเรียน' : 'แบบบันทึกการดำเนินงาน';
});
 
const formattedSendDate = computed(() => {
  return form.send_date ? moment(form.send_date).locale('th').format('dddd, DD MMMM YYYY') : 'ยังไม่ได้เลือกวันที่';
}); 
const previewMessage = computed(() => {
  return `
📢 **แจ้งเตือนนักศึกษา**

**ประเภทเอกสาร:** ${documentTypeText.value}

⏳ **วันที่กำหนดส่ง:** ${formattedSendDate.value}

📝 **กรุณาส่งเอกสารให้เสร็จสิ้นภายในวันที่กำหนด**

หากมีข้อสงสัยหรือต้องการความช่วยเหลือเพิ่มเติม โปรดติดต่อเจ้าหน้าที่เกี่ยวข้อง

ขอบคุณครับ/ค่ะ
  `;
});

const handleSubmit = () => {
  if (!form.send_date || !form.document_type) {
    Swal.fire({
      icon: 'error',
      title: 'เกิดข้อผิดพลาด',
      text: 'ไม่พบข้อมูลที่จำเป็น กรุณากรอกข้อมูลให้ครบถ้วน!',
      confirmButtonText: 'ตกลง',
      confirmButtonColor: '#dc3545',
    });
    return;
  }

  const message = previewMessage.value;

  form.post(route('publish_requests.store'), {
    onSuccess: () => {
      sendLineNotification(props.userLineNotify.map(notify => notify.user_id), message);
      form.reset();
      Swal.fire({
        icon: 'success',
        title: 'ส่งข้อความสำเร็จ!',
        text: 'ข้อความของคุณถูกส่งเรียบร้อยแล้ว',
        confirmButtonText: 'ตกลง',
        confirmButtonColor: '#28a745',
      });
    },
    onError: () => {
      Swal.fire({
        icon: 'error',
        title: 'เกิดข้อผิดพลาดในการส่งข้อความ',
        text: 'ไม่สามารถส่งข้อความได้ กรุณาลองใหม่อีกครั้ง!',
        confirmButtonText: 'ตกลง',
        confirmButtonColor: '#dc3545',
      });
    },
  });
};


// ฟังก์ชันส่ง Line Notify
const sendLineNotification = (userIds, message) => {
  const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

  if (!token) {
    console.error('CSRF token not found');
    return;
  }

  userIds.forEach(userId => {
    fetch(route('line.notify', { userId }), {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': token,
      },
      body: JSON.stringify({ message }),
    })
      .then(response => response.json())
      .then(data => {
        console.log('Line Notify Response for userId', userId, ':', data);
      })
      .catch(error => {
        console.error('Error for userId', userId, ':', error);
      });
  });
};
</script>
