<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3'; 
import { Head, Link, usePage } from '@inertiajs/vue3';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
  user: Object,
});

const form = useForm({
  citizen_id: props.user.citizen_id,
});

function submit() {
  form.put(route('students.update', props.user.id), {
    onSuccess: () => form.reset(), 
  });
}
</script>

<template>
  <Head title="Edit Student" />
 
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 bg-white sm:rounded-xl mb-20">
      <form @submit.prevent="submit">
        <div class="p-6 space-y-4">
          <div>
            <label for="citizen_id" class="block text-sm font-medium text-gray-700">Citizen ID</label>
            <TextInput
              id="citizen_id"
              v-model="form.citizen_id"
              type="text"
              placeholder="Enter Citizen ID"
              class="mt-1 block w-full"
            />
            <span v-if="form.errors.citizen_id" class="text-red-500 text-sm">{{ form.errors.citizen_id }}</span>
          </div>

          <div class="flex space-x-4">
            <PrimaryButton class="custom-button-success px-1 py-1">บันทึก</PrimaryButton>
            <Link :href="route('students.index')" class="custom-button-danger px-1 py-1">กลับ</Link>
          </div>
        </div>
      </form>
    </div> 
</template> 