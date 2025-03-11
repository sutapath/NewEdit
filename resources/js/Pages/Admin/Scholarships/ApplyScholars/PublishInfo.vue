<template>
  <div class="w-full max-w-7xl p-6 mx-auto">
    <form @submit.prevent="handleSubmit" class="flex flex-col">
      <!-- เลือกทุนการศึกษา -->
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

      <!-- วันที่กำหนดส่ง --> 
      <div class="mt-4">
        <InputLabel for="sendDate" class="block text-gray-700 text-md font-semibold mb-2">
          วันที่สัมภาษณ์:
        </InputLabel>
        <TextInput v-model="form.send_date" id="sendDate" type="datetime-local"
          class="mt-1 block w-full p-2 border border-gray-300 rounded-md text-md">
        </TextInput>
      </div>
      <!-- ข้อความตัวอย่าง -->
      <div class="mt-6">
        <InputLabel class="block text-gray-700 text-md font-semibold mb-2">ข้อความตัวอย่าง:</InputLabel>
        <textarea readonly
          class="mt-1 block w-full p-4 border border-gray-300 rounded-md text-md bg-gray-100 resize-none" rows="10">
          {{ previewMessage }}
        </textarea>
      </div>

      <!-- ปุ่มส่งและยกเลิก -->
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
moment.locale('th');

const { props } = usePage();
const scholarships = props.scholarships || [];
const userLineNotify = props.userLineNotify || [];
const form = useForm({
  scholar_id: scholarships.length > 0 ? scholarships[0].id : '',
  send_date: '',
});

const scholarshipText = computed(() => {
  const selectedScholarship = scholarships.find(s => s.id === form.scholar_id);
  return selectedScholarship ? selectedScholarship.scholar_name : 'ทุนการศึกษา';
});

const formattedSendDate = computed(() => {
  return form.send_date ? moment(form.send_date).format('DD MMMM YYYY, HH:mm') : 'ยังไม่ได้เลือกวันที่และเวลา';
});


const previewMessage = computed(() => {
  return `
📢 **แจ้งกำหนดการสัมภาษณ์นักศึกษาทุน**
  
**ทุนการศึกษา:** ${scholarshipText.value}
  
⏳ **วันที่สัมภาษณ์:** ${formattedSendDate.value} น.
  
📝 **กรุณามาในเวลาที่กำหนด**
  
หากมีข้อสงสัย โปรดติดต่อเจ้าหน้าที่
  `;
});

const handleSubmit = () => {
  const message = previewMessage.value;

  console.log("📤 ข้อมูลที่ส่งไปยัง publish_requests.createInfo:", form.data());

  form.post(route('publish_requests.createInfo'), {
    onSuccess: (response) => {
      const NotiInfo = response.props.userLineNotify;
      console.log('✅ การส่งข้อมูลสำเร็จ:', NotiInfo); // Log the response to verify structure
      if (!NotiInfo || NotiInfo.length === 0) {
        console.error('❌ ไม่มี Line Notify tokens ในข้อมูล');
        return;
      }
      sendLineNotification(NotiInfo.map(notify => notify.user_id), message);
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
      console.error('❌ เกิดข้อผิดพลาดในการส่งข้อมูล:', errors); // Log errors if any
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

const handleCancel = () => {
  Swal.fire({
    title: 'คุณแน่ใจหรือไม่?',
    text: 'คุณต้องการยกเลิกการดำเนินการนี้?',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'ยืนยัน',
    cancelButtonText: 'ยกเลิก',
    confirmButtonColor:  '#28a745',
    cancelButtonColor: '#FF5733',
  }).then((result) => {
    if (result.isConfirmed) {
      // หากยืนยันการยกเลิก
      Swal.fire({
        title: 'การยกเลิกสำเร็จ!',
        text: '',
        icon: 'success',
        confirmButtonText: 'ตกลง',
        confirmButtonColor: '#28a745'  // สีปุ่มยืนยัน (สีเขียว)
      });
      window.location.href = route('scholarship_applications.interview');
    } else {
      // หากยกเลิกการยกเลิก
      Swal.fire({
        title: 'การยกเลิกถูกยกเลิก',
        text: '',
        icon: 'info',
        confirmButtonText: 'ตกลง',
        confirmButtonColor: '#3498db'  // สีปุ่มยืนยัน (สีฟ้า)
      });
    }

  });
};

</script>
