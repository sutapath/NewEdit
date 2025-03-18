<script setup>
import { ref, computed } from "vue";
import { Head, Link, useForm, usePage, router } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Modal from "@/Components/Modal.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { usePermission } from "@/composables/permissions";
import { format, addYears } from "date-fns";
import Table from "@/Components/Table.vue";
import TableRow from "@/Components/TableRow.vue";
import TableHeaderCell from "@/Components/TableHeaderCell.vue";
import TableDataCell from "@/Components/TableDataCell.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import Swal from "sweetalert2";
const { hasRole } = usePermission();
// const form = useForm({});
const showConfirmDeleteModal = ref(false);
const applicationToDelete = ref(null);
const selectedScholarId = ref("");
const searchQuery = ref("");
const selectedApplications = ref([]);
const selectedInterviewStatus = ref("");
const props = defineProps({
  applications: Array,
  currentUser: Object,
  scholarships: Array,
  Notify: Array,
  user: Object,
});

const currentPage = ref(1);
const perPage = ref(10);
const totalItems = ref(props.applications.length);
const page = usePage();
const currentUser = computed(() => page.props.currentUser);
const filteredApplications = computed(() => {
  if (!Array.isArray(props.applications)) return [];
  const isStudentOrMember =
    currentUser.value.roles.includes("student") ||
    currentUser.value.roles.includes("member") || currentUser.value.roles.includes("scholar");
  const results = props.applications.filter((application) => {
    const matchesScholar =
      !selectedScholarId.value || application.scholar_id === selectedScholarId.value;
    const search = searchQuery.value ? searchQuery.value.toLowerCase() : "";
    const matchesSearch =
      !search ||
      application.fname.toLowerCase().includes(search) ||
      application.lname.toLowerCase().includes(search);
    const matchesUser =
      !isStudentOrMember || application.user_id === currentUser.value.id;
    return matchesScholar && matchesSearch && matchesUser;
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
    return format(nextYear, "yyyy");
  } catch (e) {
    console.error("Invalid date format", e);
    return "";
  }
};
const isAllSelected = computed(() => {
  return selectedApplications.value.length === filteredApplications.value.length;
});
const handleSelectAll = (event) => {
  if (event.target.checked) {
    selectedApplications.value = filteredApplications.value.map((app) => app.id);
  } else {
    selectedApplications.value = [];
  }
};
const handleSelect = (applicationId) => {
  const index = selectedApplications.value.indexOf(applicationId);
  if (index > -1) {
    selectedApplications.value.splice(index, 1);
  } else {
    selectedApplications.value.push(applicationId);
  }
};
const isSelected = (applicationId) => {
  return selectedApplications.value.includes(applicationId);
};
const form = useForm({
  Results: selectedApplications.value,
  status: selectedInterviewStatus,
});
const submitInterviewResults = async () => {
  if (!form.status) {
    Swal.fire({
      icon: "warning",
      title: "กรุณาเลือกนักเรียนและสถานะสัมภาษณ์ก่อนส่งข้อมูล.",
      confirmButtonText: "ตกลง",
      confirmButtonColor: "#4CAF50",
      iconColor: "#FF9800",
    });
    return;
  }
  try {
    const formData = {
      Results: selectedApplications.value,
      status: selectedInterviewStatus.value,
    };

    const form = useForm(formData);

    await form.post(route("scholarship_applications.interviewResults"));
    Swal.fire({
      icon: "success",
      title: "✅ อัปเดตผลสัมภาษณ์สำเร็จ!",
      confirmButtonText: "ตกลง",
      confirmButtonColor: "#28A745",
      iconColor: "#007BFF",
    });
  } catch (error) {
    Swal.fire({
      icon: "error",
      title: "❌ เกิดข้อผิดพลาด",
      text: "ไม่สามารถอัปเดตข้อมูลได้ โปรดลองใหม่อีกครั้ง.",
      confirmButtonText: "ตกลง",
      confirmButtonColor: "#DC3545",
      iconColor: "#FF0000",
    });
  }
};
const handleConfirmScores = () => {
  if (selectedApplications.value.length == 0) {
    Swal.fire({
      icon: "warning",
      title: "กรุณาเลือกนักศึกษาก่อนทำการยืนยันสถานะ.",
      confirmButtonText: "ตกลง",
      confirmButtonColor: "#FF9800",
    });
    return;
  }

  if (selectedInterviewStatus.value == null) {
    Swal.fire({
      icon: "warning",
      title: "กรุณาเลือกสถานะการสัมภาษณ์ก่อนทำการยืนยัน.",
      confirmButtonText: "ตกลง",
      confirmButtonColor: "#FF9800",
    });
    return;
  }

  // แสดง Swal loading เมื่อเริ่มต้นการทำงาน
  const loadingSwal = Swal.fire({
    title: "กำลังส่งข้อมูล...",
    text: "โปรดรอซักครู่",
    allowOutsideClick: false,
    didOpen: () => {
      Swal.showLoading(); // แสดงสถานะ loading
    },
  });

  const previewMessage1 = computed(() => {
    return req_data.value.cancel_status === "1"
      ? `
📢 **เอกสารผ่านการพิจารณาทุนการศึกษา**

🎉 **ยินดีด้วย! เอกสารของคุณได้รับการพิจารณาผ่านแล้ว**
หากมีข้อสงสัยหรือต้องการข้อมูลเพิ่มเติม กรุณาติดต่อเจ้าหน้าที่
    `
      : `
📢 **เอกสารไม่ผ่านการพิจารณาทุนการศึกษา**

⚠️ **กรุณาทำการแก้ไขเอกสารและส่งใหม่**
หากมีข้อสงสัยหรือต้องการคำแนะนำ กรุณาติดต่อเจ้าหน้าที่
    `;
  });

  selectedApplications.value.forEach((userData) => {
    sendLineNotification(userData, previewMessage1.value);
  });
  submitInterviewResults()
    .then(() => {
      setTimeout(() => {
        loadingSwal.close();
        Swal.fire({
          icon: "success",
          title: "การยืนยันสำเร็จ!",
          text: "ข้อมูลถูกส่งแล้ว",
          confirmButtonText: "ตกลง",
          confirmButtonColor: "#4CAF50",
        });
      }, 1500);
    })
    .catch((error) => {
      setTimeout(() => {
        loadingSwal.close();
        Swal.fire({
          icon: "error",
          title: "เกิดข้อผิดพลาด!",
          text: "ไม่สามารถส่งข้อมูลได้",
          confirmButtonText: "ตกลง",
          confirmButtonColor: "#FF0000",
        });
      }, 1500);
    });
};

const getScholarType = (scholarType) => {
  const scholarTypes = {
    0: "ทุนเพชรอินทนิล",
    1: "ทุนงดเว้นค่าธรรมเนียมการศึกษา",
  };
  return scholarTypes[scholarType] || " ";
};
const getTypeAbility = (typeAbility) => {
  const typeAbilities = {
    1: "ประเภททุนเรียนดี",
    2: "ประเภททุนเรียนดีแต่ขาดแคลนทุนทรัพย์",
    3: "ประเภททุนความสามารถพิเศษด้านกีฬา",
    4: "ประเภททุนความสามารถพิเศษด้านผลงานความคิดสร้างสรรค์",
    5: "ประเภททุนความสามารถพิเศษด้านดนตรี นาฏศิลป์",
    6: "ประเภทกิจกรรมที่ส่งเสริมภาวะผู้นำ",
  };
  return typeAbilities[typeAbility] || "";
};
const showModal = ref(false);
const openModal = () => {
  showModal.value = true;
};
const closeModal = () => {
  showModal.value = false;
};
import PublishInfo from "@/Pages/Admin/Scholarships/ApplyScholars/PublishInfo.vue";
import scholarContracts from "@/Pages/Admin/Scholarships/scholarContracts/LineNotifyforEdit.vue";

const navigateToAddContract = (applicationId) => {
  window.location.href = route("scholarship_applications.AddContract", {
    application: applicationId,
  });
};

const req_data = ref("");
const ContractFile = ref(null);
const ContractModel = ref(false);
const contractReview = ref({
  cancel_status: req_data.cancel_status,
  contract_suggestions: req_data.contract_suggestions,
  errors: {},
});
const showDocument = (application) => {
  ContractFile.value = application.scholarship_contract
    ? `/storage/files/${application.scholarship_contract}`
    : null;
  ContractModel.value = true;
  req_data.value = application; // Correct assignment
  console.log(req_data.value.id); // Access the value
};
const previewMessage2 = computed(() => {
  return req_data.value.cancel_status === "1"
    ? `
📢 **เอกสารลงนามผ่านการอนุมัติ**

🎉 **ยินดีด้วย! เอกสารการลงนามของคุณได้รับการอนุมัติแล้ว**
หากมีข้อสงสัยหรือต้องการข้อมูลเพิ่มเติม กรุณาติดต่อเจ้าหน้าที่
    `
    : `
📢 **เอกสารลงนามไม่ผ่านการอนุมัติ**

⚠️ **กรุณาทำการแก้ไขเอกสารลงนามและส่งใหม่**
หากมีข้อสงสัยหรือต้องการคำแนะนำ กรุณาติดต่อเจ้าหน้าที่
    `;
});
const submitCheck = async () => {
  const formData = {
    cancel_status: req_data.value.cancel_status,
    contract_suggestions: req_data.value.contract_suggestions,
  };
  const cancelDate = new Date();
  cancelDate.setHours(0, 0, 0, 0);

  const cancelDateString = cancelDate.toISOString().split("T")[0];

  // ตรวจสอบว่า cancel_status เป็น "1" และเพิ่มค่าที่จำเป็น
  if (req_data.value.cancel_status == "1") {
    formData.approved_date = cancelDateString;
    formData.approved_by = `${props.user.fname} ${props.user.lname}`;
  } 
  const roleData = req_data.value.cancel_status == "1"
    ? [{ id: 3, name: "scholar" }]
    : [
      req_data.value.scholar_type == "0"
        ? { id: 5, name: "member" }
        : { id: 4, name: "student" }
    ];

  console.log("Role Data:", roleData); // Log ข้อมูล roleData เพื่อดูว่าเป็นอะไร

  const userId = req_data.value.user_id || props.user?.id;
  console.log("User ID:", userId);
  console.log("User Data:", req_data.value);
  console.log("Role Data:", roleData);
  axios.put(route("users.updatescholar", { user: userId }), {
    user: req_data.value,
    roles: roleData,
  });

  console.log("data", formData);
  axios
    .put(
      route("scholarship_application.check", { application: req_data.value.id }),
      formData
    )
    .then((response) => {
      ContractModel.value = false; // ปิด Modal
      Swal.fire({
        title: "สำเร็จ!",
        text: "ผลการตรวจสอบถูกส่งแล้ว",
        icon: "success",
        confirmButtonColor: "#28a745",
      });
      sendLineNotification(req_data.value.id, previewMessage2.value);
    })
    .catch((error) => {
      contractReview.value.errors = error.response.data.errors || {};
      Swal.fire({
        title: "เกิดข้อผิดพลาด!",
        text: "ไม่สามารถส่งผลการตรวจสอบได้",
        icon: "error",
        confirmButtonColor: "#dc3545",
      });
    });
};


const sendLineNotification = (userId, message) => {
  const token = document
    .querySelector('meta[name="csrf-token"]')
    ?.getAttribute("content");

  if (!token) {
    console.error("CSRF token not found");
    return;
  }

  fetch(route("line.notify", { userId }), {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
      "X-CSRF-TOKEN": token,
    },
    body: JSON.stringify({
      message: message,
    }),
  })
    .then((response) => response.json())
    .then((data) => {
      console.log("Line Notify Response:", data);
    })
    .catch((error) => {
      console.error("Error:", error);
    });
};

const approveCancellation = (application) => {
  Swal.fire({
    title: "ยืนยันการยกเลิกทุน?",
    html: `
      <strong>ผู้ดำเนินรายการ:</strong> ${application.cancel_by} <br>
      <strong>วันที่:</strong> ${application.cancel_date} <br>
      <strong>เหตุผล:</strong> ${application.cancellation_reason} <br>
      <br>
      กรุณายืนยันการยกเลิก
    `,
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "อนุมัติ",
    cancelButtonText: "ไม่อนุมัติ",
    confirmButtonColor: "#28a745",
    cancelButtonColor: "#dc3545",
  }).then((result) => {
    let cancelStatus = "1";
    let cancelDate = "";
    let cancelBy = "";
    let reason = "";

    if (result.isConfirmed) {
      cancelStatus = "0";
      const currentDate = new Date();
      currentDate.setHours(0, 0, 0, 0);
      cancelDate = currentDate.toISOString().split("T")[0];
      cancelBy = application.cancel_by;
      reason = application.cancellation_reason;
    }
    const formData = {
      cancel_status: cancelStatus,
      cancellation_reason: reason,
      cancel_date: cancelDate,
      cancel_by: cancelBy,
    }; 
    const roleData =
      formData.cancel_status == "0"
        ? [
          props.application.scholar_type == "0"
            ? { id: 5, name: "member" }
            : { id: 4, name: "student" },
        ]
        : [];

    const userId = props.user?.id;
    console.log("User ID:", userId);
    console.log("User Data:", formData);
    console.log("Role Data:", roleData); 
    if (roleData.length > 0) {
      axios
        .put(route("users.updatescholar", { user: userId }), {
          user: props.user,
          roles: roleData,
        })
        .then(() => {
          console.log("Role updated successfully!");
        });
    }

    console.log(formData);
    axios
      .put(
        route("scholarship_application.check", { id: application.id }),
        formData,
        {
          headers: {
            "Content-Type": "application/json",
          },
        }
      )
      .then((response) => {
        console.log("อัปเดตข้อมูลสำเร็จ!", response.data);
        Swal.fire({
          icon: "success",
          title: "อัปเดตสำเร็จ!",
          text: "ข้อมูลได้รับการบันทึกแล้ว",
          confirmButtonColor: "#28a745",
          confirmButtonText: "ตกลง",
        });
      })
      .catch((error) => {
        console.error("เกิดข้อผิดพลาด:", error.response?.data || error.message);
        Swal.fire({
          icon: "error",
          title: "เกิดข้อผิดพลาด!",
          text: "ไม่สามารถอัปเดตข้อมูลได้ กรุณาลองใหม่",
          confirmButtonColor: "#dc3545",
          confirmButtonText: "ตกลง",
        });
      });

    if (result.dismiss === Swal.DismissReason.cancel) {
      Swal.fire({
        icon: "info",
        title: "การยกเลิกถูกยกเลิก",
        text: "การยกเลิกทุนการศึกษาถูกยกเลิก",
        confirmButtonColor: "#3085d6",
        confirmButtonText: "ตกลง",
      });
    }
  });
};


const confirmCancellation = () => {
  Swal.fire({
    title: "ยืนยันการยกเลิกทุน?",
    text: "กรุณากรอกเหตุผลในการยกเลิกทุนการศึกษา",
    icon: "warning",
    input: "textarea",
    inputPlaceholder: "กรอกเหตุผลที่นี่...",
    inputAttributes: {
      "aria-label": "กรอกเหตุผลที่นี่",
    },
    showCancelButton: true,
    confirmButtonText: "ยืนยัน",
    cancelButtonText: "ยกเลิก",
    confirmButtonColor: "#d33",
    cancelButtonColor: "#3085d6",
    preConfirm: (reason) => {
      if (!reason) {
        Swal.showValidationMessage("กรุณากรอกเหตุผลก่อนกดยืนยัน");
      }
      return reason;
    },
  }).then((result) => {
    if (result.isConfirmed) {
      const cancelDate = new Date();
      cancelDate.setHours(0, 0, 0, 0);

      const cancelDateString = cancelDate.toISOString().split("T")[0];
      const formData = {
        cancel_status: "0",
        cancellation_reason: result.value,
        cancel_date: cancelDateString,
        cancel_by: `${props.user.fname} ${props.user.lname}`,
      };

      console.log(formData);

      axios
        .put(
          route("scholarship_application.check", { id: req_data.value.id }),
          formData,
          {
            headers: {
              "Content-Type": "application/json",
            },
          }
        )
        .then((response) => {
          console.log("อัปเดตข้อมูลสำเร็จ!", response.data);
          Swal.fire({
            icon: "success",
            title: "อัปเดตสำเร็จ!",
            text: "ข้อมูลได้รับการบันทึกแล้ว",
            confirmButtonColor: "#28a745",
            confirmButtonText: "ตกลง",
          });
        })
        .catch((error) => {
          console.error("เกิดข้อผิดพลาด:", error.response?.data || error.message);
          Swal.fire({
            icon: "error",
            title: "เกิดข้อผิดพลาด!",
            text: "ไม่สามารถอัปเดตข้อมูลได้ กรุณาลองใหม่",
            confirmButtonColor: "#dc3545",
            confirmButtonText: "ตกลง",
          });
        });
    }
  });
};



</script>
<template>

  <Head title="Scholarship Applications" />
  <AuthenticatedLayout>
    <div class="max-w-7xl mx-auto mb-3 py-4 mt-20 px-10">
      <div class="py-2 px-4 text-white dark:text-gray-100 flex justify-between items-center">
        <div class="py-2 text-gray-800 rounded-t-lg text-xl font-bold">
          ข้อมูลการลงนามสัญญานักศึกษา
        </div>
        <div class="flex-1 mr-3 ml-3">
          <hr class="border-t border-gray-300" />
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
              class="block w-full mt-1 text-gray-800 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
          </div>
          <div class="py-1">
            <label for="interviewStatus" class="block text-sm font-medium text-gray-700">สถาณะ</label>
            <select id="interviewStatus" v-model="selectedInterviewStatus"
              class="block w-full mt-1 text-gray-800 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
              <option value="">เลือกสถานะ</option>
              <option value="1">ผ่านการพิจารณา</option>
              <option value="0">ไม่ผ่านการพิจารณา</option>
            </select>
          </div>
          <div class="pt-7 flex items-center space-x-4">
            <button @click="handleConfirmScores" class="bg-blue-500 text-white px-4 py-2 rounded-md">
              ยืนยันสถานะ
            </button>
            <!-- <PrimaryButton class="custom-button-success" @click="openModal">แจ้งกำหนดการ</PrimaryButton> -->
          </div>
        </div>
      </div>
    </div>
    <!-- <pre>{{ props.roles }}</pre> -->
    <div class="max-w-full sm:max-w-7xl mx-auto sm:px-6 lg:px-8 bg-white sm:rounded-xl overflow-x-auto">
      <Table class="whitespace-nowrap">
        <TableRow header>
          <TableHeaderCell v-if="hasRole('admin') || hasRole('officer')">
            <input type="checkbox" @change="handleSelectAll" />
          </TableHeaderCell>
          <TableHeaderCell>ลำดับ</TableHeaderCell>
          <TableHeaderCell>ผลการสัมภาษณ์</TableHeaderCell>
          <TableHeaderCell>ชื่อ - สกุล</TableHeaderCell>
          <TableHeaderCell>ประเภท</TableHeaderCell>
          <TableHeaderCell v-if="hasRole('admin') || hasRole('officer')">คะแนนสัมภาษณ์</TableHeaderCell>
          <TableHeaderCell>ลงนามสัญญา</TableHeaderCell>
          <TableHeaderCell>ยืนยันการลงนาม</TableHeaderCell>
          <TableHeaderCell>ยกเลิกทุน</TableHeaderCell>
        </TableRow>

        <TableRow v-for="(application, index) in filteredApplications" :key="application.id">
          <TableDataCell v-if="hasRole('admin') || hasRole('officer')">
            <input type="checkbox" @change="handleSelectAll"
              v-if="(hasRole('admin') || hasRole('officer')) && application.cancel_status == 1" />

          </TableDataCell>
          <TableDataCell>{{ index + 1 }}</TableDataCell>
          <TableDataCell>
            <span :class="{
              'text-green-600 athiti-semibold': application.Interview_results == '1',
              'text-red-600 athiti-semibold': application.Interview_results == '0',
              'text-gray-600 athiti-semibold': application.Interview_results == '2',
            }">
              {{
              application.Interview_results == "1"
              ? "ผ่านการพิจารณา"
              : application.Interview_results == "0"
              ? "ไม่ผ่านการพิจารณา"
              : "รอการพิจารณา"
              }}
            </span>
          </TableDataCell>

          <TableDataCell>
            <Link :href="route('scholarship_applications.ContractsDetail', application.id)"
              class="text-blue-600 hover:underline athiti-bold text-lg">
            {{ application.fname }} {{ application.lname }}
            </Link>
          </TableDataCell>
          <TableDataCell>{{ getScholarType(application.scholar_type) }}
            {{ getTypeAbility(application.type_ability) }}
          </TableDataCell>
          <TableDataCell v-if="hasRole('admin') || hasRole('officer')">{{ application.interview_score || 0 }}
          </TableDataCell>

          <TableDataCell>
            <div v-if="hasRole('admin') || hasRole('officer')">
              <PrimaryButton v-if="application.cancel_status == 1" class="custom-button-success"
                @click="showDocument(application)">
                ลงนามสำเร็จ
              </PrimaryButton>
              <PrimaryButton v-else-if="application.cancel_status == 0" class="custom-button-danger"
                @click="showDocument(application)">
                ยกเลิกการรับทุน
              </PrimaryButton>
              <PrimaryButton v-else-if="application.cancel_status == 2" class="custom-button-warning"
                @click="showDocument(application)">
                ดำเนินการแก้ไข
              </PrimaryButton>
              <PrimaryButton v-else-if="application.cancel_status == 4" class="custom-button-disabled"
                @click="showDocument(application)">
                รอตรวจสอบ
              </PrimaryButton>
              <span v-else>-</span>
            </div>


            <div v-if="hasRole('student') || hasRole('member') || hasRole('scholar')" class="flex space-x-2">
              <PrimaryButton v-if="application.cancel_status == 1" @click="navigateToAddContract(application.id)"
                class="bg-green-300 text-gray-900 p-2 athiti-medium rounded-xl">
                ลงนามสำเร็จ
              </PrimaryButton>
              <PrimaryButton v-else-if="application.cancel_status == 0" @click="navigateToAddContract(application.id)"
                class="bg-red-300 text-gray-900 p-2 athiti-medium rounded-xl">
                ยกเลิกการรับทุน
              </PrimaryButton>
              <PrimaryButton v-else-if="application.cancel_status == 2" @click="navigateToAddContract(application.id)"
                class="bg-yellow-300 text-gray-900 p-2 athiti-medium rounded-xl">
                ดำเนินการแก้ไข
              </PrimaryButton>
              <PrimaryButton v-else-if="application.cancel_status == 5" @click="navigateToAddContract(application.id)"
                class="bg-gray-100 text-gray-900 p-2 athiti-medium rounded-xl">
                รอตรวจสอบ
              </PrimaryButton>
              <PrimaryButton v-else class="bg-yellow-300 text-gray-900 p-2 athiti-medium rounded-xl"
                @click="navigateToAddContract(application.id)">

                ลงนาม
              </PrimaryButton>
              <!-- <PrimaryButton v-if="application && application.scholarship_contract && application.cancel_status != 0"
                @click="navigateToAddContract(application.id)" class="bg-blue-500 py-1 mx-2 text-white rounded-xl px-2">
                ดู
              </PrimaryButton> -->
            </div>

          </TableDataCell>
          <TableDataCell>
            <div class="text-gray-800 font-semibold">{{ application.approved_by }}</div>
            <div class="text-gray-500">{{ application.approved_date }}</div>
          </TableDataCell>

          <TableDataCell>
            <PrimaryButton v-if="application.cancel_status == 4 && (hasRole('admin') || hasRole('officer'))"
              class="custom-button-warning" @click="approveCancellation(application)">
              หมายเหตุ
            </PrimaryButton>
            <div v-if="hasRole('student') || hasRole('member') || hasRole('scholar')">

              <div class="text-gray-800 font-semibold">{{ application.cancel_by }}</div>
              <div class="text-gray-500">{{ application.cancel_date }}</div>
              <div class="text-red-500">{{ application.cancellation_reason }}</div>
            </div>
          </TableDataCell>
        </TableRow>
      </Table>
      <tbody v-if="props.applications == []" class="bg-white divide-y divide-gray-200">
        <tr class="flex justify-center items-center">
          <td colspan="5" class="px-6 py-4 text-md text-gray-700 text-center">
            ไม่มีข้อมูลการสมัคร
          </td>
        </tr>
      </tbody>


    </div>
    <Modal v-if="showModal" @close="closeModal">
      <template v-slot:header>ยืนยันการเลือกนักศึกษา</template>
      <template v-slot:footer>
        <SecondaryButton @click="closeModal">ปิด</SecondaryButton>
      </template>
      <div class="p-6">
        <scholarContracts />
      </div>
    </Modal>
    <Modal :show="ContractModel">
      <div class="p-6 relative mt-2">
        <SecondaryButton class="absolute top-2 right-2 custom-button-danger" @click="ContractModel = false">
          ✕
        </SecondaryButton>
        <h2 class="text-lg font-semibold text-slate-800 text-center">เอกสาร</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 w-full mt-6">
          <div>
            <InputLabel for="contract_suggestions" value="ผลการตรวจสอบ" />

            <select id="result" v-model="req_data.cancel_status"
              class="mt-1 block w-full bg-white rounded-xl border-gray-300">
              <option value="1">ผ่าน</option>
              <option value="2">แก้ไข</option>
              <option value="0">ยกเลิกการรับทุน</option>
            </select>
          </div>
          <!-- <pre>{{ req_data.cancel_status }}</pre>
          <pre>{{ req_data.contract_suggestions }}</pre> -->
          <div class="mb-4">
            <InputLabel for="contract_suggestions" value="ข้อเสนอแนะ" />
            <TextInput id="contract_suggestions" type="text" class="mt-1 block w-full"
              v-model="req_data.contract_suggestions" />
          </div>

        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 w-full mt-2">

          <PrimaryButton v-if="hasRole('admin') || hasRole('officer')"
            class="mt-4 mb-3 w-full bg-green-500 text-white py-2 rounded-xl" @click="submitCheck">
            ส่งผลการตรวจสอบ
          </PrimaryButton>
          <PrimaryButton class="mt-4 mb-3 w-full bg-red-500 text-white py-2 rounded-xl" @click="confirmCancellation">
            ยกเลิกทุนการศึกษา
          </PrimaryButton>
        </div>

        <embed v-if="ContractFile" :src="ContractFile" width="100%" height="600px" />
      </div>
    </Modal>
  </AuthenticatedLayout>
</template>
