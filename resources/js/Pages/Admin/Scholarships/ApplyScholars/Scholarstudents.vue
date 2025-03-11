<script setup>
import { ref, computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Modal from '@/Components/Modal.vue';
import DangerButton from '@/Components/DangerButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { usePermission } from '@/composables/permissions';
import Table from "@/Components/Table.vue";
import EditScholar from "@/Pages/Admin/Users/EditScholar.vue";
import TableRow from "@/Components/TableRow.vue";
import TableHeaderCell from "@/Components/TableHeaderCell.vue";
import TableDataCell from "@/Components/TableDataCell.vue";
// State and Methods
const { hasRole } = usePermission();
const form = useForm({});
const showConfirmDeleteModal = ref(false);
const applicationToDelete = ref(null);
const props = defineProps({
  applications: Array,
  scholarships: Array,
  currentUser: Object,
  roles: Array,
  user: Array
});
const selectedScholarId = ref('');
const searchQuery = ref('');

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
}; const formatYear = (date) => {
  const d = new Date(date);
  return d.getFullYear();
}

 
const getScholarType = (scholarType) => {
  const scholarTypes = {
    0: 'ทุนเพชรอินทนิล',
    1: 'ทุนงดเว้นค่าธรรมเนียมการศึกษา'
  };
  return scholarTypes[scholarType] || '';
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

// Pagination State
const currentPage = ref(1);
const perPage = ref(10);
const totalItems = ref(0);

// Computed for filtering applications
const filteredApplications = computed(() => {
  let filtered = props.applications;
  if (selectedScholarId.value) {
    filtered = filtered.filter(app => app.scholar_id === selectedScholarId.value);
  }
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    filtered = filtered.filter(app =>
      (app.fname + ' ' + app.lname).toLowerCase().includes(query)
    );
  }

  totalItems.value = filtered.length;
  const start = (currentPage.value - 1) * perPage.value;
  const end = start + perPage.value;
  return filtered.slice(start, end);
});

// Total Pages Computation
const totalPages = computed(() => Math.ceil(totalItems.value / perPage.value));

// Pagination Methods
const changePage = (page) => {
  if (page >= 1 && page <= totalPages.value) {
    currentPage.value = page;
  }
};
</script>
<template>

  <Head title="Admin Scholarship Applications" />
  <AuthenticatedLayout>
    <div class="max-w-7xl mx-auto mb-3 py-4 mt-20 px-10">
      <div class="py-2 px-4 text-white dark:text-gray-100 flex justify-between items-center">
        <div class="py-2 text-gray-800 rounded-t-lg text-xl font-bold">
          กำหนดสิทธิ์นักศึกษาทุน
        </div>
        <div class="flex-1 mr-3 ml-3">
          <hr class="border-t border-gray-300" />
        </div>
        <div class="flex justify-end space-x-4">
          <!-- Select scholarship -->
          <div class="py-1">
            <label for="scholar_id" class="block text-sm font-medium text-gray-700">เลือกทุนการศึกษา</label>
            <select id="scholar_id" v-model="selectedScholarId"
              class="block w-full text-gray-800 mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
              <option value="">ทั้งหมด</option>
              <option v-for="scholar in props.scholarships" :key="scholar.id" :value="scholar.id">
                {{ formatYear(scholar.apply_date) }} - {{ scholar.scholar_name }}
              </option>
            </select>
          </div>

          <!-- Search input -->
          <div class="py-1">
            <label for="searchQuery" class="block text-sm font-medium text-gray-700">ค้นหาชื่อ</label>
            <input type="text" id="searchQuery" v-model="searchQuery" placeholder="กรอกชื่อเพื่อค้นหา"
              class="block w-full mt-1 border-gray-300 text-gray-800 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
          </div>
        </div>
      </div>
    </div>
    <div class="max-w-full sm:max-w-7xl mx-auto sm:px-6 lg:px-8 bg-white sm:rounded-xl overflow-x-auto  min-h-screen">

      <Table>
        <template #header>
          <TableRow>
            <TableHeaderCell style="width: 5%; position: sticky; top: 0; z-index: 1;">
              ลำดับ
            </TableHeaderCell>
            <TableHeaderCell style="width: 15%; position: sticky; top: 0; z-index: 1;">ชื่อ -
              สกุล</TableHeaderCell>
            <TableHeaderCell style="width: 25%; position: sticky; top: 0; z-index: 1;">
              หมายเลขโทรศัพท์</TableHeaderCell>
            <TableHeaderCell style=" width: 25%; position: sticky; top: 0; z-index: 1;">Actions</TableHeaderCell>
          </TableRow>
        </template>
        <template #default>
          <TableRow v-for=" (application, index) in filteredApplications" :key="application.id"
              v-if="hasRole('admin') || hasRole('officer')"
              :class="{ 'bg-green-100': application.user.roles[0]?.name === 'scholar' }">
              <!-- <pre>{{ application.user.roles[0]?.name }}</pre> -->

              <TableDataCell>{{ index + 1 }}</TableDataCell>
              <TableDataCell>
                <template v-if="application.title === '0'">นาย</template>
                <template v-else-if="application.title === '1'">นาง</template>
                <template v-else-if="application.title === '2'">นางสาว</template>
                {{ application.fname }} {{ application.lname }}
              </TableDataCell>
              <TableDataCell>
                {{ getScholarType(application.scholar_type) }} {{ getTypeAbility(application.type_ability) }}
              </TableDataCell>
              <TableDataCell>
                <PrimaryButton v-if="hasRole('student') || hasRole('member')"
                  @click="confirmDeleteApplication(application.id)" class="custom-button-danger mr-4">
                  ลบ
                </PrimaryButton>
                <Link v-if="hasRole('student') || hasRole('member')"
                  :href="route('scholarship_applications.show', application.id)" class="custom-button-success">
                เพิ่มเติม
                </Link>
                <!-- <pre>{{application.user}}</pre> -->
                <div>
                  <EditScholar :user="application.user" :roles="props.roles" />
                </div>
              </TableDataCell>
          </TableRow>
        </template>

      </Table>
      <!-- Pagination Controls -->
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
        <span class="px-4 py-2">หน้า {{ currentPage }} จาก {{ totalPages }}</span>

        <div class="inline-flex space-x-2">
          <button class="px-4 py-2 bg-gray-300 text-gray-800 rounded" :disabled="currentPage === 1"
            @click="changePage(currentPage - 1)">
            ก่อนหน้า
          </button>

          <button v-if="currentPage < totalPages && totalItems > 0" class="px-4 py-2 bg-gray-800 text-gray-100 rounded"
            @click="changePage(currentPage + 1)">
            ถัดไป
          </button>
        </div>
      </div>
      <Modal :show="showConfirmDeleteModal" @close="closeModal">
        <div class="p-6">
          <h2 class="text-lg font-semibold text-slate-800">Confirm Deletion?</h2>
          <div class="mt-6 flex space-x-4">
            <DangerButton @click="deleteApplication">Delete</DangerButton>
            <SecondaryButton @click="closeModal">Cancel</SecondaryButton>
          </div>
        </div>
      </Modal>
    </div>
  </AuthenticatedLayout>
</template>
<style src="vue-multiselect/dist/vue-multiselect.css"></style>
