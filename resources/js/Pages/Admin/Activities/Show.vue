<template>
  <AuthenticatedLayout>
    <Head title="Scholarship Details" />
    <div class="py-2">
      <div class="max-w-7xl mx-auto py-2 mt-10">
        <div class="py-6">
          <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-700 dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg border border-gray-500 dark:border-gray-600">
              <div class="py-2 px-4 text-white dark:text-gray-100 flex justify-between items-center">
                <div class="px-4">
                  {{ scholarship.scholar_name }}
                </div>
                <div class="flex items-center space-x-4">
                  <template v-if="scholarship.status !== 1">
                    <h5 class="inline-flex items-center px-4 py-2 border-transparent rounded-md bg-white text-sm text-red uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-red-500 transition ease-in-out duration-150">
                      ปิดรับสมัคร
                    </h5>
                  </template>
                  <Link :href="route('scholarships.index')"
                    class="inline-flex items-center px-4 py-2 bg-red-500 dark:bg-red-200 border border-transparent rounded-md text-sm text-white dark:text-red-500 uppercase tracking-widest hover:bg-red-700 dark:hover:bg-white focus:bg-red-700 dark:focus:bg-white active:bg-red-900 dark:active:bg-red-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-red-500 transition ease-in-out duration-150">
                    กลับ
                  </Link>
                  <button v-if="scholarship.status === 1" 
                  class="custom-button-success">
                    สมัคร
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="max-w-7xl mx-auto px-20 mb-20">
      <div class="space-y-6"> 
        <p class="text-gray text-lg">สมัครตั้งแต่: {{ formatDate(scholarship.apply_date) }} </p>
        <p class="text-gray text-lg">รายละเอียด: {{ scholarship.description }}</p>
        <p class="text-gray text-lg">
          ผู้ให้ข้อมูล : 
          <template v-if="scholarship.user.title === '0'">นาย</template>
          <template v-else-if="scholarship.user.title === '1'">นาง</template>
          <template v-else-if="scholarship.user.title === '2'">นางสาว</template>
          {{ scholarship.user.fname }} 
          {{ scholarship.user.lname }}
        </p>
        
        <div v-if="scholarship.file && isImage(scholarship.file)" class="mb-4">
          <img :src="`/storage/files/${scholarship.file}`" alt="Scholarship Image" class="w-full h-64 object-cover rounded-lg" />
        </div>
        <div v-else-if="scholarship.file && isPDF(scholarship.file)" class="mb-4">
          <iframe :src="`/storage/files/${scholarship.file}`" class="w-full h-64 border-none" frameborder="0"></iframe>
        </div>
        <div v-else-if="scholarship.file" class="text-center mb-6">
          <p class="text-gray-600 text-lg">ไฟล์: {{ scholarship.file }}</p>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
<script setup>
import { defineProps } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
  scholarship: Object,
});

const scholarship = props.scholarship;

const isImage = (file) => {
  return file && (file.endsWith('.jpg') || file.endsWith('.jpeg') || file.endsWith('.png') || file.endsWith('.gif'));
};

const isPDF = (file) => {
  return file && file.endsWith('.pdf');
};

const formatDate = (date) => {
  if (!date) return '';
  const months = [
    'มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน',
    'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'
  ];
  const d = new Date(date);
  const day = String(d.getDate()).padStart(2, '0');
  const month = months[d.getMonth()];
  const year = d.getFullYear(); // เพิ่มปีพ.ศ.
  return `${day} ${month} ${year}`;
};
</script>
<style scoped>
img {
  max-width: 100%;
  height: auto; 
  border-radius: 0.5rem; 
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); 
} 

.text-red {
  color: #ff4d39;
}
</style>
