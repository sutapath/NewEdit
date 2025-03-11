<template>
  <Head title="Scholarship Report Summary" />
  <AuthenticatedLayout>
    <div class="max-w-7xl mx-auto py-4 mt-20 px-10">
      <div class="py-2 px-4 text-white dark:text-gray-100 flex justify-between items-center">
        <div class="py-2 text-gray-800 rounded-t-lg text-xl font-bold">
          รายงานผลข้อมูลทุนการศึกษา
        </div>
        <div class="flex-1 mr-3 ml-3">
          <hr class="border-t border-gray-300">
        </div>
        <!-- ปุ่มสำหรับเลือกประเภททุน -->
<div class="flex space-x-3 justify-end">
  <button 
    @click="selectScholarship('0')" 
    :class="{'bg-gray-800 text-white': selectedType === '0', 'bg-gray-200 text-gray-800': selectedType !== '0'}"
    class="px-4 py-2 rounded-md"
  >
    ทุนเพชรอินทนิล
  </button>
  <button 
    @click="selectScholarship('1')" 
    :class="{'bg-gray-800 text-white': selectedType === '1', 'bg-gray-200 text-gray-800': selectedType !== '1'}"
    class="px-4 py-2 rounded-md"
  >
    ทุนงดเว้นค่าธรรมเนียมการศึกษา
  </button>
</div>

      </div>
    </div>

    <div class="max-w-full sm:max-w-7xl mx-auto py-2 sm:px-6 lg:px-8 bg-white sm:rounded-xl overflow-x-auto">
      <div v-if="selectedType === '1'" class="mb-4">
        <label for="faculty-select" class="block mb-2 text-gray-700">เลือกคณะ:</label>
        <select id="faculty-select" v-model="selectedFaculty" class="border border-gray-300 rounded-md p-2">
          <option value="">ทั้งหมด</option>
          <option value="คณะครุศาสตร์">คณะครุศาสตร์</option>
          <option value="คณะวิทยาศาสตร์และเทคโนโลยี">คณะวิทยาศาสตร์และเทคโนโลยี</option>
          <option value="คณะมนุษศาสตร์และเทคโนโลยี">คณะมนุษศาสตร์และเทคโนโลยี</option>
          <option value="คณะวิทยาการจัดการ">คณะวิทยาการจัดการ</option>
          <option value="คณะเทคโนโลยี">คณะเทคโนโลยี</option>
          <option value="คณะเกษตรและอุตสาหกรรมเกษตร">คณะเกษตรและอุตสาหกรรมเกษตร</option>
        </select>
      </div>

      <table class="min-w-full divide-y divide-gray-200">
        <thead>
          <TableRow>
            <TableHeaderCell class="px-6 py-3 text-left text-md font-medium text-white uppercase tracking-wider">ลำดับ</TableHeaderCell>
            <TableHeaderCell class="px-6 py-3 text-left text-md font-medium text-white uppercase tracking-wider">ชื่อ - สกุล</TableHeaderCell>
            <TableHeaderCell v-if="selectedType === '0'" class="px-6 py-3 text-left text-md font-medium text-white uppercase tracking-wider">ทักษะ</TableHeaderCell>
            <TableHeaderCell class="px-6 py-3 text-left text-md font-medium text-white uppercase tracking-wider">ประเภททุน</TableHeaderCell>
            <TableHeaderCell v-if="selectedType === '1'" class="px-6 py-3 text-left text-md font-medium text-white uppercase tracking-wider">คณะ</TableHeaderCell>
            <TableHeaderCell v-if="selectedType === '1'" class="px-6 py-3 text-left text-md font-medium text-white uppercase tracking-wider">สาขา</TableHeaderCell>
            <TableHeaderCell v-if="selectedType === '1'" class="px-6 py-3 text-left text-md font-medium text-white uppercase tracking-wider">ค่าเทอม</TableHeaderCell>
          </TableRow>
        </thead>

        <tbody class="text-left">
          <tr v-if="filteredApplications.length === 0">
            <td colspan="6" class="px-6 py-4 text-center text-md text-gray-500">ไม่มีข้อมูล</td>
          </tr>
          <tr v-for="(application, index) in filteredApplications" :key="application.id">
            <TableDataCell class="px-6 py-4 whitespace-nowrap">{{ index + 1 }}</TableDataCell>
            <TableDataCell class="px-6 py-4 whitespace-nowrap">
              <template v-if="application.title === '0'">นาย</template>
              <template v-else-if="application.title === '1'">นาง</template>
              <template v-else-if="application.title === '2'">นางสาว</template>
              {{ application.fname }} {{ application.lname }}
            </TableDataCell>
            <TableDataCell v-if="selectedType === '0'" class="px-6 py-4 whitespace-nowrap">{{ getAbility(application.type_ability) }}</TableDataCell>
            <TableDataCell class="px-6 py-4 whitespace-nowrap">{{ getScholarType(application.scholar_type) }}</TableDataCell>
            <TableDataCell v-if="selectedType === '1'" class="px-6 py-4 whitespace-nowrap">{{ application.faculty || '-' }}</TableDataCell>
            <TableDataCell v-if="selectedType === '1'" class="px-6 py-4 whitespace-nowrap">{{ application.branch || '-' }}</TableDataCell>
            <TableDataCell v-if="selectedType === '1'" class="px-6 py-4 whitespace-nowrap">{{ application.tuition_fee }}</TableDataCell>
          </tr>
        </tbody>

        <tfoot v-if="filteredApplications.length > 0">
          <tr v-if="selectedType === '0'">
            <TableDataCell colspan="3" class="px-6 py-4 text-right text-md"> 
              <strong>จำนวนนักศึกษาทุนเพชรอินทนิล</strong>
            </TableDataCell>
            <TableDataCell class="px-6 py-4 text-md"> 
              <strong>{{ countByType.type_0_count }}</strong>
            </TableDataCell>
          </tr>
          <tr v-if="selectedType === '1'">
            <TableDataCell colspan="5" class="px-6 py-4 text-right text-md"> 
              <strong>จำนวนนักศึกษาทุนงดเว้นค่าธรรมเนียมการศึกษา</strong>
            </TableDataCell>
            <TableDataCell class="px-6 py-4 text-md"> 
              <strong>{{ countByType.type_1_count }}</strong>
            </TableDataCell>
          </tr>
          <tr v-if="selectedType === '1'">
            <TableDataCell colspan="5" class="px-6 py-4 text-right text-md"> 
              <strong>รวมค่าเทอมทั้งหมด</strong>
            </TableDataCell>
            <TableDataCell class="px-6 py-4 text-md"> 
              <strong>{{ totalFees }}</strong>
            </TableDataCell>
          </tr>
        </tfoot>
      </table>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Table from "@/Components/Table.vue";
import TableRow from "@/Components/TableRow.vue";
import TableHeaderCell from "@/Components/TableHeaderCell.vue";
import TableDataCell from "@/Components/TableDataCell.vue";

const props = defineProps({
  applications: Array,
  totalFees: Number,
  countByType: Object,
});
const selectedType = ref('0'); // ตั้งค่าเริ่มต้นเป็นทุนเพชรอินทนิล
const selectedFaculty = ref(''); // สร้างตัวแปรสำหรับเลือกคณะ

const filteredApplications = computed(() => {
  return props.applications.filter(app => {
    const matchesType = app.scholar_type == selectedType.value;
    const matchesFaculty = !selectedFaculty.value || app.faculty === selectedFaculty.value; // ใช้เงื่อนไขนี้เพื่อตรวจสอบคณะ
    return matchesType && matchesFaculty;
  });
});

const getAbility = (type) => {
  const abilities = {
    1: 'ประเภททุนเรียนดี',
    2: 'ประเภททุนเรียนดีแต่ขาดแคลนทุนทรัพย์',
    3: 'ประเภททุนความสามารถพิเศษด้านกีฬา',
    4: 'ประเภททุนความสามารถพิเศษด้านผลงานความคิดสร้างสรรค์',
    5: 'ประเภททุนความสามารถพิเศษด้านดนตรี',
    6: 'ประเภททุนความสามารถพิเศษด้านการแสดง',
  };
  return abilities[type] || 'ไม่ทราบทักษะ';
};
const selectScholarship = (type) => {
  selectedType.value = type;
  if (type === '0') {
    selectedFaculty.value = ''; // รีเซ็ตคณะเมื่อเลือกทุนเพชรอินทนิล
  }
};

const getScholarType = (type) => {
  const types = {
    0: 'ทุนเพชรอินทนิล',
    1: 'ทุนงดเว้นค่าธรรมเนียมการศึกษา',
  };
  return types[type] || 'ไม่ทราบประเภททุน';
};
</script>

<style scoped>
.table-header {
  background-color: #1f2937; /* Tailwind's gray-800 */
}
.table-cell {
  background-color: #f3f4f6; /* Tailwind's gray-200 */
}
</style>
