<script setup>
import { ref, watch, defineProps, computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { usePermission } from '@/composables/permissions';
import Table from "@/Components/Table.vue";
import TableRow from "@/Components/TableRow.vue";
import TableHeaderCell from "@/Components/TableHeaderCell.vue";
import TableDataCell from "@/Components/TableDataCell.vue";
const { hasRole } = usePermission();

const props = defineProps({
  academicYears: Array,
  summary: Object,
  scholarships: Array,
  applications: Array
});

const selectedYear = ref('');
const searchQuery = ref('');
const selectedScholarship = ref('');
console.log(props.summary);

const filteredSummary = computed(() => {
  let filtered = {};

  // กรองตามปีการศึกษา
  if (selectedYear.value) {
    filtered[selectedYear.value] = props.summary[selectedYear.value] || [];
  } else {
    filtered = props.summary; // ถ้าไม่เลือกปีการศึกษาให้ใช้ทั้งหมด
  }

  // กรองตามประเภททุน (ใช้ scholarType)
  if (selectedScholarship.value) {
    // วนลูปตามปี
    Object.keys(filtered).forEach(year => { 
      const activities = Object.values(filtered[year]); 
      const filteredActivities = activities.filter(activity => String(activity.scholarType) === selectedScholarship.value);

      filtered[year] = filteredActivities;
      console.log(`Year: ${year}`, filtered[year]);
    });
  }

  return filtered;
});
watch([selectedScholarship, selectedYear], () => {
  console.log("Selected Scholarship:", selectedScholarship.value);
  console.log("Filtered Summary:", filteredSummary.value);
});

</script>

<template>

  <Head title="Activity Report" />
  <AuthenticatedLayout>
    <div class="max-w-7xl mx-auto mb-3 py-4 mt-20 px-10">
      <div class="py-2 px-4 text-white dark:text-gray-100 flex justify-between items-center">
        <div class="py-2 text-gray-800 rounded-t-lg text-xl font-bold">สรุปข้อมูลกิจกรรม</div>
        <div class="flex-1 mr-3 ml-3">
          <hr class="border-t border-gray-300">
        </div>
        <div class="flex justify-end space-x-2">
          <div class="w-40">
            <label for="academicYear" class="block text-md font-medium text-gray-700">เลือกปีการศึกษา</label>
            <select id="academicYear" v-model="selectedYear"
              class="block w-full mt-1 text-gray-800 border-gray-300 rounded-md shadow-sm focus:border-gray-800 focus:ring-gray-800 sm:text-md">
              <option value="">ทั้งหมด</option>
              <option v-for="year in props.academicYears" :key="year" :value="year">{{ year }}</option>
            </select>
          </div>
          <div class="w-60">
            <label for="scholarshipType" class="block text-md font-medium text-gray-700">เลือกประเภททุน</label>
            <select id="scholarshipType" v-model="selectedScholarship"
              class="block w-full mt-1 text-gray-800 border-gray-300 rounded-md shadow-sm focus:border-gray-800 focus:ring-gray-800 sm:text-md">
              <option value="">ทั้งหมด</option>
              <option value="0">ทุนเพชรอินทนิล</option>
              <option value="1">ทุนงดเว้นค่าธรรมเนียมการศึกษา</option>
              <option value="2">ทุนสำหรับนักศึกษาต่างชาติ</option>
            </select>
          </div>
        </div>
      </div>
    </div>


    <div class="max-w-full sm:max-w-7xl mx-auto py-2 sm:px-6 lg:px-8 bg-white sm:rounded-xl overflow-x-auto">
      <Table class="min-w-full divide-y divide-gray-200">
        <template #header>
          <TableRow>
            <TableHeaderCell class="px-6 py-3 text-left text-md font-medium text-white uppercase tracking-wider">ชื่อ -
              สกุล</TableHeaderCell>
            <TableHeaderCell class="px-6 py-3 text-left text-md font-medium text-white uppercase tracking-wider">
              ปีการศึกษา</TableHeaderCell>
            <TableHeaderCell class="px-6 py-3 text-left text-md font-medium text-white uppercase tracking-wider">
              ชื่อทุนการศึกษา</TableHeaderCell>
            <TableHeaderCell
              class="px-6 py-3 text-left text-md font-medium text-white uppercase text-center tracking-wider">
              จำนวนชั่วโมงขั้นต่ำ</TableHeaderCell>
            <TableHeaderCell
              class="px-6 py-3 text-left text-md font-medium text-white uppercase text-center tracking-wider">
              จำนวนชั่วโมงสะสม</TableHeaderCell>
            <TableHeaderCell v-if="hasRole('admin') || hasRole('officer')"
              class="px-6 py-3 text-left text-md font-medium text-white uppercase tracking-wider">เพิ่มเติม
            </TableHeaderCell>
          </TableRow>
        </template>
        <template #default>
          <template v-for="(yearGroup, year) in filteredSummary" :key="year">
            <TableRow v-for="activity in yearGroup" :key="activity.user_id" :class="{
              'bg-green-200': (activity.scholarType === '0' && activity.total_hours >= 100) ||
                (activity.scholarType === '1' && activity.total_hours > 50)
            }">
              <TableDataCell class="px-6 py-4 whitespace-nowrap text-md text-gray-800">
                <template v-if="activity.title === '0'">นาย</template>
                <template v-else-if="activity.title === '1'">นาง</template>
                <template v-else-if="activity.title === '2'">นางสาว</template>
                {{ activity.fname }} {{ activity.lname }}
              </TableDataCell>
              <TableDataCell class="px-6 py-4 text-center whitespace-nowrap text-md text-gray-800">{{ activity.years }}
              </TableDataCell>
              <TableDataCell class="px-6 py-4 whitespace-nowrap text-md text-gray-800">
                <span v-if="activity.scholarType === '0'">ทุนเพชรอิทนิล</span>
                <span v-else-if="activity.scholarType === '1'">ทุนงดเว้นค่าธรรมเนียมการศึกษา</span>
                <span v-else>ทุนสำหรับนักศึกษาต่างชาติ</span>
              </TableDataCell>
              <TableDataCell class="px-6 py-4 text-center whitespace-nowrap text-md text-gray-800">{{ activity.min_hours
                }}
              </TableDataCell>
              <TableDataCell class="px-6 py-4 text-center whitespace-nowrap text-md">{{ activity.total_hours }} / ปีการศึกษา
              </TableDataCell>
              <TableDataCell class="px-6 py-4 whitespace-nowrap text-md text-gray-800"
                v-if="hasRole('admin') || hasRole('officer')">
                <Link :href="route('activity_saves.user', activity.user_id)" class="custom-button-primary">รายละเอียด
                </Link>
              </TableDataCell>
            </TableRow>
          </template>
        </template>
      </Table>
    </div>
  </AuthenticatedLayout>
</template>

<style scoped>
.high-hours {
  color: green !important;
}
</style>
