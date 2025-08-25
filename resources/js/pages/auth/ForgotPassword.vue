<script setup>
import { useForm } from '@inertiajs/vue3';
import AuthLayout from '@/layouts/AuthLayout.vue';

defineProps({
  status: {
    type: String,
    required: false,
  },
});

const form = useForm({
  email: '',
});

const submit = () => {
  form.post(route('password.email'));
};
</script>

<template>
  <AuthLayout title="Forgot password">
    <v-card class="pa-4 pa-md-8" rounded="xl" elevation="4">
      <v-card-title class="text-h5 font-weight-bold mb-2 text-center"> Forgot password? </v-card-title>
      <v-card-subtitle class="text-medium-emphasis mb-4 text-center"> Don't worry, we'll send you a password reset link </v-card-subtitle>

      <v-alert v-if="status" type="success" variant="tonal" class="mb-4" rounded="lg">
        {{ status }}
      </v-alert>

      <v-form @submit.prevent="submit">
        <div class="mb-6">
          <v-text-field
            id="email"
            v-model="form.email"
            label="Email address"
            type="email"
            name="email"
            autocomplete="off"
            placeholder="email@example.com"
            :error-messages="form.errors?.email"
            variant="outlined"
            rounded="lg"
            required
            autofocus
          ></v-text-field>
        </div>

        <v-btn color="primary" size="large" rounded="lg" block type="submit" :disabled="form.processing">
          <v-progress-circular v-if="form.processing" size="20" width="2" color="white" indeterminate class="mr-2"></v-progress-circular>
          Email password reset link
        </v-btn>

        <v-divider class="my-6"></v-divider>

        <div class="text-body-2 text-medium-emphasis text-center">
          <span>Or, return to</span>
          <v-btn :href="route('login')" variant="text" class="pa-0 text-blue-grey-darken-2 font-weight-bold"> log in </v-btn>
        </div>
      </v-form>
    </v-card>
  </AuthLayout>
</template>
