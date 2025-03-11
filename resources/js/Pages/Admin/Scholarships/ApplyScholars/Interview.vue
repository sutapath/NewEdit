<script setup>
import { ref, computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Modal from '@/Components/Modal.vue';
import DangerButton from '@/Components/DangerButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { usePermission } from '@/composables/permissions';
import { format, addYears } from 'date-fns';

// Components for Table
import Table from "@/Components/Table.vue";
import TableRow from "@/Components/TableRow.vue";
import TableHeaderCell from "@/Components/TableHeaderCell.vue";
import TableDataCell from "@/Components/TableDataCell.vue";

// State and Methods
const { hasRole } = usePermission();
const form = useForm({});
const showConfirmDeleteModal = ref(false);
const applicationToDelete = ref(null);
const selectedScholarId = ref('');
const searchQuery = ref(''); 
const props = defineProps({
  applications: Array,
  currentUser: Object,
  scholarships: Array
}); 
const currentPage = ref(1);
const perPage = ref(10);
const totalItems = ref(props.applications.length); 
const filteredApplications = computed(() => {
  if (!props.applications) return [];

  // กรองข้อมูลตามเงื่อนไข
  const results = props.applications.filter(application => {
    const matchesScholar = !selectedScholarId.value || application.scholar_id === selectedScholarId.value;

    const search = searchQuery.value ? searchQuery.value.toLowerCase() : "";
    const matchesSearch = !search ||
      application.fname.toLowerCase().includes(search) ||
      application.lname.toLowerCase().includes(search);

    return matchesScholar && matchesSearch;
  });

  // เรียงข้อมูลตาม Interview_results
  const order = ['2', '1', '0'];  // กำหนดลำดับการเรียงจาก Interview_results 2, 1, 0 ตามลำดับ
  results.sort((a, b) => {
    // ตรวจสอบค่า Interview_results
    const indexA = order.indexOf(a.Interview_results.toString());  // แปลงเป็น string เพื่อการเปรียบเทียบ
    const indexB = order.indexOf(b.Interview_results.toString());

    // หากไม่พบใน order ให้ถือว่าเป็นลำดับสุดท้าย
    const finalIndexA = indexA === -1 ? order.length : indexA;
    const finalIndexB = indexB === -1 ? order.length : indexB;

    return finalIndexA - finalIndexB;
  });

  totalItems.value = results.length;

  // รีเซ็ตหน้าที่แสดงเมื่อมีการกรองข้อมูล
  if (currentPage.value > Math.ceil(totalItems.value / perPage.value)) {
    currentPage.value = 1;
  }

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
const statusClass = (result) => {
  switch (result) {
    case '1':
      return 'bg-green-300 text-dark py-1 px-3 rounded-full';
    case '0':
      return 'bg-red-300 text-dark py-1 px-3 rounded-full';
    case '2':
      return 'bg-gray-300 text-dark py-1 px-3 rounded-full';
    default:
      return '';
  }
};

// Function to get scholarship type
const getScholarType = (scholarType) => {
  const scholarTypes = {
    0: 'ทุนเพชรอินทนิล',
    1: 'ทุนงดเว้นค่าธรรมเนียมการศึกษา'
  };
  return scholarTypes[scholarType] || ' ';
};

// Function to get type ability
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
const showModal = ref(false);

// Function to handle Modal open/close
const openModal = () => {
  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
};

import PublishInfo from '@/Pages/Admin/Scholarships/ApplyScholars/PublishInfo.vue';
</script>

<template>

  <Head title="Scholarship Applications" />

  <AuthenticatedLayout>
    <div class="max-w-7xl mx-auto mb-3 py-4 mt-20 px-10">
      <div class="py-2 px-4 text-white dark:text-gray-100 flex justify-between items-center">
        <div class="py-2 text-gray-800 rounded-t-lg text-xl font-bold">
          ข้อมูลนักศึกษาเข้ารับการสัมภาษณ์
        </div>
        <div class="flex-1 mr-3 ml-3">
          <hr class="border-t border-gray-300">
        </div>
        <div class="flex justify-end space-x-4" v-if="hasRole('admin') || hasRole('officer')">
          <div class="py-1">
            <label for="scholar_id" class="block text-sm font-medium text-gray-700">เลือกทุนการศึกษา</label>
            <select id="scholar_id" v-model="selectedScholarId"
              class="block w-full mt-1 text-gray-800 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
              <option value="">ทั้งหมด</option>
              <option v-for="scholar in props.scholarships" :key="scholar.id" :value="scholar.id">
                {{ formatYear(scholar.apply_date) }} - {{ scholar.scholar_name }}
              </option>
            </select>
          </div>
          <div class="py-1">
            <label for="searchQuery" class="block text-sm font-medium text-gray-700">ค้นหาชื่อ</label>
            <input type="text" id="searchQuery" v-model="searchQuery" placeholder="กรอกชื่อเพื่อค้นหา"
              class="block w-full mt-1 text-gray-800 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
          </div>
          <div class="pt-7">
            <PrimaryButton class="custom-button-success ml-4" @click="openModal">แจ้งกำหนดการ</PrimaryButton>
          </div>
        </div>
      </div>
    </div>

    <div class="max-w-full sm:max-w-7xl mx-auto sm:px-6 lg:px-8 bg-white sm:rounded-xl overflow-x-auto">
      <Table class="whitespace-nowrap">
        <TableRow header>
          <TableHeaderCell>ชื่อ - สกุล</TableHeaderCell>
          <TableHeaderCell>ประเภท</TableHeaderCell>
          <TableHeaderCell>คะแนน</TableHeaderCell>
          <TableHeaderCell>ผลการพิจารณา</TableHeaderCell>
          <TableHeaderCell>Actions</TableHeaderCell>
        </TableRow>

        <TableRow v-for="application in filteredApplications" :key="application.id">
          <TableDataCell class="whitespace-nowrap">
            <template v-if="application.title === '0'">นาย</template>
            <template v-else-if="application.title === '1'">นาง</template>
            <template v-else-if="application.title === '2'">นางสาว</template>
            {{ application.fname }} {{ application.lname }}
          </TableDataCell>
          <TableDataCell class="whitespace-nowrap">
            {{ getScholarType(application.scholar_type) }} {{ getTypeAbility(application.type_ability) }}
          </TableDataCell>
          <TableDataCell class="whitespace-nowrap">{{ application.interview_score || 0 }}</TableDataCell>
          <TableDataCell class="whitespace-nowrap">
            <span :class="statusClass(application.Interview_results)">
              <template v-if="application.Interview_results === '1'">ผ่านการพิจารณา</template>
              <template v-else-if="application.Interview_results === '0'">ไม่ผ่านการพิจารณา</template>
              <template v-else-if="application.Interview_results === '2'">รอการพิจารณา</template>
            </span>
          </TableDataCell>
          <TableDataCell class="whitespace-nowrap">
            <Link :href="route('scholarship_applications.interviewdetail', application.id)"
              class="custom-button-warning">
            เอกสารเพิ่มเติม
            </Link>
          </TableDataCell>
        </TableRow>
      </Table>

      <div class="flex justify-end items-center w-full mt-4 space-x-4">
        <div class="flex items-center">
          <label for="perPage" class="text-sm font-medium text-gray-700 mr-2">ข้อมูลต่อหน้า</label>
          <select v-model="perPage" class="px-2 py-1 border border-gray-300 rounded text-gray-800" id="perPage">
            <option value="10">10 รายการ</option>
            <option value="20">20 รายการ</option>
            <option value="50">50 รายการ</option>
            <option value="100">100 รายการ</option>
          </select>
        </div>
        <span class="px-4 py-2">หน้า {{ currentPage }} จาก {{ Math.ceil(totalItems / perPage) }}</span>

        <div class="inline-flex space-x-2">
          <button class="px-4 py-2 bg-gray-300 text-gray-800 rounded" :disabled="currentPage === 1"
            @click="currentPage--">
            ก่อนหน้า
          </button>

          <button v-if="currentPage < Math.ceil(totalItems / perPage)"
            class="px-4 py-2 bg-gray-800 text-gray-100 rounded" @click="currentPage++">
            ถัดไป
          </button>
        </div>
      </div>

    </div>
  </AuthenticatedLayout>
  <Modal :show="showModal" @close="closeModal">
    <div class="p-6">
      <PublishInfo />
    </div>
  </Modal>
</template>
