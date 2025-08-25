<script setup>
import AuthLayout from '@/layouts/AuthLayout.vue';
import { useForm } from '@inertiajs/vue3';
import { handleLogout } from '@/utils/logout';

defineProps({
  status: {
    type: Boolean,
    required: false
  }
});

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'));
};

</script>

<template>
  <!-- <AuthLayout title="Verify email" description="Please verify your email address by clicking on the link we just emailed to you.">
        <Head title="Email verification" />

        <div v-if="status === 'verification-link-sent'" class="mb-4 text-center text-sm font-medium text-green-600">
            A new verification link has been sent to the email address you provided during registration.
        </div>

        <form @submit.prevent="submit" class="space-y-6 text-center">
            <Button :disabled="form.processing" variant="secondary">
                <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                Resend verification email
            </Button>

            <TextLink :href="route('logout')" method="post" as="button" class="mx-auto block text-sm"> Log out </TextLink>
        </form>
    </AuthLayout> -->
  <AuthLayout title="Email verification">
    <v-card>
      <v-card-text class="text-center">
        <v-alert v-if="status === 'verification-link-sent'" type="success" variant="tonal" class="mb-4" rounded="lg">
          A new verification link has been sent to the email address you provided during registration.
        </v-alert>

        <div class="d-flex flex-column align-center ga-4">
          <v-btn color="primary" size="large" rounded="lg" :loading="form.processing" @click="submit">
            <v-progress-circular v-if="form.processing" size="20" width="2" color="white" indeterminate class="mr-2"></v-progress-circular>
            Resend verification email
          </v-btn>

          <v-btn @click="handleLogout" variant="text"> Log out </v-btn>
        </div>
      </v-card-text>
    </v-card>
  </AuthLayout>
</template>
