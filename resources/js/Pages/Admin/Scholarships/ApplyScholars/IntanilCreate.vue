<script setup>
import { ref } from "vue";
import { usePage } from "@inertiajs/vue3";
import { Head, Link, useForm } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import Swal from 'sweetalert2';
const { props } = usePage();

const form = useForm({
  scholar_id: props.scholar_id || '',
  user_id: props.auth.user.id || '',
  title: props.auth.user.title || '',
  fname: props.auth.user.fname || '',
  lname: props.auth.user.lname || '',
  phone: props.auth.user.phone || '',
  gpax: '',
  imagefile: null,
  scholar_type: '0',
  type_ability: '',
  conduct_cert: null,
  portfolio: null,
  fam_cert: null,
  award_certs: null,
  leader_proof: null,
  gpa_image: null,
  id_card: props.auth.user.id_card || '', 
  result: '2',
  Interview_results: '2',

});
const errorMessages = ref({
  gpax: '',
  imagefile: '',
  scholar_type: '',
  type_ability: '',
  conduct_cert: '',
  portfolio: '',
  fam_cert: '',
  award_certs: '',
  leader_proof: '',
  gpa_image: '',
});
const validateForm = () => {
  // ล้างข้อความข้อผิดพลาด  
  Object.keys(errorMessages.value).forEach((key) => {
    errorMessages.value[key] = '';
  });

  // ตรวจสอบประเภทความสามารถ  
  if (form.type_ability) {
    if (!form.imagefile) {
      errorMessages.value.imagefile = 'กรุณาเพิ่มภาพถ่าย 1 นิ้ว';
    }
    if (!form.gpa_image) {
      errorMessages.value.gpa_image = 'กรุณาเพิ่มเอกสารแสดงผลการเรียน';
    }
    if (!form.portfolio) {
      errorMessages.value.portfolio = 'กรุณาเพิ่ม portfolio';
    }
    if (!form.conduct_cert) {
      errorMessages.value.conduct_cert = 'กรุณาเพิ่มเอกสารรับรองความประพฤติ';
    }
    // ตรวจสอบเฉพาะประเภทความสามารถที่ตรงตามเงื่อนไข  
    if (form.type_ability === '2' && !form.fam_cert) {
      errorMessages.value.fam_cert = 'กรุณาเพิ่มเอกสารรับรองสถานภาพครอบครัว';
    }
    if (['3', '4', '5'].includes(form.type_ability) && !form.award_certs) {
      errorMessages.value.award_certs = 'กรุณาเพิ่มเอกสารรับรองรางวัล';
    }
    if (form.type_ability === '6' && !form.leader_proof) {
      errorMessages.value.leader_proof = 'กรุณาเพิ่มเอกสารรับรองความเป็นผู้นำ';
    }
  } else {
    errorMessages.value.type_ability = 'กรุณาเลือกประเภททุนความสามารถพิเศษ';
  }

  // ตรวจสอบ GPAX  
  if (!form.gpax) {
    errorMessages.value.gpax = 'กรุณากรอกผลการเรียน (5 ภาคเรียนหรือ 6 ภาคเรียน)';
  }
};
const handleFileChange = (event, field) => {
  form[field] = event.target.files[0];
  console.log(`File selected for ${field}:`, form[field]);
};
const handleSubmit = () => {
  console.log("🔍 เริ่มต้น handleSubmit...");

  // validateForm(); // ตรวจสอบค่าฟอร์ม
  console.log("✅ validateForm() เรียบร้อย!");

  console.log("📌 Error Messages:", errorMessages.value);
  const hasErrors = Object.values(errorMessages.value).some(message => message !== '');
  console.log("🔍 มีข้อผิดพลาดไหม?", hasErrors);

  if (hasErrors) { 
    console.warn("❌ พบข้อผิดพลาดในการกรอกฟอร์ม!");
    Swal.fire({
      title: 'เกิดข้อผิดพลาด!',
      text: 'กรุณาตรวจสอบข้อมูลและลองใหม่',
      icon: 'error',
      confirmButtonText: 'ตกลง',
      confirmButtonColor: '#dc3545',
      timer: 2000,
      timerProgressBar: true,
    });
    return;
  } 

  console.log("🚀 กำลังส่งฟอร์มไปที่:", route('scholarship_applications.store'));
  console.log("📩 ข้อมูลที่ส่ง:", form);

  form.post(route('scholarship_applications.store'), {
    onSuccess: () => {
      console.log("✅ ฟอร์มส่งสำเร็จ!");
      Swal.fire({
        title: 'สำเร็จ!',
        text: 'ส่งฟอร์มเรียบร้อยแล้ว',
        icon: 'success',
        confirmButtonText: 'ตกลง',
        confirmButtonColor: '#28a745',
        timer: 2000,
        timerProgressBar: true,
      });
    },
    onError: (errors) => {
      console.error("❌ เกิดข้อผิดพลาดขณะส่งฟอร์ม:", errors);
      Swal.fire({
        title: 'เกิดข้อผิดพลาด!',
        text: 'กรุณาตรวจสอบข้อมูลและลองใหม่',
        icon: 'error',
        confirmButtonText: 'ตกลง',
        confirmButtonColor: '#dc3545',
        timer: 2000,
        timerProgressBar: true,
      });
    },
  });
};


</script>

<template>
  <AuthenticatedLayout>

    <Head title="Create Apply Scholar" />

    <div class="py-4">
      <div class="max-w-7xl mx-auto sm:px-6 mt-20 lg:px-8">
        <div
          class="bg-gray-700 dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg border border-gray-500 dark:border-gray-600">
          <div class="py-2 px-4 text-white dark:text-gray-100 flex justify-between items-center">
            <div class="px-4">
              ข้อมูลการสมัคร
              <span v-if="form.scholar_type === '0'">ทุนเพชรอินทนิล</span>
              <span v-else-if="form.scholar_type === '1'">ทุนงดเว้นค่าธรรมเนียมการศึกษา</span>
            </div>

            <div class="flex items-center space-x-4">
              <Link :href="route('scholarships.index')" class="custom-button-danger">กลับ</Link>
              <PrimaryButton class="custom-button-success" @click="handleSubmit">บันทึก</PrimaryButton>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="max-w-7xl mx-auto mb-10 bg-white">
      <div class="bg-white rounded-xl shadow-md p-6 sm:p-8 lg:p-10 mx-4 md:mx-6 lg:mx-8">
        <form @submit.prevent="handleSubmit" enctype="multipart/form-data">
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6 w-full">
            <div>
              <InputLabel for="title" value="คำนำหน้า" />
              <select id="title" v-model="form.title" class="mt-1 block w-full bg-white rounded-xl border-gray-300"
                required>
                <option value="0">นาย (Mr.)</option>
                <option value="1">นาง (Mrs.)</option>
                <option value="2">นางสาว (Miss)</option>
              </select>
            </div>
            <div>
              <InputLabel for="fname" value="ชื่อ" />
              <TextInput disabled id="fname" type="text" class="mt-1 block w-full" v-model="form.fname" required
                autocomplete="fname" />
            </div>
            <div>
              <InputLabel for="lname" value="นามสกุล" />
              <TextInput disabled id="lname" type="text" class="mt-1 block w-full" v-model="form.lname" required
                autocomplete="lname" />
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-4 gap-6 w-full mt-3">
            <div>
              <InputLabel for="id_card" value="บัตรประชาชน" />
              <TextInput disabled type="text" id="id_card" class="mt-1 block w-full" v-model="form.id_card" />
            </div>
            <div>
              <InputLabel for="phone" value="หมายเลขโทรศัพท์" />
              <TextInput disabled id="phone" type="text" class="mt-1 block w-full" v-model="form.phone" required
                autocomplete="phone" />
            </div>
            <div>
              <InputLabel for="gpax" value="ผลการเรียน ( 5 ภาคเรียนหรือ 6 ภาคเรียน )" />
              <TextInput  type="number" id="gpax" class="mt-1 block w-full" v-model="form.gpax" />

              <span v-if="errorMessages.gpax" class="text-red-600">{{ errorMessages.gpax }}</span>
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-6 w-full mt-3">
            <div>
              <InputLabel for="type_ability" value="ประเภทความสามารถพิเศษ" />
              <select id="type_ability" v-model="form.type_ability" required
                class="mt-1 block w-full bg-white rounded-xl border-gray-300">
                <option value="" disabled selected>กรุณาเลือกประเภททุนความสามารถพิเศษ</option>
                <option value="1">ประเภททุนเรียนดี</option>
                <option value="2">ประเภททุนเรียนดีแต่ขาดแคลนทุนทรัพย์</option>
                <option value="3">ประเภททุนความสามารถพิเศษด้านกีฬา</option>
                <option value="4">ประเภททุนความสามารถพิเศษด้านผลงานความคิดสร้างสรรค์</option>
                <option value="5">ประเภททุนความสามารถพิเศษด้านดนตรี นาฏศิลป์</option>
                <option value="6">ประเภทกิจกรรมที่ส่งเสริมภาวะผู้นำ</option>
              </select>
              <span v-if="errorMessages.type_ability" class="text-red-600">{{ errorMessages.type_ability }}</span>

            </div>
            <div>
              <InputLabel for="imagefile" value="ภาพถ่าย 1 นิ้ว" />
              <TextInput type="file" @change="handleFileChange($event, 'imagefile')" id="imagefile"
                class="mt-1 block py-1 px-1 w-full" />
              <span v-if="errorMessages.imagefile" class="text-red-600">{{ errorMessages.imagefile }}</span>

            </div>
            <div>
              <InputLabel for="gpa_image" value="เอกสารรับรองผลการเรียนหรือใบ ปพ." />
              <TextInput type="file" @change="handleFileChange($event, 'gpa_image')" id="gpa_image"
                class="mt-1 block py-1 px-1 w-full" />
              <span v-if="errorMessages.gpa_image" class="text-red-600">{{ errorMessages.gpa_image }}</span>

            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-4 gap-6 w-full mt-3">
            <div>
              <InputLabel for="portfolio" value="Portfolio" />
              <TextInput type="file" @change="handleFileChange($event, 'portfolio')" id="portfolio"
                class="mt-1 block py-1 px-1 w-full" />
              <span v-if="errorMessages.portfolio" class="text-red-600">{{ errorMessages.portfolio }}</span>

            </div>
            <div>
              <InputLabel for="conduct_cert" value="เอกสารรับรองความประพฤติ" />
              <TextInput type="file" @change="handleFileChange($event, 'conduct_cert')" id="conduct_cert"
                class="mt-1 block py-1 px-1 w-full" />
              <span v-if="errorMessages.conduct_cert" class="text-red-600">{{ errorMessages.conduct_cert }}</span>
            </div>
            <div v-if="form.type_ability === '2'">
              <InputLabel for="fam_cert" value="ใบรับรองครอบครัว" />
              <TextInput type="file" @change="handleFileChange($event, 'fam_cert')" id="fam_cert"
                class="mt-1 block py-1 px-1 w-full" />
              <span v-if="errorMessages.fam_cert" class="text-red-600">{{ errorMessages.fam_cert }}</span>

            </div>
            <div v-if="form.type_ability === '5' || form.type_ability === '4' || form.type_ability === '3'">
              <InputLabel for="award_certs" value="ใบรับรองรางวัลเกียรติ" />
              <TextInput type="file" @change="handleFileChange($event, 'award_certs')" id="award_certs"
                class="mt-1 block py-1 px-1 w-full" />

              <span v-if="errorMessages.award_certs" class="text-red-600">{{ errorMessages.award_certs }}</span>
            </div>
          </div>

          <div v-if="form.type_ability === '6'" class="grid grid-cols-1 md:grid-cols-3 gap-6 w-full mt-3">
            <div>
              <InputLabel for="leader_proof" value="เอกสารรับรองภาวะผู้นำ" />
              <TextInput type="file" @change="handleFileChange($event, 'leader_proof')" id="leader_proof"
                class="mt-1 block py-1 px-1 w-full" />
              <span v-if="errorMessages.leader_proof" class="text-red-600">{{ errorMessages.leader_proof }}</span>

            </div>
          </div>
        </form>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
