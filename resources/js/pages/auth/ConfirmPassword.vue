<script setup>
import AuthLayout from '@/layouts/AuthLayout.vue';
import { useForm } from '@inertiajs/vue3';

const form = useForm({
  password: '',
});

const submit = () => {
  form.post(route('password.confirm'), {
    onFinish: () => {
      form.reset();
    },
  });
};
</script>

<template>
  <AuthLayout title="Confirm your password">
    <v-card class="pa-4 pa-md-8" elevation="4">
      <v-card-title> Confirm password </v-card-title>
      <v-card-subtitle> Please confirm your password before continuing </v-card-subtitle>

      <v-form @submit.prevent="submit">
        <div class="my-6">
          <v-text-field
            id="password"
            v-model="form.password"
            label="Password"
            type="password"
            placeholder="Password"
            :error-messages="form.errors?.password"
            variant="outlined"
            rounded="lg"
            required
            autofocus
          ></v-text-field>
        </div>

        <v-btn color="primary" size="large" block type="submit" :disabled="form.processing">
          <v-progress-circular v-if="form.processing" size="20" width="2" color="white" indeterminate class="mr-2"></v-progress-circular>
          Confirm Password
        </v-btn>
      </v-form>
    </v-card>
  </AuthLayout>
</template>
