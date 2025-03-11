<template>

  <Head title="Scholarship activities" />

  <AuthenticatedLayout>
    <div class="max-w-7xl mx-auto py-4 mt-20 px-10">
      <div class="py-2 px-4 text-white dark:text-gray-100 flex flex-col lg:flex-row justify-between items-center">
        <div class="py-2 text-gray-800 rounded-t-lg text-xl font-bold">
          กิจกรรมนักศึกษา
        </div>
        <div class="flex-1 w-full lg:mx-3 mt-2 lg:mt-0">
          <hr class="border-t border-gray-300" />
        </div>
        <div class="flex flex-col lg:flex-row justify-end space-y-4 lg:space-y-0 lg:space-x-4 w-full mt-4 lg:mt-0">
          <div class="w-full lg:w-60">
            <label for="activity_id" class="block text-sm font-medium text-gray-700">เลือกกิจกรรม</label>
            <select id="activity_id" v-model="selectedid"
              class="block w-full mt-1 text-gray-800 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm text-sm">
              <option value="">ทั้งหมด</option>
              <option v-for="activity in props.activities" :key="activity.id" :value="activity.id">
                {{ activity.activity_name }} - {{ activity.organizer }}
              </option>
            </select>
          </div>
          <div class="w-full lg:w-60" v-if="hasRole('admin') || hasRole('officer')">
            <label for="search" class="block text-sm text-gray-700">ค้นหาตามชื่อ</label>
            <input id="search" type="text" v-model="searchQuery" placeholder="กรอกชื่อเพื่อค้นหา"
              class="block w-full mt-1 text-gray-800 border-gray-300 rounded-md shadow-sm focus:border-gray-800 text-sm focus:ring-gray-800 sm:text-sm" />
          </div>
        </div>
      </div>
    </div>


    <div class="max-w-full sm:max-w-7xl mx-auto py-4 sm:px-6 lg:px-8 bg-white sm:rounded-xl overflow-x-auto">
      <form @submit.prevent="submitForm" class="flex flex-col">
        <Table>
          <template #header>
            <TableRow>
              <TableHeaderCell class="px-6 py-3 text-left text-md font-medium uppercase tracking-wider">ลำดับ
              </TableHeaderCell>
              <TableHeaderCell class="px-6 py-3 text-left text-md font-medium uppercase tracking-wider">ชื่อ - สกุล
              </TableHeaderCell>
              <TableHeaderCell class="px-6 py-3 text-left text-md font-medium uppercase tracking-wider">กิจกรรม
              </TableHeaderCell>
              <TableHeaderCell
                v-if="hasRole('admin') || hasRole('officer') || hasRole('scholar') || hasRole('intlscholar')"
                class="px-6 py-3 text-left text-md font-medium uppercase tracking-wider">ผลการตรวจ</TableHeaderCell>
              <TableHeaderCell class="px-6 py-3 text-left text-md font-medium uppercase tracking-wider">การจัดการ
              </TableHeaderCell>
            </TableRow>
          </template>

          <template #default>
            <!-- กรณีมีข้อมูล -->
            <template v-if="paginatedActivities.length > 0">
              <TableRow v-for="(registration, index) in paginatedActivities" :key="registration.id">
                <TableDataCell class="px-6 py-4 whitespace-nowrap text-md font-medium text-gray-900">{{ index + 1 }}
                </TableDataCell>
                <TableDataCell class="px-6 py-4 whitespace-nowrap text-md text-gray-500">
                  <template v-if="registration.title === '0'">นาย</template>
                  <template v-else-if="registration.title === '1'">นาง</template>
                  <template v-else-if="registration.title === '2'">นางสาว</template>
                  {{ registration.fname }} {{ registration.lname }}
                </TableDataCell>
                <TableDataCell class="px-6 py-4 whitespace-nowrap text-md text-gray-500">
                  <template v-if="props.activities.find(activity => activity.id === registration.activity_id)">
                    {{ props.activities.find(activity => activity.id === registration.activity_id).activity_name }}
                  </template>
                </TableDataCell>
                <TableDataCell
                  v-if="hasRole('admin') || hasRole('officer') || hasRole('scholar') || hasRole('intlscholar')"
                  class="px-6 py-4 whitespace-nowrap text-sm">
                  <template v-if="hasActivitySave(registration.id)">
                    <span :class="statusClass(getActivitySave(registration.id).result)">
                      <template v-if="getActivitySave(registration.id).result === '1'">เรียบร้อย</template>
                      <template v-else-if="getActivitySave(registration.id).result === '4'">แก้ไข</template>
                      <template v-else-if="getActivitySave(registration.id).result === '2'">รอการตรวจสอบ</template>
                      <template v-else-if="getActivitySave(registration.id).result === '3'">ไม่ผ่านกิจกรรม</template>
                    </span>
                  </template>
                </TableDataCell>
                <TableDataCell class="px-6 py-4 whitespace-nowrap text-md">
                  <div class="flex items-center space-x-2">
                    <template v-if="registration.user_id === props.currentUser.id">
                      <Link
                        :href="hasActivitySave(registration.id) ? route('activity_saves.edit', { id: getMatchingActivitySaveId(registration.id) }) : route('activity_saves.create', { regis_id: registration.id })"
                        :class="hasActivitySave(registration.id) ? 'bg-yellow-100' : 'bg-green-100'"
                        class="text-white font-bold py-1 px-2 rounded-full">
                      <template v-if="hasActivitySave(registration.id)">
                        <svg class="h-6 w-6 text-orange-500" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                          fill="none" stroke-linecap="round" stroke-linejoin="round">
                          <path stroke="none" d="M0 0h24v24H0z" />
                          <path d="M9 7h-3a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-3" />
                          <path d="M9 15h3l8.5 -8.5a1.5 1.5 0 0 0 -3 -3l-8.5 8.5v3" />
                          <line x1="16" y1="5" x2="19" y2="8" />
                        </svg>
                      </template>
                      <template v-else>
                        <svg class="h-6 w-6 text-green-700" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                          stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                          <circle cx="12" cy="12" r="10" />
                          <line x1="12" y1="8" x2="12" y2="16" />
                          <line x1="8" y1="12" x2="16" y2="12" />
                        </svg>
                      </template>
                      </Link>
                    </template>
                    <div v-if="hasActivitySave(registration.id)">
                      <Link :href="route('activity_saves.show', { save: getMatchingActivitySaveId(registration.id) })">
                      <svg class="h-8 w-8 text-indigo-700 bg-indigo-100 py-1 px-2 rounded-full" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                      </svg>
                      </Link>
                    </div>
                    <div>
                      <button v-if="registration.user_id === props.currentUser.id">
                        <svg @click="confirmDeleteActivity(registration.id)"
                          class="h-8 w-8 py-1 mt-1 px-2 text-red-500 bg-red-100 rounded-2xl" width="24" height="24"
                          viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                          stroke-linejoin="round">
                          <path stroke="none" d="M0 0h24v24H0z" />
                          <line x1="4" y1="7" x2="20" y2="7" />
                          <line x1="10" y1="11" x2="10" y2="17" />
                          <line x1="14" y1="11" x2="14" y2="17" />
                          <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                          <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
                        </svg>
                      </button>
                    </div>
                  </div>
                </TableDataCell>
              </TableRow>
            </template>

            <!-- กรณีไม่มีข้อมูล -->
            <TableRow v-else>
              <TableDataCell colspan="5" class="px-6 py-4 text-center text-md text-gray-500">
                ไม่มีข้อมูล
              </TableDataCell>
            </TableRow>
          </template>
        </Table>

      </form>
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


          <button class="px-4 py-2 bg-gray-800 text-gray-100 rounded" :disabled="currentPage === totalPages"
            @click="currentPage++">
            ถัดไป
          </button>
        </div>
      </div>


      <!-- Modal for Confirming Deletion -->
      <Modal :show="showConfirmDeleteActivityModal" @close="closeModal">
        <div class="p-6">
          <h2 class="text-lg font-semibold text-slate-800">
            คุณแน่ใจหรือไม่ว่าต้องการลบกิจกรรมนี้?
          </h2>
          <div class="mt-6 flex space-x-4">
            <DangerButton class="custom-button-danger" @click="deleteActivity">
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
import { useForm } from '@inertiajs/vue3';
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { usePermission } from "@/composables/permissions";
import Modal from '@/Components/Modal.vue';

import Table from "@/Components/Table.vue";
import TableRow from "@/Components/TableRow.vue";
import TableHeaderCell from "@/Components/TableHeaderCell.vue";
import TableDataCell from "@/Components/TableDataCell.vue";
import DangerButton from '@/Components/DangerButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import Swal from 'sweetalert2';  // เพิ่มการ import SweetAlert2

const { hasRole } = usePermission();
const selectedid = ref('');
const searchQuery = ref('');
const props = defineProps({
  activities: Array,
  currentUser: Object,
  registrations: Array,
  activitySaves: {
    type: Array,
    default: () => []
  },
  activityRegisters: {
    type: Array,
    default: () => []
  }
});
const form = useForm({});
const currentPage = ref(1);
const perPage = ref(10);
const totalItems = ref(props.registrations.length);

const showConfirmDeleteActivityModal = ref(false);
const selectedActivityId = ref(null);

const confirmDeleteActivity = (id) => {
  Swal.fire({
    title: 'คุณแน่ใจหรือไม่?',
    text: "คุณจะไม่สามารถกู้คืนกิจกรรมนี้ได้!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'ใช่, ลบเลย!',
    cancelButtonText: 'ยกเลิก',
    confirmButtonColor: '#d33', // กำหนดสีของปุ่มยืนยัน
    cancelButtonColor: '#3085d6', // กำหนดสีของปุ่มยกเลิก
    reverseButtons: true
  }).then((result) => {
    if (result.isConfirmed) {
      // ถ้ายืนยันการลบให้เรียกฟังก์ชัน deleteActivity
      deleteActivity(id);
    }
  });
};

const closeModal = () => {
  showConfirmDeleteActivityModal.value = false;
  selectedActivityId.value = null;
};

const deleteActivity = (id) => {
  if (id) {
    form.delete(route('activity_registrations.destroy', id), {
      onSuccess: () => {
        Swal.fire({
          title: 'ลบสำเร็จ!',
          text: 'กิจกรรมของคุณถูกลบแล้ว.',
          icon: 'success', // ใช้ icon ที่ถูกต้อง
          confirmButtonText: 'ตกลง',
          confirmButtonColor: '#3085d6'
        });
        closeModal();  // ปิด modal หลังจากการลบ
      },
      onError: () => {
        Swal.fire({
          title: 'เกิดข้อผิดพลาด!',
          text: 'ไม่สามารถลบกิจกรรมได้.',
          icon: 'error',  // ใช้ icon ที่ถูกต้อง
          confirmButtonText: 'ตกลง',
          confirmButtonColor: '#d33'
        });
      }
    });
  }
};


const paginatedActivities = computed(() => {
  let results = props.registrations;

  // กรองข้อมูลตาม selectedid
  if (selectedid.value) {
    results = results.filter(registration => registration.activity_id === selectedid.value);
  }

  // กรองข้อมูลตาม searchQuery
  if (searchQuery.value) {
    results = results.filter(registration =>
      `${registration.fname} ${registration.lname}`.toLowerCase().includes(searchQuery.value.toLowerCase())
    );
  }

  // อัปเดต totalItems ตามผลลัพธ์ที่กรองแล้ว
  totalItems.value = results.length;

  // คำนวณข้อมูลตามหน้าปัจจุบัน
  const start = (currentPage.value - 1) * perPage.value;
  return results.slice(start, start + perPage.value);
});

// คำนวณจำนวนหน้าทั้งหมดใหม่ทุกครั้งที่ totalItems เปลี่ยน
const totalPages = computed(() => {
  return Math.ceil(totalItems.value / perPage.value);
});

const hasActivitySave = (registration_id) => props.activitySaves.some(save => save.regis_id === registration_id);
const getActivitySave = (registration_id) => props.activitySaves.find(save => save.regis_id === registration_id);
const getMatchingActivitySaveId = (registration_id) => getActivitySave(registration_id)?.id || null;
const statusClass = (status) => {
  switch (status) {
    case '1':
      return 'bg-green-200 rounded-2xl p-1 w-full';
    case '4':
      return 'bg-yellow-200 rounded-2xl p-1 w-full';
    case '2':
      return 'bg-gray-200  rounded-2xl p-1 w-full';
    case '3':
      return 'bg-red-200  rounded-2xl p-1 w-full';
    default:
      return 'rounded-full p-1 w-full';
  }
};  
</script>
