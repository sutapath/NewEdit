<script setup>
import { ref, computed, watch } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Modal from '@/Components/Modal.vue';
import DangerButton from '@/Components/DangerButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { usePermission } from '@/composables/permissions';
import { format, addYears } from 'date-fns';
import TextInput from '@/Components/TextInput.vue';
import Swal from 'sweetalert2';
import Table from "@/Components/Table.vue";
import TableRow from "@/Components/TableRow.vue";
import TableHeaderCell from "@/Components/TableHeaderCell.vue";
import TableDataCell from "@/Components/TableDataCell.vue";
import { router } from '@inertiajs/vue3'
import InputError from '@/Components/InputError.vue';
import moment from 'moment';
const { hasRole } = usePermission();
const form = useForm({
  interview_score: '',
});
const selectedScholarId = ref('');
const searchQuery = ref('');
const props = defineProps({
  applications: Array,
  currentUser: Object,
  scholarships: Array,
  publicInfos: Array
});
const currentPage = ref(1);
const perPage = ref(10);
const totalItems = ref(props.applications.length);
const filteredApplications = computed(() => {
  if (!props.applications) return [];
  const results = props.applications.filter(application => {
    const matchesScholar = !selectedScholarId.value || application.scholar_id === selectedScholarId.value;
    const search = searchQuery.value ? searchQuery.value.toLowerCase() : "";
    const matchesSearch = !search ||
      application.fname.toLowerCase().includes(search) ||
      application.lname.toLowerCase().includes(search);

    return matchesScholar && matchesSearch;
  });
  const order = ['2', '1', '0'];
  results.sort((a, b) => {
    const indexA = order.indexOf(a.Interview_results.toString());
    const indexB = order.indexOf(b.Interview_results.toString());
    const finalIndexA = indexA === -1 ? order.length : indexA;
    const finalIndexB = indexB === -1 ? order.length : indexB;
    return finalIndexA - finalIndexB;
  });
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
const handleSubmit = (applicationId, interviewScore) => {
  console.log('Submitting for application ID:', applicationId);
  if (interviewScore > 100) {
    console.log('Error: interview_score cannot be greater than 100');
    form.errors.interview_score = "คะแนนสัมภาษณ์ต้องไม่เกิน 100";
    return;
  }
  form.interview_score = interviewScore;
  console.log('Interview Score:', interviewScore);
  form.put(route('scholarship_application.interviewedit', applicationId), {
    onSuccess: () => {
      console.log("Submit successful");
      form.errors.interview_score = '';
    },
    onError: () => {
      console.log("Submit failed", form.errors);
    },
  });
};
const formatDate = (date) => {
  if (!date) return '';
  const thaiYear = moment(date).year() + 543;
  return moment(date).format(`DD/MM/${thaiYear} HH:mm`);
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

const goToEditPage = (id) => {
  router.get(route('publish_requests.EditInfo', id))
}
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
    <div class="max-w-7xl mx-auto mb-3 py-2 px-5 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-3 gap-4">
      <div v-for="publish in props.publicInfos" :key="publish.id">
        <div v-for="scholar in props.scholarships" :key="scholar.id">
          <div v-if="publish.scholar_id == scholar.id"
            class="bg-white p-6 rounded-md shadow-lg min-h-[200px] flex flex-col justify-between items-start w-full relative">

            <div class="text-left">
              <h3 class="text-xl font-bold text-gray-800">{{ scholar.scholar_name }}</h3>
              <p class="text-gray-600 text-lg">
                สัมภาษณ์ {{ formatDate(publish.send_date) }} น. <br>ที่ {{ publish.location }}
              </p>
            </div>
            <div v-if="hasRole('admin') || hasRole('officer')" class="absolute bottom-4 right-4 flex space-x-2">
              <PrimaryButton class="custom-button-danger" @click="handleDelete(publish.id)">
                ลบ
              </PrimaryButton>
              <PrimaryButton class="custom-button-warning" @click="goToEditPage(publish.id)">
                แก้ไข
              </PrimaryButton>

            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="max-w-full sm:max-w-7xl mx-auto sm:px-6 lg:px-8 bg-white sm:rounded-xl overflow-x-auto">
      <Table class="whitespace-nowrap">
        <TableRow header>
          <TableHeaderCell>ลำดับ</TableHeaderCell>
          <TableHeaderCell>ชื่อ - สกุล</TableHeaderCell>
          <TableHeaderCell>ประเภท</TableHeaderCell>
          <TableHeaderCell>คะแนน</TableHeaderCell>
          <TableHeaderCell>Actions</TableHeaderCell>
        </TableRow>
        <TableRow v-for="(application, index) in filteredApplications" :key="application.id">
          <!-- คอลัมน์ลำดับ -->
          <TableDataCell class="whitespace-nowrap">{{ index + 1 }}</TableDataCell>
          <TableDataCell class="whitespace-nowrap">
            <template v-if="application.title === '0'">นาย</template>
            <template v-else-if="application.title === '1'">นาง</template>
            <template v-else-if="application.title === '2'">นางสาว</template>
            {{ application.fname }} {{ application.lname }}
          </TableDataCell>
          <TableDataCell class="whitespace-nowrap">
            {{ getScholarType(application.scholar_type) }} {{ getTypeAbility(application.type_ability) }}
          </TableDataCell>
          <TableDataCell style="white-space: nowrap;">

            <form @submit.prevent="handleSubmit(application.id, application.interview_score)"
              enctype="multipart/form-data" class="w-[100px]">

              <TextInput id="interview_score" type="number" class="mt-1 block w-full"
                v-model="application.interview_score" :disabled="application.cancel_status" />

              <div v-if="application.interview_score > 100" class="mt-2 text-red-500">
                คะแนนสัมภาษณ์ต้องไม่เกิน 100
              </div>

              <div v-else-if="form.errors[application.id]?.interview_score" class="mt-2 text-red-500">
                {{ form.errors[application.id].interview_score }}
              </div>
            </form>
          </TableDataCell>
          <TableDataCell class="whitespace-nowrap">
            <Link :href="route('scholarship_applications.interviewdetail', application.id)"
              class="custom-button-warning">
            เอกสารเพิ่มเติม
            </Link>
          </TableDataCell>
        </TableRow>
<!-- <tbody v-if="!props.applications.some(application => application.user_id === props.currentUser.id)"
  class="bg-white divide-y divide-gray-200">
  <tr class="flex justify-center items-center">
    <td colspan="5" class="px-6 py-4 text-md text-gray-700 text-center">
      ไม่มีข้อมูลการสมัคร
    </td>
  </tr>
</tbody> -->
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
  <Modal :show="showModalEdit" @close="closeModalEdit">
    <div class="p-6">
      <PublishInfoEdit :publishId="selectedPublishId" :publishData="publishData" />
    </div>
  </Modal>


</template>
