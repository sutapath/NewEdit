<script setup>
import { ref, watch } from 'vue';
import { usePage } from "@inertiajs/vue3";
import { Head, Link, useForm } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import Scholarship from '@/Layouts/Scholarship.vue';
import Swal from 'sweetalert2';
const { props } = usePage();

const form = useForm({
    scholar_id: props.scholar_id || '',
    user_id: props.auth.user.id || '',
    title: props.auth.user.title || '',
    fname: props.auth.user.fname || '',
    lname: props.auth.user.lname || '',
    phone: props.auth.user.phone || '',
    tuition_fee: '',
    scholar_form: null,
    gpa_image: null,
    gpax: '',
    branch: '',
    faculty: '',
    fee_receipt: null,
    imagefile: null,
    certificates: null,
    scholar_type: '1',
    id_card: props.auth.user.id_card || '',
    result: '2',
    reg_form: null,
    Interview_results: '2',
});
const handleFileChange = (event, field) => {
  form[field] = event.target.files[0];
  console.log(`File selected for ${field}:`, form[field]);
};
const errorMessages = ref({
    tuition_fee: '',
    scholar_form: '',
    gpa_image: '',
    gpax: '',
    branch: '',
    faculty: '',
    fee_receipt: '',
    imagefile: '',
    certificates: '',
    reg_form: '',
});
const faculties = {
    "คณะครุศาสตร์": [
        "สาขาวิชาการศึกษาปฐมวัย",
        "สาขาวิชาพลศึกษา",
        "สาขาวิชาการประถมศึกษา",
        "สาขาวิชาเทคโนโลยีสารสนเทศและสื่อสารการศึกษา",
        "สาขาวิชาวิทยาศาสตร์การกีฬา"
    ],
    "คณะวิทยาศาสตร์และเทคโนโลยี": [
        "สาขาวิชาวิทยาศาสตร์ทั่วไป",
        "สาขาวิชาชีววิทยา",
        "สาขาวิชาเคมี",
        "สาขาวิชาฟิสิกส์",
        "สาขาวิชาคณิตศาสตร์ (ค.บ.)",
        "สาขาวิชาคอมพิวเตอร์",
        "สาขาวิชาคหกรรมศาสตร์",
        "สาขาวิชาวิทยาการคอมพิวเตอร์",
        "สาขาวิชาวิทยาศาสตร์สิ่งแวดล้อม",
        "สาขาวิชาเคมีประยุกต์",
        "สาขาวิชาชีววิทยาเชิงสร้างสรรค์",
        "สาขาวิชาสาธารณสุขศาสตร์",
        "สาขาวิชาคณิตศาสตร์ (วท.บ.)",
        "สาขาวิชาธุรกิจการแปรรูปอาหาร",
        "สาขาวิชาการแพทย์แผนไทย"
    ],
    "คณะมนุษศาสตร์": [
        "สาขาวิชาภาษาไทย (ค.บ.)",
        "สาขาวิชาภาษาอังกฤษ(ค.บ.)",
        "สาขาวิชาดนตรีศึกษา(ค.บ.)",
        "สาขาวิชาสังคมศึกษา(ค.บ.)",
        "สาขาวิชาบรรณารักษศาสตร์และสารสนเทศศาสตร์(ค.บ.)",
        "สาขาวิชานาฏศิลป์(ค.บ.)",
        "สาขาวิชาการพัฒนาสังคม(ศศ.บ.)",
        "สาขาวิชาภาษาอังกฤษเพื่อการสื่อสาร(ศศ.บ.)",
        "สาขาวิชาทัศนศิลป์และการออกแบบ(ศป.บ.)",
        "สาขาวิชารัฐประศาสนศาสตร์(รป.บ.)",
        "สาขาวิชานิติศาสตร์(น.บ.)",
        "สาขาวิชารัฐศาสตร์(ร.บ)"
    ],
    "คณะวิทยาการจัดการ": [
        "สาขาวิชาเศรษฐศาสตร์(ศ.บ.)",
        "สาขาวิชาบริหารธุรกิจ(บธ.บ.)",
        "สาขาวิชาการตลาดสมัยใหม่(การจัดการตลาดสมัยใหม่)(บธ.บ.)",
        "สาขาวิชาการตลาดสมัยใหม่(การจัดการตลาดค้าปลีก)(บธ.บ.)",
        "สาขาวิชาเทคโนโลยีดิจิทัลธุรกิจ(บธ.บ.)",
        "สาขาวิชาการจัดการโลจิสติกส์(บธ.บ.)",
        "สาขาวิชาการจัดการท่องเที่ยวและโรงแรม(บธ.บ.)",
        "สาขาวิชาบัญชีบัณฑิต(บช.บ.)",
        "สาขาวิชาบัญชีบัณฑิตต่อเนื่อง(บช.บ.)",
        "สาขาวิชานิเทศศาสตร์และนวัตกรรมการสื่อสาร(นศ.บ.)"
    ],
    "คณะเทคโนโลยี": [
        "สาขาวิชาอุตสาหกรรมศิลป์(ค.อ.บ.)",
        "สาขาวิชาเทคโนโลยีเครื่องกล(วท.บ.)",
        "สาขาวิชาเทคโนโลยีวิศวกรรมโยธาและสถาปัตยกรรม(วท.บ.)",
        "สาขาวิชานวัตกรรมการออกแบบ(วท.บ)",
        "สาขาวิชาเทคโนโลยีการผลิต(ทล.บ.)",
        "สาขาวิชาเทคโนโลยีคอมพิวเตอร์(ทล.บ.)",
        "สาขาวิชาวิศวกรรมการจัดการและโลจิสติกส์(วศ.บ.)"
    ],
    "คณะเกษตรและอุตสาหกรรมเกษตร": [
        "สาขาวิชาเกษตรศาสตร์(ค.บ.)",
        "สาขาวิชาเกษตรศาสตร์(วท.บ.)",
        "สาขาวิชาสัตวศาสตร์(วท.บ.)"
    ]
};

const selectedFaculty = ref('');
const branches = ref([]);
watch(selectedFaculty, (newFaculty) => {
    branches.value = faculties[newFaculty] || [];
}); 
const handleSubmit = () => {  
  validateForm();  

  const formData = new FormData();  

  for (const key in form) {  
    if (form.hasOwnProperty(key) && form[key] !== null) {  
      formData.append(key, form[key]);  
    }  
  }  

  form.post(route('scholarship_applications.store'), {  
    data: formData,  
    forceFormData: true,  
    headers: {  
      'Content-Type': 'multipart/form-data',  
    },  
    onSuccess: () => {  
      Swal.fire({  
        icon: 'success',  
        title: 'สำเร็จ!',  
        text: 'ส่งข้อมูลเรียบร้อยแล้ว',  
        confirmButtonColor: '#28a745',  
        cancelButtonColor: '#d33',  
        showCancelButton: true,  
        confirmButtonText: 'ตกลง',  
        cancelButtonText: 'ปิด',  
      });  
    },  
    onError: (errors) => {  
      
      let errorMessage = 'กรุณาตรวจสอบข้อมูลอีกครั้ง';  
    if (errors.response) {  
        console.log('Error Response:', errors.response);  

        if (errors.response.status === 422) {  
          // เช็คข้อความข้อผิดพลาด  
          if (errors.response.data.error === 'คุณได้สมัครทุนนี้ไปแล้ว') {  
            errorMessage = 'คุณได้สมัครทุนนี้ไปแล้ว กรุณาตรวจสอบสถานะของคุณ';  
          } else {  
            const errorList = Object.values(errors.response.data.errors)  
              .flat()  
              .join('\n');  
            errorMessage = `ข้อผิดพลาด:\n${errorList}`;  
          }  
        } else if (errors.response.status === 500) {  
          errorMessage = 'เกิดข้อผิดพลาดภายในเซิร์ฟเวอร์ กรุณาลองใหม่อีกครั้ง';  
        } else {  
          errorMessage = `เกิดข้อผิดพลาด: ${errors.response.statusText}`;  
        }  
      }  

      // แสดงข้อความแจ้งเตือน  
      Swal.fire({  
        icon: 'error',  
        title: 'เกิดข้อผิดพลาด!',  
        text: errors.error || errorMessage,  
        confirmButtonColor: '#dc3545',  
        cancelButtonColor: '#6c757d',  
        showCancelButton: true,  
        confirmButtonText: 'ลองอีกครั้ง',  
        cancelButtonText: 'ปิด',  
      });  
    },  
  });  
};  

const validateForm = () => {
    Object.keys(errorMessages.value).forEach(key => {
        errorMessages.value[key] = '';
    });

    if (!form.type_ability) {
        errorMessages.value.type_ability = 'กรุณากรอกชื่อประเภทความสามารถ';
    }
    if (!form.imagefile) {
        errorMessages.value.imagefile = 'กรุณาเพิ่มภาพถ่าย 1 นิ้ว';
    }
    if (!form.gpa_image) {
        errorMessages.value.gpa_image = 'กรุณากเพิ่มเอกสารแสดงผลการเรียน';
    }
    if (!form.portfolio) {
        errorMessages.value.portfolio = 'กรุณาเพิ่ม portfolio';
    }
    if (!form.branch) {
        errorMessages.value.branch = 'กรุณาเลือกสาขาวิขา';
    }
    if (!form.faculty) {
        errorMessages.value.faculty = 'กรุณาเลือกคณะ';
    }
    if (!form.tuition_fee) {
        errorMessages.value.tuition_fee = 'ค่าธรรมเนียมการศึกษา';
    }
    if (!form.scholar_form) {
        errorMessages.value.scholar_form = 'แบบฟอร์ยื่นกู้ทุนการศึกษา';
    }
    if (!form.scholar_form) {
        errorMessages.value.scholar_form = 'แบบฟอร์ยื่นกู้ทุนการศึกษา';
    }
    if (!form.reg_form) {
        errorMessages.value.reg_form = 'กรุณาเพิ่มเอกสารลงทะเบียนเรียน';
    }
    if (!form.fee_receipt) {
        errorMessages.value.fee_receipt = 'กรุณาเพิ่มใบเสร็จชำระค่าเทอม';
    }
    if (!form.certificates) {
        errorMessages.value.certificates = 'กรุณาเพิ่มผลงานหรือกิจกรรมประกอบการพิจารณา';
    }
    if (!form.gpax) {
        errorMessages.value.gpax = 'กรุณากรอกผลการเรียน ( 5 ภาคเรียนหรือ 6 ภาคเรียน )';
    }
};
</script>

<template>
    <AuthenticatedLayout>
        <div class="py-2">
            <div class="max-w-full sm:max-w-7xl mx-auto px-4 mt-6">
                <div class="py-6">
                    <div class="max-w-full mx-auto sm:px-6 lg:px-8">
                        <!-- <Scholarship /> -->
                    </div>
                </div>
            </div>
        </div>

        <Head title="Create Apply Scholar" />

        <div class="py-4">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
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
                            <select id="title" v-model="form.title"
                                class="mt-1 block w-full bg-white rounded-xl border-gray-300" required>
                                <option value="0">นาย (Mr.)</option>
                                <option value="1">นาง (Mrs.)</option>
                                <option value="2">นางสาว (Miss)</option>
                            </select>
                        </div>
                        <div>
                            <InputLabel for="fname" value="ชื่อ" />
                            <TextInput require id="fname" type="text" class="mt-1 block w-full" v-model="form.fname"
                                required autocomplete="fname" />
                        </div>
                        <div>
                            <InputLabel for="lname" value="นามสกุล" />
                            <TextInput require id="lname" type="text" class="mt-1 block w-full" v-model="form.lname"
                                required autocomplete="lname" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 w-full mt-3">
                        <div>
                            <InputLabel for="id_card" value="บัตรประชาชน" />
                            <TextInput require type="text" id="id_card" class="mt-1 block w-full"
                                v-model="form.id_card" />
                        </div>
                        <div>
                            <InputLabel for="phone" value="หมายเลขโทรศัพท์" />
                            <TextInput require id="phone" type="text" class="mt-1 block w-full" v-model="form.phone"
                                required autocomplete="phone" />
                        </div>
                        <div>
                            <InputLabel for="faculty" value="คณะ" />
                            <select id="faculty" v-model="form.faculty" @change="selectedFaculty = form.faculty"
                                class="mt-1 block w-full bg-white rounded-xl border-gray-300">
                                <option value="">กรุณาเลือกคณะ</option>
                                <option value="คณะครุศาสตร์">คณะครุศาสตร์</option>
                                <option value="คณะวิทยาศาสตร์และเทคโนโลยี">คณะวิทยาศาสตร์และเทคโนโลยี</option>
                                <option value="คณะมนุษศาสตร์และเทคโนโลยี">คณะมนุษศาสตร์และเทคโนโลยี</option>
                                <option value="คณะวิทยาการจัดการ">คณะวิทยาการจัดการ</option>
                                <option value="คณะเทคโนโลยี">คณะเทคโนโลยี</option>
                                <option value="คณะเกษตรและอุตสาหกรรมเกษตร">คณะเกษตรและอุตสาหกรรมเกษตร</option>
                            </select>
                            <span v-if="errorMessages.faculty" class="text-red-600">{{ errorMessages.faculty }}</span>

                        </div>

                        <div>
                            <InputLabel for="branch" value="สาขา" />
                            <select id="branch" v-model="form.branch"
                                class="mt-1 block w-full bg-white rounded-xl border-gray-300">
                                <option value="">กรุณาเลือกสาขา</option>
                                <option v-for="branch in branches" :key="branch" :value="branch">
                                    {{ branch }}
                                </option>
                            </select>
                            <span v-if="errorMessages.branch" class="text-red-600">{{ errorMessages.branch }}</span>

                        </div>
                        <div>
                            <InputLabel for="gpax" value="ผลการเรียน" />
                            <TextInput require type="text" id="gpax" class="mt-1 block w-full" v-model="form.gpax" />
                            <span v-if="errorMessages.gpax" class="text-red-600">{{ errorMessages.gpax }}</span>
                        </div>
                        <div>
                            <InputLabel for="tuition_fee" value="ค่าธรรมเนียมการศึกษา" />
                            <TextInput require type="text" id="tuition_fee" class="mt-1 block w-full"
                                v-model="form.tuition_fee" />
                            <span v-if="errorMessages.tuition_fee" class="text-red-600">{{ errorMessages.tuition_fee
                                }}</span>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 w-full mt-3">
                        <div>
                            <InputLabel for="imagefile" value="ภาพถ่าย 1 นิ้ว" />
                            <TextInput require type="file" @change="handleFileChange($event, 'imagefile')"
                                id="imagefile" class="mt-1 block py-1 px-1 w-full" />
                            <span v-if="errorMessages.imagefile" class="text-red-600">{{ errorMessages.imagefile
                                }}</span>
                        </div>
                        <div>
                            <InputLabel for="scholar_form" value="แบบฟอร์ยื่นกู้ทุนการศึกษา" />
                            <TextInput require type="file" @change="handleFileChange($event, 'scholar_form')"
                                id="scholar_form" class="mt-1 block py-1 px-1 w-full" />
                            <span v-if="errorMessages.scholar_form" class="text-red-600">{{ errorMessages.scholar_form
                                }}</span>
                        </div>
                        <div>
                            <InputLabel for="gpa_image" value="ผลการเรียน" />
                            <TextInput require type="file" @change="handleFileChange($event, 'gpa_image')"
                                id="gpa_image" class="mt-1 block py-1 px-1 w-full" />
                            <span v-if="errorMessages.gpa_image" class="text-red-600">{{ errorMessages.gpa_image
                                }}</span>
                        </div>
                        <div>
                            <InputLabel for="reg_form" value="เอกสารลงทะเบียนเรียน" />
                            <TextInput require type="file" @change="handleFileChange($event, 'reg_form')" id="reg_form"
                                class="mt-1 block py-1 px-1 w-full" />
                            <span v-if="errorMessages.reg_form" class="text-red-600">{{ errorMessages.reg_form }}</span>
                        </div>
                        <div>
                            <InputLabel for="fee_receipt" value="ใบเสร็จชำระค่าเทอม" />
                            <TextInput require type="file" @change="handleFileChange($event, 'fee_receipt')"
                                id="fee_receipt" class="mt-1 block py-1 px-1 w-full" />
                            <span v-if="errorMessages.fee_receipt" class="text-red-600">{{ errorMessages.fee_receipt
                                }}</span>
                        </div>
                        <div>
                            <InputLabel for="certificates" value="ผลงานหรือกิจกรรมประกอบการพิจารณา" />
                            <TextInput require type="file" @change="handleFileChange($event, 'certificates')"
                                id="certificates" class="mt-1 block py-1 px-1 w-full" />
                            <span v-if="errorMessages.certificates" class="text-red-600">{{ errorMessages.certificates
                                }}</span>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
