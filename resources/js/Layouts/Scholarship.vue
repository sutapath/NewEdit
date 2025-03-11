<template>
  <div class="w-full">
    <div class="w-full flex justify-end md:hidden">
      <button @click="toggleMenu" class="text-white bg-gray-900 rounded py-1 px-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16m-7 6h7" />
        </svg>
      </button>
    </div>

    <!-- Navigation Links -->
    <div :class="showingNavigationDropdown ? 'block' : 'hidden'" class="md:grid grid-cols-1 md:grid-cols-5 gap-4 mt-3">
      <Link :href="route('scholarships.index')" v-if="hasRole('student') || hasRole('member') ||hasRole('admin') || hasRole('officer')" :class="isActive('scholarships.index')"
        class="bg-gray-700 dark:bg-gray-800 overflow-hidden shadow-lg rounded-lg flex items-center hover:bg-gray-600 dark:hover:bg-gray-700 transition-colors px-4 py-2 w-full text-sm">
        <span class="text-white flex-1">ทุนการศึกษา</span>
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-900 bg-white rounded-lg" fill="none"
          viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
        </svg>
      </Link>

      <Link v-if="hasRole('student') || hasRole('member')" :href="route('applications.index')"
        :class="isActive('applications.index')"
        class="bg-gray-700 dark:bg-gray-800 overflow-hidden shadow-lg rounded-lg flex items-center hover:bg-gray-600 dark:hover:bg-gray-700 transition-colors px-4 py-2 mt-2 md:mt-0 w-full text-sm">
        <span class="text-white flex-1">ข้อมูลการสมัคร</span>
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-900 bg-white rounded-lg" fill="none"
          viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
        </svg>
      </Link>

      <Link v-if="hasRole('admin') || hasRole('officer')" :href="route('scholarship_applications.indexadmin')"
        :class="isActive('scholarship_applications.indexadmin')"
        class="bg-gray-700 dark:bg-gray-800 overflow-hidden shadow-lg rounded-lg flex items-center hover:bg-gray-600 dark:hover:bg-gray-700 transition-colors px-4 py-2 mt-2 md:mt-0 w-full text-sm">
        <span class="text-white flex-1">ตรวจข้อมูลการสมัคร</span>
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-900 bg-white rounded-lg" fill="none"
          viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
        </svg>
      </Link>

      <Link v-if="hasRole('admin') || hasRole('officer')" :href="route('scholarship_applications.interview')"
        :class="isActive('scholarship_applications.interview')"
        class="bg-gray-700 dark:bg-gray-800 overflow-hidden shadow-lg rounded-lg flex items-center hover:bg-gray-600 dark:hover:bg-gray-700 transition-colors px-4 py-2 mt-2 md:mt-0 w-full text-sm">
        <span class="text-white flex-1">สัมภาษณ์</span>
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-900 bg-white rounded-lg" fill="none"
          viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
        </svg>
      </Link>
      <Link v-if="hasRole('admin') || hasRole('officer')" :href="route('scholarship_applications.scholarstudents')"
        :class="isActive('scholarship_applications.scholarstudents')"
        class="bg-gray-700 dark:bg-gray-800 overflow-hidden shadow-lg rounded-lg flex items-center hover:bg-gray-600 dark:hover:bg-gray-700 transition-colors px-4 py-2 mt-2 md:mt-0 w-full text-sm">
        <span class="text-white flex-1">นักศึกษาทุน</span>
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-900 bg-white rounded-lg" fill="none"
          viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
        </svg>
      </Link>
    </div>
  </div>
</template>

<script setup>
import { Link } from "@inertiajs/vue3";
import { ref } from "vue";
import { usePermission } from "@/composables/permissions";

const showingNavigationDropdown = ref(false);
const { hasRole } = usePermission();

const toggleMenu = () => {
  showingNavigationDropdown.value = !showingNavigationDropdown.value;
};

// Function to check if the current route matches the given route name
const isActive = (routeName) => {
  return route().current(routeName) ? 'bg-blue-600 dark:bg-blue-600 text-white' : '';
};
</script>
