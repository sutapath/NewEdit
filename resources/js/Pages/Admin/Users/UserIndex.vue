<!-- resources/js/Pages/Admin/Users/UserIndex.vue -->
<script setup>
import { ref, computed ,watch } from "vue";
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
import Swal from "sweetalert2";
import debounce from 'lodash/debounce';

const props = defineProps({
  users: Array,
});
const file = ref(null);

const handleFileUpload = (event) => {
  file.value = event.target.files[0];
};

const submitFile = async () => {
  if (!file.value) return alert("กรุณาเลือกไฟล์ก่อน!");

  const formData = new FormData();
  formData.append("file", file.value);

  try {
    await axios.post(route("import.users"), formData, {
      headers: { "Content-Type": "multipart/form-data" },
    });
    alert("อัปโหลดสำเร็จ!");
  } catch (error) {
    alert("เกิดข้อผิดพลาดในการอัปโหลด");
  }
};
const searchQuery = ref(""); // ค่าค้นหา
const selectedRole = ref(""); // ค่าที่เลือกจาก Select Box

const roles = [
  { value: 'admin', label: 'ผู้ดูแลระบบ' },
  { value: 'officer', label: 'เจ้าหน้าที่' },
  { value: 'student', label: 'นักศึกษา' },
  { value: 'scholar', label: 'ทุนการศึกษา' },
  { value: 'member', label: 'สมาชิก' },
  { value: 'manager', label: 'ผู้จัดการ' },
  { value: 'intlscholar', label: 'ทุนการศึกษานานาชาติ' }
];

const confirmDeleteUser = (id) => {
  Swal.fire({
    title: 'คุณต้องการลบผู้ใช้งานนี้?',
    text: 'การลบจะไม่สามารถย้อนกลับได้!',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#3085d6',
    confirmButtonText: 'ใช่, ลบเลย!',
    cancelButtonText: 'ยกเลิก'
  }).then((result) => {
    if (result.isConfirmed) {
      console.log("User ID to delete:", id);
      userIdToDelete.value = id; 
      deleteUser(); 
    }
  });
}; 
const form = useForm({
  role: "", 
});

const showConfirmDeleteUserModal = ref(false);
const userIdToDelete = ref(null);
const closeModal = () => {
  showConfirmDeleteUserModal.value = false;
};
const deleteUser = () => {
  if (userIdToDelete.value) {
    console.log("Deleting user with ID:", userIdToDelete.value);
    form.delete(route("users.destroy", userIdToDelete.value), {
      onSuccess: () => {
        closeModal();
        Swal.fire({
          title: "สำเร็จ!",
          text: "ลบผู้ใช้งานสำเร็จ",
          icon: "success",
          confirmButtonText: "ตกลง",
          customClass: {
            confirmButton: 'bg-green-500 text-white'
          }
        });
      },
      onError: () => {
        Swal.fire({
          title: "เกิดข้อผิดพลาด",
          text: "ไม่สามารถลบผู้ใช้งานได้",
          icon: "error",
          confirmButtonText: "ลองใหม่อีกครั้ง",
          customClass: {
            confirmButton: 'bg-red-500 text-white'
          }
        });
      }
    });
  } else {
    console.log("No user ID to delete");
  }
};


const currentPage = ref(1);
const perPage = ref(10); 
const totalItems = ref(0); 

const filteredUsers = computed(() => { 
  let results = props.users; 
  if (selectedRole.value) {
    results = results.filter((user) =>
      user.roles.some((role) => role.name === selectedRole.value)
    );
  } 
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    results = results.filter((user) => {
      const fullName = `${user.fname} ${user.lname}`.toLowerCase();
      return (
        fullName.includes(query) ||
        user.name.toLowerCase().includes(query) ||
        (user.email && user.email.toLowerCase().includes(query)) 
      );
    });
  }

  totalItems.value = results.length; 
  const start = (currentPage.value - 1) * perPage.value; 
  const end = start + perPage.value;  
  return results.slice(start, end);   
});  
// Debounce the search function  
const updateFilteredUsers = debounce(() => {
  totalItems.value = filteredUsers.value.length;
}, 300);
watch(perPage, () => {
  currentPage.value = 1;
});
 
watch([currentPage, perPage], () => {
  const maxPage = Math.ceil(totalItems.value / perPage.value);
  if (currentPage.value > maxPage) {
    currentPage.value = 1;
  }
});
const totalPages = computed(() => {
  return Math.ceil(totalItems.value / perPage.value);
});
</script>

<template>

  <Head title="Users Index" />

  <AuthenticatedLayout>
    <div class="max-w-7xl mx-auto py-4 mt-20 px-4 sm:px-10">
      <div class="py-2 px-4 text-white dark:text-gray-100 flex flex-col sm:flex-row justify-between items-center">

        <div class="py-2 text-gray-800 rounded-t-lg text-xl font-bold">ผู้ใช้งาน</div>
        <div class="flex-1 mx-3 hidden sm:block">
          <hr class="border-t border-gray-300" />
        </div>
        <div class="flex items-center space-x-4 sm:space-x-6 mt-3 sm:mt-0">
          <div class="flex items-center space-x-4 sm:space-x-6 mt-3 sm:mt-0">
            <div class="flex items-center space-x-4 sm:space-x-6 mt-3 sm:mt-0">
              <select v-model="selectedRole"
                class="px-3 py-2 border border-gray-300 rounded-lg text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500 w-48 sm:w-48">
                <option value="">ทั้งหมด</option>
                <option v-for="role in roles" :key="role.value" :value="role.value">
                  {{ role.label }}
                </option>
              </select>

              <input v-model="searchQuery" type="text" placeholder="ค้นหาผู้ใช้..."
                class="px-3 py-2 border border-gray-300 rounded-lg text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500 w-48 sm:w-48" />
            </div>
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex space-x-2 mt-3 sm:mt-0 ml-3">
          <!-- <Link :href="route('members.index')" class="custom-button-primary">
          สมาชิก
          </Link>
          <Link :href="route('students.index')" class=" ml-3 custom-button-primary">
          นักศึกษา
          </Link>
          <Link :href="route('intlscholars.index')" class=" ml-3 custom-button-primary">
          นักศึกษาต่างชาติ
          </Link>
          <Link :href="route('intlscholars.index')" class=" ml-3 custom-button-primary">
          เจ้าหน้าที่
          </Link> -->
          <Link :href="route('members.create')" class="custom-button-success">
          เพิ่ม
          </Link>
        </div>
      </div>
    </div>


    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 bg-white sm:rounded-xl mb-20 overflow-x-auto">
      <Table class="min-w-full">
        <template #header>
          <TableRow>
            <TableHeaderCell>#</TableHeaderCell>
            <TableHeaderCell>ชื่อ - นามสกุล</TableHeaderCell>
            <TableHeaderCell>ชื่อผู้ใช้</TableHeaderCell>
            <TableHeaderCell>รหัส</TableHeaderCell>
            <TableHeaderCell>บทบาท</TableHeaderCell>
            <TableHeaderCell>เพิ่มเติม</TableHeaderCell>
          </TableRow>
        </template>
        <template #default>
          <TableRow v-for="(user, index) in filteredUsers" :key="user.id" class="border-b" :class="{
            'bg-admin': user.roles.some((role) => role.name === 'admin'),
  'bg-officer': user.roles.some((role) => role.name === 'officer'),
  'bg-manager': user.roles.some((role) => role.name === 'manager'),
            'text-no-role': user.roles.length === 0,
          }">
            <TableDataCell class="whitespace-nowrap">{{ index + 1 }}</TableDataCell>
            <TableDataCell class="whitespace-nowrap">
              <template v-if="user.title === '0'">นาย</template>
              <template v-else-if="user.title === '1'">นาง</template>
              <template v-else-if="user.title === '2'">นางสาว</template>
              {{ user.fname }} {{ user.lname }}
            </TableDataCell>
            <TableDataCell class="whitespace-nowrap">{{ user.name }}</TableDataCell>
            <TableDataCell class="whitespace-nowrap">
              <pre>{{user.officer_code}}</pre>
              <!-- {{ user.student_code ?? user.officer_code ?? '-' }} -->
              {{ user.id_card || '-' }}
            </TableDataCell>

            <TableDataCell class="whitespace-nowrap">
              {{user.roles.map((role) => role.name).join(", ")}}
            </TableDataCell>
            <TableDataCell>
              <div class="table-cell-icons">
                <Link :href="route('users.edit', user.id)"
                  class="flex items-center justify-center p-1 bg-yellow-100 rounded-full">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-yellow-500" fill="none" viewBox="0 0 24 24"
                  stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round"
                    d="M15.232 5.232l3.536 3.536m-2.768-1.768a2.5 2.5 0 113.536 3.536L8.25 20.25a4 4 0 01-1.768 1.057l-3 1a1 1 0 01-1.293-1.293l1-3a4 4 0 011.057-1.768l10.75-10.75z" />
                </svg>
                </Link>

                <button @click="confirmDeleteUser(user.id)"
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
  <Modal :show="showConfirmDeleteUserModal" @close="closeModal">
    <div class="p-6">
      <h2 class="text-lg font-semibold text-slate-800">
        Are you sure to delete this user?
      </h2>
      <div class="mt-6 flex space-x-4">
        <DangerButton @click="deleteUser">Delete</DangerButton>
        <SecondaryButton @click="closeModal">Cancel</SecondaryButton>
      </div>
    </div>
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
}

.custom-dropdown:focus {
  outline: none;
  border-color: #3b82f6;
}

.mtb {
  margin-bottom: 10rem; /* ใช้ rem สำหรับระยะห่างที่มีความยืดหยุ่น */
}

@media (max-width: 640px) {
  .custom-button-success-outline,
  .custom-button-success {
    width: 100%; /* ปรับให้ปุ่มเต็มความกว้าง */
  }
} 
.table-cell-icons {
  display: flex;
  gap: 0.5rem;
}

.table-cell-nowrap {
  white-space: nowrap;
}
</style>
