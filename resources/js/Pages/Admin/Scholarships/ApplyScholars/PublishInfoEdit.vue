<template>
  <div class="w-full max-w-7xl p-6 mx-auto">
    <form @submit.prevent="handleSubmit" class="flex flex-col">
      <!-- Scholarship Selection -->
      <div class="mt-4">
        <InputLabel for="docType" class="block text-gray-700 text-md font-semibold mb-2">
          ทุนการศึกษาทั้งหมด:
        </InputLabel>
        <select v-model="form.scholar_id" id="docType"
          class="mt-1 block w-full p-2 border border-gray-300 rounded-md text-md">
          <option v-for="scholar in scholarships" :key="scholar.id" :value="scholar.id">
            {{ scholar.scholar_name }}
          </option>
          <option v-if="scholarships.length === 0" disabled>ไม่มีข้อมูลทุนการศึกษา</option>
        </select>
      </div>

      <!-- Document Request Preview -->
      <pre>{{ documentRequest }}</pre>

      <!-- Interview Date -->
      <div class="mt-4">
        <InputLabel for="sendDate" class="block text-gray-700 text-md font-semibold mb-2">
          วันที่สัมภาษณ์:
        </InputLabel>
        <TextInput v-model="form.send_date" id="sendDate" type="datetime-local"
          class="mt-1 block w-full p-2 border border-gray-300 rounded-md text-md">
        </TextInput>
      </div>

      <!-- Interview Location -->
      <div class="mt-4">
        <InputLabel for="location" class="block text-gray-700 text-md font-semibold mb-2">
          สถานที่สัมภาษณ์:
        </InputLabel>
        <TextInput v-model="form.location" id="location" type="text"
          class="mt-1 block w-full p-2 border border-gray-300 rounded-md text-md">
        </TextInput>
      </div>

      <!-- Preview Message -->
      <div class="mt-6">
        <InputLabel class="block text-gray-700 text-md font-semibold mb-2">ข้อความตัวอย่าง:</InputLabel>
        <textarea readonly
          class="mt-1 block w-full p-4 border border-gray-300 rounded-md text-md bg-gray-100 resize-none" rows="10">
          {{ previewMessage }}
        </textarea>
      </div>

      <!-- Submit & Cancel Buttons -->
      <div class="mt-6 flex justify-end space-x-4">
        <button type="submit" class="custom-button-success">ส่งข้อความ</button>
        <button type="button" @click="handleCancel" class="custom-button-danger">ยกเลิก</button>
      </div>
    </form>
  </div>
</template>
<script setup>
import { ref, computed } from 'vue';
import moment from 'moment';
import 'moment/locale/th';
import { useForm, usePage } from '@inertiajs/vue3';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Swal from 'sweetalert2';

const props = defineProps({
  scholarships: {
    type: Array,
    default: () => []
  },
  documentRequest: {
    type: Object,
    default: () => ({})
  },
  userLineNotify: {
    type: Array,
    default: () => []
  }
});

console.log(props)
const form = useForm({
  scholar_id: props.scholarships.length > 0 ? props.scholarships[0].id : '',
  send_date: '',
  type: 0,
  location: '',
}); 
const scholarshipText = computed(() => {
  const selectedScholarship = props.scholarships.find(s => s.id === form.scholar_id);
  return selectedScholarship ? selectedScholarship.scholar_name : 'ทุนการศึกษา';
});

// ฟอร์แมตวันที่ที่เลือก
const formattedSendDate = computed(() => {
  return form.send_date ? moment(form.send_date).format('DD MMMM YYYY, HH:mm') : 'ยังไม่ได้เลือกวันที่และเวลา';
});

// สร้างข้อความที่จะแสดง
const previewMessage = computed(() => {
  return `
📢 **แจ้งแก้ไขกำหนดการสัมภาษณ์นักศึกษาทุน**
**ทุนการศึกษา:** ${scholarshipText.value}
⏳ **วันที่สัมภาษณ์ใหม่:** ${formattedSendDate.value} น.
📍 **สถานที่สัมภาษณ์:** ${form.location || 'ยังไม่ได้กำหนดสถานที่'}
📝 **กรุณามาในเวลาที่กำหนด**
หากมีข้อสงสัย โปรดติดต่อเจ้าหน้าที่
  `;
});

// ฟังก์ชันสำหรับการส่งข้อมูล
const handleSubmit = () => {
  const message = previewMessage.value;
  if (!form.send_date || !form.scholar_id || !form.location) {
    Swal.fire({
      title: 'เกิดข้อผิดพลาด!',
      text: 'กรุณากรอกข้อมูลให้ครบถ้วน (วันที่ส่ง, นักศึกษาที่เกี่ยวข้อง, และสถานที่)',
      icon: 'error',
      confirmButtonText: 'ตกลง',
      confirmButtonColor: '#e74c3c',
    });
    return;
  }

  form.post(route('publish_requests.createInfo'), {
    onSuccess: (response) => {
      const NotiInfo = response.props.userLineNotify;
      sendLineNotification(NotiInfo.map(notify => notify.user_id), message); // ส่งการแจ้งเตือนผ่าน Line
      form.reset();
      Swal.fire({
        title: 'สำเร็จ!',
        text: 'การส่งข้อความสำเร็จ!',
        icon: 'success',
        confirmButtonText: 'ตกลง',
        confirmButtonColor: '#28a745',
      }).then(() => {
        window.location.href = route('scholarship_applications.interview');
      });
    },
    onError: (errors) => {
      console.error('❌ เกิดข้อผิดพลาดในการส่งข้อมูล:', errors);
      Swal.fire({
        title: 'เกิดข้อผิดพลาด!',
        text: 'เกิดข้อผิดพลาดในการส่งข้อความ',
        icon: 'error',
        confirmButtonText: 'ตกลง',
        confirmButtonColor: '#e74c3c',
      });
    },
  });
};

// ฟังก์ชันสำหรับการส่งการแจ้งเตือนผ่าน Line
const sendLineNotification = (userIds, message) => {
  const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
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

// ฟังก์ชันยกเลิกการดำเนินการ
const handleCancel = () => {
  Swal.fire({
    title: 'คุณแน่ใจหรือไม่?',
    text: 'คุณต้องการยกเลิกการดำเนินการนี้?',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'ยืนยัน',
    cancelButtonText: 'ยกเลิก',
    confirmButtonColor: '#28a745',
    cancelButtonColor: '#FF5733',
  }).then((result) => {
    if (result.isConfirmed) {
      window.location.href = route('scholarship_applications.interview');
    }
  });
};
</script>
