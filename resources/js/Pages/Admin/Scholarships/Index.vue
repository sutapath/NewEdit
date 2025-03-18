<template>

  <Head title="Scholarship Index" />

  <AuthenticatedLayout>
    <div class="max-w-7xl mx-auto mb-3 py-4 mt-20 px-10">
      <div class="flex justify-between items-center">
        <div class="py-2 mr-3 text-gray-800 rounded-t-lg text-xl font-bold">
          ทุนการศึกษา มหาวิทยาลัยราชภัฏสุรินทร์
        </div>

        <div class="flex-1">
          <hr class="border-t border-gray-300" />
        </div>
        <Link v-if="(hasRole('admin') || hasRole('officer'))" :href="route('scholarships.create')"
          class="custom-button-success ml-4">
        ประกาศทุน
        </Link>
        <Link v-if="hasRole('student') || hasRole('member')" :href="route('applications.index')"
          class="custom-button-success ml-4">
        ข้อมูลการสมัคร
        </Link>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mt-3">
        <div v-for="scholarship in filteredScholarships" :key="scholarship.id"
          class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 relative">
          <Link :href="route('scholarships.show', { id: scholarship.id })">

          <div v-if="scholarship.file && isImage(scholarship.file)" class="mb-4">
            <a :href="`/storage/files/${scholarship.file}`" target="_blank" class="relative group">
              <img :src="`/storage/files/${scholarship.file}`" alt="Scholarship Image"
                class="w-full h-64 object-cover rounded-lg transition-transform duration-300 group-hover:scale-105" />
              <div
                class="absolute inset-0 bg-black bg-opacity-30 opacity-100 transition-opacity duration-300 flex items-center justify-center text-white text-lg group-hover:bg-opacity-70">
                ไปที่ไฟล์
              </div>
            </a>
          </div>
          <div v-else-if="scholarship.file && isPDF(scholarship.file)" class="mb-4">
            <a :href="`/storage/files/${scholarship.file}`" target="_blank" class="relative group">
              <iframe :src="`/storage/files/${scholarship.file}`" class="w-full h-64 border-none"
                frameborder="0"></iframe>
              <div
                class="absolute inset-0 bg-black bg-opacity-30 opacity-100 transition-opacity duration-300 flex items-center justify-center text-white text-lg group-hover:bg-opacity-70">
                ไปที่ไฟล์
              </div>
            </a>
          </div>

          <div v-else class="text-center mb-4">
            <img src="/assets/AcadLogo.jpg" alt="Default Image" class="w-full h-64 object-cover rounded-lg" />
          </div>
          <div class="absolute top-2 left-2 px-3 py-1 text-xs font-bold uppercase rounded-lg" :class="{
            'bg-red-500 text-white': scholarship.status === '0',
            'bg-green-500 text-white': scholarship.status === '1'
          }">
            {{ scholarship.status === '1' ? 'เปิดรับสมัคร' : 'ปิดรับสมัคร' }}
          </div>
          <h2 class="text-xl font-semibold">ประกาศ{{ scholarship.scholar_name }}</h2>
          <p class="text-gray-600 dark:text-gray-300 mb-2">
            รับสมัครตั้งแต่วันที่ {{ formatDate(scholarship.apply_date) }} ถึง {{ formatDate(scholarship.due_date) }}
          </p>
          <p class="description mb-10">
            {{ scholarship.description }}
          </p>
          </Link>
          <div class="absolute bottom-4 right-4 mt-10 flex space-x-2">
            <template v-if="hasRole('admin') || hasRole('officer')">
              <button @click="editScholarship(scholarship.id)" class="custom-button-warning">
                แก้ไข
              </button>
            </template>

            <template v-if="(hasRole('admin') || hasRole('officer'))">
              <button @click="confirmDeleteScholarship(scholarship.id)" class="custom-button-danger">
                ลบ
              </button>
            </template>

            <template v-if="scholarship.type == 0 && hasRole('member') && scholarship.status == 1">
              <Link :href="route('scholarship_applications.intanilcreate', { scholar_id: scholarship.id })"
                class="custom-button-success">
              สมัคร
              </Link>
            </template>

            <template v-if="scholarship.type == 1 && hasRole('student') && scholarship.status == 1">
              <Link :href="route('scholarship_applications.pertermcreate', { scholar_id: scholarship.id })"
                class="custom-button-success">
              สมัคร
              </Link>
            </template>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import { usePermission } from "@/composables/permissions";
import Swal from "sweetalert2";

const { hasRole, hasPermission } = usePermission();
const props = defineProps({
  scholarships: Array,
  userId: Number,
});
const form = useForm({});
const showConfirmDeleteScholarshipModal = ref(false);
const selectedScholarshipId = ref(null);
const editScholarship = (id) => {
  window.location.href = route('scholarships.edit', { id })
}
const confirmDeleteScholarship = (id) => {
  Swal.fire({
    title: 'คุณแน่ใจหรือไม่?',
    text: 'คุณต้องการลบข้อมูลทุนการศึกษานี้หรือไม่?',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'ลบ',
    cancelButtonText: 'ยกเลิก',
    confirmButtonColor: "#dc3545", // สีปุ่ม 'ลบ' เป็นสีแดง
    cancelButtonColor: "#6c757d", // สีปุ่ม 'ยกเลิก' เป็นสีเทา
  }).then((result) => {
    if (result.isConfirmed) {
      // เรียกใช้งานการลบเมื่อคลิกปุ่ม "ลบ"
      if (id) {
        form.delete(route("scholarships.destroy", id), {
          onSuccess: () => {
            Swal.fire({
              title: "สำเร็จ!",
              text: "ลบข้อมูลทุนการศึกษาเรียบร้อยแล้ว",
              icon: "success",
              confirmButtonText: "ตกลง",
              confirmButtonColor: "#28a745", // กำหนดสีปุ่มเป็นสีเขียวด้วย hex
            });
          },
        });
      }
    }
  });
};

const closeModal = () => {
  showConfirmDeleteScholarshipModal.value = false;
  selectedScholarshipId.value = null;
};

const isImage = (file) => {
  return file && /\.(jpg|jpeg|png|gif)$/i.test(file);
};

const isPDF = (file) => {
  return file && /\.pdf$/i.test(file);
};

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

const filteredScholarships = computed(() => {
  return props.scholarships.filter(scholarship => scholarship.user_id === props.userId);
});
</script>

<style scoped>
.description {
  display: block;
  white-space: normal;
}

/* ปุ่มยืนยันการลบ (สีแดง) */
.swal-confirm-danger {
  background-color: #dc3545 !important;
  color: white !important;
}

/* ปุ่มยกเลิก (สีเทา) */
.swal-cancel {
  background-color: #6c757d !important;
  color: white !important;
}

/* ปุ่มยืนยันการลบสำเร็จ (สีเขียว) */
.swal-confirm-green {
  background-color: #28a745 !important;
  color: white !important;
}
</style>
