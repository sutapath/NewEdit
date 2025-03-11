<template>

  <Head title="Activity Saves" />

  <AuthenticatedLayout>




    <div class="max-w-7xl mx-auto py-4 mt-20 px-10">
      <div class="px-4 text-white dark:text-gray-100 flex justify-between items-center">
        <div class="py-2 text-gray-800 rounded-t-lg text-xl font-bold">
          <span v-if="saves.length > 0 && saves[0].title == 0">นาย</span>
            <span v-else-if="saves.length > 0 && saves[0].title == 1">นาง</span>
            <span v-else-if="saves.length > 0 && saves[0].title == 2">นางสาว</span>
            {{ saves.length > 0 ? `${saves[0].fname} ${saves[0].lname}` : 'ไม่มีข้อมูล' }}
         
        </div>
        <div class="flex-1 mr-3 ml-3">
          <hr class="border-t border-gray-300" />
        </div>
        <div class="flex justify-end space-x-4">
          <Link :href="route('scholarship_applications.report')" class="custom-button-danger px-4">
            กลับ
            </Link>
        </div>
      </div>
    </div>
     
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
      <Table>
        <thead>
          <TableRow>
            <TableHeaderCell>ลำดับ</TableHeaderCell>
            <TableHeaderCell>ชื่อกิจกรรม</TableHeaderCell>
            <TableHeaderCell>วันที่</TableHeaderCell>
            <TableHeaderCell>สถานที่</TableHeaderCell>
            <TableHeaderCell>ชั่วโมง</TableHeaderCell>
            <TableHeaderCell>ภาพ</TableHeaderCell>
            <TableHeaderCell>ผลลัพธ์</TableHeaderCell>
            <TableHeaderCell>ตรวจสอบ</TableHeaderCell>
          </TableRow>
        </thead>
        <tbody>
          <TableRow v-for="(save, index) in saves" :key="save.id">
            <TableDataCell>{{ index + 1 }}</TableDataCell>
            <TableDataCell>{{ save.activity_name }}</TableDataCell>
            <TableDataCell>{{ formatDate(save.date) }}</TableDataCell>
            <TableDataCell>{{ save.location }}</TableDataCell>
            <TableDataCell>{{ save.hours }}</TableDataCell>
            <TableDataCell>
              <button @click="openImageModal(save.file)">
                <img :src="`/storage/files/${save.file}`" alt="Activity image"
                  class="w-16 h-16 object-cover rounded-lg shadow-md" />
              </button>
            </TableDataCell>
            <TableDataCell :class="{
              'text-green-500': save.result === '1',
              'text-yellow-500': save.result === '4',
              'text-gray-500': save.result === '2',
              'text-red-500': save.result === '3'
            }">
              {{ getResultText(save.result) }}
            </TableDataCell>
            <TableDataCell>
              <button @click="openCheckModal(save)" class="custom-button-warning"
                v-if="(hasRole('admin') || hasRole('officer'))">
                ตรวจ
              </button>
            </TableDataCell>
          </TableRow>
        </tbody>
      </Table>
    </div>

    <!-- Image Modal -->
    <Modal :show="showImageModal" @close="closeImageModal">
      <div class="p-6 flex justify-center items-center">
        <img :src="selectedImage" alt="Large view" class="max-w-full max-h-screen object-contain" />
      </div>
    </Modal>

    <!-- Check Activity Modal -->
    <Modal :show="showCheckModal" @close="closeCheckModal">
      <Check :save="currentSave" />
    </Modal>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Modal from '@/Components/Modal.vue';
import { usePermission } from "@/composables/permissions";
import Check from '@/Pages/Admin/Activities/Saves/Check.vue';
import Table from "@/Components/Table.vue";
import TableRow from "@/Components/TableRow.vue";
import TableHeaderCell from "@/Components/TableHeaderCell.vue";
import TableDataCell from "@/Components/TableDataCell.vue";

// ใช้ props
const props = defineProps({
  saves: Array,
  user: Object // เพิ่ม props สำหรับ user
});

const { hasRole } = usePermission();

const showImageModal = ref(false);
const selectedImage = ref('');
const showCheckModal = ref(false);
const currentSave = ref(null);

// Date formatting function
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

function getResultText(result) {
  switch (result) {
    case '1':
      return 'ผ่าน';
    case '4':
      return 'แก้ไข';
    case '2':
      return 'รอตรวจสอบ';
    case '3':
      return 'ไม่ผ่าน';
    default:
      return '';
  }
}

const openImageModal = (file) => {
  selectedImage.value = `/storage/files/${file}`;
  showImageModal.value = true;
};

const closeImageModal = () => {
  showImageModal.value = false;
  selectedImage.value = '';
};

const openCheckModal = (save) => {
  currentSave.value = save;
  showCheckModal.value = true;
};

const closeCheckModal = () => {
  showCheckModal.value = false;
  currentSave.value = null;
};
</script>
