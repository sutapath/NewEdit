<template>

  <Head title="Activity Saves" />

  <AuthenticatedLayout>
    <div class="max-w-7xl mx-auto py-4 mt-20 px-10">
      <div class="px-4 text-white dark:text-gray-100 flex justify-between items-center">
        <div class="py-2 text-gray-800 rounded-t-lg text-xl font-bold">กิจกรรม</div>
        <div class="flex-1 mr-3 ml-3">
          <hr class="border-t border-gray-300" />
        </div>
        <div class="flex justify-end space-x-4 mr-3">
          <p v-if="hasRole('scholar') || hasRole('intlscholar')" class="custom-button-warning px-4">
            จำนวนชั่วโมง : {{ props.totalHours }}
          </p>
        </div>
        <div class="flex justify-end space-x-4">
          <Link :href="route('activity_registrations.index')" v-if="hasRole('scholar') || hasRole('intlscholar')"
            class="custom-button-success px-4">
          เพิ่ม
          </Link>
        </div>
        <div class="w-60" v-if="hasRole('admin') || hasRole('officer')">
          <label for="search" class="block text-sm text-gray-700">ค้นหาตามชื่อ</label>
          <input id="search" type="text" v-model="searchQuery" placeholder="กรอกชื่อเพื่อค้นหา"
            class="block w-full mt-1 text-gray-800 border-gray-300 rounded-md shadow-sm focus:border-gray-800 text-sm focus:ring-gray-800 sm:text-sm" />
        </div>
      </div>
    </div>


    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
      <Table>
        <thead>
          <tr>
            <TableHeaderCell class="min-w-[110px]">ลำดับ</TableHeaderCell>
            <TableHeaderCell class="min-w-[150px]">ชื่อ - สกุล</TableHeaderCell>
            <TableHeaderCell class="min-w-[200px]">ชื่อกิจกรรม</TableHeaderCell>
            <TableHeaderCell class="min-w-[120px]">วันที่</TableHeaderCell>
            <TableHeaderCell class="min-w-[150px]">สถานที่</TableHeaderCell>
            <TableHeaderCell class="min-w-[100px]">ชั่วโมง</TableHeaderCell>
            <TableHeaderCell class="min-w-[120px]">ภาพ</TableHeaderCell>
            <TableHeaderCell class="min-w-[150px]">ผลลัพธ์</TableHeaderCell>
            <TableHeaderCell class="min-w-[120px]">เพิ่มเติม</TableHeaderCell>
          </tr>
        </thead>

        <tbody>
          <!-- ถ้าไม่มีข้อมูลที่กรองแล้วจะแสดงข้อความ -->
          <tr v-if="filteredSaves.length === 0">
            <td colspan="9" class="text-center py-4 text-gray-500 w-full min-h-[100px]">ไม่มีข้อมูล</td>
          </tr>

          <!-- แสดงข้อมูลถ้ามี -->
          <tr v-for="(save, index) in filteredSaves" :key="save.id">
            <TableDataCell v-if="isFirstOfGroup(save)" class="min-h-[100px]">{{ index + 1 }}</TableDataCell>
            <TableDataCell v-else class="min-h-[100px]"></TableDataCell>
            <TableDataCell class="min-h-[150px] break-words">{{ displayName(save) }}</TableDataCell>
            <TableDataCell class="min-h-[100px] break-words">{{ save.activity_name }}</TableDataCell>
            <TableDataCell class="min-h-[100px] break-words">{{ formatDate(save.date) }}</TableDataCell>
            <TableDataCell class="min-h-[100px] break-words">{{ save.location }}</TableDataCell>
            <TableDataCell class="min-h-[100px] break-words">{{ save.hours }}</TableDataCell>
            <TableDataCell class="min-h-[100px]">
              <img :src="`/storage/files/${save.file}`" alt="Activity file" @click="openImageModal(save.file)"
                class="w-16 h-16 object-cover rounded-lg cursor-pointer" />
            </TableDataCell>
            <TableDataCell class="min-h-[100px]">
              <div :class="{
            'bg-green-300 text-gray-800': save.result === '1',
            'bg-yellow-300 text-gray-800': save.result === '4',
            'bg-gray-300 text-gray-800': save.result === '2',
            'bg-red-300 text-gray-800': save.result === '3'
          }" class="px-2 py-1 rounded-md break-words">
                {{ getResultText(save.result) }}
              </div>
            </TableDataCell>
            <TableDataCell class="min-h-[100px]">
              <div class="flex space-x-2">
                <Link :href="route('activity_saves.edit', save.id)"
                  v-if="hasRole('scholar') || hasRole('intlscholar')"
                  class="custom-button-warning">เพิ่มเติม</Link>
                <Link :href="route('activity_saves.edit', save.id)"
                  v-if="hasRole('scholar') || hasRole('intlscholar') && (save.result !== '1')"
                  class="custom-button-warning">แก้ไข</Link>
                <button @click="confirmDelete(save.id)"
                  v-if="hasRole('scholar') || hasRole('intlscholar') && (save.result !== '1')"
                  class="custom-button-danger">ลบ</button>
                <button @click="showCheckActivity(save.id)"
                  v-if="(hasRole('admin') || hasRole('officer')) && (save.result !== '1' || save.result !== '3')"
                  class="custom-button-warning">ตรวจ</button>
              </div>
            </TableDataCell>
          </tr>
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


    </div>

    <!-- Image Modal -->
    <Modal :show="showImageModal" @close="closeImageModal">
      <div class="p-6 flex justify-center items-center">
        <img :src="selectedImage" alt="Large view" class="max-w-full max-h-screen object-contain" />
      </div>
    </Modal>

    <!-- Confirm Delete Modal -->
    <Modal :show="showConfirmDeleteModal" @close="closeConfirmDeleteModal">
      <div class="p-6">
        <h2 class="text-lg font-semibold text-slate-800">ยืนยันการลบ?</h2>
        <div class="mt-6 flex space-x-4">
          <DangerButton class="custom-button-danger" @click="deleteSave">ลบ</DangerButton>
          <SecondaryButton @click="closeConfirmDeleteModal">ยกเลิก</SecondaryButton>
        </div>
      </div>
    </Modal>
    <div v-if="isModalVisible" @click="hideModal"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div @click.stop class="bg-white rounded-lg shadow-lg p-6 max-w-lg mx-auto relative">
        <button @click="hideModal" class="absolute top-2 right-2 text-gray-600 hover:text-gray-900">
          <span class="text-2xl">&times;</span> <!-- Close Button -->
        </button>
        <div>ทดสอบการแสดงผลของ Modal</div> <!-- ทดสอบการแสดง Modal -->
      </div>
    </div>

  </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Modal from '@/Components/Modal.vue';
import DangerButton from '@/Components/DangerButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { usePermission } from "@/composables/permissions";
import Check from '@/Pages/Admin/Activities/Saves/Check.vue';
import Table from "@/Components/Table.vue";
import TableRow from "@/Components/TableRow.vue";
import TableHeaderCell from "@/Components/TableHeaderCell.vue";
import TableDataCell from "@/Components/TableDataCell.vue";
import { defineProps, defineAsyncComponent } from 'vue';

const { hasRole } = usePermission();

const props = defineProps({
  saves: Array,
  totalHoursPerYear:Object,
  totalHours: Object
}); 
// Reactive variable to control modal visibility
const isModalVisible = ref(false);
const currentPage = ref(1);
const perPage = ref(10);
const totalItems = ref(props.saves.length);

const showCheckActivity = (id) => {
  window.location.href = `/activity-saves/${id}/show`;
};

// Method to toggle modal visibility
const showModal = () => {
  isModalVisible.value = true;
};

const hideModal = () => {
  isModalVisible.value = false;
}; 
const CheckComponent = defineAsyncComponent(() => import('@/Pages/Admin/Activities/Saves/Check.vue'));

const form = useForm({});
const showConfirmDeleteModal = ref(false);
const currentId = ref(null);
const showImageModal = ref(false);
const selectedImage = ref('');
const showCheckModal = ref(false);
const currentSave = ref(null);
const searchQuery = ref('');
// const totalHours = computed(() => {
//   return filteredSaves.value.reduce((total, save) => total + (save.hours || 0), 0);
// });

const filteredSaves = computed(() => {
  let results = [...props.saves]; 
  if (searchQuery.value) {
    results = results.filter(save => {
      const fullName = `${save.fname} ${save.lname}`;
      return fullName.includes(searchQuery.value);
    });
  }
  const resultOrder = ['2', '4', '1', '3']; 
  results.sort((a, b) => {
    const indexA = resultOrder.indexOf(String(a.result));
    const indexB = resultOrder.indexOf(String(b.result));

    return (indexA === -1 ? Number.MAX_SAFE_INTEGER : indexA) -
      (indexB === -1 ? Number.MAX_SAFE_INTEGER : indexB);
  });
  totalItems.value = results.length;
  const start = (currentPage.value - 1) * perPage.value;
  return results.slice(start, start + perPage.value);
});

const totalPages = computed(() => {
  return Math.ceil(totalItems.value / perPage.value);
});

const isFirstOfGroup = (save) => {
  const previousSaveIndex = filteredSaves.value.indexOf(save) - 1;
  const previousSave = filteredSaves.value[previousSaveIndex];
  return !previousSave || previousSave.user_id !== save.user_id;
};
const displayName = (save) => {
  const titles = ['นาย', 'นาง', 'นางสาว'];
  return `${titles[save.title]} ${save.fname} ${save.lname}`;
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

const confirmDelete = (id) => {
  currentId.value = id;
  showConfirmDeleteModal.value = true;
};

const deleteSave = async () => {
  await form.delete(route('activity_saves.destroy', currentId.value));
  showConfirmDeleteModal.value = false;
};

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

const closeConfirmDeleteModal = () => {
  showConfirmDeleteModal.value = false;
};

// Function to get result text
const getResultText = (result) => {
  switch (result) {
    case '1':
      return 'ผ่าน';
    case '2':
      return 'ไม่ผ่าน';
    case '3':
      return 'ไม่เข้าร่วม';
    case '4':
      return 'รอตรวจสอบ';
    default:
      return '';
  }
};
</script>

 