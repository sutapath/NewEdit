<script setup>
import { watch, ref, computed } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import Table from "@/Components/Table.vue";
import TableRow from "@/Components/TableRow.vue";
import TableHeaderCell from "@/Components/TableHeaderCell.vue";
import TableDataCell from "@/Components/TableDataCell.vue";
import Modal from "@/Components/Modal.vue";
import DangerButton from "@/Components/DangerButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { usePermission } from "@/composables/permissions";
import Swal from "sweetalert2"; // เพิ่มการ import SweetAlert2
import { router } from "@inertiajs/vue3";

const { hasRole } = usePermission();
const props = defineProps({
  docsSends: Array,
  publicInfos: Array,
});
const form = useForm({});
const currentPage = ref(1);
const perPage = ref(10);
const totalItems = ref(props.docsSends.length);

const resetPage = ref(false);
const showConfirmDeleteDocsModal = ref(false);
const selectedDocumentType = ref("all");
const DocsIdToDelete = ref(null);
import moment from "moment";

const documentModal = ref(false);
const documentFile = ref(null);

const showDocument = (file) => {
  documentFile.value = `storage/files/${file}`;
  documentModal.value = true;
};

const selectedYear = ref("all");

const yearOptions = [...new Set(props.docsSends.map((docsSends) => docsSends.years))];

const openLineNotifyModal = () => {
  lineNotifyModal.value = true;
};

const lineNotifyModal = ref(false);
const confirmDeleteDocs = (id) => {
  DocsIdToDelete.value = id;
  Swal.fire({
    title: "คุณแน่ใจหรือไม่?",
    text: "หากคุณลบเอกสารนี้แล้ว จะไม่สามารถกู้คืนได้",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "ยืนยัน",
    cancelButtonText: "ยกเลิก",
    confirmButtonColor: "#d33",
    cancelButtonColor: "#3085d6",
  }).then((result) => {
    if (result.isConfirmed) {
      deleteDocs();
    } else {
      Swal.fire({
        icon: "info",
        title: "การลบถูกยกเลิก",
        text: "เอกสารของคุณยังคงอยู่",
        timer: 1500,
        showConfirmButton: false,
      });
    }
  });
};
const selectedResult = ref("all");
const filteredDocsSends = computed(() => {
  if (!props.docsSends) return [];

  const results = props.docsSends.filter((docsSends) => {
    const matchesType =
      selectedDocumentType.value === "all" ||
      (selectedDocumentType.value === "transcripts" && docsSends.transcripts) ||
      (selectedDocumentType.value === "operat_docs" && docsSends.operat_docs);
    const matchesYear =
      selectedYear.value === "all" || docsSends.years === selectedYear.value;
    const matchesResult =
      selectedResult.value === "all" || docsSends.result === selectedResult.value;

    return matchesType && matchesYear && matchesResult; // รวมเงื่อนไขทั้งหมดในการกรอง
  });

  totalItems.value = results.length;
  const start = (currentPage.value - 1) * perPage.value;
  const end = start + perPage.value;

  return results.slice(start, end);
});

const formatDate = (date) => {
  if (!date) return "";
  const thaiYear = moment(date).year() + 543;
  return moment(date).format(`DD/MM/${thaiYear}`);
};
// const filteredDocsSends = computed(() => {
//   // ตรวจสอบ props.docsSends ว่ามีข้อมูลหรือไม่
//   if (!props.docsSends) return [];

//   // กรองข้อมูลทั้งหมดตามเงื่อนไขที่กำหนดจากข้อมูลทั้งหมด
//   const results = props.docsSends.filter(docsSends => {
//     const matchesType = selectedDocumentType.value === 'all' ||
//       (selectedDocumentType.value === 'transcripts' && docsSends.transcripts) ||
//       (selectedDocumentType.value === 'operat_docs' && docsSends.operat_docs);
//     const matchesYear = selectedYear.value === 'all' || docsSends.years === selectedYear.value;
//     return matchesType && matchesYear;
//   });
//   const order = ["2", "1", "0"];
//   const sortedResults = results.sort((a, b) => {
//     const aResult = a.result;
//     const bResult = b.result;
//     return order.indexOf(aResult) - order.indexOf(bResult);
//   });

//   totalItems.value = sortedResults.length;
//   const start = (currentPage.value - 1) * perPage.value;
//   const end = start + perPage.value;
//   return sortedResults.slice(start, end);
// });

watch(
  [() => props.docsSends, () => selectedDocumentType.value, () => selectedYear.value],
  () => {
    currentPage.value = 1;
  }
);
// คำนวณจำนวนหน้าทั้งหมด
const totalPages = computed(() => {
  return Math.ceil(totalItems.value / perPage.value);
});
const closeModal = () => {
  showConfirmDeleteDocsModal.value = false;
  DocsIdToDelete.value = null;
};

const statusClass = (result) => {
  switch (result) {
    case "0":
      return "bg-green-300 text-dark py-1 px-3 rounded-full";
    case "1":
      return "bg-yellow-300 text-dark py-1 px-3 rounded-full";
    case "2":
      return "bg-gray-300 text-dark py-1 px-3 rounded-full";
    default:
      return "";
  }
};
const handleDelete = async (publishId) => {
  try {
    const result = await Swal.fire({
      title: "คุณแน่ใจไหม?",
      text: "คุณต้องการลบข้อมูลนี้หรือไม่?",
      icon: "warning",
      showCancelButton: true,
      confirmButtonText: "ใช่, ลบ!",
      cancelButtonText: "ยกเลิก",
      reverseButtons: true,
      confirmButtonColor: "#FF4D4D", // สีปุ่มยืนยัน (สีแดง)
      cancelButtonColor: "#4A90E2", // สีปุ่มยกเลิก (สีน้ำเงิน)
      buttonsStyling: true, // ใช้การกำหนดสีของปุ่มโดยตรง
    });

    if (result.isConfirmed) {
      form.delete(route("publish_requests.delete", publishId), {
        onSuccess: () => {
          Swal.fire({
            title: "สำเร็จ!",
            text: "ลบข้อมูลสำเร็จ",
            icon: "success",
            confirmButtonColor: "#28A745", // สีเขียวสำหรับปุ่ม OK
          });
        },
        onError: () => {
          Swal.fire({
            title: "เกิดข้อผิดพลาด",
            text: "ไม่สามารถลบข้อมูลได้",
            icon: "error",
            confirmButtonColor: "#FF4D4D", // สีแดงสำหรับปุ่ม OK
          });
        },
      });
    }
  } catch (error) {
    console.error("Error deleting publish info:", error);
    Swal.fire({
      title: "เกิดข้อผิดพลาด",
      text: "ไม่สามารถดำเนินการได้",
      icon: "error",
      confirmButtonColor: "#FF4D4D",
    });
  }
};
const deleteDocs = () => {
  if (DocsIdToDelete.value) {
    form.delete(route("docs_sends.destroy", DocsIdToDelete.value), {
      onSuccess: () => {
        closeModal(); // ปิด modal หลังจากลบ
        Swal.fire({
          icon: "success",
          title: "ลบเอกสารเรียบร้อย",
          text: "เอกสารถูกลบออกจากระบบแล้ว",
          timer: 1500,
          showConfirmButton: false,
        });
      },
      onError: (error) => {
        console.error("Error deleting document:", error);
        Swal.fire({
          icon: "error",
          title: "เกิดข้อผิดพลาด",
          text: "ไม่สามารถลบเอกสารได้ โปรดลองใหม่อีกครั้ง",
          showConfirmButton: true,
        });
      },
    });
  } else {
    // หากกด "ยกเลิก"
    Swal.fire({
      icon: "info",
      title: "การลบถูกยกเลิก",
      text: "เอกสารของคุณยังคงอยู่",
      timer: 1500,
      showConfirmButton: false,
    });
  }
};

const goToEditPage = (id) => {
  router.get(route("publish_requests.show", id));
};
// const selectedUserIds = ref([]);
// const toggleUserSelection = (userId) => {
//   console.log("Checkbox clicked for User ID:", userId);
//   console.log("Current selectedUserIds:", selectedUserIds.value); // ใช้ .value เพื่อเข้าถึงค่าของ ref

//   if (selectedUserIds.value.includes(userId)) {
//     selectedUserIds.value = selectedUserIds.value.filter((id) => id !== userId);
//   } else {
//     selectedUserIds.value.push(userId);
//   }

//   console.log("Updated selectedUserIds:", selectedUserIds.value); // แสดงค่าใหม่หลังจากการเลือก
// };

// const confirmAndProcessUser = async () => {
//   if (selectedUserIds.value.length === 0) {
//     Swal.fire({
//       icon: "info",
//       title: "ไม่พบผู้ใช้",
//       text: "กรุณาเลือกผู้ใช้ก่อนที่จะทำการดำเนินการ",
//       confirmButtonText: "ตกลง",
//       confirmButtonColor: "#87CEEB",
//     });
//     return;
//   }

//   for (const userId of selectedUserIds.value) {
//     const result = await Swal.fire({
//       title: "ยืนยันการทำรายการ",
//       text: `ต้องการเปลี่ยนสถานะผู้ใช้งานหรือไม่`,
//       icon: "warning",
//       showCancelButton: true,
//       confirmButtonText: "ใช่",
//       cancelButtonText: "ไม่",
//       confirmButtonColor: "#FF5733",
//       cancelButtonColor: "#33B5FF",
//     });

//     if (result.isConfirmed) {
//       try {
//         const roleData = [{ id: 4, name: "student" }];
//    const response = await axios.put(route("users.updatescholar", { user: userId }), {
//   user: userId,
//   roles: roleData,
//   _method: 'PUT', // In case you're using Laravel's method spoofing for PUT requests
// });

//         const form = useForm({
//           result: 5,
//         });
//         // axios.put(route("docs_sends.check", { docSend: userId }), {
//         //   form,
//         // });

//         Swal.fire({
//           icon: "success",
//           title: "สำเร็จ!",
//           text: `ผู้ใช้อัปเดตเรียบร้อยแล้ว`,
//           confirmButtonColor: "#4CAF50",
//           cancelButtonColor: "#FF5733",
//         });
//       } catch (error) {
//         Swal.fire({
//           icon: "error",
//           title: "เกิดข้อผิดพลาด",
//           text: `ไม่สามารถอัปเดตผู้ใช้ได้`,
//           confirmButtonColor: "#FF5733",
//           cancelButtonColor: "#FFB200",
//         });
//         console.error("Error updating user:", error);
//       }
//     }
//   }
//   selectedUserIds.value = [];
// };
</script>

<template>
  <Head title="Docs Index" />

  <AuthenticatedLayout>
    <div class="max-w-7xl mx-auto mb-3 py-4 mt-20 px-10">
      <div class="flex justify-between items-center">
        <div class="py-2 mr-3 text-gray-800 rounded-t-lg text-xl font-bold">
          การส่งเอกสาร
        </div>
        <div class="flex-1">
          <hr class="border-t border-gray-300" />
        </div>
        <Link
          v-if="hasRole('scholar') || hasRole('intlscholar')"
          :href="route('docs_sends.create')"
          class="custom-button-success ml-4"
        >
          เพิ่มข้อมูล
        </Link>
        <div v-if="hasRole('admin') || hasRole('officer')">
          <Link
            :href="route('publish_requests.create')"
            class="custom-button-success ml-4"
            >แจ้งกำหนดการ</Link
          >
        </div>
        <!-- <PrimaryButton
          v-if="hasRole('admin') || hasRole('officer')"
          class="custom-button-primary ml-4"
          @click="confirmAndProcessUser"
          >อัปเดตสถานะ</PrimaryButton
        > -->
      </div>
    </div>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 bg-white sm:rounded-xl mb-5">
      <div class="px-4 flex justify-end space-x-6">
        <div class="relative w-1/3">
          <label
            for="docType"
            class="block text-gray-700 dark:text-gray-200 text-md font-semibold mb-2"
            >ประเภทเอกสาร:</label
          >
          <select
            v-model="selectedDocumentType"
            id="docType"
            class="mt-1 block w-full p-2 border border-gray-300 rounded-md text-md"
          >
            <option value="all">ทั้งหมด</option>
            <option value="transcripts">เอกสารแสดงผลการเรียน</option>
            <option value="operat_docs">แบบบันทึกการดำเนินงาน</option>
          </select>
        </div>
        <div class="relative w-1/3">
          <label
            for="yearSelect"
            class="block text-gray-700 dark:text-gray-200 text-md font-semibold mb-2"
            >ปีการศึกษา:</label
          >
          <select
            v-model="selectedYear"
            id="yearSelect"
            class="mt-1 block w-full p-2 border border-gray-300 rounded-md text-md"
          >
            <option value="all">ทั้งหมด</option>
            <option v-for="year in yearOptions" :key="year" :value="year">
              {{ year }}
            </option>
          </select>
        </div>
        <div class="relative w-1/3">
          <label
            for="resultSelect"
            class="block text-gray-700 dark:text-gray-200 text-md font-semibold mb-2"
            >สถานะ:</label
          >
          <select
            v-model="selectedResult"
            id="resultSelect"
            class="mt-1 block w-full p-2 border border-gray-300 rounded-md text-md"
          >
            <option value="all">ทั้งหมด</option>
            <option value="0">ผ่าน</option>
            <option value="1">แก้ไข</option>
            <option value="2">รอตรวจ</option>
          </select>
        </div>
      </div>
    </div>
    <div
      class="max-w-7xl mx-auto mb-3 py-2 px-5 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-3 gap-4"
    >
      <div v-for="publish in props.publicInfos" :key="publish.id">
        <div class="bg-white p-6 rounded-lg shadow-lg flex justify-between items-start">
          <div>
            <!-- ใช้ v-if เพื่อเช็ค document_type -->
            <h3 class="text-xl font-bold text-gray-800">
              <span v-if="publish.document_type === 'transcripts'"
                >เอกสารแสดงผลการเรียน</span
              >
              <span v-else-if="publish.document_type === 'operat_docs'"
                >แบบบันทึกการดำเนินงาน</span
              >
              <span v-else>เอกสารอื่นๆ</span>
            </h3>
            <p class="text-gray-600 text-lg">
              กำหนดส่ง {{ formatDate(publish.send_date) }}
            </p>
          </div>
          <div class="ml-4 space-x-2">
            <!-- Add space between buttons -->
            <PrimaryButton class="custom-button-danger" @click="handleDelete(publish.id)">
              ลบ
            </PrimaryButton>
            <PrimaryButton
              class="custom-button-warning"
              @click="goToEditPage(publish.id)"
            >
              แก้ไข
            </PrimaryButton>
          </div>
        </div>
      </div>
    </div>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 bg-white sm:rounded-xl mb-20">
      <div class="overflow-x-auto">
        <Table class="min-w-full">
          <template #header>
            <TableRow>
              <!-- <TableHeaderCell v-if="hasRole('admin') || hasRole('officer')">
              </TableHeaderCell> -->
              <TableHeaderCell>#</TableHeaderCell>
              <TableHeaderCell>ชื่อ - นามสกุล</TableHeaderCell>
              <TableHeaderCell>ปีการศึกษา</TableHeaderCell>
              <TableHeaderCell>เอกสาร</TableHeaderCell>
              <TableHeaderCell>สถานะ</TableHeaderCell>
              <TableHeaderCell>เพิ่มเติม</TableHeaderCell>
            </TableRow>
          </template>
          <template #default>
            <TableRow
              v-for="(docsSends, index) in filteredDocsSends"
              :key="docsSends.id"
              class="border-b"
            >
              <!-- <TableDataCell
                class="text-xs md:text-sm whitespace-nowrap"
                v-if="hasRole('admin') || hasRole('officer')"
              >
                <template v-if="docsSends.result == '0'">
                  <input
                    type="checkbox"
                    :value="docsSends.user_id"
                    v-model="selectedUserIds"
                  />
                </template>
              </TableDataCell> -->

              <TableDataCell class="text-xs md:text-sm whitespace-nowrap">
                {{ index + 1 }}</TableDataCell
              >
              <TableDataCell class="text-xs md:text-sm whitespace-nowrap">
                <template v-if="docsSends.title === '0'">นาย</template>
                <template v-else-if="docsSends.title === '1'">นาง</template>
                <template v-else-if="docsSends.title === '2'">นางสาว</template>
                {{ docsSends.fname }} {{ docsSends.lname }}
              </TableDataCell>
              <TableDataCell class="text-xs md:text-sm whitespace-nowrap">{{
                docsSends.years
              }}</TableDataCell>
              <TableDataCell class="text-xs md:text-sm whitespace-nowrap">
                <template v-if="docsSends.transcripts">
                  <button
                    @click="showDocument(docsSends.transcripts)"
                    class="custom-button-primary"
                  >
                    เอกสารแสดงผลการเรียน
                  </button>
                </template>
                <template v-if="docsSends.operat_docs">
                  <button
                    @click="showDocument(docsSends.operat_docs)"
                    class="custom-button-primary"
                  >
                    แบบบันทึกการดำเนินงาน
                  </button>
                </template>
              </TableDataCell>
              <TableDataCell class="text-xs md:text-sm whitespace-nowrap">
                <span :class="statusClass(docsSends.result)">
                  <template v-if="docsSends.result === '0'">ผ่าน</template>
                  <template v-else-if="docsSends.result === '1'">แก้ไข</template>
                  <template v-else-if="docsSends.result === '2'">รอการตรวจ</template>
                </span>
              </TableDataCell>
              <TableDataCell>
                <PrimaryButton
                  @click="confirmDeleteDocs(docsSends.id)"
                  v-if="hasRole('scholar')"
                  class="custom-button-danger"
                  >ลบ
                </PrimaryButton>
                <template v-if="hasRole('admin') || hasRole('officer')">
                  <Link
                    :href="route('docs_sends.show', docsSends.id)"
                    class="custom-button-warning ml-3"
                    >ตรวจสอบ</Link
                  >
                </template>
                <template v-else-if="hasRole('scholar') || hasRole('intlscholar')">
                  <Link
                    :href="route('docs_sends.edit', docsSends.id)"
                    class="custom-button-warning ml-3"
                    >แก้ไข</Link
                  >
                </template>
              </TableDataCell>
            </TableRow>
          </template>
        </Table>
        <div class="flex justify-end items-center w-full mt-4 space-x-4">
          <div class="flex items-center">
            <label for="perPage" class="text-sm font-medium text-gray-700 mr-2"
              >ข้อมูลต่อหน้า</label
            >
            <select
              v-model="perPage"
              class="px-2 py-1 border border-gray-300 rounded text-gray-800"
              id="perPage"
            >
              <option value="10">10 รายการ</option>
              <option value="20">20 รายการ</option>
              <option value="50">50 รายการ</option>
              <option value="100">100 รายการ</option>
            </select>
          </div>
          <span class="px-4 py-2">หน้า {{ currentPage }} จาก {{ totalPages }}</span>

          <div class="inline-flex space-x-2">
            <button
              class="px-4 py-2 bg-gray-300 text-gray-800 rounded"
              :disabled="currentPage === 1"
              @click="currentPage--"
            >
              ก่อนหน้า
            </button>
            <button
              v-if="currentPage < totalPages && totalItems > 0"
              class="px-4 py-2 bg-gray-800 text-gray-100 rounded"
              @click="currentPage++"
            >
              ถัดไป
            </button>
          </div>
        </div>
      </div>
    </div>
    <Modal :show="documentModal">
      <div class="p-6">
        <h2 class="text-lg font-semibold text-slate-800">เอกสาร</h2>
        <embed v-if="documentFile" :src="documentFile" width="100%" height="600px" />
        <SecondaryButton @click="documentModal = false" class="mt-4"
          >Close</SecondaryButton
        >
      </div>
    </Modal>
  </AuthenticatedLayout>
</template>
