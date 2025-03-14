<script setup>
import { ref, computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Modal from '@/Components/Modal.vue';
import DangerButton from '@/Components/DangerButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { usePermission } from '@/composables/permissions';
import { format, addYears } from 'date-fns'; // นำเข้าฟังก์ชันที่จำเป็นจาก date-fns

import Table from "@/Components/Table.vue";
import TableRow from "@/Components/TableRow.vue";
import TableHeaderCell from "@/Components/TableHeaderCell.vue";
import TableDataCell from "@/Components/TableDataCell.vue"; 
const { hasRole } = usePermission();
const form = useForm({});
const showConfirmDeleteModal = ref(false);
const applicationToDelete = ref(null);
const selectedScholarId = ref('');
const searchQuery = ref(''); 
import debounce from 'lodash/debounce';

// Data from the page props
const props = defineProps({
  applications: Array,
  currentUser: Object,
  scholarships: Array 
});

const currentPage = ref(1);
const perPage = ref(10);
const totalItems = ref(props.applications.length);
const debouncedSearch = debounce((query) => {
  searchQuery.value = query;
}, 500);
const updateSearchQuery = (query) => {
  debouncedSearch(query);
};
const filteredApplications = computed(() => {
  if (!props.applications) return [];

  const results = props.applications.filter(application => {
    if (selectedScholarId.value && application.scholar_id !== selectedScholarId.value) {
      return false;
    }

    const fullName = `${application.fname} ${application.lname}`.toLowerCase();
    if (searchQuery.value && !fullName.includes(searchQuery.value.toLowerCase())) {
      return false;
    }
    return true;
  });
 
  totalItems.value = results.length; 
  if (currentPage.value > Math.ceil(totalItems.value / perPage.value)) {
    currentPage.value = 1;
  } 
  const order = ['2','1', '0', '3'];
  results.sort((a, b) => {
    const indexA = order.indexOf(a.result);
    const indexB = order.indexOf(b.result);
    return indexA - indexB;
  });

  // คำนวณข้อมูลที่จะแสดงในหน้าปัจจุบัน (pagination)
  const start = (currentPage.value - 1) * perPage.value;
  return results.slice(start, start + perPage.value);
});


const totalPages = computed(() => {
  return Math.ceil(totalItems.value / perPage.value);
});

const formatYear = (date) => {
  try {
    const parsedDate = new Date(date);
    const nextYear = addYears(parsedDate, 1);
    return format(nextYear, 'yyyy');
  } catch (e) {
    console.error('Invalid date format', e);
    return '';
  }
};

const confirmDeleteApplication = (id) => {
  applicationToDelete.value = id;
  showConfirmDeleteModal.value = true;
};

const closeModal = () => {
  showConfirmDeleteModal.value = false;
  applicationToDelete.value = null;
};

const deleteApplication = () => {
  if (applicationToDelete.value) {
    form.delete(route('scholarship_applications.destroy', applicationToDelete.value), {
      onSuccess: () => closeModal()
    });
  }
};

const statusClass = (result) => {
  switch (result) {
    case '0':
      return 'bg-green-300 text-dark py-1 px-2 rounded-md';
    case '1':
      return 'bg-yellow-300 text-dark py-1 px-2 rounded-md';
    case '2':
      return 'bg-gray-300 text-dark py-1 px-2 rounded-md';
    case '3':
      return 'bg-red-300 text-dark py-1 px-2 rounded-md';
    default:
      return '';
  }
};
const getScholarType = (scholarType) => {
  const scholarTypes = {
    0: 'ทุนเพชรอินทนิล',
    1: 'ทุนงดเว้นค่าธรรมเนียมการศึกษา'
  };
  return scholarTypes[scholarType] || ' ';
};

const getTypeAbility = (typeAbility) => {
  const typeAbilities = {
    1: 'ประเภททุนเรียนดี',
    2: 'ประเภททุนเรียนดีแต่ขาดแคลนทุนทรัพย์',
    3: 'ประเภททุนความสามารถพิเศษด้านกีฬา',
    4: 'ประเภททุนความสามารถพิเศษด้านผลงานความคิดสร้างสรรค์',
    5: 'ประเภททุนความสามารถพิเศษด้านดนตรี นาฏศิลป์',
    6: 'ประเภทกิจกรรมที่ส่งเสริมภาวะผู้นำ'
  };
  return typeAbilities[typeAbility] || '';
};
</script>

<template>

  <Head title="Scholarship Applications" />

  <AuthenticatedLayout>
    <div class="max-w-7xl mx-auto mb-3  py-4 mt-20 px-10">

      <div class=" px-4 text-white dark:text-gray-100 flex justify-between items-center">
        <div class=" py-2  text-gray-800 rounded-t-lg text-xl font-bold">
          ตรวจข้อมูลการสมัคร
        </div>
        <div class="flex-1 mr-3 ml-3 ">
          <hr class="border-t border-gray-300">
        </div>
        <div class="flex justify-end space-x-4">
          <!-- Select scholarship -->
          <div>
            <label for="scholar_id" class="block text-sm font-medium text-gray-700">เลือกทุนการศึกษา</label>
            <select id="scholar_id" v-model="selectedScholarId"
              class="block w-full mt-1  text-gray-800  border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
              <option value="">ทั้งหมด</option>
              <option v-for="scholar in props.scholarships" :key="scholar.id" :value="scholar.id">
                {{ formatYear(scholar.apply_date) }} - {{ scholar.scholar_name }}
              </option>
            </select>
          </div>

          <div>
            <label for="searchQuery" class="block text-sm font-medium text-gray-700">ค้นหาชื่อ</label>
            <input type="text" id="searchQuery" v-model="searchQuery" placeholder="กรอกชื่อเพื่อค้นหา"
              class="block w-full mt-1 border-gray-300  text-gray-800  rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
          </div>
        </div>
      </div>
    </div>



    <div class="max-w-full sm:max-w-7xl mx-auto sm:px-6 lg:px-8 bg-white sm:rounded-xl overflow-x-auto">
      <Table class="min-w-full divide-y divide-gray-200">
        <template #header>

          <TableRow>
            <TableHeaderCell class="px-6 py-3 text-left text-sm font-medium text-gray-100 uppercase tracking-wider">
              ลำดับ</TableHeaderCell>
            <TableHeaderCell class="px-6 py-3 text-left text-sm font-medium text-gray-100 uppercase tracking-wider">ชื่อ
              - สกุล</TableHeaderCell>
            <TableHeaderCell class="px-6 py-3 text-left text-sm font-medium text-gray-100 uppercase tracking-wider">
              หมายเลขโทรศัพท์
            </TableHeaderCell>
            <TableHeaderCell class="px-6 py-3 text-left text-sm font-medium text-gray-100 uppercase tracking-wider">
              ประเภท</TableHeaderCell>
            <TableHeaderCell class="px-6 py-3 text-left text-sm font-medium text-gray-100 uppercase tracking-wider">
              การตรวจสอบ</TableHeaderCell>
            <TableHeaderCell class="px-6 py-3 text-left text-sm font-medium text-gray-100 uppercase tracking-wider">
              เพิ่มเติม</TableHeaderCell>
          </TableRow>
        </template>

        <template #default>
          <TableRow class="bg-white divide-y divide-gray-200" v-for="(application, index) in filteredApplications">
            <TableDataCell class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ index + 1 }}
            </TableDataCell>
            <TableDataCell class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
              <template v-if="application.title === '0'">นาย</template>
              <template v-else-if="application.title === '1'">นาง</template>
              <template v-else-if="application.title === '2'">นางสาว</template>
              {{ application.fname }} {{ application.lname }}
            </TableDataCell>
            <TableDataCell class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ application.phone }}
            </TableDataCell>
            <TableDataCell class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
              {{ getScholarType(application.scholar_type) }} {{ getTypeAbility(application.type_ability) }}
            </TableDataCell>
            <TableDataCell class="px-6 py-4 whitespace-nowrap text-sm">
              <span :class="statusClass(application.result)">
                <template v-if="application.result === '0'">ผ่าน</template>
                <template v-else-if="application.result === '1'">แก้ไข</template>
                <template v-else-if="application.result === '2'">รอการตรวจ</template>
                <template v-else-if="application.result === '3'">ไม่ผ่านเงื่อนไข</template>
              </span>
            </TableDataCell>
            <TableDataCell class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-500">
              <PrimaryButton v-if="hasRole('student') || hasRole('member')"
                @click="confirmDeleteApplication(application.id)" class="custom-button-danger">
                ลบ
              </PrimaryButton>
              <Link :href="route('scholarship_applications.show', application.id)" class="custom-button-warning">
              เพิ่มเติม
              </Link>
            </TableDataCell>
          </TableRow>
        </template>

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
      <Modal :show="showConfirmDeleteModal" @close="closeModal">
        <div class="p-6">
          <h2 class="text-lg font-semibold text-slate-800">ยืนยันการลบ?</h2>
          <div class="mt-6 flex space-x-4">
            <DangerButton class="custom-button-danger" @click="deleteApplication">ลบ</DangerButton>
            <SecondaryButton class="custom-button-dark" @click="closeModal">ยกเลิก</SecondaryButton>
          </div>
        </div>
      </Modal>
    </div>
  </AuthenticatedLayout>
</template>
