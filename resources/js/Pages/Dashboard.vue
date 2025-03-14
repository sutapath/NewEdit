<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import '../../css/style.css';
import { Head, Link } from "@inertiajs/vue3";

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    laravelVersion: String,
    phpVersion: String,
  scholarships: Array,

});
const images = {
  act01: '/assets/act-01.jpg',
  act02: '/assets/act-02.jpg',
  act03: '/assets/act-03.jpg',
  act04: '/assets/act-04.jpg',
  act05: '/assets/act-05.jpg',
  act06: '/assets/act-06.jpg',
};
</script>

<template>

  <Head title="Dashboard" />

  <AuthenticatedLayout>


    <section class="px-2 py-32 bg-white md:px-0 mt-20">
      <div class="container items-center max-w-6xl px-8 mx-auto xl:px-5">
        <div class="flex flex-wrap items-center sm:-mx-3">
          <div class="w-full md:w-1/2 md:px-3">
            <div
              class="w-full pb-6 space-y-6 sm:max-w-md lg:max-w-lg md:space-y-4 lg:space-y-8 xl:space-y-9 sm:pr-5 lg:pr-0 md:pb-0">
              <h1
                class="text-4xl font-extrabold tracking-tight text-gray-900 sm:text-5xl md:text-4xl lg:text-5xl xl:text-6xl">
                <span class="block xl:inline">ทุนการศึกษา </span>
                <span class="block text-purple-500 xl:inline">มหาวิทยาลัยราชภัฎสุรินทร์</span>
              </h1>
            </div>
          </div>
          <div class="w-full md:w-1/2">
            <div class="w-full h-auto overflow-hidden rounded-md sm:rounded-xl">
              <img src="https://cdn.devdojo.com/images/november2020/feature-graphic.png" alt="feature graphic">
            </div>
          </div>
        </div>
      </div>
    </section>
    <section v-if="scholarships.length !== 0" class="text-gray-700 body-font border-t border-gray-200">
      <div class="container px-5 py-12 mx-auto">
        <div class="flex flex-col text-center w-full mb-20">
          <h2 class="text-xs text-indigo-500 tracking-widest font-medium title-font mb-1">ข้อมูลทุนการศึกษา</h2>
          <h1 class="sm:text-3xl text-2xl font-medium title-font text-gray-900">ทุนการศึกษาของมหาวิทยาลัยราชภัฏสุรินทร์
          </h1>
        </div>
        <div class="flex flex-wrap justify-center -m-4">
          <!-- Loop through each scholarship -->
          <div v-for="(scholarship, index) in scholarships" :key="index" class="p-4 md:w-1/3">
            <div class="flex rounded-lg h-full bg-gray-100 p-8 flex-col">
              <div class="flex items-center mb-3">
                <div
                  class="w-8 h-8 mr-3 inline-flex items-center justify-center rounded-full bg-indigo-500 text-white flex-shrink-0">
                  <!-- Icon for Scholarship -->
                  <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    class="w-5 h-5" viewBox="0 0 24 24">
                    <path d="M12 2L2 7h20L12 2z"></path>
                    <path d="M12 22v-7"></path>
                    <path d="M5 7v9a2 2 0 002 2h10a2 2 0 002-2V7"></path>
                  </svg>
                </div>
                <h2 class="text-gray-900 text-lg title-font font-medium">{{ scholarship.scholar_name }}</h2>
              </div>
              <div class="flex-grow">
                <p class="leading-relaxed text-base">{{ scholarship.description }}</p>
                <p class="mt-2 text-gray-600">เปิดรับสมัคร: {{ scholarship.apply_date }}</p>
                <!-- แสดงวันที่เปิดรับสมัคร -->
                <a v-if="scholarship.link" :href="scholarship.link"
                  class="mt-3 text-indigo-500 inline-flex items-center">
                  เพิ่มเติม
                  <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    class="w-4 h-4 ml-2" viewBox="0 0 24 24">
                    <path d="M5 12h14M12 5l7 7-7 7"></path>
                  </svg>
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    <section class="text-gray-700 body-font border-t border-gray-200">
      <div class="container px-5 py-24 mx-auto">
        <div class="flex flex-col text-center w-full mb-20">
          <h2 class="text-xs text-indigo-500 tracking-widest font-medium title-font mb-1">ข้อมูลระบบ</h2>
          <h1 class="sm:text-3xl text-2xl font-medium title-font text-gray-900">ทุนการศึกษา มหาวิทยาลัยราชภัฏสุรินทร์
          </h1>
        </div>
        <div class="flex flex-wrap justify-center -m-4">
          <div class="p-4 md:w-1/3">
            <div class="flex rounded-lg h-full bg-gray-100 p-8 flex-col">
              <div class="flex items-center mb-3">
                <div
                  class="w-8 h-8 mr-3 inline-flex items-center justify-center rounded-full bg-indigo-500 text-white flex-shrink-0">
                  <!-- Icon for University -->
                  <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    class="w-5 h-5" viewBox="0 0 24 24">
                    <path d="M12 2L2 7h20L12 2z"></path>
                    <path d="M12 22v-7"></path>
                    <path d="M5 7v9a2 2 0 002 2h10a2 2 0 002-2V7"></path>
                  </svg>
                </div>
                <h2 class="text-gray-900 text-lg title-font font-medium">มหาวิทยาลัยราชภัฏสุรินทร์</h2>
              </div>
              <div class="flex-grow">
                <p class="leading-relaxed text-base">
                  มหาวิทยาลัยราชภัฏสุรินทร์เป็นสถาบันการศึกษาที่มีเป้าหมายในการพัฒนาการศึกษาและสร้างสรรค์สังคม
                  โดยมีทุนการศึกษาที่หลากหลายให้แก่นักศึกษาเพื่อสนับสนุนการศึกษาและพัฒนาทักษะที่จำเป็นสำหรับการทำงานในอนาคต
                  นอกจากนี้ยังมีหลักสูตรการเรียนการสอนที่ตอบสนองต่อความต้องการของตลาดแรงงาน</p>
                <a class="mt-3 text-indigo-500 inline-flex items-center" href="https://www.srru.ac.th/">เพิ่มเติม
                  <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    class="w-4 h-4 ml-2" viewBox="0 0 24 24">
                    <path d="M5 12h14M12 5l7 7-7 7"></path>
                  </svg>
                </a>
              </div>
            </div>
          </div>
          <div class="p-4 md:w-1/3">
            <div class="flex rounded-lg h-full bg-gray-100 p-8 flex-col">
              <div class="flex items-center mb-3">
                <div
                  class="w-8 h-8 mr-3 inline-flex items-center justify-center rounded-full bg-indigo-500 text-white flex-shrink-0">
                  <!-- Icon for Activity Hours System -->
                  <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    class="w-5 h-5" viewBox="0 0 24 24">
                    <path d="M4 6V4a2 2 0 012-2h12a2 2 0 012 2v2"></path>
                    <path d="M4 10h16v12H4V10z"></path>
                    <path d="M16 10v10M8 10v10"></path>
                  </svg>
                </div>
                <h2 class="text-gray-900 text-lg title-font font-medium">ระบบเก็บชั่วโมงกิจกรรม</h2>
              </div>
              <div class="flex-grow">
                <p class="leading-relaxed text-base">
                  ระบบเก็บชั่วโมงกิจกรรมของมหาวิทยาลัยราชภัฏสุรินทร์ช่วยให้นักศึกษาเก็บชั่วโมงกิจกรรมที่เข้าร่วมไว้ในระบบ
                  ซึ่งสามารถตรวจสอบชั่วโมงสะสมและประเมินความก้าวหน้าในการเข้าร่วมกิจกรรมต่าง ๆ ได้อย่างสะดวกสบาย
                  เพื่อส่งเสริมการพัฒนาทักษะนอกห้องเรียนและเตรียมความพร้อมสำหรับการทำงานในอนาคต</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>


    <footer class="bg-gray-800 text-white">
      <div class="container mx-auto max-w-4xl px-4">
        <div class="flex justify-between items-center py-4">

          <div class="flex flex-col">
            <h2 class="text-lg font-semibold">ข้อมูลมหาวิทยาลัย</h2>
            <p class="mt-1 text-sm">มหาวิทยาลัยราชภัฏสุรินทร์</p>
            <p class="text-sm">186 หมู่ 1 ถ.สุรินทร์-ปราสาท ต.นอกเมือง อ.เมืองสุรินทร์ จ.สุรินทร์ 32000</p>
            <p class="text-sm">โทรศัพท์: 044-710000</p>
            <p class="text-sm">โทรสาร: 044-514694</p>
            <p class="text-sm">อีเมล: saraban@srru.ac.th</p>
          </div>

          <!-- ข้อมูลติดต่อ -->
          <div class="text-center">
            <h3 class="text-md font-semibold">ข้อมูลติดต่อ</h3>
            <p class="text-sm">สำนักงาน: 123/1 ถนนมหาวิทยาลัย</p>
            <p class="text-sm">โทรศัพท์: 044-987-654</p>
          </div>
        </div>

        <div class="py-4 border-t border-gray-700 text-center"> <!-- เปลี่ยน py-6 เป็น py-4 -->
          <p class="text-sm">
            © 2024 มหาวิทยาลัยราชภัฏสุรินทร์. สงวนสิทธิ์.
          </p>
        </div>
      </div>
    </footer>

  </AuthenticatedLayout>
</template>

 