<template>
    <Head title="Edit Scholarship Application" />
    <div class="max-w-7xl mx-auto mb-3  py-4 mt-20 px-10">

      <div class="bg-white rounded-xl shadow-md p-6 sm:p-8 lg:p-10 mx-4 md:mx-6 lg:mx-8">
        <form @submit.prevent="handleSubmit" enctype="multipart/form-data">
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6 w-full">
            <div>
              <InputLabel for="act_check" value="ผลการตรวจ" />
              <select id="act_check" v-model="form.act_check"
                      class="mt-1 block w-full bg-white rounded-xl border-gray-300" required>
                <option value="0">ผ่าน</option>
                <option value="1">ไม่ผ่าน</option>
                <option value="2">รอการตรวจ</option>
              </select>
              <InputError class="mt-2" :message="form.errors.act_check" />
            </div>
            <div class="mb-4">
              <InputLabel for="check_date" value="ข้อเสนอแนะ" />
              <TextInput id="check_date" type="date" class="mt-1 block w-full" v-model="form.check_date"
                         required />
              <InputError class="mt-2" :message="form.errors.check_date" />
            </div>
          </div>
          <div class="flex items-center space-x-4">
            <PrimaryButton :href="route('applications.index')" class="custom-button-danger">กลับ</PrimaryButton>
            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                           class="custom-button-success">
                <span v-if="form.processing">กำลังโหลด...</span>
                <span v-else> บันทึก</span>

            </PrimaryButton>
          </div>
        </form>
      </div>
    </div>
  </template>
  
  <script setup>
  import { usePage, useForm } from '@inertiajs/vue3';
  import { Head } from '@inertiajs/vue3'; 
  import InputLabel from '@/Components/InputLabel.vue';
  import TextInput from '@/Components/TextInput.vue';
  import PrimaryButton from '@/Components/PrimaryButton.vue';
  import InputError from '@/Components/InputError.vue';  
  const { props } = usePage();
  const initialForm = props.activities || {};
  
  // Get today's date in YYYY-MM-DD format
  const today = new Date().toISOString().split('T')[0];
  
  const form = useForm({
    act_check: initialForm.act_check || '',
    check_date: initialForm.check_date || today, // Set default value to today's date
    officer_id: props.auth.user.id,
  });
  
  const handleSubmit = () => {
    form.put(route('activity_registrations.update', initialForm.id));
  };
  </script>
