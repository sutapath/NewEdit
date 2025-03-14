<script setup>
import { ref, watch } from 'vue';
import { usePage } from "@inertiajs/vue3";
import { Head, Link, useForm } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from '@/Components/InputError.vue';

const props = defineProps({
    application: Object,
    auth: Object,
});

const form = useForm({  
    title: props.application.title || '',
    fname: props.application.fname || '',
    lname: props.application.lname || '',
    phone: props.application.phone || '',
    tuition_fee: props.application.tuition_fee || '', // ใช้ค่าจาก props.application
    scholar_form: null,
    gpa_image: null,
    gpax: props.application.gpax || '',
    branch: props.application.branch || '',
    faculty: props.application.faculty || '',
    fee_receipt: null,
    imagefile: null,
    certificates: null,
    scholar_type: '1',
    id_card: props.application.id_card || '',
    result: '2',
    reg_form: null,
    Interview_results: '2',
});

const handleSubmit = () => {
    validateForm();

    const hasNewData = Object.keys(form).some(key => form[key] !== (props.application[key] || ''));

    if (!hasNewData) {
        console.log('ไม่มีการเปลี่ยนแปลงข้อมูลใหม่');
        return; // ไม่ต้องทำอะไรถ้าไม่มีการเปลี่ยนแปลง
    }

    const formData = new FormData();

    // เพิ่มข้อมูลใน formData โดยใช้ไฟล์เดิมถ้าไม่มีการเลือกไฟล์ใหม่
    for (const key in form) {
        // ถ้า key เป็นไฟล์และไม่มีการเลือกไฟล์ใหม่ ให้ใช้ค่าเดิมจาก props.application
        if (['imagefile', 'gpa_image', 'scholar_form', 'reg_form', 'fee_receipt', 'certificates'].includes(key) && !form[key]) {
            formData.append(key, props.application[key]);
        } else {
            formData.append(key, form[key]);
        }
    }

    form.post(route('scholarship_applications.update', { id: props.application.id }), {
        data: formData,
        onSuccess: (response) => {
            // อัปเดต URL ของไฟล์ที่อัปโหลด
            imagefileUrl.value = `/public/file/${response.imagefile}`; 
            gpa_imageUrl.value = `/public/file/${response.gpa_image}`; 
            regFormUrl.value = `/public/file/${response.reg_form}`; 
            feeReceiptUrl.value = `/public/file/${response.fee_receipt}`; 
            certificatesUrl.value = `/public/file/${response.certificates}`; 
            scholarFormUrl.value = `/public/file/${response.scholar_form}`; 
        },
        onError: (error) => {
            console.error(error);
        },
    });
};


console.log(props.application.id);

const imagefileUrl = ref(`/public/file/${props.application.imagefile}`);
const gpa_imageUrl = ref(`/public/file/${props.application.gpa_image}`);
const regFormUrl = ref(`/public/file/${props.application.reg_form}`);
const feeReceiptUrl = ref(`/public/file/${props.application.fee_receipt}`);
const certificatesUrl = ref(`/public/file/${props.application.certificates}`);
const scholarFormUrl = ref(`/public/file/${props.application.scholar_form}`);


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
    "คณะมนุษศาสตร์และเทคโนโลยี": [
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

const filteredFaculties = ref([]);
const filteredBranches = ref([]);

// Filter faculties based on user input
const filterFaculties = () => {
    filteredFaculties.value = Object.keys(faculties).filter(faculty =>
        faculty.includes(form.faculty)
    );
};

// Select a faculty from suggestions
const selectFaculty = (faculty) => {
    form.faculty = faculty;
    filteredFaculties.value = [];
    filteredBranches.value = faculties[faculty] || [];
};

// Filter branches based on user input
const filterBranches = () => {
    filteredBranches.value = faculties[form.faculty]?.filter(branch =>
        branch.includes(form.branch)
    ) || [];
};

// Select a branch from suggestions
const selectBranch = (branch) => {
    form.branch = branch;
    filteredBranches.value = [];
};

const handleFileChange = (event, field) => {
    const file = event.target.files[0];
    form[field] = file;

    if (file) {
        const fileUrl = URL.createObjectURL(file);
        switch (field) {
            case 'imagefile':
                imagefileUrl.value = fileUrl;
                break;
            case 'gpa_image':
                gpa_imageUrl.value = fileUrl;
                break;
            case 'portfolio':
                portfoliotUrl.value = fileUrl;
                break;
            case 'conduct_cert':
                conductcertUrl.value = fileUrl;
                break;
            case 'fam_cert':
                famcertUrl.value = fileUrl;
                break;
            case 'award_certs':
                awardcertsUrl.value = fileUrl;
                break;
            case 'leader_proof':
                leaderproofUrl.value = fileUrl;
                break;
            case 'reg_form':
                regFormUrl.value = fileUrl;
                break;
            case 'fee_receipt':
                feeReceiptUrl.value = fileUrl;
                break;
            case 'certificates':
                certificatesUrl.value = fileUrl;
                break;
            case 'scholar_form':
                scholarFormUrl.value = fileUrl;
                break;
        }
    }
};
 
 
const errorMessages = ref({
    tuition_fee: '',
    gpax: '',
    branch: '',
    faculty: '', 
});
const validateForm = () => {
    Object.keys(errorMessages.value).forEach(key => {
        errorMessages.value[key] = '';
    });

    const validations = [
        { field: 'branch', message: 'กรุณาเลือกสาขาวิชา' },
        { field: 'faculty', message: 'กรุณาเลือกคณะ' },
        { field: 'tuition_fee', message: 'ค่าธรรมเนียมการศึกษา' }, 
        { field: 'gpax', message: 'กรุณากรอกผลการเรียน (5 ภาคเรียนหรือ 6 ภาคเรียน)' }
    ];

    validations.forEach(({ field, message }) => {
        if (!form[field]) {
            errorMessages.value[field] = message;
        }
    });
};

</script>

<template>
    <AuthenticatedLayout>

        <Head title="Create Apply Scholar" />

        <div class=" py-4 mt-20 ">
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
                            <Link :href="route('scholarship_applications.index')" class="custom-button-danger">กลับ</Link>
                            <button class="custom-button-success" @click="handleSubmit">บันทึก</button>
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
                    <TextInput id="faculty"  placeholder="คณะ" type="text" class="mt-1 block w-full" v-model="form.faculty"
                        @input="filterFaculties" required />
                    <div v-if="filteredFaculties.length">
                        <ul>
                            <li v-for="faculty in filteredFaculties" :key="faculty" @click="selectFaculty(faculty)">
                                {{ faculty }}
                            </li>
                        </ul>
                    </div>
                    <InputError class="mt-2" :message="form.errors.faculty" />
                </div>
                <div>
                    <InputLabel for="branch" value="สาขา" />
                    <TextInput  placeholder="สาขาวิชา" id="branch" type="text" class="mt-1 block w-full" v-model="form.branch"
                        @input="filterBranches" required />
                    <div v-if="filteredBranches.length" class="relative">
                        <ul class="absolute z-10 w-full max-h-48 overflow-auto bg-white border rounded-lg shadow-lg">
                            <li v-for="branch in filteredBranches" :key="branch" @click="selectBranch(branch)"
                                class="cursor-pointer hover:bg-gray-200 p-2">
                                {{ branch }}
                            </li>
                        </ul>
                    </div>
                    <InputError class="mt-2" :message="form.errors.branch" />
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
                                                            <!-- แสดงไฟล์ปัจจุบัน -->
                            <div v-if="application.imagefile" class="flex justify-between items-center mt-2">
                                <span></span> <!-- ใช้เพื่อให้ Flexbox ทำงาน -->
                                <a :href="`/storage/files/${application.imagefile}`" class="text-indigo-500"
                                    target="_blank">ดูไฟล์ปัจจุบัน</a>
                            </div>
                        </div>
                        <div>
                            <InputLabel for="scholar_form" value="แบบฟอร์ยื่นกูทุนการศึกษา" />
                            <TextInput require type="file" @change="handleFileChange($event, 'scholar_form')"
                                id="scholar_form" class="mt-1 block py-1 px-1 w-full" />
                            <span v-if="errorMessages.scholar_form" class="text-red-600">{{ errorMessages.scholar_form
                                }}</span>

<div v-if="application.scholar_form" class="flex justify-between items-center mt-2">
                                <span></span> <!-- ใช้เพื่อให้ Flexbox ทำงาน -->
                                <a :href="`/storage/files/${application.scholar_form}`" class="text-indigo-500"
                                    target="_blank">ดูไฟล์ปัจจุบัน</a>
                            </div>
                        </div>
                        <div>
                            <InputLabel for="gpa_image" value="ผลการเรียน" />
                            <TextInput require type="file" @change="handleFileChange($event, 'gpa_image')"
                                id="gpa_image" class="mt-1 block py-1 px-1 w-full" />
                            <span v-if="errorMessages.gpa_image" class="text-red-600">{{ errorMessages.gpa_image
                                }}</span>

<div v-if="application.gpa_image" class="flex justify-between items-center mt-2">
                                <span></span> <!-- ใช้เพื่อให้ Flexbox ทำงาน -->
                                <a :href="`/storage/files/${application.gpa_image}`" class="text-indigo-500"
                                    target="_blank">ดูไฟล์ปัจจุบัน</a>
                            </div>
                        </div>
                        <div>
                            <InputLabel for="reg_form" value="เอกสารลงทะเบียนเรียน" />
                            <TextInput require type="file" @change="handleFileChange($event, 'reg_form')" id="reg_form"
                                class="mt-1 block py-1 px-1 w-full" />
                            <span v-if="errorMessages.reg_form" class="text-red-600">{{ errorMessages.reg_form }}</span>
                        
                            <div v-if="application.reg_form" class="flex justify-between items-center mt-2">
                                <span></span> <!-- ใช้เพื่อให้ Flexbox ทำงาน -->
                                <a :href="`/storage/files/${application.reg_form}`" class="text-indigo-500"
                                    target="_blank">ดูไฟล์ปัจจุบัน</a>
                            </div>
                        </div>
                        <div>
                            <InputLabel for="fee_receipt" value="ใบเสร็จชำระค่าเทอม" />
                            <TextInput require type="file" @change="handleFileChange($event, 'fee_receipt')"
                                id="fee_receipt" class="mt-1 block py-1 px-1 w-full" />
                            <span v-if="errorMessages.fee_receipt" class="text-red-600">{{ errorMessages.fee_receipt
                                }}</span>
                                                     <div v-if="application.fee_receipt" class="flex justify-between items-center mt-2">
                                <span></span> <!-- ใช้เพื่อให้ Flexbox ทำงาน -->
                                <a :href="`/storage/files/${application.fee_receipt}`" class="text-indigo-500"
                                    target="_blank">ดูไฟล์ปัจจุบัน</a>
                            </div>
                        </div>
                        <div>
                            <InputLabel for="certificates" value="ผลงานหรือกิจกรรมประกอบการพิจารณา" />
                            <TextInput require type="file" @change="handleFileChange($event, 'certificates')"
                                id="certificates" class="mt-1 block py-1 px-1 w-full" />
                            <span v-if="errorMessages.certificates" class="text-red-600">{{ errorMessages.certificates
                                }}</span>
                                                     <div v-if="application.certificates" class="flex justify-between items-center mt-2">
                                <span></span> <!-- ใช้เพื่อให้ Flexbox ทำงาน -->
                                <a :href="`/storage/files/${application.certificates}`" class="text-indigo-500"
                                    target="_blank">ดูไฟล์ปัจจุบัน</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
