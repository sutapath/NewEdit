<template>

  <Head title="Activity Index" />
  <AuthenticatedLayout>

    <div class="max-w-7xl mx-auto mb-3  py-4 mt-20 px-10">

      <div class="flex justify-between items-center">
        <div class=" py-2 mr-3  text-gray-800 rounded-t-lg text-xl font-bold">
          กิจกรรมนักศึกษา
        </div>

        <div class="flex-1">
          <hr class="border-t border-gray-300">
        </div>
        <Link v-if="hasRole('admin') || hasRole('officer')" :href="route('activities.create')"
          class="custom-button-success ml-4">
        เพิ่มข้อมูล
        </Link>
      </div>

      <!-- ตารางการแสดงผลกิจกรรม -->
      <Table class="w-full mt-4 max-w-7xl">
  <thead>
    <TableRow>
      <TableHeaderCell class="w-16 text-center">ลำดับ</TableHeaderCell>
      <TableHeaderCell class="w-1/4">ชื่อกิจกรรม</TableHeaderCell>
      <TableHeaderCell class="w-1/4">วันที่จัดกิจกรรม</TableHeaderCell>
      <TableHeaderCell class="w-1/6">สถานที่จัด</TableHeaderCell>
      <TableHeaderCell class="w-1/6">คำอธิบาย</TableHeaderCell>
      <TableHeaderCell class="w-1/4 text-center">จำนวนผู้เข้าร่วม</TableHeaderCell>
      <TableHeaderCell class="w-1/4 text-center">ชั่วโมง</TableHeaderCell>
      <TableHeaderCell class="w-32 text-center">จัดการ</TableHeaderCell>
    </TableRow>
  </thead>
  <tbody>
    <TableRow v-for="(activity, index) in sortedActivities" :key="activity.id">
      <TableDataCell class="text-center">{{ index + 1 }}</TableDataCell>
      <TableDataCell>{{ activity.activity_name }}</TableDataCell>
      <TableDataCell>{{ formatDate(activity.start_date) }}</TableDataCell>
      <TableDataCell>{{ activity.location }}</TableDataCell>
      <TableDataCell>{{ activity.description }}</TableDataCell>

      <TableDataCell v-if="activity.id !== 1" class="text-center">{{ activity.limits }}</TableDataCell>
      <TableDataCell v-else class="text-center">-</TableDataCell>

      <TableDataCell>{{ activity.hours }}</TableDataCell>
      <TableDataCell class="text-center">
        <div class="flex justify-center space-x-2">
          <template v-if="hasRole('admin') || hasRole('officer')">
            <button @click="() => $inertia.get(route('activities.edit', activity.id))"
              class="custom-button-warning">
              แก้ไข
            </button>
          </template>
          <template v-if="hasRole('scholar') || hasRole('intlscholar')">
            <Link :href="route('activity_registrations.create', { activity_id: activity.id })"
              class="custom-button-primary">
              ลงชื่อ
            </Link>
          </template>
          <template v-if="hasRole('admin') || hasRole('officer')">
            <button @click="confirmDeleteActivity(activity.id)" class="custom-button-danger">
              ลบ
            </button>
          </template>
        </div>
      </TableDataCell>
    </TableRow>
  </tbody>
</Table>

      <div class="flex justify-end items-center w-full mt-4 space-x-4">
        <!-- Dropdown สำหรับเลือกจำนวนข้อมูลต่อหน้า -->
        <div class="flex items-center">
          <label for="perPage" class="text-sm font-medium text-gray-700 mr-2">ข้อมูลต่อหน้า</label>
          <select v-model="perPage" class="px-2 py-1 border border-gray-300 rounded text-gray-800" id="perPage">
            <option value="10">10 รายการ</option>
            <option value="20">20 รายการ</option>
            <option value="50">50 รายการ</option>
            <option value="100">100 รายการ</option>
          </select>
        </div><!-- Pagination -->
        <!-- แสดงหน้าและจำนวนหน้าทั้งหมด -->
        <span class="px-4 py-2">หน้า {{ currentPage }} จาก {{ totalPages }}</span>

        <div class="inline-flex space-x-2">
          <button class="px-4 py-2 bg-gray-300 text-gray-800 rounded" :disabled="currentPage === 1"
            @click="currentPage--">
            ก่อนหน้า
          </button>


          <button v-if="currentPage < totalPages && totalItems > 0" class="px-4 py-2 bg-gray-800 text-gray-100 rounded"
            @click="currentPage++">
            ถัดไป
          </button>
        </div>
      </div>

      <!-- Modal สำหรับยืนยันการลบ -->
      <Modal :show="showConfirmDeleteActivityModal" @close="closeModal">
        <div class="p-6">
          <h2 class="text-lg font-semibold text-slate-800">
            คุณแน่ใจหรือไม่ว่าต้องการลบกิจกรรมนี้?
          </h2>
          <div class="mt-6 flex space-x-4">
            <DangerButton @click="confirmDeleteActivity">
              ลบ
            </DangerButton>
            <SecondaryButton @click="closeModal">
              ยกเลิก
            </SecondaryButton>
          </div>
        </div>
      </Modal>

    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { usePermission } from '@/composables/permissions';
import Modal from '@/Components/Modal.vue';
import DangerButton from '@/Components/DangerButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue'; 
import Table from "@/Components/Table.vue";
import TableRow from "@/Components/TableRow.vue";
import TableHeaderCell from "@/Components/TableHeaderCell.vue";
import TableDataCell from "@/Components/TableDataCell.vue";
import Swal from 'sweetalert2';
const { hasRole } = usePermission();
const props = defineProps({
  registrations: Array,
  currentUser: Object,
  activities: Array
});
const currentPage = ref(1);
const perPage = ref(10);
const totalItems = ref(props.activities.length);
const form = useForm({});
const showConfirmDeleteActivityModal = ref(false);
const selectedActivityId = ref(null); 
const confirmDeleteActivity = (id) => {
  selectedActivityId.value = id;

  // แสดงการยืนยันการลบ
  Swal.fire({
    title: 'คุณแน่ใจหรือไม่?',
    text: 'กิจกรรมนี้จะถูกลบ!',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'ลบเลย',
    cancelButtonText: 'ยกเลิก',
    reverseButtons: true,
    customClass: {
      confirmButton: 'bg-red-600 text-white',
      cancelButton: 'bg-gray-300 text-black',
    },
  }).then((result) => {
    if (result.isConfirmed) {
      // ถ้ายืนยันแล้วให้ลบ
      deleteActivity(selectedActivityId.value);
    }
  });
};

const deleteActivity = (activityId) => {
  // ขั้นตอนแรก: การแสดง Swal ว่ากำลังลบ
  Swal.fire({
    title: 'กำลังลบกิจกรรม...',
    text: 'โปรดรอสักครู่',
    icon: 'info',
    showConfirmButton: false, // ไม่ให้ปุ่มยืนยัน
    willOpen: () => {
      Swal.showLoading(); // แสดง loading
    },
    background: '#f4f6f9', // พื้นหลัง
    timer: 3000, // แสดงให้โหลด 3 วินาที
  });

  // การลบกิจกรรม
  axios.post(route('activities.destroy', { activity: activityId }))
    .then((response) => {
      // เมื่อสำเร็จ
      Swal.fire({
        title: 'ลบกิจกรรมสำเร็จ!',
        text: 'กิจกรรมได้ถูกลบเรียบร้อยแล้ว.',
        icon: 'success',
        confirmButtonText: 'ตกลง',
        confirmButtonColor: '#4caf50', // ปุ่มยืนยันสีเขียว
        background: '#e8f5e9', // พื้นหลังสีเขียวอ่อน
        timer: 3000, // แสดงข้อความสำเร็จ 3 วินาที
      }).then(() => {
        // รีเฟรชหน้าหลังจากแสดง Swal
        window.location.reload();  // รีเฟรชหน้า
      });
    })
    .catch((error) => {
      // หากเกิดข้อผิดพลาด
      let errorMessage = 'ไม่สามารถลบกิจกรรมได้.';
      if (error.response && error.response.data) {
        errorMessage = error.response.data.message || errorMessage;
      }
      Swal.fire({
        title: 'เกิดข้อผิดพลาด!',
        text: errorMessage,
        icon: 'error',
        confirmButtonText: 'ตกลง',
        confirmButtonColor: '#f44336', // ปุ่มยืนยันสีแดง
        background: '#ffebee', // พื้นหลังสีแดงอ่อน
        customClass: {
          confirmButton: 'text-white font-semibold py-2 px-4 rounded-full', // ปรับปุ่มให้สวย
        },
      });
    });
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

const sortedActivities = computed(() => {
  if (!props.activities) return []; 
  const idOneActivity = props.activities.find(activity => activity.id === 1); 
  const otherActivities = props.activities
    .filter(activity => activity.id !== 1)
    .sort((a, b) => new Date(b.start_date) - new Date(a.start_date)); 
  const results = idOneActivity ? [idOneActivity, ...otherActivities] : otherActivities;
  totalItems.value = results.length; 
  if (currentPage.value > Math.ceil(totalItems.value / perPage.value)) {
    currentPage.value = 1;
  } 
  const start = (currentPage.value - 1) * perPage.value;
  return results.slice(start, start + perPage.value);
}); 
const totalPages = computed(() => {
  return Math.ceil(totalItems.value / perPage.value);
});

</script>

<style scoped>
.description {
  display: block;
  white-space: normal;
}
</style>
