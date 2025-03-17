<script setup>
import { ref, computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Modal from '@/Components/Modal.vue';
import DangerButton from '@/Components/DangerButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { usePermission } from '@/composables/permissions';

const { hasRole } = usePermission();
const form = useForm({});
const showConfirmDeleteModal = ref(false);
const applicationToDelete = ref(null);

// Data from the page props
const props = defineProps({
  applications: Array,
  currentUser: Object
});

const confirmDeleteApplication = (id) => {
  applicationToDelete.value = id;
  showConfirmDeleteModal.value = true;
};

const closeModal = () => {
  showConfirmDeleteModal.value = false;
  applicationToDelete.value = null;
};

const deleteApplication = () => {
  if (applicationToDelete.value) {
    form.delete(route('scholarship_applications.destroy', applicationToDelete.value), {
      onSuccess: () => closeModal()
    });
  }
};

const statusClass = (result) => {
  switch (result) {
    case '0':
      return 'bg-green-300 text-dark py-1 px-3 rounded-full'; // สีเขียวอ่อน
    case '1':
      return 'bg-yellow-300 text-dark py-1 px-3 rounded-full'; // สีเหลืองอ่อน
    case '2':
      return 'bg-gray-300 text-dark py-1 px-3 rounded-full'; // สีเทาอ่อน
    case '3':
      return 'bg-red-300 text-dark py-1 px-3 rounded-full'; // สีเทาอ่อน
    default:
      return '';
  }
};
const getScholarType = (scholarType) => {
  const scholarTypes = {
    0: 'ทุนเพชรอินทนิล',
    1: 'ทุนงดเว้นค่าธรรมเนียมการศึกษา'
  };
  return scholarTypes[scholarType] || 'ไม่ระบุ';
};

const getTypeAbility = (typeAbility) => {
  const typeAbilities = {
    1: 'ประเภททุนเรียนดี',
    2: 'ประเภททุนเรียนดีแต่ขาดแคลนทุนทรัพย์',
    3: 'ประเภททุนความสามารถพิเศษด้านกีฬา',
    4: 'ประเภททุนความสามารถพิเศษด้านผลงานความคิดสร้างสรรค์',
    5: 'ประเภททุนความสามารถพิเศษด้านดนตรี นาฏศิลป์',
    6: 'ประเภทกิจกรรมที่ส่งเสริมภาวะผู้นำ'
  };
  return typeAbilities[typeAbility] || '';
};
</script>

<template>

  <Head title="Admin Scholarship Applications" />

  <AuthenticatedLayout>
    <div class="max-w-7xl mx-auto mb-3 py-4 mt-20 px-10">
      <div class="flex justify-between items-center">
        <div class="py-2 mr-3 text-gray-800 rounded-t-lg text-xl font-bold">
          ข้อมูลเพิ่มเติม
        </div>

        <div class="flex-1">
          <hr class="border-t border-gray-300" />
        </div>

      </div>
    </div>


    <div class="max-w-full sm:max-w-7xl mx-auto py-4 sm:px-6 lg:px-8 bg-white sm:rounded-xl overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-700">
          <tr>
            <!-- <th class="px-6 py-3 text-left text-md  font-medium text-gray-700 uppercase tracking-wider">ลำดับ</th> -->
            <th class="px-6 py-3 text-left text-md  font-medium text-white uppercase tracking-wider">ชื่อ - สกุล</th>
            <th class="px-6 py-3 text-left text-md  font-medium text-white uppercase tracking-wider">หมายเลขโทรศัพท์
            </th>
            <th class="px-6 py-3 text-left text-md  font-medium text-white uppercase tracking-wider">ประเภท</th>
            <th class="px-6 py-3 text-left text-md  font-medium text-white uppercase tracking-wider"
              v-if="hasRole('admin') || hasRole('officer') || hasRole('student') || hasRole('member')">การตรวจสอบ</th>
            <th class="px-6 py-3 text-left text-md  font-medium text-white uppercase tracking-wider">Actions</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200" v-if="props.applications.length > 0">
          <template v-for="(application) in props.applications" :key="application.id">
            <tr v-if="application.user_id === props.currentUser.id">
              <td class="px-6 py-4 whitespace-nowrap text-md text-gray-700">
                <template v-if="application.title === '0'">นาย</template>
                <template v-else-if="application.title === '1'">นาง</template>
                <template v-else-if="application.title === '2'">นางสาว</template>
                {{ application.fname }} {{ application.lname }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-md text-gray-700">{{ application.phone }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-md text-gray-700">
                {{ getScholarType(application.scholar_type) }} {{ getTypeAbility(application.type_ability) }}
              </td>
              <td v-if="hasRole('admin') || hasRole('officer') || hasRole('student') || hasRole('member')"
                class="px-6 py-4 whitespace-nowrap text-md">
                <span :class="statusClass(application.result)">
                  <template v-if="application.result === '0'">ผ่าน</template>
                  <template v-else-if="application.result === '1'">แก้ไข</template>
                  <template v-else-if="application.result === '2'">รอการตรวจ</template>
                  <template v-else-if="application.result === '3'">ไม่ผ่านการพิจารณา</template>
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-md font-medium text-gray-700">
                <Link v-if="hasRole('admin') || hasRole('officer')"
                  :href="route('scholarship_applications.show', application.id)"
                  class="text-yellow-600 hover:text-yellow-900">
                ตรวจสอบ
                </Link>
                <PrimaryButton v-if="(hasRole('student') || hasRole('member')) && application.result !== 0"
                  @click="confirmDeleteApplication(application.id)" class="custom-button-danger mr-4">
                  ลบ
                </PrimaryButton>
                <Link v-if="hasRole('student') || hasRole('member') || hasRole('scholar')"
                  :href="route('scholarship_applications.show', application.id)" class="custom-button-primary ms-5">
                เพิ่มเติม
                </Link>
              </td>
            </tr>
          </template>
          <template v-if="['student', 'member'].includes(props.currentUser.role) &&
            !props.applications.some(application => application.user_id === props.currentUser.id)">
            <tr>
              <td colspan="5" class="px-6 py-4 text-md text-gray-700 text-center">
                ไม่มีข้อมูลการสมัคร
              </td>
            </tr>
          </template>

        </tbody>

        <tbody class="bg-white divide-y divide-gray-200" v-for="(application, index) in props.applications"
          :key="application.id">
          <tr v-if="hasRole('admin') || hasRole('officer')">
            <td class="px-6 py-4 whitespace-nowrap text-md font-medium text-gray-900">{{ index + 1 }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-md text-gray-700">
              <template v-if="application.title === '0'">นาย</template>
              <template v-else-if="application.title === '1'">นาง</template>
              <template v-else-if="application.title === '2'">นางสาว</template>
              {{ application.fname }} {{ application.lname }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-md text-gray-700">{{ application.phone }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-md text-gray-700">
              {{ getScholarType(application.scholar_type) }} {{ getTypeAbility(application.type_ability) }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-md font-medium text-gray-700">
              <!-- <Link v-if="hasRole('admin') || hasRole('officer')"
                :href="route('scholarship_applications.show', application.id)" class="custom-button-success">
              เพิ่มเติม
              </Link> -->
              <PrimaryButton
                v-if="(hasRole('student') || hasRole('member')) && application.result !== 0 && application.result !== '0'"
                @click="confirmDeleteApplication(application.id)" class="custom-button-danger mr-4">
                ลบ
              </PrimaryButton>


              <Link v-if="hasRole('student') || hasRole('member')"
                :href="route('scholarship_applications.show', application.id)" class="custom-button-success">
              เพิ่มเติม
              </Link>

            </td>
          </tr>
        </tbody>
      </table>
      <Modal :show="showConfirmDeleteModal" @close="closeModal">
        <div class="p-6">
          <h2 class="text-lg font-semibold text-slate-800">Confirm Deletion?</h2>
          <div class="mt-6 flex space-x-4">
            <DangerButton class="custom-button-success" @click="deleteApplication">Delete</DangerButton>
            <SecondaryButton @click="closeModal">Cancel</SecondaryButton>
          </div>
        </div>
      </Modal>
    </div>
  </AuthenticatedLayout>
</template>
