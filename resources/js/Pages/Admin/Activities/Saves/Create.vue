<template>
  <AuthenticatedLayout>

    <Head title="Create Activity" />

    <div class="max-w-7xl mx-auto py-4 mt-20 px-10">
      <div class="px-4 text-white dark:text-gray-100 flex justify-between items-center">
        <div class="py-2 text-gray-800 rounded-t-lg text-xl font-bold">
          บันทึกกิจกรรม
        </div>
        <div class="flex-1 mr-3 ml-3">
          <hr class="border-t border-gray-300" />
        </div>
        <div class="flex justify-end space-x-4">
          <PrimaryButton :href="route('activity_saves.index')" class="custom-button-danger">
            กลับ
          </PrimaryButton>
          <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing" @click="handleSubmit"
            class="custom-button-success">
            บันทึก
          </PrimaryButton>
        </div>
      </div>
    </div>
    <div class="max-w-7xl mx-auto">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <form @submit.prevent="handleSubmit" enctype="multipart/form-data">
          <div class="grid grid-cols-1 md:grid-cols-4 gap-6 w-full">
            <div class="mb-4">
              <InputLabel for="title" value="คำนำหน้า" />
              <select id="title" v-model="form.title" class="mt-1 block w-full bg-white rounded-xl border-gray-300"
                required>
                <option value="0">นาย (Mr.)</option>
                <option value="1">นาง (Mrs.)</option>
                <option value="2">นางสาว (Miss)</option>
              </select>
            </div>

            <div class="mb-4">
              <InputLabel for="fname" value="ชื่อ" />
              <TextInput id="fname" readonly type="text" class="mt-1 block w-full" v-model="form.fname" required />
            </div>

            <div class="mb-4">
              <InputLabel for="lname" value="นามสกุล" />
              <TextInput id="lname" readonly type="text" class="mt-1 block w-full" v-model="form.lname" required />
            </div>
          </div>
          <pre></pre>
          <div class="grid grid-cols-1 md:grid-cols-4 gap-6 w-full mt-3">
            <div class="mb-4">
              <InputLabel for="activity_name" value="ชื่อกิจกรรม" />
              <TextInput id="activity_name" type="text" class="mt-1 block w-full" v-model="form.activity_name"
                required />
              <div v-if="form.errors.activity_name" class="text-red-500 text-sm mt-1">{{ 'กรุณากรอกชื่อกิจกรรม'}}</div>
            </div>

            <div class="mb-4">
              <InputLabel for="date" value="วันที่" />
              <TextInput id="date" type="date" class="mt-1 block w-full" v-model="form.date" required />
              <div v-if="form.errors.date" class="text-red-500 text-sm mt-1">{{ 'กรุณากรอกวันที่' }}</div>
            </div>

            <div class="mb-4">
              <InputLabel for="years" value="ปีการศึกษา ( เช่น 2564 , 2565 )" />
              <TextInput id="years" type="text" class="mt-1 block w-full" v-model="form.years" required />
              <div v-if="form.errors.years" class="text-red-500 text-sm mt-1">{{ 'กรุณากรอกปีการศึกษา' }}</div>
            </div>

            <div class="mb-4">
              <InputLabel for="file" value="อัพโหลดไฟล์" />
              <TextInput id="file" type="file"
                class="mt-1 block w-full border-gray-300 py-1 px-2 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                @change="handleFileChange($event, 'file')" />
              <div v-if="form.errors.file" class="text-red-500 text-sm mt-1">{{ 'กรุณาเพิ่มหลักฐานการเข้าร่วมกิจกรรม' }}
              </div>
            </div>
          </div>

          <div class="mb-4">
            <InputLabel for="description" value="คำอธิบายกิจกรรม" />
            <textarea id="description"
              class="mt-1 block w-full border-gray-300 py-1 px-2 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
              v-model="form.description" rows="4"></textarea>
            <div v-if="form.errors.description" class="text-red-500 text-sm mt-1">{{ 'กรุณากรอกคำอธิบาย' }}</div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-5 gap-6 w-full">
            <div class="mb-4">
              <InputLabel for="institution" value="หน่วยงาน" />
              <TextInput id="institution" type="text" class="mt-1 block w-full" v-model="form.institution" />
              <div v-if="form.errors.institution" class="text-red-500 text-sm mt-1">{{ 'กรุณาเพิ่มหน่อยงาน' }}</div>
            </div>

            <div class="mb-4">
              <InputLabel for="launch" value="เริ่มกิจกรรม" />
              <TextInput id="launch" type="time" class="mt-1 block w-full" v-model="form.launch" />
              <div v-if="form.errors.launch" class="text-red-500 text-sm mt-1">{{ 'กรุณาเพิ่มเวลาเริ่มงาน' }}</div>
            </div>

            <div class="mb-4">
              <InputLabel for="end" value="จบกิจกรรม" />
              <TextInput id="end" type="time" class="mt-1 block w-full" v-model="form.end" />
              <div v-if="form.errors.end" class="text-red-500 text-sm mt-1">{{ 'กรุณาเพิ่มเวลาจบงาน' }}</div>
            </div>

            <div class="mb-4">
              <InputLabel for="location" value="สถานที่จัดกิจกรรม" />
              <TextInput id="location" type="text" class="mt-1 block w-full" v-model="form.location" />
              <div v-if="form.errors.location" class="text-red-500 text-sm mt-1">{{ 'กรุณาเพิ่มสถานที่' }}</div>
            </div>
            <div class="mb-4">
              <InputLabel value="ชั่วโมง" />
              <p class="mt-1 block w-full p-2 border rounded bg-gray-100 text-gray-700">
                {{ props.allactivities.hours }}
              </p>
            </div>

          </div>
        </form>
        <!-- <pre>{{props.allactivities}}</pre> -->
      </div>
    </div>
  </AuthenticatedLayout>
</template>
<script setup>
import { useForm } from '@inertiajs/vue3';
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
  auth: Object,
  regis_id: String,
  activities:Object,
  allactivities:Object,
});

// Initialize the form
const form = useForm({ 
  title: props.auth.user.title,
  fname: props.auth.user.fname,
  lname: props.auth.user.lname,
  date: '',
  user_id: props.auth.user.id,
  description: '',
  activity_name: '',
  file: null,
  institution: '',
  launch: '',
  end: '',
  location: '',
  hours: props.allactivities.hours,
  years: '',
  result: 4,
  regis_id: props.regis_id,
});

// Handle file input
const handleFileChange = (event) => {
  form.file = event.target.files[0]; // Get the first file from the input
};

// Handle form submission
const handleSubmit = () => {
  const formData = new FormData();

  // Append each field from the form object to FormData
  formData.append('title', form.title);
  formData.append('fname', form.fname);
  formData.append('lname', form.lname);
  formData.append('date', form.date);
  formData.append('years', form.years);
  formData.append('user_id', form.user_id);
  formData.append('description', form.description);
  formData.append('activity_name', form.activity_name);
  formData.append('file', form.file); // Upload file
  formData.append('institution', form.institution);
  formData.append('launch', form.launch);
  formData.append('end', form.end);
  formData.append('location', form.location);
  formData.append('regis_id', form.regis_id);

  // Post the FormData
  form.post(route('activity_saves.store'), {
    data: formData,
    onSuccess: () => {
      form.reset(); // Reset form on success
    },
    onError: (errors) => {
      console.log('เกิดข้อผิดพลาด:', errors);
    },
  });
};
</script>
