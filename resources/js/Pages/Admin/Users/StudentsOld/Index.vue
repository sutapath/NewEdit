<script setup>
import { ref, computed } from "vue";
import { useForm } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import Table from "@/Components/Table.vue";
import TableRow from "@/Components/TableRow.vue";
import TableHeaderCell from "@/Components/TableHeaderCell.vue";
import TableDataCell from "@/Components/TableDataCell.vue";
import Modal from "@/Components/Modal.vue";
import DangerButton from "@/Components/DangerButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import { usePermission } from '@/composables/permissions';
import Edit from "@/Pages/Admin/Users/Students/Edit.vue";

const { hasRole } = usePermission();

const props = defineProps({
  students: {
    type: Array,
    default: () => [],
  },
});
 
const currentPage = ref(1);
const perPage = ref(10);
const totalItems = ref(props.students.length);

const searchQuery = ref("");
const sortedStudents = computed(() => {
  let filteredStudents = [...props.students];

  // Filtering based on search query
  if (searchQuery.value) {
    filteredStudents = filteredStudents.filter(student =>
      student.citizen_id && student.citizen_id.toString().toLowerCase().includes(searchQuery.value.toLowerCase())
    );
  }

  // Sorting by created_at date
  filteredStudents = filteredStudents.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));

  // Pagination
  totalItems.value = filteredStudents.length; // Use filteredStudents here
  if (currentPage.value > Math.ceil(totalItems.value / perPage.value)) {
    currentPage.value = 1;
  }
  const start = (currentPage.value - 1) * perPage.value;
  return filteredStudents.slice(start, start + perPage.value); // Use filteredStudents here
});

const totalPages = computed(() => {
  return Math.ceil(totalItems.value / perPage.value);
});

const showConfirmDeleteUserModal = ref(false);
const showEditUserModal = ref(false);
const userToDelete = ref(null);
const userToEdit = ref(null);

function openDeleteModal(user) {
  userToDelete.value = user;
  showConfirmDeleteUserModal.value = true;
}

function closeDeleteModal() {
  showConfirmDeleteUserModal.value = false;
  userToDelete.value = null;
}

function deleteUser() {
  if (userToDelete.value) {
    this.$inertia.delete(route('students.destroy', userToDelete.value.id));
    closeDeleteModal();
  }
}

function openEditModal(user) {
  userToEdit.value = {
    id: user.id,
    citizen_id: user.citizen_id,
  };
  showEditUserModal.value = true;
}

function closeEditModal() {
  showEditUserModal.value = false;
  userToEdit.value = null;
} 
</script>


<template>

  <Head title="Users Index" />

  <AuthenticatedLayout class="mtb">
    <div class="max-w-7xl mx-auto mb-3 py-4 mt-20 px-10">
      <div class="py-2 px-4 text-white dark:text-gray-100 flex justify-between items-center">
        <div class="py-2 text-gray-800 rounded-t-lg text-xl font-bold">
          ผู้ใช้งาน
        </div>
        <div class="flex-1 mr-3 ml-3">
          <hr class="border-t border-gray-300">
        </div>
        <div class="flex justify-end space-x-4" v-if="hasRole('admin') || hasRole('officer')">
          <div class="flex items-center mb-4">
            <input type="text" v-model="searchQuery" placeholder="ค้นหาจากหมายเลขบัตรประชาชน" class="custom-dropdown" />
          </div>
        </div>
        <div class="flex space-x-4 mb-4 ml-3">
          <Link :href="route('students.create')" class="custom-button-success">
          เพิ่มผู้ใช้
          </Link>
          <button @click="triggerFileInput" class="custom-button-success">นำเข้า</button>
          <input type="file" ref="fileInput" @change="importFile" style="display: none;" />

          <Link :href="route('users.index')" class="custom-button-danger">
          กลับ
          </Link>
        </div>
      </div>
    </div>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 bg-white sm:rounded-xl mb-20">
      <Table class="w-full overflow-x-auto">
        <template #header>
          <TableRow>
            <TableHeaderCell>#</TableHeaderCell>
            <TableHeaderCell>หมายเลขบัตรประชาชน</TableHeaderCell>
            <TableHeaderCell>เพิ่มเติม</TableHeaderCell>
          </TableRow>
        </template>

        <template #default>
          <TableRow v-for="(student, index) in sortedStudents" :key="student.id" class="border-b">
            <TableDataCell>{{ index + 1 }}</TableDataCell>
            <TableDataCell>{{ student.citizen_id }}</TableDataCell>
            <TableDataCell class="flex space-x-2">
              <button @click="openEditModal(student)"
                class="flex items-center justify-center p-1 bg-yellow-100 rounded-full">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-yellow-500" fill="none" viewBox="0 0 24 24"
                  stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round"
                    d="M15.232 5.232l3.536 3.536m-2.768-1.768a2.5 2.5 0 113.536 3.536L8.25 20.25a4 4 0 01-1.768 1.057l-3 1a1 1 0 01-1.293-1.293l1-3a4 4 0 011.057-1.768l10.75-10.75z" />
                </svg>
              </button>

              <button @click="openDeleteModal(student)"
                class="flex items-center justify-center p-1 bg-red-100 rounded-full">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-red-500" fill="none" viewBox="0 0 24 24"
                  stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </TableDataCell>
          </TableRow>
        </template>
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
  </AuthenticatedLayout>

  <!-- Delete Confirmation Modal -->
  <Modal :show="showConfirmDeleteUserModal" @close="closeDeleteModal">
    <div class="p-6">
      <h2 class="text-lg font-semibold text-slate-800">คุณแน่ใจที่จะลบผู้ใช้นี้หรือไม่?</h2>
      <div class="mt-6 flex space-x-4">
        <DangerButton @click="deleteUser">ลบ</DangerButton>
        <SecondaryButton @click="closeDeleteModal">ยกเลิก</SecondaryButton>
      </div>
    </div>
  </Modal>

  <!-- Edit User Modal -->
  <Modal :show="showEditUserModal" @close="closeEditModal">
    <Edit :user="userToEdit" @close="closeEditModal" />
  </Modal>
</template>

<style scoped>
.custom-dropdown {
  padding: 0.5rem;
  border-radius: 0.25rem;
  border: 1px solid #d1d5db;
  background-color: #ffffff;
  cursor: pointer;
  font-size: 1rem;
  width: 100%;
}

.custom-dropdown:focus {
  outline: none;
  border-color: #3b82f6;
}

.mtb {
  margin-bottom: 10rem;
}
</style>
