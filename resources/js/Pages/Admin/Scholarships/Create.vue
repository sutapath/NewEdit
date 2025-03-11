<template>
    <AuthenticatedLayout>

        <Head title="Create Scholar Inform" />

        <div class="max-w-7xl mx-auto py-4 mt-20 px-10">
            <div class="px-4 text-white dark:text-gray-100 flex justify-between items-center">
                <div class="py-2 text-gray-800 rounded-t-lg text-xl font-bold">
                    ประเภททุนการศึกษา
                </div>
                <div class="flex-1 mr-3 ml-3">
                    <hr class="border-t border-gray-300" />
                </div>
                <div class="flex justify-end space-x-4">
                    <Link href="#" class="custom-button-danger" @click.prevent="confirmCancel">
                    ยกเลิก
                    </Link>

                    <PrimaryButton class="custom-button-success" :disabled="form.processing" @click="handleSubmit">
                        <span v-if="form.processing">กำลังโหลด...</span>
                        <span v-else>บันทึก</span>
                    </PrimaryButton>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto py-2 px-10">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <form @submit.prevent="handleSubmit" enctype="multipart/form-data">
                    <input type="hidden" name="_method" value="PUT" />

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-5">
                        <div>
                            <InputLabel for="scholar_name" value="ชื่อทุนการศึกษา" />
                            <TextInput id="scholar_name" type="text" class="mt-1 block w-full"
                                v-model="form.scholar_name" autofocus />
                            <span v-if="errorMessages.scholar_name" class="text-red-600">{{ errorMessages.scholar_name
                                }}</span>
                        </div>

                        <div>
                            <InputLabel for="type" value="ประเภททุนการศึกษา" />
                            <select id="type" class="mt-1 block w-full" v-model="form.type">
                                <option value="" disabled>เลือกประเภททุนการศึกษา</option>
                                <option value="1">
                                    ทุนงดเว้นค่าธรรมเนียมการศึกษา
                                </option>
                                <option value="0">ทุนเพชรอินทนิล</option>
                            </select>
                            <span v-if="errorMessages.type" class="text-red-600">{{ errorMessages.type }}</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-5">
                        <div>
                            <InputLabel for="apply_date" value="วันที่สมัคร" />
                            <TextInput id="apply_date" type="date" class="mt-1 block w-full"
                                v-model="form.apply_date" />
                            <span v-if="errorMessages.apply_date" class="text-red-600">{{ errorMessages.apply_date
                                }}</span>
                        </div>
                        <div>
                            <InputLabel for="due_date" value="วันสิ้นสุด" />
                            <TextInput id="due_date" type="date" class="mt-1 block w-full" v-model="form.due_date" />
                            <span v-if="errorMessages.due_date" class="text-red-600">{{ errorMessages.due_date }}</span>
                        </div>
                        <div>
                            <InputLabel for="status" value="สถานะ" />
                            <select id="status" class="mt-1 block w-full" v-model="form.status">
                                <option value="" disabled>เลือกสถานะ</option>
                                <option value="0">ปิด</option>
                                <option value="1">เปิด</option>
                            </select>
                            <span v-if="errorMessages.status" class="text-red-600">{{ errorMessages.status }}</span>
                        </div>
                    </div>

                    <div class="mt-2">
                        <InputLabel for="description" value="คำอธิบาย" />
                        <textarea id="description"
                            class="mt-1 block w-full border rounded-md border-gray-300 focus:border-gray-500 focus:ring focus:ring-gray-200 focus:ring-opacity-50"
                            v-model="form.description"></textarea>
                        <span v-if="errorMessages.description" class="text-red-600">{{ errorMessages.description
                            }}</span>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mt-5">
                        <div>
                            <InputLabel for="link" value="ลิงค์" />
                            <TextInput id="link" type="text" class="mt-1 block w-full" v-model="form.link" />
                            <span v-if="errorMessages.link" class="text-red-600">{{ errorMessages.link }}</span>
                        </div>
                        <div>
                            <InputLabel for="file" value="ไฟล์" />
                            <TextInput id="file" type="file" class="mt-1 block w-full py-1 px-2"
                                @change="handleFileChange" />
                            <span v-if="errorMessages.file" class="text-red-600">{{ errorMessages.file }}</span>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref } from "vue";
import { useForm, usePage, router } from "@inertiajs/vue3";
import { Head, Link } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import TextInput from "@/Components/TextInput.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import Swal from "sweetalert2";

const { props } = usePage();

const form = useForm({
    scholar_name: "",
    type: "",
    apply_date: "",
    due_date: "",
    status: "",
    description: "",
    link: "",
    officer_code: props.auth.user.id,
    file: null,
});

const errorMessages = ref({
    scholar_name: "",
    type: "",
    apply_date: "",
    due_date: "",
    status: "",
    description: "",
    link: "",
    file: "",
});
const confirmCancel = () => {
    Swal.fire({
        title: "คุณแน่ใจหรือไม่?",
        text: "การเปลี่ยนแปลงทั้งหมดจะไม่ถูกบันทึก",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "ใช่, กลับไป",
        cancelButtonText: "ยกเลิก",
    }).then((result) => {
        if (result.isConfirmed) {
            router.visit(route("scholarships.index"));
        }
    });
};

const validateForm = () => {
    // เคลียร์ข้อความข้อผิดพลาดก่อน
    Object.keys(errorMessages.value).forEach((key) => {
        errorMessages.value[key] = "";
    });

    if (!form.scholar_name) {
        errorMessages.value.scholar_name = "กรุณากรอกชื่อทุนการศึกษา";
    }
    if (!form.type) {
        errorMessages.value.type = "กรุณาเลือกประเภททุนการศึกษา";
    }
    if (!form.apply_date) {
        errorMessages.value.apply_date = "กรุณากรอกวันที่สมัคร";
    }
    if (!form.due_date) {
        errorMessages.value.due_date = "กรุณากรอกวันสิ้นสุด";
    }
    if (!form.status) {
        errorMessages.value.status = "กรุณาเลือกสถานะ";
    }
    if (!form.description) {
        errorMessages.value.description = "กรุณากรอกคำอธิบาย";
    }
    if (!form.link) {
        errorMessages.value.link = "กรุณากรอก Link ";
    }
    if (!form.file) {
        errorMessages.value.file = "กรุณาเลือกเอกสาร";
    }
};

const handleFileChange = (e) => {
    form.file = e.target.files[0];
};

const handleSubmit = () => {
    validateForm();

    // Log the error messages before validating form
    console.log("Error messages before submission:", errorMessages.value);

    // ถ้ามีข้อผิดพลาดไม่ให้ส่งฟอร์ม
    if (Object.values(errorMessages.value).some((error) => error)) {
        console.log("Form has validation errors. Form will not be submitted.");
        return;
    }

    console.log("Form data before submission:", form);

    form.post(route("scholarships.store"), {
        onSuccess: () => {
            // Log success
            console.log("Form submission success!");

            // Show success notification using SweetAlert2
            Swal.fire({
                title: "บันทึกสำเร็จ!",
                text: "ข้อมูลทุนการศึกษาถูกบันทึกแล้ว",
                icon: "success",
                confirmButtonText: "ตกลง",
                confirmButtonColor: "#3085d6", // เปลี่ยนสีปุ่มเป็นสีน้ำเงิน
            });

            const message = `${form.status === "0" ? "❌ ปิดรับสมัคร" : "✅ เปิดรับสมัคร"}
นักศึกษาโครงการทุน: **${form.scholar_name}**

🗓️ **วันที่เริ่มรับสมัคร**: ${form.apply_date}  
📅 **วันสิ้นสุดการรับสมัคร**: ${form.due_date}

📄 **รายละเอียด**:  
${form.description}

🌐 **ลิงค์สมัคร**: [ลงทะเบียนที่นี่](${form.link})`;

            sendLineNotification(
                props.userLineNotify.map((notify) => notify.user_id),
                message
            );
        },
        onError: () => {
            // Log error
            console.log("Form submission failed. Error occurred.");

            // Show error notification using SweetAlert2
            Swal.fire({
                title: "ผิดพลาด!",
                text: "เกิดข้อผิดพลาดในการบันทึกข้อมูลทุนการศึกษา",
                icon: "error",
                confirmButtonText: "ตกลง",
                confirmButtonColor: "#d33", // สีแดง
            });
        },
    });
};


const sendLineNotification = (userIds, message) => {
    const token = document
        .querySelector('meta[name="csrf-token"]')
        ?.getAttribute("content");

    if (!token) {
        console.error("CSRF token not found");
        return;
    }

    userIds.forEach((userId) => {
        fetch(route("line.notify", { userId }), {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": token,
            },
            body: JSON.stringify({ message }),
        })
            .then((response) => response.json())
            .then((data) => {
                console.log(
                    "Line Notify Response for userId",
                    userId,
                    ":",
                    data
                );
            })
            .catch((error) => {
                console.error("Error for userId", userId, ":", error);
            });
    });
};
</script>