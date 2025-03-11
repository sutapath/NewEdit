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

    <div class="max-w-7xl mx-auto sm:px-4 lg:px-8 bg-white md:rounded-xl mb-6">
      <div class="flex justify-end">
        <div class="inline-flex items-center py-2 px-4 bg-gray-300 rounded">
          <label for="docType" class="font-semibold mr-2">เลือกประเภทเอกสาร</label>
          <select id="docType" v-model="selectedDocType" class="custom-dropdown w-48">
            <option value="" disabled>เลือกประเภทเอกสาร</option>
            <option value="transcript">เอกสารแสดงผลการเรียน</option>
            <option value="activityReport">แบบบันทึกการดำเนินงาน</option>
          </select>
        </div>
      </div>
    </div>

    <div v-if="selectedDocType" class="max-w-7xl mx-auto mb-10 bg-white">
      <div class="bg-white rounded-xl shadow-md p-6 sm:p-8 lg:p-10 mx-4 md:mx-6 lg:mx-8">
        <form @submit.prevent="handleSubmit">
          <div class="grid grid-cols-1 md:grid-cols-4 gap-6 w-full">
            <div>
              <InputLabel for="title" value="คำนำหน้า" />
              <select id="title" v-model="form.title" class="mt-1 block w-full bg-white rounded-xl border-gray-300" disabled>
                <option value="0">นาย (Mr.)</option>
                <option value="1">นาง (Mrs.)</option>
                <option value="2">นางสาว (Miss)</option>
              </select>
              <p v-if="errors.title" class="text-red-500 text-sm">{{ errors.title }}</p>
            </div>
            <div>
              <InputLabel for="fname" value="ชื่อ" />
              <TextInput id="fname" readonly type="text" class="mt-1 block w-full" v-model="form.fname" required />
              <p v-if="errors.fname" class="text-red-500 text-sm">{{ errors.fname }}</p>
            </div>
            <div>
              <InputLabel for="lname" value="นามสกุล" />
              <TextInput id="lname" readonly type="text" class="mt-1 block w-full" v-model="form.lname" required />
              <p v-if="errors.lname" class="text-red-500 text-sm">{{ errors.lname }}</p>
            </div>
          <div>
  <InputLabel for="date" value="วันที่" />
  <TextInput id="date" type="date" class="mt-1 block w-full" v-model="form.date" required :disabled="true" />
  <p v-if="errors.date" class="text-red-500 text-sm">{{ errors.date }}</p>
</div>

          </div>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-6 w-full mt-3">
            <div>
              <InputLabel for="years" value="ปีการศึกษา ( เช่น 2564 , 2565 )" />
              <TextInput id="years" type="text" class="mt-1 block w-full" v-model="form.years" required />
              <p v-if="errors.years" class="text-red-500 text-sm">{{ errors.years }}</p>
            </div>

            <div v-if="selectedDocType === 'activityReport'" class="mb-4">
              <InputLabel for="operat_docs" value="แบบบันทึกการดำเนินงาน" />
              <TextInput id="operat_docs" type="file" class="mt-1 block w-full border-gray-300 py-1 px-2 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" @change="handleFileChange($event, 'operat_docs')" />
              <p v-if="errors.operat_docs" class="text-red-500 text-sm">{{ errors.operat_docs }}</p>
            </div>

            <div v-if="selectedDocType === 'transcript'" class="mb-4">
              <InputLabel for="transcripts" value="เอกสารแสดงผลการเรียน" />
              <TextInput id="transcripts" type="file" class="mt-1 block w-full border-gray-300 py-1 px-2 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" @change="handleFileChange($event, 'transcripts')" />
              <p v-if="errors.transcripts" class="text-red-500 text-sm">{{ errors.transcripts }}</p>
            </div>
          </div>

          <div class="mt-6 d-flex align-items-center">
            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing" class="custom-button-success me-4">
              <span v-if="form.processing">กำลังโหลด...</span>
              <span v-else> บันทึก</span>
            </PrimaryButton>
            <Link :href="route('docs_sends.index')" class="custom-button-danger ml-3">กลับ</Link>
          </div>
        </form>
      </div>
    </div>
  </AuthenticatedLayout>

  <Modal :show="showConfirmDeleteDocsModal" @close="closeModal">
    <div class="p-6">
      <h2 class="text-lg font-semibold text-slate-800">Are you sure to delete this Docs?</h2>
      <div class="mt-6 flex space-x-4">
        <DangerButton @click="deleteDocs">ลบ</DangerButton>
        <SecondaryButton @click="closeModal">ยกเลิก</SecondaryButton>
      </div>
    </div>
  </Modal>
</template>
<script setup>
import { ref, defineProps } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Modal from '@/Components/Modal.vue';
import DangerButton from '@/Components/DangerButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Swal from 'sweetalert2';


// ฟังก์ชันสำหรับดึงวันที่ปัจจุบันในรูปแบบ YYYY-MM-DD
const getCurrentDate = () => {
  const today = new Date();
  const year = today.getFullYear();
  const month = String(today.getMonth() + 1).padStart(2, '0');
  const day = String(today.getDate()).padStart(2, '0');
  return `${year}-${month}-${day}`;
};

const props = defineProps({
  auth: Object,
  docsSendss: Array
});

const selectedDocType = ref('');
const errors = ref({});

// ตั้งค่า form ให้วันที่เริ่มต้นเป็นวันปัจจุบัน
const form = useForm({
  user_id: props.auth.user.id,
  title: props.auth.user.title,
  fname: props.auth.user.fname,
  lname: props.auth.user.lname,
  operat_docs: null,
  transcripts: null,
  date: getCurrentDate(), // เริ่มต้นวันที่เป็นวันปัจจุบัน
  years: '',
  result: 2
});

const handleSubmit = () => {
  errors.value = {};

  // Validate fields
  if (!form.title) errors.value.title = 'กรุณาเลือกคำนำหน้า';
  if (!form.fname) errors.value.fname = 'กรุณากรอกชื่อ';
  if (!form.lname) errors.value.lname = 'กรุณากรอกนามสกุล';
  if (!form.date) errors.value.date = 'กรุณากรอกวันที่';
  if (!form.years) errors.value.years = 'กรุณากรอกปีการศึกษา';

  if (selectedDocType.value === 'activityReport' && !form.operat_docs) {
    errors.value.operat_docs = 'กรุณาอัปโหลดแบบบันทึกการดำเนินงาน';
  }
  if (selectedDocType.value === 'transcript' && !form.transcripts) {
    errors.value.transcripts = 'กรุณาอัปโหลดเอกสารแสดงผลการเรียน';
  }

  // If there are errors, stop the form submission
  if (Object.keys(errors.value).length > 0) return;

  form.post(route('docs_sends.store'), {
    onSuccess: () => {
      Swal.fire({
        icon: 'success',
        title: 'เอกสารถูกบันทึกแล้ว',
        text: 'เอกสารของคุณถูกบันทึกสำเร็จ',
        timer: 1500,
        showConfirmButton: false,
      });
    },
    onError: () => {
      Swal.fire({
        icon: 'error',
        title: 'เกิดข้อผิดพลาด',
        text: 'ไม่สามารถบันทึกเอกสารได้ โปรดลองใหม่อีกครั้ง',
        showConfirmButton: true,
      });
    }
  });
};


const handleFileChange = (event, field) => {
  form[field] = event.target.files[0];
};

const showConfirmDeleteDocsModal = ref(false);
const DocsIdToDelete = ref(null);

const confirmDeleteDocs = (id) => {
  DocsIdToDelete.value = id;
  showConfirmDeleteDocsModal.value = true;
};

const closeModal = () => {
  showConfirmDeleteDocsModal.value = false;
};
</script>

<style scoped>
.custom-dropdown {
  padding: 0.5rem;
  border-radius: 0.25rem;
  border: 1px solid #d1d5db;
  background-color: #ffffff;
  cursor: pointer;
  font-size: 1rem;
}
 
</style>
