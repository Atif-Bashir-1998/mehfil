<script setup>
import AuthLayout from '@/layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
  token: {
    type: String,
    required: true,
  },
  email: {
    type: String,
    required: true,
  },
});

const form = useForm({
  token: props.token,
  email: props.email,
  password: '',
  password_confirmation: '',
});

const submit = () => {
  form.post(route('password.store'), {
    onFinish: () => {
      form.reset('password', 'password_confirmation');
    },
  });
};
</script>

<template>
  <AuthLayout title="Reset password" description="Please enter your new password below">
    <Head title="Reset password" />

    <v-card class="pa-4 pa-md-8" rounded="xl" elevation="4">
      <v-card-title class="text-h5 font-weight-bold mb-2 text-center"> Reset your password </v-card-title>
      <v-card-subtitle class="text-medium-emphasis mb-4 text-center"> Enter and confirm your new password below </v-card-subtitle>

      <v-form @submit.prevent="submit">
        <div class="mb-4">
          <v-text-field
            id="email"
            v-model="form.email"
            label="Email address"
            type="email"
            name="email"
            autocomplete="email"
            variant="outlined"
            rounded="lg"
            required
            readonly
          ></v-text-field>
        </div>

        <div class="mb-4">
          <v-text-field
            id="password"
            v-model="form.password"
            label="Password"
            type="password"
            name="password"
            autocomplete="new-password"
            placeholder="Password"
            :error-messages="form.errors?.password"
            variant="outlined"
            rounded="lg"
            required
            autofocus
          ></v-text-field>
        </div>

        <div class="mb-6">
          <v-text-field
            id="password_confirmation"
            v-model="form.password_confirmation"
            label="Confirm Password"
            type="password"
            name="password_confirmation"
            autocomplete="new-password"
            placeholder="Confirm password"
            :error-messages="form.errors?.password_confirmation"
            variant="outlined"
            rounded="lg"
            required
          ></v-text-field>
        </div>

        <v-btn color="primary" size="large" rounded="lg" block type="submit" :disabled="form.processing">
          <v-progress-circular v-if="form.processing" size="20" width="2" color="white" indeterminate class="mr-2"></v-progress-circular>
          Reset password
        </v-btn>
      </v-form>
    </v-card>
  </AuthLayout>
</template>
