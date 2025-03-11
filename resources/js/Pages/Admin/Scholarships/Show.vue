<template>
  <Head :title="scholarship.scholar_name" />

  <AuthenticatedLayout>
    <div class="max-w-7xl mx-auto mb-3 py-4 mt-20 px-10">
      <div class="flex justify-between items-center mb-3">
        <div class="py-2 mr-3 text-gray-800 rounded-t-lg text-xl font-bold">
          {{ scholarship.scholar_name }}
        </div>

        <div class="flex-1">
          <hr class="border-t border-gray-300">
        </div>
        <div v-if="scholarship.link">
  <a :href="scholarship.link" target="_blank" class="custom-button-primary ml-4 ">
    ประกาศเพิ่มเติม
  </a>
</div>
        <Link :href="route('scholarships.index')" class="custom-button-danger ml-4">
          กลับ
        </Link>
      </div>

      <div class="mb-6">
        <div class="mb-3">
          <p class="text-gray-800 dark:text-gray-200 whitespace-pre-line">
            {{ scholarship.description }}
          </p>
        </div>

        <p class="mb-6">
          รับสมัครตั้งแต่วันที่ <span class="text-red-500">{{ formatDate(scholarship.apply_date) }}</span>
          ถึง <span class="text-red-500">{{ formatDate(scholarship.due_date) }}</span>
        </p>
        <div v-if="scholarship.file && isImage(scholarship.file)" class="mb-6">
  <img :src="`/storage/files/${scholarship.file}`" alt="Scholarship Image" class="w-full h-screen object-cover rounded-lg" />
</div>

<div v-else-if="scholarship.file && isPDF(scholarship.file)" class="mb-6">
  <iframe :src="`/storage/files/${scholarship.file}`" class="w-full h-screen border-none" frameborder="0"></iframe>
</div>


      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref } from "vue";
import { Head, Link } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { usePermission } from "@/composables/permissions";

const props = defineProps({
  scholarship: Object,
  userId: Number,
});

const { hasRole, hasPermission } = usePermission();

// Function to check if the file is an image
const isImage = (file) => {
  return file && /\.(jpg|jpeg|png|gif)$/i.test(file);
};

// Function to check if the file is a PDF
const isPDF = (file) => {
  return file && /\.pdf$/i.test(file);
};

// Function to format the date in Thai
const formatDate = (date) => {
  const months = [
    'มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน',
    'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'
  ];

  const d = new Date(date);
  const day = d.getDate().toString().padStart(2, '0');
  const month = months[d.getMonth()];
  const year = d.getFullYear().toString();

  return `${day} ${month} ${year}`;
};
</script>

<style scoped>
.whitespace-pre-line {
  white-space: pre-line;
}
</style>
