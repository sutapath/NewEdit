 <template>
  <Head title="Document Details" />

  <AuthenticatedLayout>
    <div class="max-w-7xl mx-auto py-4 mt-20 px-10">
      <div class="px-4 text-white dark:text-gray-100 flex justify-between items-center">
        <div class="py-2 text-gray-800 rounded-t-lg text-xl font-bold">
          รายละเอียดเอกสาร
        </div>
        <div class="flex-1 mr-3 ml-3">
          <hr class="border-t border-gray-300" />
        </div>
        <div class="flex justify-end space-x-4">
          <button @click="showCheck = !showCheck" class="custom-button-warning" v-if="hasRole('admin') || hasRole('officer')">ตรวจสอบ</button>
          <button @click="enableEdit" v-if="hasRole('scholar') || hasRole('intlscholar')" class="custom-button-warning">แก้ไข</button>
          <Link :href="route('docs_sends.index')" class="custom-button-danger">กลับ</Link>
        </div>
      </div>
    </div>

    <div class="max-w-7xl mx-auto mb-3 px-10">
      <div class="bg-white rounded-xl shadow-md p-6 mt-6">
        <Check v-if="showCheck" />

        <form @submit.prevent="handleSubmit" enctype="multipart/form-data">
          <div class="grid grid-cols-1 md:grid-cols-5 gap-6 w-full">
            <div>
              <InputLabel for="title" value="คำนำหน้า" />
              <select id="title" v-model="form.title" class="mt-1 block w-full bg-white rounded-xl border-gray-300" :disabled="!isEditable">
                <option value="0">นาย (Mr.)</option>
                <option value="1">นาง (Mrs.)</option>
                <option value="2">นางสาว (Miss)</option>
              </select>
            </div>
            <div>
              <InputLabel for="fname" value="ชื่อ" />
              <TextInput id="fname" :readonly="!isEditable" type="text" class="mt-1 block w-full" v-model="form.fname" required />
            </div>
            <div>
              <InputLabel for="lname" value="นามสกุล" />
              <TextInput id="lname" :readonly="!isEditable" type="text" class="mt-1 block w-full" v-model="form.lname" required />
            </div>
            <div>
              <InputLabel for="date" value="วันที่" />
              <TextInput id="date" type="date" :readonly="!isEditable" class="mt-1 block w-full" v-model="form.date" required />
              <p v-if="errors.date" class="text-red-500 text-sm">{{ errors.date }}</p>
            </div>
            <div>
              <InputLabel for="years" value="ปีการศึกษา" />
              <TextInput id="years" type="text" :readonly="!isEditable" class="mt-1 block w-full" v-model="form.years" required />
              <p v-if="errors.years" class="text-red-500 text-sm">{{ errors.years }}</p>
            </div>
          </div>

          <div v-if="hasRole('scholar') || hasRole('intlscholar')" class="grid grid-cols-1 md:grid-cols-3 gap-6 w-full mt-3">
            <div v-if="!docSend.transcripts">
              <InputLabel for="operat_docs" value="แบบบันทึกการดำเนินงาน" />
              <input id="operat_docs" type="file" class="mt-1 block w-full border-gray-300 py-1 px-2 rounded-md" @change="handleFileChange($event, 'operat_docs')" :disabled="!isEditable" />
              <p v-if="errors.operat_docs" class="text-red-500 text-sm">{{ errors.operat_docs }}</p>
            </div>

            <div v-if="!docSend.operat_docs">
              <InputLabel for="transcripts" value="เอกสารแสดงผลการเรียน" />
              <input id="transcripts" type="file" class="mt-1 block w-full border-gray-300 py-1 px-2 rounded-md" @change="handleFileChange($event, 'transcripts')" :disabled="!isEditable" />
              <p v-if="errors.transcripts" class="text-red-500 text-sm">{{ errors.transcripts }}</p>
            </div>
          </div>

          <div class="mt-6 flex items-center" v-if="showSaveButtons">
            <PrimaryButton @click="handleSubmit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing || !isEditable" class="custom-button-success me-4">
              บันทึก
            </PrimaryButton>
            <Link :href="route('docs_sends.index')" class="custom-button-danger ml-3">กลับ</Link>
          </div>
        </form>

        <div class="mt-6">
          <iframe v-if="docSend.operat_docs" :src="operatDocsUrl" class="w-full h-96 border-0 mt-3"></iframe>
          <iframe v-if="docSend.transcripts" :src="transcriptsUrl" class="w-full h-96 border-0 mt-3"></iframe>
          <p v-else class="text-gray-500">ไม่มีไฟล์แนบ</p>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import Check from "@/Pages/Admin/DocsSends/Check.vue";
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { usePermission } from '@/composables/permissions';

const { hasRole } = usePermission();

const props = defineProps({
  docSend: Object,
});

const form = useForm({
  title: props.docSend.title,
  fname: props.docSend.fname,
  lname: props.docSend.lname,
  date: props.docSend.date,
  years: props.docSend.years,
  operat_docs: null,
  transcripts: null,
  result: 3,
  suggestions: '',
  check_date: '',
});

// Variable to hold errors
const errors = ref({});

const showCheck = ref(false);
const isEditable = ref(false);
const operatDocsUrl = ref(`/storage/files/${props.docSend.operat_docs}`);
const transcriptsUrl = ref(`/storage/files/${props.docSend.transcripts}`);

const handleFileChange = (event, field) => {
  const file = event.target.files[0];
  form[field] = file;

  if (field === 'operat_docs') {
    operatDocsUrl.value = URL.createObjectURL(file);
  } else if (field === 'transcripts') {
    transcriptsUrl.value = URL.createObjectURL(file);
  }
};

const handleSubmit = () => {
  errors.value = {}; // Reset errors before submission
 
  if (!form.date) errors.value.date = 'กรุณากรอกวันที่';
  if (!form.years) errors.value.years = 'กรุณากรอกปีการศึกษา';

  // If there are errors, don't submit the form
  if (Object.keys(errors.value).length > 0) return;

  form.put(route('docs_sends.update', props.docSend.id), {
    headers: {
      'Content-Type': 'multipart/form-data'
    }
  });
};

const showSaveButtons = ref(false);

const enableEdit = () => {
  isEditable.value = true;
  showSaveButtons.value = true;
};
</script>
