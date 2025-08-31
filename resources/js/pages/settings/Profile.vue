<script setup>
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

import DeleteUser from '@/components/DeleteUser.vue';
import DashboardLayout from '@/layouts/DashboardLayout.vue';

const { status, mustVerifyEmail } = defineProps({
  status: {
    required: false,
  },
  mustVerifyEmail: {
    type: Boolean,
  },
});

const page = usePage();
const user = page.props.auth.user;

const form = useForm({
  name: user.name,
  email: user.email,
  profile_image: null,
  cover_image: null,
});

const submit = () => {
  form
    .transform((data) => ({
      ...data,
      _method: 'patch',
    }))
    .post(route('profile.update'), {
      preserveScroll: true,
      // _method: 'patch',
    });
};

// Image previews
const profileImagePreview = ref(user.profile_image?.image_url || null);
const coverImagePreview = ref(user.cover_image?.image_url || null);

const handleProfileImageChange = (e) => {
  const file = e.target.files[0];
  if (file) {
    profileImagePreview.value = URL.createObjectURL(file);
    form.profile_image = file;
  }
};

const handleCoverImageChange = (e) => {
  const file = e.target.files[0];
  if (file) {
    coverImagePreview.value = URL.createObjectURL(file);
    form.cover_image = file;
  }
};
</script>

<template>
  <Head title="Profile settings" />

  <DashboardLayout>
    <v-container class="my-8" style="max-width: 900px">
      <v-card class="pa-6 mb-8" rounded="lg" elevation="4">
        <v-card-title class="text-h4 font-weight-bold">Profile Information</v-card-title>
        <v-card-subtitle class="text-body-1"> Update your account's profile information and email address. </v-card-subtitle>

        <v-card-text>
          <v-form @submit.prevent="submit">
            <v-container>
              <v-row class="mb-4">
                <v-col cols="12" sm="6">
                  <v-card flat class="bg-grey-lighten-4 pa-4 rounded-lg">
                    <v-card-title class="text-h6 font-weight-bold pa-0 mb-2">Profile Image</v-card-title>
                    <v-file-input
                      label="Upload Profile Image"
                      prepend-icon="mdi-camera-account"
                      accept="image/*"
                      variant="outlined"
                      density="compact"
                      :error-messages="form.errors.profile_image"
                      @change="handleProfileImageChange"
                    ></v-file-input>
                    <v-img v-if="profileImagePreview" :src="profileImagePreview" height="150" class="mt-2 rounded-lg" contain></v-img>
                  </v-card>
                </v-col>
                <v-col cols="12" sm="6">
                  <v-card flat class="bg-grey-lighten-4 pa-4 rounded-lg">
                    <v-card-title class="text-h6 font-weight-bold pa-0 mb-2">Cover Image</v-card-title>
                    <v-file-input
                      label="Upload Cover Image"
                      prepend-icon="mdi-image-plus"
                      accept="image/*"
                      variant="outlined"
                      density="compact"
                      :error-messages="form.errors.cover_image"
                      @change="handleCoverImageChange"
                    ></v-file-input>
                    <v-img v-if="coverImagePreview" :src="coverImagePreview" height="150" class="mt-2 rounded-lg" contain></v-img>
                  </v-card>
                </v-col>
              </v-row>

              <v-row class="mb-4">
                <v-col cols="12">
                  <v-text-field
                    v-model="form.name"
                    label="Name"
                    placeholder="Full name"
                    prepend-inner-icon="mdi-account"
                    variant="outlined"
                    :error-messages="form.errors.name"
                    required
                  ></v-text-field>
                </v-col>
              </v-row>

              <v-row class="mb-4">
                <v-col cols="12">
                  <v-text-field
                    v-model="form.email"
                    label="Email address"
                    placeholder="Email address"
                    prepend-inner-icon="mdi-email"
                    variant="outlined"
                    type="email"
                    :error-messages="form.errors.email"
                    required
                  ></v-text-field>
                </v-col>
              </v-row>

              <v-row v-if="mustVerifyEmail && !user.email_verified_at">
                <v-col cols="12" class="-mt-4">
                  <v-alert type="warning" variant="outlined" color="warning" density="compact" class="mb-4">
                    Your email address is unverified.
                    <Link :href="route('verification.send')" method="post" as="button" class="text-decoration-underline text-warning">
                      Click here to resend the verification email.
                    </Link>
                  </v-alert>

                  <v-alert v-if="status === 'verification-link-sent'" type="success" variant="tonal" density="compact" class="mt-2">
                    A new verification link has been sent to your email address.
                  </v-alert>
                </v-col>
              </v-row>

              <v-row>
                <v-col cols="12" class="d-flex align-center ga-4">
                  <v-btn type="submit" color="primary" variant="flat" :loading="form.processing"> Save </v-btn>

                  <v-fade-transition>
                    <p v-show="form.recentlySuccessful" class="text-body-2 text-medium-emphasis">Saved.</p>
                  </v-fade-transition>
                </v-col>
              </v-row>
            </v-container>
          </v-form>
        </v-card-text>
      </v-card>

      <v-divider class="my-6"></v-divider>

      <DeleteUser />
    </v-container>
  </DashboardLayout>
</template>
