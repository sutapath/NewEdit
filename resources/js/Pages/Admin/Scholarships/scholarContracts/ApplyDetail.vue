<template>

  <Head title="Scholarship Application Details" />

  <AuthenticatedLayout>
    <div class="max-w-7xl mx-auto mb-3  py-4 mt-20 px-10">

      <div v-if="application" class="card border p-4 mb-4 rounded-lg shadow-lg">
        <div
          class="bg-gray-700 rounded-lg dark:bg-gray-800 mb-3 overflow-hidden shadow-lg sm:rounded-lg border border-gray-500 dark:border-gray-600">
          <div
            class="py-2 px-4 text-white dark:text-gray-100 border-b border-gray-600 flex justify-between items-center">
            <div class="flex items-center space-x-2">
              <span>ข้อมูลการสมัคร</span>
              <span v-if="application.scholar_type === '0'">ทุนเพชรอินทนิล</span>
              <span v-else-if="application.scholar_type === '1'">ทุนงดเว้นค่าธรรมเนียมการศึกษา</span>
            </div>
            <div class="flex space-x-2 ml-auto">
              <Link :href="route('scholarship_applications.AllStudents')" class="custom-button-danger">กลับ
              </Link>
            </div>
          </div>
        </div>

        <div class="grid grid-cols-1 mt-3 md:grid-cols-4 gap-6 w-full">
          <div>
            <InputLabel for="title" value="คำนำหน้า" />
            <select id="title" v-model="application.title" class="mt-1 block w-full bg-white rounded-xl border-gray-300"
              required>
              <option value="0">นาย (Mr.)</option>
              <option value="1">นาง (Mrs.)</option>
              <option value="2">นางสาว (Miss)</option>
            </select>
          </div>

          <!-- First Name Field -->
          <div>
            <InputLabel for="fname" value="ชื่อจริง" />
            <TextInput readonly id="fname" type="text" class="mt-1 block w-full" v-model="application.fname" required
              autocomplete="fname" />
          </div>

          <!-- Last Name Field -->
          <div>
            <InputLabel for="lname" value="นามสกุล" />
            <TextInput readonly id="lname" type="text" class="mt-1 block w-full" v-model="application.lname" required
              autocomplete="lname" />
          </div>
          <div>
            <InputLabel for="id_card " value="หมายเลขบัตรประชาชน" />
            <TextInput readonly id="id_card " type="text" class="mt-1 block w-full" v-model="application.id_card"
              required autocomplete="lname" />
          </div>
        </div>


        <div class="grid grid-cols-1 mt-3  md:grid-cols-4 gap-6 w-full">
          <div>
            <InputLabel for="gpax" value="ผลการเรียน" />
            <TextInput readonly id="gpax" type="text" class="mt-1 block w-full" v-model="application.gpax" required
              autocomplete="gpax" />
          </div>
          <div v-if="application.scholar_type === '1'">
            <InputLabel for="tuition_fee" value="ค่าธรรมเนียมการศึกษ" />
            <TextInput readonly id="tuition_fee" type="text" class="mt-1 block w-full" v-model="application.tuition_fee"
              required autocomplete="tuition_fee" />
          </div>

          <!-- First Name Field -->
          <div>
            <InputLabel for="phone" value="หมายเลขโทรศัพท์" />
            <TextInput readonly id="phone" type="text" class="mt-1 block w-full" v-model="application.phone" required
              autocomplete="fname" />
          </div>
        </div>

        <template v-if="application.scholar_type === '0'">
          <div>
            <InputLabel for="type_ability" value="ประเภทความสามารถพิเศษ" />
            <select id="type_ability" v-model="application.type_ability" required
              class="mt-1 block w-full bg-white rounded-xl border-gray-300">
              <option value="" disabled selected>กรุณาเลือกประเภททุนความสามารถพิเศษ</option>
              <option value="1">ประเภททุนเรียนดี</option>
              <option value="2">ประเภททุนเรียนดีแต่ขาดแคลนทุนทรัพย์</option>
              <option value="3">ประเภททุนความสามารถพิเศษด้านกีฬา</option>
              <option value="4">ประเภททุนความสามารถพิเศษด้านผลงานความคิดสร้างสรรค์</option>
              <option value="5">ประเภททุนความสามารถพิเศษด้านดนตรี นาฏศิลป์</option>
              <option value="6">ประเภทกิจกรรมที่ส่งเสริมภาวะผู้นำ</option>
            </select>
          </div>



          <!-- File components -->
          <div class="grid grid-cols-1 md:grid-cols-3 mt-3  gap-4 w-full mt-3">
            <!-- gpa_image Section -->
            <div class='w-full max-w-lg px-10 py-8 mx-auto bg-white rounded-lg shadow-xl'>
              <div class='max-w-md mx-auto space-y-6'>
                <p class=" font-bold">ผลการเรียน</p>
                <div v-if="application.gpa_image" class="file-container mt-4">
                  <iframe :src="`/storage/files/${application.gpa_image}`" class="w-full h-40" frameborder="0"></iframe>
                  <div class="flex justify-end"><a :href="`/storage/files/${application.gpa_image}`"
                      target="_blank" style="display: inline-block;  color: white; text-decoration: none;"  class=" custom-button-primary no-underline mt-3">ดูเพิ่มเติม</a>
                  </div>
                </div>
              </div>
            </div>

            <div class='w-full max-w-lg px-10 py-8 mx-auto bg-white rounded-lg shadow-xl'>
              <div class='max-w-md mx-auto space-y-6'>
                <p class=" font-bold">รูปผู้สมัคร</p>
                <div v-if="application.imagefile" class="file-container mt-4">
                  <iframe :src="`/storage/files/${application.imagefile}`" class="w-full h-40" frameborder="0"></iframe>
                  <div class="flex justify-end"><a :href="`/storage/files/${application.imagefile}`"
                      target="_blank" style="display: inline-block;  color: white; text-decoration: none;"  class=" custom-button-primary no-underline mt-3">ดูเพิ่มเติม</a>
                  </div>
                </div>
              </div>
            </div>

            <!-- conduct_cert Section -->
            <!-- conduct_cert Section -->
            <div class='w-full max-w-lg px-10 py-8 mx-auto bg-white rounded-lg shadow-xl'>
              <div class='max-w-md mx-auto space-y-6'>
                <p class=" font-bold">ใบรับรองการประพฤติ</p>
                <div v-if="application.conduct_cert" class="file-container mt-4">
                  <iframe :src="`/storage/files/${application.conduct_cert}`" class="w-full h-40"
                    frameborder="0"></iframe>
                  <div class="flex justify-end"><a :href="`/storage/files/${application.conduct_cert}`"
                      target="_blank" style="display: inline-block;  color: white; text-decoration: none;"  class=" custom-button-primary no-underline mt-3">ดูเพิ่มเติม</a>
                  </div>
                </div>
              </div>
            </div>
            <!-- portfolio Section -->
            <div class='w-full max-w-lg px-10 py-8 mx-auto bg-white rounded-lg shadow-xl'>
              <div class='max-w-md mx-auto space-y-6'>
                <p class=" font-bold">Portfolio</p>
                <div v-if="application.portfolio" class="file-container mt-4">
                  <iframe :src="`/storage/files/${application.portfolio}`" class="w-full h-40" frameborder="0"></iframe>
                  <div class="flex justify-end"><a :href="`/storage/files/${application.portfolio}`"
                      target="_blank" style="display: inline-block;  color: white; text-decoration: none;"  class=" custom-button-primary no-underline mt-3">ดูเพิ่มเติม</a>
                  </div>
                </div>
              </div>
            </div>
            <div class='w-full max-w-lg px-10 py-8 mx-auto bg-white rounded-lg shadow-xl'
              v-if="application.type_ability === '2'">
              <div class='max-w-md mx-auto space-y-6'>
                <p class=" font-bold">หนังสือรับรองรายได้ครอบครัว</p>
                <div v-if="application.fam_cert" class="file-container mt-4">
                  <iframe :src="`/storage/files/${application.fam_cert}`" class="w-full h-40" frameborder="0"></iframe>
                  <div class="flex justify-end"><a :href="`/storage/files/${application.fam_cert}`" target="_blank" style="display: inline-block;  color: white; text-decoration: none;" 
                      class=" custom-button-primary no-underline mt-3">ดูเพิ่มเติม</a>
                  </div>
                </div>
              </div>
            </div>

            <!-- award_certs Section -->
            <div
              v-if="application.type_ability === '5' || application.type_ability === '4' || application.type_ability === '3'"
              class='w-full max-w-lg px-10 py-8 mx-auto bg-white rounded-lg shadow-xl'>
              <div class='max-w-md mx-auto space-y-6'>
                <p class=" font-bold">ใบรับรองรางวัลเกียรติ</p>
                <div v-if="application.award_certs" class="file-container mt-4">
                  <iframe :src="`/storage/files/${application.award_certs}`" class="w-full h-40"
                    frameborder="0"></iframe>
                  <div class="flex justify-end"><a :href="`/storage/files/${application.award_certs}`"
                      target="_blank" style="display: inline-block;  color: white; text-decoration: none;"  class=" custom-button-primary no-underline mt-3">ดูเพิ่มเติม</a>
                  </div>
                </div>
              </div>
            </div>
            <!-- leader_proof Section -->
            <div v-if="application.type_ability === '6'"
              class='w-full max-w-lg px-10 py-8 mx-auto bg-white rounded-lg shadow-xl'>
              <div class='max-w-md mx-auto space-y-6'>
                <p class=" font-bold">เอกสารรับรองภาวะผู้นำ</p>
                <div v-if="application.leader_proof" class="file-container mt-4">
                  <iframe :src="`/storage/files/${application.leader_proof}`" class="w-full h-40"
                    frameborder="0"></iframe>
                  <div class="flex justify-end"><a :href="`/storage/files/${application.leader_proof}`"
                      target="_blank" style="display: inline-block;  color: white; text-decoration: none;"  class=" custom-button-primary no-underline mt-3">ดูเพิ่มเติม</a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </template>



        <template v-if="application.scholar_type === '1'">


          <!-- File components -->
          <div class="grid grid-cols-1 md:grid-cols-3 mt-3  gap-4 w-full mt-3">
            <!-- gpa_image Section -->
            <div class='w-full max-w-lg px-10 py-8 mx-auto bg-white rounded-lg shadow-xl'>
              <div class='max-w-md mx-auto space-y-6'>
                <p class=" font-bold">ผลการเรียน</p>
                <div v-if="application.gpa_image" class="file-container mt-4">
                  <iframe :src="`/storage/files/${application.gpa_image}`" class="w-full h-40" frameborder="0"></iframe>
                  <div class="flex justify-end"><a :href="`/storage/files/${application.gpa_image}`"
                      target="_blank" style="display: inline-block;  color: white; text-decoration: none;"  class=" custom-button-primary no-underline mt-3">ดูเพิ่มเติม</a>

                  </div>
                </div>
              </div>
            </div>

            <div class='w-full max-w-lg px-10 py-8 mx-auto bg-white rounded-lg shadow-xl'>
              <div class='max-w-md mx-auto space-y-6'>
                <p class=" font-bold">รูปผู้สมัคร</p>
                <div v-if="application.imagefile" class="file-container mt-4">
                  <iframe :src="`/storage/files/${application.imagefile}`" class="w-full h-40" frameborder="0"></iframe>
                  <div class="flex justify-end"><a :href="`/storage/files/${application.imagefile}`"
                      target="_blank" style="display: inline-block;  color: white; text-decoration: none;"  class=" custom-button-primary no-underline mt-3">ดูเพิ่มเติม</a>
                  </div>
                </div>
              </div>
            </div>

            <!-- conduct_cert Section -->
            <div class='w-full max-w-lg px-10 py-8 mx-auto bg-white rounded-lg shadow-xl'>
              <div class='max-w-md mx-auto space-y-6'>
                <p class=" font-bold">แบบฟอร์ยื่นกูทุนการศึกษา</p>
                <div v-if="application.scholar_form" class="file-container mt-4">
                  <iframe :src="`/storage/files/${application.scholar_form}`" class="w-full h-40"
                    frameborder="0"></iframe>
                  <div class="flex justify-end"><a :href="`/storage/files/${application.scholar_form}`"
                      target="_blank" style="display: inline-block;  color: white; text-decoration: none;"  class=" custom-button-primary no-underline mt-3">ดูเพิ่มเติม</a>
                  </div>
                </div>
              </div>
            </div>
            <!-- portfolio Section -->
            <div class='w-full max-w-lg px-10 py-8 mx-auto bg-white rounded-lg shadow-xl'>
              <div class='max-w-md mx-auto space-y-6'>
                <p class=" font-bold">เอกสารลงทะเบียนเรียน</p>
                <div v-if="application.reg_form" class="file-container mt-4">
                  <iframe :src="`/storage/files/${application.reg_form}`" class="w-full h-40" frameborder="0"></iframe>
                  <div class="flex justify-end"><a :href="`/storage/files/${application.reg_form}`" target="_blank" style="display: inline-block;  color: white; text-decoration: none;" 
                      class=" custom-button-primary no-underline mt-3">ดูเพิ่มเติม</a>
                  </div>
                </div>
              </div>
            </div>

            <div class='w-full max-w-lg px-10 py-8 mx-auto bg-white rounded-lg shadow-xl'>
              <div class='max-w-md mx-auto space-y-6'>
                <p class=" font-bold">ใบเสร็จชำระค่าเทอม</p>
                <div v-if="application.fee_receipt" class="file-container mt-4">
                  <iframe :src="`/storage/files/${application.fee_receipt}`" class="w-full h-40"
                    frameborder="0"></iframe>
                  <div class="flex justify-end"><a :href="`/storage/files/${application.fee_receipt}`"
                      target="_blank" style="display: inline-block;  color: white; text-decoration: none;"  class=" custom-button-primary no-underline mt-3">ดูเพิ่มเติม</a>
                  </div>
                </div>
              </div>
            </div>

            <div class='w-full max-w-lg px-10 py-8 mx-auto bg-white rounded-lg shadow-xl'>
              <div class='max-w-md mx-auto space-y-6'>
                <p class=" font-bold">ผลงานหรือกิจกรรมประกอบการพิจารณา</p>
                <div v-if="application.certificates" class="file-container mt-4">
                  <iframe :src="`/storage/files/${application.certificates}`" class="w-full h-40"
                    frameborder="0"></iframe>
                  <div class="flex justify-end"><a :href="`/storage/files/${application.certificates}`"
                      target="_blank" style="display: inline-block;  color: white; text-decoration: none;"  class=" custom-button-primary no-underline mt-3">ดูเพิ่มเติม</a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </template>

      </div>
    </div>
  </AuthenticatedLayout>
</template>
<script setup>
import { defineProps, ref } from 'vue';
import { useForm } from '@inertiajs/vue3'; // Make sure to import useForm if it's not defined elsewhere
import { usePermission } from "@/composables/permissions";
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';

import { Head, Link } from '@inertiajs/vue3';
// import Scholarship from '@/Layouts/Scholarship.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputError from '@/Components/InputError.vue';
import Swal from 'sweetalert2';
const { hasRole, hasPermission } = usePermission();
const props = defineProps({
  application: Object
});

 
const form = useForm({
  Interview_results: props.application.Interview_results || '',
  interview_score: props.application.interview_score || '',
  title: props.application.title,
  fname: props.application.fname ,
  lname: props.application.lname , 
});

 

const handleSubmit = () => {
  form.put(route('scholarship_application.interviewedit', props.application.id), {
    onSuccess: () => {
      let titleText;
      switch (form.title) {
        case '0':
          titleText = 'นาย';
          break;
        case '1':
          titleText = 'นาง';
          break;
        case '2':
          titleText = 'นางสาว';
          break;
        default:
          titleText = '';
          break;
      }

      let message;
      switch (form.Interview_results) {
        case '1':
          message = `\nขอแสดงความยินดีกับ${titleText} ${form.fname} ${form.lname} อย่างยิ่งกับการได้รับทุนการศึกษาในครั้งนี้! 🎉 `;
          break;
        case '2':
          message = `\nผลการสัมภาษณ์ทุนการศึกษา\n${titleText} ${form.fname} ${form.lname} \nรอผลการสัมภาษณ์`;
          break;
        case '0':
          message = `\nผลการสัมภาษณ์ทุนการศึกษา\n${titleText} ${form.fname} ${form.lname} \nไม่ผ่านเกณฑ์การพิจารณา ขอบคุณสำหรับความพยายามและความตั้งใจที่คุณได้ทุ่มเท`;
          break;
        default:
          message = `\nผลการสัมภาษณ์ทุนการศึกษา${titleText} ${form.fname} ${form.lname}\nไม่ผ่านเกณฑ์การพิจารณา ขอบคุณสำหรับความพยายามและความตั้งใจที่คุณได้ทุ่มเท`;
          break;
      }

      sendLineNotification(props.application.user_id, message);
      // แสดง swal สำหรับกรณีสำเร็จ
      Swal.fire({
        icon: 'success',
        title: 'การบันทึกสำเร็จ',
        text: 'ข้อมูลการสัมภาษณ์ได้รับการบันทึกสำเร็จแล้ว!',
        confirmButtonText: 'ตกลง',
        showCloseButton: true,  // เพิ่มปุ่มปิด
        closeButtonAriaLabel: 'ปิด',  // เพิ่มการระบุปุ่มปิด
        customClass: {
          confirmButton: 'bg-blue-500 text-white hover:bg-blue-600',  // ปรับสีของปุ่ม "ตกลง"
          closeButton: 'text-red-500'  // ปรับสีของปุ่มปิด
        }
      }).then(() => {
        window.location.href = route('scholarship_applications.interviewdetail', { application: props.application.id });
      }); 
    },
    onError: () => {
      // แสดง swal สำหรับกรณีไม่สำเร็จ
      Swal.fire({
        icon: 'error',
        title: 'เกิดข้อผิดพลาด',
        text: 'ไม่สามารถบันทึกข้อมูลได้ โปรดลองใหม่อีกครั้ง.',
        confirmButtonText: 'ตกลง',
        showCloseButton: true,  // เพิ่มปุ่มปิด
        closeButtonAriaLabel: 'ปิด',  // เพิ่มการระบุปุ่มปิด
        customClass: {
          confirmButton: 'bg-red-500 text-white hover:bg-red-600',  // ปรับสีของปุ่ม "ตกลง"
          closeButton: 'text-gray-500 hover:text-gray-700'  // ปรับสีของปุ่มปิด
        }
      });
    },
  });
};
// ฟังก์ชันเรียกไปยัง LineNotifyController
const sendLineNotification = (userId, message) => {
  const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

  if (!token) {
    console.error('CSRF token not found');
    return;
  }

  fetch(route('line.notify', { userId }), {
    method: 'POST',
    headers: {
      'Content-Type': 'save/json',
      'X-CSRF-TOKEN': token,
    },
    body: JSON.stringify({
      message: message,
    }),
  })
  .then(response => response.json())
  .then(data => {
    console.log('Line Notify Response:', data);
  })
  .catch(error => {
    console.error('Error:', error);
  });
};
</script>


<style scoped>
.card {
  background-color: #f8f9fa;
}

.image-container img {
  width: 100%;
  height: auto;
  border-radius: 0.5rem;
}

.file-container iframe {
  width: 100%;
  height: 200px;
  border: none;
}

.file-container img {
  width: 100%;
  height: auto;
}

.file-container a {
  display: block;
  margin-top: 0.5rem;
  color: #1d4ed8;
  text-decoration: underline;
}
</style>