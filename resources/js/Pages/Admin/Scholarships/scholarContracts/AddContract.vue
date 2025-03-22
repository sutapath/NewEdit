<template>
  <AuthenticatedLayout>
    <div class="max-w-7xl mx-auto py-4 mt-20 px-10">
      <div class="px-4 text-white dark:text-gray-100 flex justify-between items-center">
        <div class="py-2 text-gray-800 rounded-t-lg text-xl font-bold">เอกสารลงนามทุนการศึกษา</div>
        <div class="flex-1 mr-3 ml-3">
          <hr class="border-t border-gray-300" />
        </div>

        <div class="flex justify-end space-x-4">
          <div v-if="hasRole('student') || hasRole('member') || hasRole('scholar')">
            <PrimaryButton class="custom-button-disabled" @click="confirmCancellation">
              ยกเลิกทุนการศึกษา
            </PrimaryButton>
          </div>
          <PrimaryButton class="custom-button-danger" @click="goBack">
            กลับ
          </PrimaryButton>
          <PrimaryButton :class="{ 'opacity-25': form.processing }" v-if="props.application?.cancel_status != 4 && props.application?.cancel_status != 3 && props.application?.cancel_status != 1 && props.application?.cancel_status != 0 && props.application?.cancel_status == null" @click="handleSubmit" class="custom-button-success">
            บันทึก
          </PrimaryButton>
        </div>
      </div>
    </div>
    <!-- <pre>{{ props.application }}</pre>
    <pre>{{ props.user }}</pre> -->
    <div class="max-w-7xl mx-auto">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <form @submit.prevent="handleSubmit" enctype="multipart/form-data">
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6 w-full">
            <!-- อัพโหลดไฟล์ -->
            <div class="mb-4">
              <InputLabel for="scholarship_contract" value="อัพโหลดไฟล์" />
              <TextInput id="scholarship_contract" type="file"
                class="mt-1 block w-full border-gray-300 py-1 px-2 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                @change="handleFileChange($event, 'scholarship_contract')" />
              <div v-if="form.errors.scholarship_contract" class="text-red-500 text-sm mt-1">
                กรุณาเพิ่มข้อมูล
              </div>
            </div>
            <div class="mb-4" v-if="props.application?.contract_suggestions">
              <InputLabel for="contract_suggestions" value="ข้อเสนอแนะ" />
              <p id="contract_suggestions" class="mt-1 block w-full bg-gray-100 p-2 rounded">
                {{ props.application?.contract_suggestions }}
              </p>
            </div>

          </div>
        </form>
        <div class="mb-4">
          <label class="block text-gray-700">ไฟล์ที่อัพโหลด:</label>
          <div v-if="form.scholarship_contract || scholarship_contract">
            <span v-if="!props.application?.scholarship_contract" class="text-green-500">ไฟล์ที่เลือก:
              {{ form.scholarship_contract?.name || "ไม่มีชื่อไฟล์" }}</span>
            <div v-if="filePreviewUrl">
              <div v-if="isImage(filePreviewUrl)">
                <img :src="filePreviewUrl" alt="Preview" class="mt-2 max-w-full h-auto" />
              </div>
              <div v-else-if="isPdf(filePreviewUrl)">
                <iframe :src="filePreviewUrl" class="mt-2 w-full h-64" title="PDF Preview"></iframe>
              </div>
            </div>
            <div v-else-if="scholarship_contract">
              <div v-if="isImage(scholarship_contract)">
                <img :src="scholarship_contract" alt="File Preview" class="mt-2 max-w-full h-auto" />
              </div>
              <div v-else-if="isPdf(scholarship_contract)">
                <iframe :src="scholarship_contract" class="mt-2 w-full h-64" title="PDF Preview"></iframe>
              </div>
            </div>
          </div>
          <div v-else>
            <span class="text-red-500">ไม่มีไฟล์</span>
          </div>
          <div class="mt-4">
            <PrimaryButton class="custom-button-primary mr-3" :href="exampleDocumentLink" target="_blank">
              ดูตัวอย่างเอกสาร
            </PrimaryButton>
            <PrimaryButton class="custom-button-primary mr-3" :href="downloadDocumentLink" download>
              ดาวน์โหลดเอกสาร
            </PrimaryButton>
            <button
              v-if="props.application?.cancel_status != 4 && props.application?.cancel_status != 3 && props.application?.cancel_status != 1 && props.application?.cancel_status != 0 && props.application?.cancel_status"
              @click="handleDelete" class="custom-button-danger">ลบไฟล์</button>

          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, onMounted } from "vue";
import Swal from "sweetalert2"; // นำเข้า SweetAlert2
import { useForm } from "@inertiajs/vue3";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { usePermission } from "@/composables/permissions";
const { hasRole } = usePermission();

const props = defineProps({
  application: Object,
  user: Object,
});

const form = useForm({ 
  scholarship_contract: null,
  data_id: props.application?.id || null, 
});

const filePreviewUrl = ref(null);
const scholarship_contract = ref(
  `/storage/files/${props.application?.contract_suggestions || ""}`
);
const exampleDocumentLink = "/path/to/example_document.pdf";
const downloadDocumentLink = "/path/to/document_to_download.pdf";

const isImage = (url) => url && /\.(png|jpg|jpeg)$/i.test(url);
const isPdf = (url) => url && /\.pdf$/i.test(url);

const handleFileChange = (event, field) => {
  const file = event.target.files[0];
  form[field] = file;

  if (file) {
    const reader = new FileReader();
    reader.onload = (e) => {
      filePreviewUrl.value = e.target.result;
      scholarship_contract.value = `/public/files/${file.name}`; // Update the file URL when a new file is selected
    };
    reader.readAsDataURL(file);
  }
};

const handleSubmit = () => {
  if (props.application?.scholarship_contract) {
    Swal.fire({
      title: "คุณแน่ใจหรือไม่?",
      text: "คุณกำลังจะแก้ไขไฟล์ที่อัพโหลดแล้ว, ยืนยันการแก้ไข?",
      icon: "warning",
      showCancelButton: true,
      confirmButtonText: "ยืนยัน",
      cancelButtonText: "ยกเลิก",
      confirmButtonColor: "#28a745",
      cancelButtonColor: "#dc3545",
    }).then((result) => {
      if (result.isConfirmed) {
        submitForm();
      }
    });
  } else {
    submitForm();
  }
};
const submitForm = () => {
  if (!form.scholarship_contract) {
    Swal.fire({
      title: "ข้อมูลไม่ครบถ้วน!",
      text: "กรุณาเลือกไฟล์ก่อนส่งข้อมูล.",
      icon: "warning",
      confirmButtonColor: "#dc3545",
    });
    return;
  }
  const formData = new FormData();

  formData.append("data_id", form.data_id);

  form.post(route("Contracts.Save", { id: form.data_id }), {
    preserveScroll: true,
    onSuccess: () => {
      Swal.fire({
        title: "สำเร็จ!",
        text: "บันทึกข้อมูลสำเร็จ!",
        icon: "success",
        confirmButtonColor: "#28a745",
      });
      window.location.reload();
    },
    onError: (errors) => {
      Swal.fire({
        title: "เกิดข้อผิดพลาด!",
        text: "กรุณาลองใหม่อีกครั้ง",
        icon: "error",
        confirmButtonColor: "#dc3545", // สีแดงสำหรับปุ่มยืนยัน
      });
      console.error("Error on Form Submit:", errors);
    },
  });
};
const handleDelete = () => {
  Swal.fire({
    title: "คุณแน่ใจหรือไม่?",
    text: "คุณกำลังจะลบไฟล์ที่อัพโหลดแล้ว, ยืนยันการลบ?",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "ยืนยัน",
    cancelButtonText: "ยกเลิก",
    confirmButtonColor: "#dc3545",
    cancelButtonColor: "#28a745",
  }).then((result) => {
    if (result.isConfirmed) {
      axios
        .post(route("Contracts.deleteContracts", { id: form.data_id }))
        .then((response) => {
          Swal.fire({
            title: "สำเร็จ!",
            text: "ลบไฟล์เรียบร้อยแล้ว",
            icon: "success",
            confirmButtonColor: "#28a745",
          });
          window.location.reload();
        })
        .catch((error) => {
          Swal.fire({
            title: "เกิดข้อผิดพลาด!",
            text: "ไม่สามารถลบไฟล์ได้, กรุณาลองใหม่อีกครั้ง",
            icon: "error",
            confirmButtonColor: "#dc3545",
          });
          console.error("Error during file delete:", error);
        });
    }
  });
};
const goBack = () => {
  window.location.href = route("scholarship_applications.AllStudents"); // ใช้ชื่อ route ที่คุณกำหนดใน Laravel
};

onMounted(() => {
  if (props.application) {
    form.data_id = props.application.id;
    if (props.application.scholarship_contract) {
      scholarship_contract.value = `/storage/files/${props.application.scholarship_contract}`;
    }
  }
});
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
        cancel_status: "4",
        cancellation_reason: result.value,
        cancel_date: cancelDateString,
        cancel_by: `${props.user.fname} ${props.user.lname}`,
      };
      // const roleData =
      //   formData.cancel_status == "0"
      //     ? [
      //       props.application.scholar_type == "0"
      //         ? { id: 5, name: "member" }
      //         : { id: 4, name: "student" },
      //     ]
      //     : [];

      // const userId = props.user?.id;
      // console.log("User ID:", userId);
      // console.log("User Data:", formData);
      // console.log("Role Data:", roleData);
      // if (roleData) {
      //   axios.put(route("users.updatescholar", { user: userId }), {
      //     user: props.user,
      //     roles: roleData,
      //   });
      // }

      console.log(formData);

      axios
        .put(
          route("scholarship_application.check", { id: props.application?.id }),
          formData, // ส่งข้อมูลในรูปแบบ JSON
          {
            headers: {
              "Content-Type": "application/json", // ใช้ JSON แทน multipart/form-data
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
