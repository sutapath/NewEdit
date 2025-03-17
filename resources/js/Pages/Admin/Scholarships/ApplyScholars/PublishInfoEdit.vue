<template>

  <Head title="Create Document" />

  <AuthenticatedLayout class="mtb">
    <div class="max-w-7xl mx-auto py-4 mt-20 px-10">
      <div class="px-4 text-white dark:text-gray-100 flex justify-between items-center">
        <div class="py-2 text-gray-800 rounded-t-lg text-xl font-bold">
          เพิ่มเอกสาร
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
 
        <div class="mt-4">
          <InputLabel for="docType" class="block text-gray-700 text-md font-semibold mb-2">
            ทุนการศึกษาทั้งหมด:
          </InputLabel>
          <select v-model="form.scholar_id" id="docType"
            class="mt-1 block w-full p-2 border border-gray-300 rounded-md text-md bg-gray-100 cursor-not-allowed"
            disabled>
            <option v-for="scholar in scholarships" :key="scholar.id" :value="scholar.id">
              {{ scholar.scholar_name }}
            </option>
          </select>

        </div> 
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
        <div class="mt-6 flex justify-end space-x-4">
          <button type="submit" class="custom-button-success">ส่งข้อความ</button>
          <button type="button" @click="handleCancel" class="custom-button-danger">ยกเลิก</button>
        </div>
      </form>
    </div>
  </AuthenticatedLayout>

</template>
<script setup>
import { ref, computed } from 'vue';
import moment from 'moment';
import 'moment/locale/th';
import { useForm, usePage } from '@inertiajs/vue3';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Swal from 'sweetalert2';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

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
  id: props.documentRequest.id || '',
  scholar_id: props.documentRequest.scholar_id || '',
  send_date: props.documentRequest.send_date || '',
  type: props.documentRequest.type || 0,
  location: props.documentRequest.location || '',
});
const scholarshipText = computed(() => {
  const selectedScholarship = props.scholarships.find(s => s.id === form.scholar_id);
  return selectedScholarship ? selectedScholarship.scholar_name : 'ทุนการศึกษา';
});  
const formattedSendDate = computed(() => {
  return form.send_date ? moment(form.send_date).format('DD/MM/YYYY') : 'ยังไม่ได้เลือกวันที่';
});

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

  form.post(route('publish_requests.store'), {
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
}
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
