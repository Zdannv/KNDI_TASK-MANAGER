<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';

defineProps({ canResetPassword: Boolean, status: String });

const isLoaded = ref(false);
onMounted(() => {
  setTimeout(() => isLoaded.value = true, 100);
});

const form = useForm({
  email: '',
  password: '',
  remember: false,
});

const submit = () => {
  form.post(route('login'), {
    onFinish: () => form.reset('password'),
  });
};
</script>

<template>
  <GuestLayout :isLoaded="isLoaded">
    <Head title="Log in" />

    <form @submit.prevent="submit" class="space-y-6">

      <!-- Email Field -->
      <div
        class="transition-all duration-700 ease-out"
        :class="{ 'scale-100 opacity-100': isLoaded, 'scale-95 opacity-0': !isLoaded }"
        style="transition-delay: 300ms;"
      >
        <InputLabel for="email" value="Email" />
        <TextInput
          id="email"
          type="email"
          class="mt-1 block w-full"
          v-model="form.email"
          required
          autofocus
          autocomplete="username"
        />
        <InputError class="mt-2" :message="form.errors.email" />
      </div>

      <!-- Password Field -->
      <div
        class="transition-all duration-700 ease-out"
        :class="{ 'scale-100 opacity-100': isLoaded, 'scale-95 opacity-0': !isLoaded }"
        style="transition-delay: 500ms;"
      >
        <InputLabel for="password" value="Password" />
        <TextInput
          id="password"
          type="password"
          class="mt-1 block w-full"
          v-model="form.password"
          required
          autocomplete="current-password"
        />
        <InputError class="mt-2" :message="form.errors.password" />
      </div>

      <!-- Remember Me -->
      <div
        class="transition-all duration-600 ease-out"
        :class="{ 'scale-100 opacity-100': isLoaded, 'scale-90 opacity-0': !isLoaded }"
        style="transition-delay: 700ms;"
      >
        <label class="flex items-center">
          <Checkbox name="remember" v-model:checked="form.remember" />
          <span class="ms-2 text-sm text-gray-600 dark:text-gray-100">Remember me</span>
        </label>
      </div>

      <!-- Actions: Forgot Password + Login Button -->
      <div
        class="flex flex-col sm:flex-row items-center justify-between gap-4 transition-all duration-700 ease-out"
        :class="{ 'scale-100 opacity-100': isLoaded, 'scale-95 opacity-0': !isLoaded }"
        style="transition-delay: 900ms;"
      >
        <Link
          v-if="canResetPassword"
          :href="route('password.request')"
          class="text-sm text-indigo-600 dark:text-indigo-400 underline hover:text-indigo-800 dark:hover:text-indigo-300 transition-colors"
        >
          Forgot your password?
        </Link>

        <PrimaryButton
          :class="{ 'opacity-25': form.processing }"
          :disabled="form.processing"
          class="w-full justify-center sm:w-auto"
        >
          Log in
        </PrimaryButton>
      </div>

    </form>
  </GuestLayout>
</template>