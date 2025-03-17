<template>
  <AuthenticatedLayout>
    <Head title="Create Activity" />

    <div class="max-w-7xl mx-auto py-4 mt-20 px-10">
      <div class="px-4 text-white dark:text-gray-100 flex justify-between items-center">
        <div class="py-2 text-gray-800 rounded-t-lg text-xl font-bold">
          แก้ไขข้อมูลกิจกรรม
        </div>
        <div class="flex-1 mr-3 ml-3">
          <hr class="border-t border-gray-300" />
        </div>
        <div class="flex justify-end space-x-4">
          <Link :href="route('activity_saves.index')" class="custom-button-danger">
            กลับ
          </Link>
          <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing" @click="handleSubmit" class="custom-button-success">
            บันทึก
          </PrimaryButton>
        </div>
      </div>
    </div>
    <!-- Section for input fields -->
    <div class="max-w-7xl mx-auto">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <form @submit.prevent="handleSubmit" enctype="multipart/form-data">
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6 w-full">
            <!-- คำนำหน้า -->
            <div class="mb-4">
              <InputLabel for="title" value="คำนำหน้า" />
              <select id="title" v-model="form.title" class="mt-1 block w-full bg-white rounded-xl border-gray-300" required>
                <option value="0">นาย (Mr.)</option>
                <option value="1">นาง (Mrs.)</option>
                <option value="2">นางสาว (Miss)</option>
              </select>
            </div>

            <!-- ชื่อ -->
            <div class="mb-4">
              <InputLabel for="fname" value="ชื่อ" />
              <TextInput id="fname" readonly type="text" class="mt-1 block w-full" v-model="form.fname" required autocomplete="fname" />
            </div>

            <!-- นามสกุล -->
            <div class="mb-4">
              <InputLabel for="lname" value="นามสกุล" />
              <TextInput id="lname" readonly type="text" class="mt-1 block w-full" v-model="form.lname" required autocomplete="lname" />
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-6 w-full mt-3">
            <div class="mb-4">
              <InputLabel for="activity_name" value="ชื่อกิจกรรม" />
              <TextInput id="activity_name" type="text" class="mt-1 block w-full" v-model="form.activity_name" required autocomplete="activity_name" />
            </div>

            <!-- วันที่ -->
            <div class="mb-4">
              <InputLabel for="date" value="วันที่" />
              <TextInput id="date" type="date" class="mt-1 block w-full" v-model="form.date" required />
              <p v-if="form.errors.date" class="text-red-600 text-sm mt-1">{{ 'กรุณากรอกวันที่' }}</p>
            </div>

            <div class="mb-4">
              <InputLabel for="years" value="ปีการศึกษา ( เช่น 2564 , 2565 )" />
              <TextInput id="years" type="text" class="mt-1 block w-full" v-model="form.years" required />
              <p v-if="form.errors.years" class="text-red-600 text-sm mt-1">{{ 'กรุณากรอกปีการศึกษา' }}</p>
            </div>

            <!-- อัพโหลดไฟล์ -->
            <div class="mb-4">
              <InputLabel for="file" value="อัพโหลดไฟล์" />
              <TextInput id="file" type="file" class="mt-1 block w-full border-gray-300 py-1 px-2 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" @change="handleFileChange($event, 'file')" />
              <p v-if="form.errors.file" class="text-red-600 text-sm mt-1">{{ 'กรุณาอัพโหลดไฟล์' }}</p>
            </div>
          </div>

          <!-- คำอธิบายกิจกรรม -->
          <div class="mb-4">
            <InputLabel for="description" value="คำอธิบายกิจกรรม" />
            <textarea id="description" class="mt-1 block w-full border-gray-300 py-1 px-2 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" v-model="form.description" rows="4"></textarea>
            <p v-if="form.errors.description" class="text-red-600 text-sm mt-1">{{ 'กรุณากรอกคำอธิบายกิจกรรม' }}</p>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-5 gap-6 w-full">
            <!-- หน่วยงาน -->
            <div class="mb-4">
              <InputLabel for="institution" value="หน่วยงาน" />
              <TextInput id="institution" type="text" class="mt-1 block w-full" v-model="form.institution" />
              <p v-if="form.errors.institution" class="text-red-600 text-sm mt-1">{{ 'กรุณากรอกหน่วยงาน' }}</p>
            </div>

            <!-- เริ่มกิจกรรม -->
            <div class="mb-4">
              <InputLabel for="launch" value="เริ่มกิจกรรม" />
              <TextInput id="launch" type="time" class="mt-1 block w-full" v-model="form.launch" />
              <p v-if="form.errors.launch" class="text-red-600 text-sm mt-1">{{ 'กรุณากรอกเวลาเริ่มกิจกรรม' }}</p>
            </div>

            <!-- จบกิจกรรม -->
            <div class="mb-4">
              <InputLabel for="end" value="จบกิจกรรม" />
              <TextInput id="end" type="time" class="mt-1 block w-full" v-model="form.end" />
              <p v-if="form.errors.end" class="text-red-600 text-sm mt-1">{{ 'กรุณากรอกเวลาเสร็จสิ้นกิจกรรม' }}</p>
            </div>

            <!-- สถานที่จัดกิจกรรม -->
            <div class="mb-4">
              <InputLabel for="location" value="สถานที่จัดกิจกรรม" />
              <TextInput id="location" type="text" class="mt-1 block w-full" v-model="form.location" />
              <p v-if="form.errors.location" class="text-red-600 text-sm mt-1">{{ 'กรุณากรอกสถานที่จัดกิจกรรม' }}</p>
            </div>
          </div>

        </form>
          <div>
  <p class="font-semibold">ไฟล์ปัจจุบัน:</p>
  <img v-if="save.file" :src="file" alt="Current File" class="max-w-md h-auto object-contain" />
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
import PrimaryButton from '@/Components/PrimaryButton.vue'; 
const props = defineProps({
  activities: Array,
  auth: Object,
  save: Object,
  activity_registers: Array,
});
const file = ref(`/storage/files/${props.save.file}`);

const form = useForm({
  title: props.auth.user.title,
  fname: props.auth.user.fname,
  lname: props.auth.user.lname,
  date: props.save.date || '',
  user_id: props.auth.user.id,
  description: props.save.description || '',
  activity_name: props.save.activity_name || '',
  file: null,
  institution: props.save.institution || '',
  launch: '',
  end: '',
  location: props.save.location || '',
  result: '4',
  suggestions: '',
  years: props.save.years || '',
});

 

const handleFileChange = (event, field) => {
  const file = event.target.files[0];
  form[field] = file;

  if (field === 'file') {
    file.value = URL.createObjectURL(file);
  } 
};
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
  if (form.file) {
    formData.append('file', form.file);
  } 
  form.post(route('activity_saves.update', { id: props.save.id }), {
    data: formData,
    headers: {
      'Content-Type': 'multipart/form-data'
    }
  }) 
};

 
</script>
 