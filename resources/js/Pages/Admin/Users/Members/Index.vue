<script setup>
import Swal from 'sweetalert2';
import { ref, computed } from "vue";
import { useForm, router } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import Table from "@/Components/Table.vue";
import TableRow from "@/Components/TableRow.vue";
import TableHeaderCell from "@/Components/TableHeaderCell.vue";
import TableDataCell from "@/Components/TableDataCell.vue";
import { usePermission } from '@/composables/permissions';
import Edit from "@/Pages/Admin/Users/Members/Edit.vue";
import Modal from "@/Components/Modal.vue";
const { hasRole } = usePermission();
const props = defineProps({
  members: {
    type: Array,
    default: () => [],
  },
});

const searchQuery = ref("");
const showEditUserModal = ref(false);
const userToEdit = ref(null);

// ฟังก์ชันแปลงค่า title
const getTitle = (title) => {
  const titles = {
    0: "นาย",
    1: "นาง",
    2: "นางสาว"
  };
  return titles[title] || "ไม่ระบุ";
};
const currentPage = ref(1);
const perPage = ref(10);
const totalItems = ref(props.members.length);

const sortedMembers = computed(() => {
  let filteredMembers = [...props.members];

  if (searchQuery.value) {
    filteredMembers = filteredMembers.filter(member =>
      member.citizen_id && member.citizen_id.toString().toLowerCase().includes(searchQuery.value.toLowerCase())
    );
  }

  filteredMembers.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));

  // Pagination
  totalItems.value = filteredMembers.length;
  const totalPages = Math.ceil(totalItems.value / perPage.value);

  if (currentPage.value > totalPages) {
    currentPage.value = 1;
  }

  const start = (currentPage.value - 1) * perPage.value;
  return filteredMembers.slice(start, start + perPage.value);
});


// ฟังก์ชันสำหรับลบผู้ใช้
function deleteUser(user) {
  Swal.fire({
    title: 'คุณแน่ใจที่จะลบผู้ใช้นี้หรือไม่?',
    text: "การกระทำนี้ไม่สามารถย้อนกลับได้!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'ลบ',
    cancelButtonText: 'ยกเลิก',
    customClass: {
      confirmButton: 'bg-blue-500 text-white hover:bg-blue-600',
      cancelButton: 'bg-red-500 text-white hover:bg-red-600'
    }
  }).then((result) => {
    if (result.isConfirmed) {
      router.delete(route("members.destroy", user.id), {
        onSuccess: () => {
          Swal.fire({
            icon: 'success',
            title: 'ลบผู้ใช้สำเร็จ!',
            text: 'ผู้ใช้ถูกลบออกจากระบบแล้ว',
            confirmButtonText: 'ปิด',
            customClass: {
              confirmButton: 'bg-green-500 text-white hover:bg-green-600'
            }
          });
        },
        onError: (error) => {
          Swal.fire({
            icon: 'error',
            title: 'เกิดข้อผิดพลาดในการลบผู้ใช้!',
            text: 'กรุณาลองใหม่อีกครั้ง',
            confirmButtonText: 'ปิด',
            customClass: {
              confirmButton: 'bg-gray-500 text-white hover:bg-gray-600'
            }
          });
          console.error("There was an error deleting the user:", error);
        },
      });
    }
  });
}

// ฟังก์ชันเปิด modal สำหรับการแก้ไข
function openEditModal(user) {
  userToEdit.value = user;
  showEditUserModal.value = true;
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
          <div class="flex items-center mb-4 text-gray-800">
            <input type="text" v-model="searchQuery" placeholder="ค้นหาข้อมูล" class="custom-dropdown" />
          </div>
        </div>
        <div class="flex space-x-4 mb-4 ml-3">
          <Link :href="route('members.create')" class="custom-button-success">
          เพิ่ม
          </Link>
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
            <TableHeaderCell>ชื่อ - นามสกุล</TableHeaderCell>
            <TableHeaderCell>รหัสสอบ</TableHeaderCell>
            <TableHeaderCell>เพิ่มเติม</TableHeaderCell>
          </TableRow>
        </template>

        <template #default>
          <TableRow v-for="(member, index) in sortedMembers" :key="member.id" class="border-b">
            <TableDataCell>{{ index + 1 }}</TableDataCell>
            <TableDataCell class="table-cell-nowrap">{{ member.citizen_id }}</TableDataCell>
            <TableDataCell class="table-cell-nowrap">
              {{ getTitle(member.title) }} {{ member.fname }} {{ member.lname }}
            </TableDataCell>
            <TableDataCell class="table-cell-nowrap">{{ member.code }}</TableDataCell>
            <TableDataCell>
              <div class="table-cell-icons">
                <button @click="openEditModal(member)"
                  class="flex items-center justify-center p-1 bg-yellow-100 rounded-full">
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-yellow-500" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                      d="M15.232 5.232l3.536 3.536m-2.768-1.768a2.5 2.5 0 113.536 3.536L8.25 20.25a4 4 0 01-1.768 1.057l-3 1a1 1 0 01-1.293-1.293l1-3a4 4 0 011.057-1.768l10.75-10.75z" />
                  </svg>
                </button>

                <button @click="deleteUser(member)"
                  class="flex items-center justify-center p-1 bg-red-100 rounded-full">
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-red-500" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </div>
            </TableDataCell>
          </TableRow>
        </template>
      </Table>

      <!-- Pagination -->
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

  <Modal :show="showEditUserModal">
    <Edit :user="userToEdit" />
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

.table-cell-nowrap {
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.table-cell-icons {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 8px;
}
</style>
