<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm, usePage, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { usePermission } from "@/composables/permissions";
import Swal from 'sweetalert2'; 
const { hasRole, hasPermission } = usePermission();
const props = defineProps({
    mustVerifyEmail: Boolean,
    status: String,
});
import moment from 'moment';
const user = usePage().props.auth.user;
console.debug(user.birthday)

const form = useForm({
    name: user.name,
    email: user.email,
    title: user.title,
    fname: user.fname,
    lname: user.lname,
    birthday: moment(user.birthday).format('YYYY-MM-DD'),
    id_card: user.id_card,
    student_code: user.student_code,
    institution: user.institution,
    branch: user.branch,
    faculty: user.faculty,
    phone: user.phone,
    house_no: user.house_no,
    village_name: user.village_name,
    sub_district: user.sub_district,
    district: user.district,
    province: user.province,
    zip_code: user.zip_code,
    en_year: user.en_year,
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
const filterFaculties = () => {
    filteredFaculties.value = Object.keys(faculties).filter(faculty =>
        faculty.includes(form.faculty)
    );
};
 
const selectFaculty = (faculty) => {
    form.faculty = faculty;
    filteredFaculties.value = [];
    filteredBranches.value = faculties[faculty] || [];
}; 
const filterBranches = () => {
    filteredBranches.value = faculties[form.faculty]?.filter(branch =>
        branch.includes(form.branch)
    ) || [];
}; 
const selectBranch = (branch) => {
    form.branch = branch;
    filteredBranches.value = [];
};


const submitForm = () => {
    // เช็คข้อมูลที่เปลี่ยนแปลงก่อน
    const hasChanges = form.dirty();  // ตรวจสอบว่ามีการแก้ไขข้อมูลหรือไม่

    if (hasChanges) {
        // ถ้ามีการเปลี่ยนแปลง ขอยืนยันก่อน
        Swal.fire({
            title: 'คุณแน่ใจหรือไม่?',
            text: 'ข้อมูลที่คุณแก้ไขจะถูกอัปเดต',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'ยืนยัน',
            cancelButtonText: 'ยกเลิก',
            customClass: {
                confirmButton: 'bg-blue-500 text-white hover:bg-blue-600',
                cancelButton: 'bg-gray-500 text-white hover:bg-gray-600',
            },
        }).then((result) => {
            if (result.isConfirmed) { 
                form.patch(route('profile.update', { id: user.id }), {
                    onSuccess: () => {
                        Swal.fire({
                            icon: 'success',
                            title: 'อัปเดตข้อมูลสำเร็จ!',
                            text: 'ข้อมูลของคุณได้รับการอัปเดตแล้ว',
                            confirmButtonText: 'ปิด',
                            customClass: {
                                confirmButton: 'bg-green-500 text-white hover:bg-green-600',
                            },
                        });
                    },
                    onError: (error) => {
                        Swal.fire({
                            icon: 'error',
                            title: 'เกิดข้อผิดพลาด!',
                            text: 'การอัปเดตข้อมูลล้มเหลว',
                            confirmButtonText: 'ปิด',
                            customClass: {
                                confirmButton: 'bg-red-500 text-white hover:bg-red-600',
                            },
                        });
                        console.error('Error:', error);
                    },
                });
            }
        });
    } else { 
        Swal.fire({
            title: 'ไม่มีการเปลี่ยนแปลง!',
            text: 'ข้อมูลของคุณไม่มีการเปลี่ยนแปลง',
            icon: 'info',
            confirmButtonText: 'ปิด',
            customClass: {
                confirmButton: 'bg-yellow-500 text-white hover:bg-yellow-600',
            },
        });
    }
};
</script>

<template>
    <section class="ml-5">
        <form @submit.prevent="submitForm" enctype="multipart/form-data">
            <div style="display: flex; align-items: center;" class="mb-5">
                <p style="margin-right: 8px;" class="text-xl font-bold">ข้อมูลส่วนตัว</p>
                <hr style="flex-grow: 1; margin: 0;" />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-5 gap-4 mb-3">
                <div>
                    <InputLabel for="title" value="คำนำหน้า" />
                    <select id="title" v-model="form.title"
                        class="mt-1 block w-full bg-white rounded-xl border-gray-300" required>
                        <option value="0">นาย (Mr.)</option>
                        <option value="1">นาง (Mrs.)</option>
                        <option value="2">นางสาว (Miss)</option>
                    </select>
                    <InputError class="mt-2" :message="form.errors.title" />
                </div>
                <div>
                    <InputLabel for="fname" value="ชื่อจริง" />
                    <TextInput id="fname" type="text" class="mt-1 block w-full" v-model="form.fname" required
                        autocomplete="fname" />
                    <InputError class="mt-2" :message="form.errors.fname" />
                </div>
                <div>
                    <InputLabel for="lname" value="นามสกุล" />
                    <TextInput id="lname" type="text" class="mt-1 block w-full" v-model="form.lname" required
                        autocomplete="lname" />
                    <InputError class="mt-2" :message="form.errors.lname" />
                </div>
                <div>
                    <InputLabel for="birthday" value="วันเกิด" />
                    <TextInput id="birthday" type="date" class="mt-1 block w-full" v-model="form.birthday" required
                        autocomplete="bday" />
                    <InputError class="mt-2" :message="form.errors.birthday" />
                </div>
                <div>
                    <InputLabel for="id_card" value="รหัสบัตรประชาชน" />
                    <TextInput id="id_card" type="text" class="mt-1 block w-full" v-model="form.id_card" required
                        autocomplete="id_card" />
                    <InputError class="mt-2" :message="form.errors.id_card" />
                </div>
            </div>
            <div v-if="hasRole('student') || hasRole('scholar')  || hasRole('intlscholar')" style="display: flex; align-items: center;"
                class="mb-5 mt-5">
                <p style="margin-right: 8px;" class="text-xl font-bold">ข้อมูลนักศึกษา</p>
                <hr style="flex-grow: 1; margin: 0;" />
            </div>
            <div v-if="hasRole('student') || hasRole('scholar')  || hasRole('intlscholar')" class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-3">

                <div>
                    <InputLabel for="student_code" value="รหัสนักศึกษา" />
                    <TextInput id="student_code"  placeholder="รหัสนักศึกษา"  type="text" class="mt-1 block w-full" v-model="form.student_code"
                        required autocomplete="student_code" />
                    <InputError class="mt-2" :message="form.errors.student_code" />
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
                    <InputLabel for="en_year" value="ปีการศึกษา" />
                    <TextInput id="en_year" type="text" class="mt-1 block w-full bg-white rounded-xl border-gray-300"
                        v-model="form.en_year" placeholder="กรอกปีการศึกษา" />
                    <InputError class="mt-2" :message="form.errors.en_year" />
                </div>
            </div>
            <div style="display: flex; align-items: center;" class="mb-5 mt-5">
                <p style="margin-right: 8px;" class="text-xl font-bold">ข้อมูลติดต่อ</p>
                <hr style="flex-grow: 1; margin: 0;" />
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-7 gap-4 mb-3">
                <div>
                    <InputLabel for="house_no" value="บ้านเลขที่" />
                    <TextInput id="house_no" type="text" class="mt-1 block w-full" v-model="form.house_no"  placeholder="บ้านเลขที่" required />
                    <InputError class="mt-2" :message="form.errors.house_no" />
                </div>
                <div>
                    <InputLabel for="village_name" value="ชื่อหมู่บ้าน" />
                    <TextInput id="village_name" type="text" class="mt-1 block w-full" placeholder="ชื่อหมู่บ้าน" v-model="form.village_name"
                        required />
                    <InputError class="mt-2" :message="form.errors.village_name" />
                </div>
                <div>
                    <InputLabel for="sub_district" value="ตำบล" />
                    <TextInput id="sub_district" type="text" class="mt-1 block w-full"  placeholder="ตำบล" v-model="form.sub_district"
                        required />
                    <InputError class="mt-2" :message="form.errors.sub_district" />
                </div>
                <div>
                    <InputLabel for="district" value="อำเภอ" />
                    <TextInput id="district" type="text" class="mt-1 block w-full"   placeholder="ตำบล" v-model="form.district" required />
                    <InputError class="mt-2" :message="form.errors.district" />
                </div>
                <div>
                    <InputLabel for="province" value="จังหวัด" />
                    <TextInput id="province" type="text" class="mt-1 block w-full" v-model="form.province"  placeholder="จังหวัด" required />
                    <InputError class="mt-2" :message="form.errors.province" />
                </div>
                <div>
                    <InputLabel for="zip_code" value="รหัสไปรษณีย์" />
                    <TextInput id="zip_code"  placeholder="รหัสไปรษณีย์"  type="text" class="mt-1 block w-full" v-model="form.zip_code" required />
                    <InputError class="mt-2" :message="form.errors.zip_code" />
                </div>

                <div>
                    <InputLabel for="phone" value="เบอร์โทรศัพท์" />
                    <TextInput id="phone"  placeholder="เบอร์โทรศัพท์"  type="text" class="mt-1 block w-full" v-model="form.phone" required />
                    <InputError class="mt-2" :message="form.errors.phone" />
                </div>
            </div>
            <div class="flex items-center justify-end">
                <PrimaryButton class="custom-button-success mt-5">บันทึกข้อมูล</PrimaryButton>
            </div>
        </form>
    </section>
</template>