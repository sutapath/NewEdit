<template>
    <div>
        <div class="min-h-screen bg-white dark:bg-white ">
            <nav class="fixed top-0 left-0 w-full bg-white dark:bg-gray-800 border-b  z-50">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex">
                            <div class="shrink-0 flex items-center">
                                <Link :href="route('dashboard')">
                                <ApplicationLogo
                                    class="block h-20 w-auto fill-current text-gray-800 dark:text-gray-200" />
                                </Link>
                            </div>
                            <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                                <NavLink :href="route('dashboard')" :active="route().current('dashboard')">
                                    หน้าหลัก
                                </NavLink>
                                <NavLink v-if="hasRole('admin') " :href="route('users.index')"
                                    :active="route().current('users.index')">
                                    ผู้ใช้งาน
                                </NavLink>
                                <NavLink v-if="hasRole('scholar')" :href="route('scholarship_applications.index')"
                                    :active="route().current('scholarship_applications.index')">
                                    ข้อมูลทุนการศึกษา
                                </NavLink>
                                <div class="flex justify-center items-center"
                                    v-if="hasRole('student') || hasRole('admin') || hasRole('member') || hasRole('officer')">
                                    <Dropdown align="left" width="48">
                                        <template #trigger>
                                            <span class="inline-flex rounded-md">
                                                <button type="button"
                                                    :class="{ 'text-blue-500 font-bold': isScholarshipDropdownActive, 'text-gray-500': !isScholarshipDropdownActive }"
                                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                                                    ทุนการศึกษา
                                                    <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                                        viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd"
                                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                </button>
                                            </span>
                                        </template>
                                        <template #content>
                                            <ResponsiveNavLink :href="route('scholarships.index')"
                                                :class="{ 'text-blue-500 font-bold text-sm': isActiveRoute('scholarships.index') }">
                                                ทุนการศึกษา
                                            </ResponsiveNavLink>
                                            <ResponsiveNavLink v-if="hasRole('student') || hasRole('member')"
                                                :href="route('applications.index')"
                                                :class="{ 'text-blue-500 font-bold text-sm': isActiveRoute('applications.index') }">
                                                ข้อมูลการสมัคร
                                            </ResponsiveNavLink>
                                            <ResponsiveNavLink v-if="hasRole('admin') || hasRole('officer')"
                                                :href="route('scholarship_applications.indexadmin')"
                                                :class="{ 'text-blue-500 font-bold text-sm': isActiveRoute('scholarship_applications.indexadmin') }">
                                                ตรวจข้อมูลการสมัคร
                                            </ResponsiveNavLink>
                                            <ResponsiveNavLink v-if="hasRole('admin') || hasRole('officer') || hasRole('student') "
                                                :href="route('scholarship_applications.interview')"
                                                :class="{ 'text-blue-500 font-bold text-sm': isActiveRoute('scholarship_applications.interview') }">
                                                สัมภาษณ์
                                            </ResponsiveNavLink>
                                            <ResponsiveNavLink v-if="hasRole('admin')"
                                                :href="route('scholarship_applications.scholarstudents')"
                                                :class="{ 'text-blue-500 font-bold text-sm': isActiveRoute('scholarship_applications.scholarstudents') }">
                                                นักศึกษาทุน
                                            </ResponsiveNavLink>
                                            <ResponsiveNavLink v-if="hasRole('student') || hasRole('member') || hasRole('admin')" 
                                                :href="route('scholarship_applications.AllStudents')"
                                                :class="{ 'text-blue-500 font-bold text-sm': isActiveRoute('scholarship_applications.AllStudents') }">
                                                ข้อมูลนักศึกษาทุน
                                            </ResponsiveNavLink>
                                        </template>
                                    </Dropdown>
                                </div>



                                <!-- กิจกรรม Dropdown -->
                                <div class="flex justify-center items-center"
                                    v-if="hasRole('scholar') || hasRole('intlscholar') || hasRole('admin') || hasRole('officer')">
                                    <Dropdown align="left" width="48">
                                        <template #trigger>
                                            <span class="inline-flex rounded-md">
                                                <button type="button"
                                                    :class="{ 'text-blue-500 font-bold': isActivityDropdownActive, 'text-gray-500': !isActivityDropdownActive }"
                                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                                                    กิจกรรม
                                                    <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                                        viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd"
                                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                </button>
                                            </span>
                                        </template>
                                        <template #content>
                                            <ResponsiveNavLink :href="route('activities.index')"
                                                :class="{ 'text-blue-500 font-bold text-sm': isActiveRoute('activities.index') }">
                                                กิจกรรม
                                            </ResponsiveNavLink>
                                            <ResponsiveNavLink
                                                v-if="hasRole('scholar') || hasRole('intlscholar')  || hasRole('admin') || hasRole('officer')"
                                                :href="route('activity_registrations.index')"
                                                :class="{ 'text-blue-500 font-bold text-sm': isActiveRoute('activity_registrations.index') }">
                                                ข้อมูลกิจกรรมนักศึกษา
                                            </ResponsiveNavLink>
                                            <ResponsiveNavLink
                                                v-if="hasRole('scholar') || hasRole('intlscholar')  || hasRole('admin') || hasRole('officer')"
                                                :href="route('activity_saves.index')"
                                                :class="{ 'text-blue-500 font-bold text-sm': isActiveRoute('activity_saves.index') }">
                                                บันทึกกิจกรรม
                                            </ResponsiveNavLink>
                                        </template>
                                    </Dropdown>
                                </div>

                                <div class="flex justify-center items-center"
                                    v-if="hasRole('admin') || hasRole('officer')|| hasRole('manager')">
                                    <Dropdown align="left" width="48">
                                        <template #trigger>
                                            <span class="inline-flex rounded-md">
                                                <button type="button"
                                                    :class="{ 'text-blue-500 font-bold': isActivityDropdownReport, 'text-gray-500': !isActivityDropdownReport }"
                                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                                                    รายงาน
                                                    <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                                        viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd"
                                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                </button>
                                            </span>
                                        </template>
                                        <template #content
                                            v-if="hasRole('admin') || hasRole('officer') || hasRole('manager')">
                                            <ResponsiveNavLink :href="route('scholarship_applications.secoundreport')"
                                                :class="{ 'text-blue-500 font-bold text-sm': isActiveRoute('scholarship_applications.secoundreport') }">
                                                สรุปข้อมูลทุนการศึกษา
                                            </ResponsiveNavLink>
                                            <ResponsiveNavLink :href="route('scholarship_applications.report')"
                                                :class="{ 'text-blue-500 font-bold text-sm': isActiveRoute('scholarship_applications.report') }">
                                                สรุปข้อมูลกิจกรรม
                                            </ResponsiveNavLink>
                                        </template>
                                    </Dropdown>
                                </div>

                                <NavLink
                                    v-if="hasRole('scholar') || hasRole('intlscholar') || hasRole('admin') || hasRole('officer')"
                                    :href="route('docs_sends.index')" :active="route().current('docs_sends.index')">
                                    เอกสาร
                                </NavLink>

                            </div>
                        </div>

                        <div class="hidden sm:flex sm:items-center sm:ml-6">
                            <div class="ml-3 relative">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button type="button"
                                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                                                {{ getTitle($page.props.auth.user.title) }}
                                                {{ $page.props.auth.user.fname }}
                                                {{ $page.props.auth.user.lname }}
                                                <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd"
                                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>
                                    <template #content>
                                        <div class="px-4 py-2 text-blue-800 font-bold text-sm">จัดการบัญชีผู้ใช้
                                        </div>

                                        <DropdownLink :href="route('users.index')" v-if="hasRole('admin')">จัดการผู้ใช้
                                        </DropdownLink>
                                        <DropdownLink :href="route('profile.edit')">
                                            บัญชี
                                        </DropdownLink>

                                        <div class="px-4 py-2 text-blue-800 font-bold text-sm">
                                            ทุนการศึกษา
                                        </div>
                                        <DropdownLink
                                            v-if="hasRole('student') || hasRole('member') || hasRole('admin') || hasRole('officer')"
                                            :href="route('scholarships.index')">
                                            ทุนการศึกษา
                                        </DropdownLink>
                                        <DropdownLink v-if="hasRole('student') || hasRole('member')"
                                            :href="route('applications.index')">
                                            ข้อมูลการสมัคร
                                        </DropdownLink>
                                        <DropdownLink v-if="hasRole('admin') || hasRole('officer')"
                                            :href="route('scholarship_applications.indexadmin')">
                                            ตรวจข้อมูลการสมัคร
                                        </DropdownLink>

                                        <DropdownLink v-if="hasRole('admin') || hasRole('officer')"
                                            :href="route('scholarship_applications.interview')">
                                            สัมภาษณ์
                                        </DropdownLink>

                                        <DropdownLink v-if="hasRole('admin')"
                                            :href="route('scholarship_applications.scholarstudents')">
                                            นักศึกษาทุน
                                        </DropdownLink>
                                        <DropdownLink v-if="hasRole('scholar') "
                                            :href="route('scholarship_applications.index')">
                                            ข้อมูลทุนการศึกษา
                                        </DropdownLink>

                                        <div class="px-4 py-2 text-blue-800 font-bold text-sm"
                                            v-if="hasRole('admin') || hasRole('officer') || hasRole('scholar') || hasRole('intlscholar')">
                                            กิจกรรม
                                        </div>
                                        <DropdownLink
                                            v-if="hasRole('admin') || hasRole('officer')|| hasRole('scholar') || hasRole('intlscholar')"
                                            :href="route('activities.index')">
                                            กิจกรรม
                                        </DropdownLink>

                                        <DropdownLink v-if="hasRole('scholar') || hasRole('intlscholar')"
                                            :href="route('activity_registrations.index')">
                                            ข้อมูลกิจกรรมนักศึกษา
                                        </DropdownLink>
                                        <DropdownLink v-if="hasRole('scholar') || hasRole('intlscholar')"
                                            :href="route('activity_saves.index')">
                                            บันทึกกิจกรรม
                                        </DropdownLink>
                                        <DropdownLink v-if="hasRole('admin') || hasRole('officer')|| hasRole('manager')"
                                            :href="route('scholarship_applications.report')">
                                            สรุปข้อมูล
                                        </DropdownLink>

                                        <div v-if="hasRole('intlscholar') || hasRole('scholar') || hasRole('admin') || hasRole('officer')"
                                            class="px-4 py-2 text-blue-800 font-bold text-sm">
                                            การดำเนินการ
                                        </div>

                                        <DropdownLink
                                            v-if="hasRole('intlscholar') || hasRole('scholar') || hasRole('admin') || hasRole('officer')"
                                            :href="route('docs_sends.index')">
                                            การส่งเอกสาร
                                        </DropdownLink>

                                        <hr />



                                        <div v-if="hasRole('admin') || hasRole('officer') || hasRole('manager')"
                                            class="px-4 py-2 text-blue-800 font-bold text-sm">
                                            รายงาน
                                        </div>
                                        <DropdownLink
                                            v-if="hasRole('admin') || hasRole('officer') || hasRole('manager')"
                                            :href="route('scholarship_applications.secoundreport')"
                                            :class="{ 'text-blue-500 font-bold text-sm': isActiveRoute('scholarship_applications.secoundreport') }">
                                            สรุปข้อมูลทุนการศึกษา
                                        </DropdownLink>
                                        <DropdownLink
                                            v-if="hasRole('admin') || hasRole('officer') || hasRole('manager')"
                                            :href="route('scholarship_applications.report')"
                                            :class="{ 'text-blue-500 font-bold text-sm': isActiveRoute('scholarship_applications.report') }">
                                            สรุปข้อมูลกิจกรรม
                                        </DropdownLink>
                                        <hr />

                                        <DropdownLink :href="route('logout')" method="post" as="button"
                                            class="text-red-500 font-bold text-sm">
                                            ออกจากระบบ
                                        </DropdownLink>

                                    </template>
                                </Dropdown>
                            </div>
                        </div>
                        <div class="-mr-2 flex items-center sm:hidden">
                            <button @click="showingNavigationDropdown = !showingNavigationDropdown"
                                class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out">
                                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                    <path :class="{
                                        hidden: showingNavigationDropdown,
                                        'inline-flex': !showingNavigationDropdown,
                                    }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16" />
                                    <path :class="{
                                        hidden: !showingNavigationDropdown,
                                        'inline-flex': showingNavigationDropdown,
                                    }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>


                <div :class="{
                    block: showingNavigationDropdown,
                    hidden: !showingNavigationDropdown,
                }" class="sm:hidden">
                    <div class="pt-2 pb-3 space-y-1">
                        <ResponsiveNavLink :href="route('dashboard')" :active="route().current('dashboard')">
                            หน้าหลัก
                        </ResponsiveNavLink>
                    </div>

                    <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
                        <div class="px-4">
                            <div class="font-medium text-base text-gray-800 dark:text-gray-200">
                                {{ $page.props.auth.user.name }}
                            </div>
                            <div class="font-medium text-sm text-gray-500">
                                {{ $page.props.auth.user.email }}
                            </div>
                        </div>

                        <div class="px-4 py-2 text-blue-800 font-bold text-sm">จัดการบัญชีผู้ใช้
                        </div>

                        <ResponsiveNavLink :href="route('users.index')" v-if="hasRole('admin')">จัดการบัญชีผู้ใช้
                        </ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('profile.edit')">
                            บัญชี
                        </ResponsiveNavLink>

                        <div class="px-4 py-2 text-blue-800 font-bold text-sm">
                            ทุนการศึกษา
                        </div>
                        <ResponsiveNavLink
                            v-if="hasRole('student') || hasRole('member') || hasRole('admin') || hasRole('officer')"
                            :href="route('scholarships.index')">
                            ทุนการศึกษา
                        </ResponsiveNavLink>
                        <ResponsiveNavLink v-if="hasRole('student') || hasRole('member')"
                            :href="route('applications.index')">
                            ข้อมูลการสมัคร
                        </ResponsiveNavLink>
                        <ResponsiveNavLink v-if="hasRole('admin') || hasRole('officer')"
                            :href="route('scholarship_applications.indexadmin')">
                            ตรวจข้อมูลการสมัคร
                        </ResponsiveNavLink>

                        <ResponsiveNavLink v-if="hasRole('admin') || hasRole('officer')"
                            :href="route('scholarship_applications.interview')">
                            สัมภาษณ์
                        </ResponsiveNavLink>

                        <ResponsiveNavLink v-if="hasRole('admin')"
                            :href="route('scholarship_applications.scholarstudents')">
                            นักศึกษาทุน
                        </ResponsiveNavLink>
                        <ResponsiveNavLink v-if="hasRole('scholar') " :href="route('scholarship_applications.index')">
                            ข้อมูลทุนการศึกษา
                        </ResponsiveNavLink>

                        <div class="px-4 py-2 text-blue-800 font-bold text-sm"
                            v-if="hasRole('admin') || hasRole('officer') || hasRole('scholar') || hasRole('intlscholar')">
                            กิจกรรม
                        </div>
                        <ResponsiveNavLink
                            v-if="hasRole('admin') || hasRole('officer') || hasRole('scholar') || hasRole('intlscholar')"
                            :href="route('activities.index')">
                            กิจกรรมทั้งหมด
                        </ResponsiveNavLink>

                        <ResponsiveNavLink
                            v-if="hasRole('scholar') || hasRole('intlscholar')  || hasRole('admin') || hasRole('officer')"
                            :href="route('activity_registrations.index')">
                            ข้อมูลกิจกรรมนักศึกษา
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            v-if="hasRole('scholar') || hasRole('intlscholar')  || hasRole('admin') || hasRole('officer')"
                            :href="route('activity_saves.index')">
                            บันทึกกิจกรรม
                        </ResponsiveNavLink>
                        <ResponsiveNavLink v-if="hasRole('admin') || hasRole('officer') || hasRole('manager')"
                            :href="route('scholarship_applications.report')">
                            สรุปข้อมูล
                        </ResponsiveNavLink>

                        <div v-if="hasRole('intlscholar') || hasRole('scholar') || hasRole('admin') || hasRole('officer')"
                            class="px-4 py-2 text-blue-800 font-bold text-sm">
                            การดำเนินการ
                        </div>

                        <ResponsiveNavLink
                            v-if="hasRole('intlscholar') || hasRole('scholar') || hasRole('admin') || hasRole('officer')"
                            :href="route('docs_sends.index')">
                            การส่งเอกสาร
                        </ResponsiveNavLink>


                    </div>
                    <div v-if="hasRole('admin') || hasRole('officer') || hasRole('manager')"
                        class="px-4 py-2 text-blue-800 font-bold text-sm">
                        รายงาน
                    </div>
                    <ResponsiveNavLink v-if="hasRole('admin') || hasRole('officer') || hasRole('manager')"
                        :href="route('scholarship_applications.secoundreport')"
                        :class="{ 'text-blue-500 font-bold text-sm': isActiveRoute('scholarship_applications.secoundreport') }">
                        สรุปข้อมูลทุนการศึกษา
                    </ResponsiveNavLink>
                    <ResponsiveNavLink v-if="hasRole('admin') || hasRole('officer') || hasRole('manager')"
                        :href="route('scholarship_applications.report')"
                        :class="{ 'text-blue-500 font-bold text-sm': isActiveRoute('scholarship_applications.report') }">
                        สรุปข้อมูลกิจกรรม
                    </ResponsiveNavLink>
                    <hr />
                    <ResponsiveNavLink :href="route('logout')" method="post" as="button"
                        class="text-white font-bold text-sm bg-red-500">
                        ออกจากระบบ
                    </ResponsiveNavLink>
                </div>


            </nav>

            <!-- Page Heading -->
            <header class="bg-white dark:bg-gray-800 shadow mt-10" v-if="$slots.header">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <main class="mt-5">
                <slot />
            </main>
        </div>
    </div>
</template>

<script setup>
import { ref } from "vue";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import NavLink from "@/Components/NavLink.vue";
import ResponsiveNavLink from "@/Components/ResponsiveNavLink.vue";
import { Link } from "@inertiajs/vue3";
import { usePermission } from "@/composables/permissions";

const showingNavigationDropdown = ref(false);
const { hasRole } = usePermission();
import { computed } from 'vue';

// ฟังก์ชันเพื่อตรวจสอบ route ว่าเป็น active อยู่หรือไม่
const isActiveRoute = (routeName) => {
    return route().current(routeName);
};

// ตรวจสอบ dropdown ทุนการศึกษา active หรือไม่
const isScholarshipDropdownActive = computed(() => {
    return route().current('scholarships.index') || route().current('applications.index') ||
        route().current('scholarship_applications.indexadmin') || route().current('scholarship_applications.interview') ||
        route().current('scholarship_applications.scholarstudents');
});

// ตรวจสอบ dropdown กิจกรรม active หรือไม่
const isActivityDropdownActive = computed(() => {
    return route().current('activities.index') || route().current('activity_registrations.index') ||
        route().current('activity_saves.index');
});
const isActivityDropdownReport = computed(() => {
    return route().current('scholarship_applications.report') ||
        route().current('scholarship_applications.secoundreport');
});
const getTitle = (titleValue) => {
    switch (titleValue) {
        case '0':
            return 'นาย';
        case '1':
            return 'นาง';
        case '2':
            return 'นางสาว';
        default:
            return '';
    }
};


</script>
