<template>
  <AuthenticatedLayout>
    <Head title="Create Activity" /> 
  
    <div class="max-w-7xl mx-auto py-4 mt-20 px-10">
      <div class="px-4 text-white dark:text-gray-100 flex justify-between items-center">
        <div class="py-2 text-gray-800 rounded-t-lg text-xl font-bold">
          สร้างข้อมูลกิจกรรมใหม่
        </div>
        <div class="flex-1 mr-3 ml-3">
          <hr class="border-t border-gray-300" />
        </div>
        <div class="flex justify-end space-x-4">
          <Link :href="route('activities.index')" class="custom-button-danger">กลับ</Link>
          <PrimaryButton :class="{ 'opacity-25': form.processing }" 
              :disabled="form.processing" 
              @click="handleSubmit" 
              class="custom-button-success">
              <span v-if="form.processing">กำลังโหลด...</span>
              <span v-else> บันทึก</span>
          </PrimaryButton>
        </div>
      </div>
    </div>
    <div class="max-w-7xl mx-auto px-5 mt-5">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <form @submit.prevent="handleSubmit">
          <div class="grid grid-cols-1 md:grid-cols-4 gap-6 w-full mb-5">
            <div class="mb-4">
              <InputLabel for="activity_name" value="ชื่อกิจกรรม" />
              <TextInput 
                id="activity_name" 
                type="text" 
                class="mt-1 block w-full" 
                v-model="form.activity_name" 
                required 
                autofocus 
              />
              <div v-if="form.errors.activity_name" class="text-red-500 text-sm mt-1">
                {{  'กรุณากรอกชื่อกิจกรรม' }}
              </div>
            </div>

            <div class="mb-4">
              <InputLabel for="start_date" value="วันที่จัดกิจกรรม" />
              <TextInput 
                id="start_date" 
                type="date" 
                class="mt-1 block w-full" 
                v-model="form.start_date" 
                required 
              />
              <div v-if="form.errors.start_date" class="text-red-500 text-sm mt-1">
                {{  'กรุณากรอกวันที่จัดกิจกรรม' }}
              </div>
            </div>

            <div class="mb-4">
              <InputLabel for="location" value="สถานที่จัดกิจกรรม" />
              <TextInput 
                id="location" 
                type="text" 
                class="mt-1 block w-full" 
                v-model="form.location" 
                required 
              />
              <div v-if="form.errors.location" class="text-red-500 text-sm mt-1">
                {{  'กรุณากรอกสถานที่จัดกิจกรรม' }}
              </div>
            </div>

            <div class="mb-4">
              <InputLabel for="organizer" value="ผู้จัดกิจกรรม" />
              <TextInput 
                id="organizer" 
                type="text" 
                class="mt-1 block w-full" 
                v-model="form.organizer" 
                required 
              />
              <div v-if="form.errors.organizer" class="text-red-500 text-sm mt-1">
                {{  'กรุณากรอกผู้จัดกิจกรรม' }}
              </div>
            </div>
          </div> 

          <div class="grid grid-cols-1 md:grid-cols-4 gap-6 w-full">
            <div class="mb-4">
              <InputLabel for="description" value="คำอธิบาย" />
              <textarea
              id="description"
              class="mt-1 block w-full border rounded-md border-gray-300 focus:border-gray-500 focus:ring focus:ring-gray-200 focus:ring-opacity-50"
              v-model="form.description"
            ></textarea>
              <div v-if="form.errors.description" class="text-red-500 text-sm mt-1">
                {{  'กรุณากรอกคำอธิบาย' }}
              </div>
            </div>

            <div class="mb-4">
              <InputLabel for="limits" value="จำนวนที่จำกัด" />
              <TextInput 
                id="limits" 
                type="number" 
                class="mt-1 block w-full" 
                v-model="form.limits" 
                required 
              />
              <div v-if="form.errors.limits" class="text-red-500 text-sm mt-1">
                {{  'กรุณากรอกจำนวนที่จำกัด' }}
              </div>
            </div>

            <div class="mb-4">
              <InputLabel for="regis_due" value="วันที่หมดเขตลงทะเบียน" />
              <TextInput 
                id="regis_due" 
                type="date" 
                class="mt-1 block w-full" 
                v-model="form.regis_due" 
                required 
              />
              <div v-if="form.errors.regis_due" class="text-red-500 text-sm mt-1">
                {{  'กรุณากรอกวันที่หมดเขตลงทะเบียน' }}
              </div>
            </div>

            <div class="mb-4">
              <InputLabel for="link" value="ลิงค์" />
              <TextInput 
                id="link" 
                type="text" 
                class="mt-1 block w-full" 
                v-model="form.link" 
              />
              <div v-if="form.errors.link" class="text-red-500 text-sm mt-1">
                {{  'กรุณากรอกลิงค์ถ้ามี' }}
              </div>
            </div>
          </div> 
        </form>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { useForm, usePage } from '@inertiajs/vue3';
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue'; 

const { props } = usePage();

const form = useForm({
  activity_name: '',
  start_date: '', 
  location: '',
  organizer: '',
  description: '',
  limits: '',
  regis_due: '', 
  link: '', 
  user_id: props.auth.user.id,
}); 

const handleSubmit = () => {
    form.post(route('activities.store'), {
        onSuccess: () => {
            const message = `📢 **เชิญชวนนักศึกษาเข้าร่วมกิจกรรม**: ${form.activity_name}
            
📅 **วันที่**: ${form.start_date}
👥 **จำนวนที่รับ**: ${form.limits} คน
            
📜 **รายละเอียดกิจกรรม**:  
${form.description}
            
🏫 **จัดโดย**: ${form.organizer}  
📍 **สถานที่**: ${form.location}`;

            sendLineNotification(props.userLineNotify.map(notify => notify.user_id), message);
        },
        onError: (errors) => {
            console.log('ข้อผิดพลาด:', errors);
        }
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
            body: JSON.stringify({
                message: message,
            }),
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
