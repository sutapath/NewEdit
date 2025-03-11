<template>

  <Head title="Token Line Notify" />

  <AuthenticatedLayout>
    <div class="max-w-7xl mx-auto mb-3 py-4 mt-20 px-10">
      <div class="flex justify-between items-center">
        <div class="py-2 mr-3 text-gray-800 rounded-t-lg text-xl font-bold">
          Token Line Notify
        </div>
        <div class="flex-1">
          <hr class="border-t border-gray-300" />
        </div>
        <PrimaryButton class="custom-button-success ml-5">
          <a href="/assets/line-token.pdf" target="_blank" class="no-underline">
            วิธีขอ token
          </a>
        </PrimaryButton>
        <!-- ปุ่มเปิด modal สำหรับการสร้างการแจ้งเตือน -->
        <PrimaryButton @click="openCreateNotifyModal" class="custom-button-primary ml-4">
          เพิ่มช่องทาง
        </PrimaryButton>
      </div>
    </div>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 bg-white sm:rounded-xl mb-20 table-container">
      <div class="overflow-x-auto">
        <Table class="min-w-full">
          <template #header>
            <TableRow>
              <TableHeaderCell>#</TableHeaderCell>
              <TableHeaderCell>Token</TableHeaderCell>
              <TableHeaderCell>Created At</TableHeaderCell>
              <TableHeaderCell>Actions</TableHeaderCell>
            </TableRow>
          </template>
          <template #default>
            <TableRow v-for="(notification, index) in props.notifications" :key="notification.id" class="border-b">
              <TableDataCell class="text-xs md:text-sm table-cell">{{ index + 1 }}</TableDataCell>
              <TableDataCell class="text-xs md:text-sm table-cell">{{ notification.line_notify_token }}</TableDataCell>
              <TableDataCell class="text-xs md:text-sm table-cell">{{ new
                Date(notification.created_at).toLocaleDateString() }}</TableDataCell>
              <TableDataCell>
                <PrimaryButton @click="confirmDeleteLine(notification.id)" class="custom-button-danger">ลบ
                </PrimaryButton>
              </TableDataCell>
            </TableRow>
          </template>
        </Table>
      </div>
    </div>

    <!-- Modal สำหรับการสร้างการแจ้งเตือน -->
    <Modal :show="showCreateNotifyModal" @close="closeModal">
      <UserLineNotifyCreate />
    </Modal>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Table from '@/Components/Table.vue';
import TableRow from '@/Components/TableRow.vue';
import TableHeaderCell from '@/Components/TableHeaderCell.vue';
import TableDataCell from '@/Components/TableDataCell.vue';
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { usePermission } from '@/composables/permissions';
import UserLineNotifyCreate from '@/Pages/UserLineNotify/Create.vue';
import Swal from 'sweetalert2';

const { hasRole } = usePermission();
const props = defineProps({
  notifications: Array,
});

const form = useForm({});

const showCreateNotifyModal = ref(false); // State สำหรับ modal การสร้าง
const LineToDelete = ref(null);

// ฟังก์ชันเปิด modal การสร้าง
const openCreateNotifyModal = () => {
  showCreateNotifyModal.value = true;
};

// ฟังก์ชันเปิด SweetAlert2 สำหรับการยืนยันการลบ
const confirmDeleteLine = (id) => {
  LineToDelete.value = id;
  Swal.fire({
    title: 'คุณแน่ใจหรือไม่?',
    text: 'ข้อมูลนี้จะถูกลบไปอย่างถาวร!',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'ลบ',
    cancelButtonText: 'ยกเลิก',
    confirmButtonColor: '#d33',
    cancelButtonColor: '#3085d6',
  }).then((result) => {
    if (result.isConfirmed) {
      deleteLine(); // เรียกฟังก์ชันลบข้อมูลถ้าเลือก "ลบ"
    }
  });
};

// ฟังก์ชันลบข้อมูล
const deleteLine = () => {
  if (LineToDelete.value) {
    // แสดง Swal แจ้งเตือนการกำลังทำงาน
    Swal.fire({
      title: 'กำลังดำเนินการ...',
      text: 'กรุณารอสักครู่ ข้อมูลกำลังถูกลบ',
      icon: 'info',
      showConfirmButton: false,
      allowOutsideClick: false, // ป้องกันไม่ให้ผู้ใช้คลิกนอก Swal
      didOpen: () => {
        Swal.showLoading(); // แสดงโหลด
      }
    });

    form.delete(route('user_line_notify.destroy', LineToDelete.value), {
      onSuccess: () => {
        Swal.fire({
          title: 'ลบแล้ว!',
          text: 'ข้อมูลของคุณได้ถูกลบเรียบร้อยแล้ว',
          icon: 'success',
          confirmButtonText: 'ตกลง',
          confirmButtonColor: '#4CAF50',
          background: '#f4f6f9',
          iconColor: '#388e3c',
          showCloseButton: true,
          timer: 3000,
          customClass: {
            title: 'text-lg font-semibold text-gray-800',
            content: 'text-sm text-gray-600',
          },
        });
      },
      onError: (error) => {
        Swal.fire({
          title: 'เกิดข้อผิดพลาด!',
          text: 'ไม่สามารถลบข้อมูลได้ กรุณาลองใหม่อีกครั้ง',
          icon: 'error',
          confirmButtonText: 'ตกลง',
          confirmButtonColor: '#f44336',
          background: '#f4f6f9',
          iconColor: '#d32f2f',
          showCloseButton: true,
          customClass: {
            title: 'text-lg font-semibold text-gray-800',
            content: 'text-sm text-gray-600',
          },
        });
        console.error('Error deleting notification:', error);
      },
    });
  }
};

</script>

<style scoped>
.table-container {
  overflow-x: auto;
  /* เพื่อให้สามารถเลื่อนตารางได้เมื่อหน้าจอเล็ก */
}

.table-cell {
  white-space: nowrap;
  /* ป้องกันไม่ให้ข้อความตัดบรรทัด */
  text-overflow: ellipsis;
  /* เพิ่ม ... เมื่อข้อความยาวเกิน */
  overflow: hidden;
  /* ซ่อนข้อความที่เกิน */
}
</style>
