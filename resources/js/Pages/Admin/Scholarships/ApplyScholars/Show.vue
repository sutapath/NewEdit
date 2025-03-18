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
            <div>
              <template v-if="hasRole('student') || hasRole('member')" class="flex space-x-2 ml-auto">
                <Link v-if="application.scholar_type == 0 && application.scholar_type != '0' "
                  :href="route('scholarship_applications.intanil', { id: application.id })"
                  class="custom-button-warning  mr-3">
                แก้ไข
                </Link>

                <Link v-else-if="application.scholar_type == 1 && application.scholar_type != '0'"
                  :href="route('scholarship_applications.perterm', { id: application.id })"
                  class="custom-button-warning mr-3">
                แก้ไข
                </Link>
              </template>
              <a v-if="(hasRole('admin') || hasRole('officer'))" @click="showModal" class="custom-button-warning mr-3">
                ตรวจเอกสาร
              </a>
              <Link v-if="(hasRole('student') || hasRole('member'))" :href="route('scholarship_applications.index')"
                class="custom-button-danger">กลับ</Link>
              <Link v-if="(hasRole('admin') || hasRole('officer'))" :href="route('scholarship_applications.indexadmin')"
                class="custom-button-danger">กลับ</Link>
            </div>
          </div>
        </div>

        <div v-if="hasRole('student') || hasRole('member') || hasRole('admin') || hasRole('officer')">
          <InputLabel for="suggestions" value="ข้อเสนอแนะ" />
          <TextInput readonly id="suggestions" type="text" class="mt-1 block w-full" v-model="application.suggestions"
            required autocomplete="suggestions" />
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
                  <div class="flex justify-end"><a :href="`/storage/files/${application.gpa_image}`" target="_blank"
                      class="custom-button-primary mt-3"
                      style="display: inline-block; color: white; text-decoration: none;"
                      onmouseover="this.style.color='darkblue';" onmouseout="this.style.color='white'; ">ดูเพิ่มเติม</a>
                  </div>
                </div>
              </div>
            </div>

            <div class='w-full max-w-lg px-10 py-8 mx-auto bg-white rounded-lg shadow-xl'>
              <div class='max-w-md mx-auto space-y-6'>
                <p class=" font-bold">รูปผู้สมัคร</p>
                <div v-if="application.imagefile" class="file-container mt-4">
                  <iframe :src="`/storage/files/${application.imagefile}`" class="w-full h-40" frameborder="0"></iframe>
                  <div class="flex justify-end"><a :href="`/storage/files/${application.imagefile}`" target="_blank"
                      class="custom-button-primary mt-3"
                      style="display: inline-block; color: white; text-decoration: none;"
                      onmouseover="this.style.color='darkblue';" onmouseout="this.style.color='white'; ">ดูเพิ่มเติม</a>
                  </div>
                </div>
              </div>
            </div>

            <!-- conduct_cert Section -->
            <div class='w-full max-w-lg px-10 py-8 mx-auto bg-white rounded-lg shadow-xl'>
              <div class='max-w-md mx-auto space-y-6'>
                <p class=" font-bold">ใบรับรองการประพฤติ</p>
                <div v-if="application.conduct_cert" class="file-container mt-4">
                  <iframe :src="`/storage/files/${application.conduct_cert}`" class="w-full h-40"
                    frameborder="0"></iframe>
                  <div class="flex justify-end"><a :href="`/storage/files/${application.conduct_cert}`" target="_blank"
                      class="custom-button-primary mt-3"
                      style="display: inline-block; color: white; text-decoration: none;"
                      onmouseover="this.style.color='darkblue';" onmouseout="this.style.color='white'; ">ดูเพิ่มเติม</a>
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
                  <div class="flex justify-end"><a :href="`/storage/files/${application.portfolio}`" target="_blank"
                      class="custom-button-primary mt-3"
                      style="display: inline-block; color: white; text-decoration: none;"
                      onmouseover="this.style.color='darkblue';" onmouseout="this.style.color='white'; ">ดูเพิ่มเติม</a>
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
                  <div class="flex justify-end"><a :href="`/storage/files/${application.fam_cert}`" target="_blank"
                      class="custom-button-primary mt-3"
                      style="display: inline-block; color: white; text-decoration: none;"
                      onmouseover="this.style.color='darkblue';" onmouseout="this.style.color='white'; ">ดูเพิ่มเติม</a>
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
                  <div class="flex justify-end"><a :href="`/storage/files/${application.award_certs}`" target="_blank"
                      class="custom-button-primary mt-3"
                      style="display: inline-block; color: white; text-decoration: none;"
                      onmouseover="this.style.color='darkblue';" onmouseout="this.style.color='white'; ">ดูเพิ่มเติม</a>
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
                  <div class="flex justify-end"><a :href="`/storage/files/${application.leader_proof}`" target="_blank"
                      class="custom-button-primary mt-3"
                      style="display: inline-block; color: white; text-decoration: none;"
                      onmouseover="this.style.color='darkblue';" onmouseout="this.style.color='white'; ">ดูเพิ่มเติม</a>
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
                  <div class="flex justify-end"><a :href="`/storage/files/${application.gpa_image}`" target="_blank"
                      class="custom-button-primary mt-3"
                      style="display: inline-block; color: white; text-decoration: none;"
                      onmouseover="this.style.color='darkblue';" onmouseout="this.style.color='white'; ">ดูเพิ่มเติม</a>

                  </div>
                </div>
              </div>
            </div>

            <div class='w-full max-w-lg px-10 py-8 mx-auto bg-white rounded-lg shadow-xl'>
              <div class='max-w-md mx-auto space-y-6'>
                <p class=" font-bold">รูปผู้สมัคร</p>
                <div v-if="application.imagefile" class="file-container mt-4">
                  <iframe :src="`/storage/files/${application.imagefile}`" class="w-full h-40" frameborder="0"></iframe>
                  <div class="flex justify-end"><a :href="`/storage/files/${application.imagefile}`" target="_blank"
                      class="custom-button-primary mt-3"
                      style="display: inline-block; color: white; text-decoration: none;"
                      onmouseover="this.style.color='darkblue';" onmouseout="this.style.color='white'; ">ดูเพิ่มเติม</a>
                  </div>
                </div>
              </div>
            </div>

            <!-- conduct_cert Section -->
            <div class='w-full max-w-lg px-10 py-8 mx-auto bg-white rounded-lg shadow-xl'>
              <div class='max-w-md mx-auto space-y-6'>
                <p class=" font-bold">แบบฟอร์ยื่นกู้ทุนการศึกษา</p>
                <div v-if="application.scholar_form" class="file-container mt-4">
                  <iframe :src="`/storage/files/${application.scholar_form}`" class="w-full h-40"
                    frameborder="0"></iframe>
                  <div class="flex justify-end"><a :href="`/storage/files/${application.scholar_form}`" target="_blank"
                      class="custom-button-primary mt-3"
                      style="display: inline-block; color: white; text-decoration: none;"
                      onmouseover="this.style.color='darkblue';" onmouseout="this.style.color='white'; ">ดูเพิ่มเติม</a>
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
                  <div class="flex justify-end"><a :href="`/storage/files/${application.reg_form}`" target="_blank"
                      class="custom-button-primary mt-3"
                      style="display: inline-block; color: white; text-decoration: none;"
                      onmouseover="this.style.color='darkblue';" onmouseout="this.style.color='white'; ">ดูเพิ่มเติม</a>
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
                  <div class="flex justify-end"><a :href="`/storage/files/${application.fee_receipt}`" target="_blank"
                      class="custom-button-primary mt-3"
                      style="display: inline-block; color: white; text-decoration: none;"
                      onmouseover="this.style.color='darkblue';" onmouseout="this.style.color='white'; ">ดูเพิ่มเติม</a>
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
                  <div class="flex justify-end"><a :href="`/storage/files/${application.certificates}`" target="_blank"
                      class="custom-button-primary mt-3"
                      style="display: inline-block; color: white; text-decoration: none;"
                      onmouseover="this.style.color='darkblue';" onmouseout="this.style.color='white'; ">ดูเพิ่มเติม</a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </template>

        <div v-if="isModalVisible" @click="hideModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
  <div @click.stop class="bg-white rounded-lg shadow-lg p-6 max-w-lg mx-auto relative">
    <button @click="hideModal" class="absolute top-2 right-2 text-gray-600 hover:text-gray-900">
      <span class="text-2xl">&times;</span> <!-- เครื่องหมาย X -->
    </button>
    <CheckComponent />
  </div>
</div>

      </div>
    </div>
  </AuthenticatedLayout>
</template>
<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link } from '@inertiajs/vue3';
// import Scholarship from '@/Layouts/Scholarship.vue';
import { usePermission } from "@/composables/permissions";
import { defineProps, ref, defineAsyncComponent } from 'vue';
const { hasRole, hasPermission } = usePermission();

const props = defineProps({
  application: Object
});
 
// Define a reactive variable to control the modal visibility
const isModalVisible = ref(false);

// Method to toggle modal visibility
const showModal = () => {
  isModalVisible.value = true;
};

const hideModal = () => {
  isModalVisible.value = false;
};
const CheckComponent = defineAsyncComponent(() => import('@/Pages/Admin/Scholarships/ApplyScholars/Check.vue'));

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
