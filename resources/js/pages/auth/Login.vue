<script setup>
import AuthLayout from '@/layouts/AuthLayout.vue';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps({
  status: {
    required: false,
    type: String,
  },
  canResetPassword: {
    type: Boolean,
  },
});

const form = useForm({
  email: '',
  password: '',
  remember: false,
});

const showPassword = ref(false);

const submit = () => {
  form.post(route('login'), {
    onFinish: () => form.reset('password'),
  });
};
</script>

<template>
  <AuthLayout title="Login">
    <v-card class="pa-4 pa-md-8 w-75" rounded="xl" elevation="4">
      <v-card-title class="text-h5 font-weight-bold mb-2 text-center"> Log in to your account </v-card-title>
      <v-card-subtitle class="text-medium-emphasis mb-4 text-center"> Enter your email and password below to log in </v-card-subtitle>

      <v-alert v-if="status" type="success" variant="tonal" class="mb-4" rounded="lg">
        {{ status }}
      </v-alert>

      <v-form @submit.prevent="submit" ref="formRef">
        <v-text-field
          label="Email"
          v-model="form.email"
          type="email"
          :error-messages="form.errors.email"
          :loading="form.processing"
          required
          autofocus
        ></v-text-field>

        <v-text-field
          label="Password"
          v-model="form.password"
          :type="showPassword ? 'text' : 'password'"
          :append-inner-icon="showPassword ? 'mdi-eye-off' : 'mdi-eye'"
          @click:append-inner="showPassword = !showPassword"
          :error-messages="form.errors.password"
          :loading="form.processing"
          required
        ></v-text-field>

        <v-checkbox v-model="form.remember" label="Remember me" color="primary" density="compact" hide-details></v-checkbox>

        <v-btn color="primary" size="large" block type="submit" :disabled="form.processing">
          <v-progress-circular v-if="form.processing" size="20" width="2" color="white" indeterminate class="mr-2"></v-progress-circular>
          Log in
        </v-btn>
      </v-form>

      <v-card-actions class="flex-column mt-4">
        <div>
          Don't have an account?
          <v-btn variant="text" :href="route('register')"> Sign up </v-btn>
        </div>
        <div>
          Forgot Password?
          <v-btn variant="text" :href="route('password.request')"> Reset your password </v-btn>
        </div>
      </v-card-actions>
    </v-card>
  </AuthLayout>
</template>
