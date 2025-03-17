<script setup>
import { ref, computed, watch } from "vue";
import VueMultiselect from "vue-multiselect";
import { useForm } from "@inertiajs/vue3";
import Swal from "sweetalert2";

const props = defineProps({
  user: {
    type: Object,
    required: true,
  },
  roles: {
    type: Array,
    required: true,
  },
});

const form = useForm({
  name: props.user?.name || "",
  email: props.user?.email || "",
  roles: props.user?.roles.length > 0 ? props.user.roles : [],
}); 
const filteredRoles = computed(() => {
  const userRoles = props.user?.roles?.map((role) => role.name) || []; 
  if (userRoles.length === 0) {
    return props.roles;
  } 
  if (userRoles.includes("admin")) {
    return props.roles.filter((role) =>
      ["officer", "manager", "admin"].includes(role.name)
    );
  } else if (userRoles.includes("officer") || userRoles.includes("manager")) {
    return props.roles.filter((role) =>
      ["officer", "manager", "admin"].includes(role.name)
    );
  } else if (userRoles.includes("scholar") || userRoles.includes("intlscholar") || userRoles.includes("student")) {
    return props.roles.filter((role) =>
      ["scholar", "intlscholar", "student"].includes(role.name)
    );
  } else if (userRoles.includes("member")) {
    return props.roles.filter((role) => role.name === "scholar");
  } 
  return [];
}); 
const uniqueRoles = computed(() => {
  if (form.roles.length > 1) { 
    return [form.roles[form.roles.length - 1]];
  }
  return form.roles;
}); 
const submit = () => {
  form.roles = uniqueRoles.value;

  if (form.roles.length === 0) {
    Swal.fire({
      icon: 'error',
      title: 'ข้อผิดพลาด',
      text: 'จำเป็นต้องเลือกบทบาท!',
    });
    return;
  }

  // Log ข้อมูลก่อนส่ง
  console.log("ข้อมูลที่ส่งไปยัง users.updatescholar:", {
    userId: props.user?.id,
    data: {
      ...form.data,
      roles: form.roles,
    }
  });

  form.put(route("users.updatescholar", props.user?.id), {
    data: {
      ...form.data,
      roles: form.roles,
    },
    onSuccess: () => {
      Swal.fire({
        icon: 'success',
        title: 'สำเร็จ!',
        text: 'บทบาทของผู้ใช้ได้ถูกอัปเดตแล้ว',
        customClass: {
          confirmButton: 'bg-green-500 hover:bg-green-600 text-white',
        },
        timer: 1500,
        timerProgressBar: true,
      });
    },
    onError: (errors) => {
      console.error("เกิดข้อผิดพลาด:", errors); // แสดง error ใน console
      Swal.fire({
        icon: 'error',
        title: 'ไม่สามารถอัปเดตได้',
        text: 'มีปัญหาในการอัปเดตบทบาทของผู้ใช้',
        timerProgressBar: true,
        confirmButtonColor: '#d33',
      });
    }
  });
};


watch(() => form.roles, (newRoles) => {
  if (newRoles.length > 0) {
    submit();
  }
});
</script>

<template>
  <form @submit.prevent="submit" >
    <label for="roles">Roles</label>
    
    <VueMultiselect v-model="form.roles" :options="filteredRoles" track-by="id" label="name" placeholder="Pick roles"
      :multiple="true" :max="2" />
    <span v-if="form.errors.roles" class="mt-2 text-red-500">{{ form.errors.roles }}</span>
  </form>
</template>

<style src="vue-multiselect/dist/vue-multiselect.css"></style>
