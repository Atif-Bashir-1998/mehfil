<!-- <script setup>
import DashboardLayout from '@/layouts/DashboardLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { onMounted, ref, computed } from 'vue';
import { useToast } from 'vue-toastification';
import dayjs from 'dayjs';

const { ad, adStatusTypes } = defineProps({
  ad: Object,
  adStatusTypes: Array,
});

const toast = useToast();

// Form setup
const form = useForm({
  title: ad?.title || '',
  content: ad?.content || '',
  target_url: ad?.target_url || '',
  image: null,
  points_spent: ad?.points_spent || 30,
  status: ad?.status || 'active',
  starts_at: ad?.starts_at ? dayjs(ad.starts_at).format('YYYY-MM-DDTHH:mm') : dayjs().format('YYYY-MM-DDTHH:mm'),
  ends_at: ad?.ends_at ? dayjs(ad.ends_at).format('YYYY-MM-DDTHH:mm') : dayjs().add(7, 'day').format('YYYY-MM-DDTHH:mm'),
  remove_image: false,
});

const imagePreview = ref(null);
const isSubmitting = ref(false);

// Handle image upload
const handleImageUpload = (event) => {
  const file = event.target.files[0];
  if (file) {
    form.image = file;

    // Create preview
    const reader = new FileReader();
    reader.onload = (e) => {
      imagePreview.value = e.target.result;
    };
    reader.readAsDataURL(file);
  }
};

// Remove image
const removeImage = () => {
  form.image = null;
  form.remove_image = true;
  imagePreview.value = null;
};

// Form submission
const submitAd = () => {
  isSubmitting.value = true;

  if (ad) {
    form
      .transform((data) => ({
        ...data,
        _method: 'put',
      }))
      .post(route('ad.update', { ad: ad.id }), {
        headers: {
          'Content-Type': 'multipart/form-data',
        },
        onSuccess: () => {
          toast.success('Ad updated successfully!');
        },
        onError: () => {
          toast.error('Ad could not be updated');
          isSubmitting.value = false;
        },
        onFinish: () => {
          isSubmitting.value = false;
        },
      });
  } else {
    form.post(route('ad.store'), {
      onSuccess: () => {
        toast.success('Ad created successfully!');
        form.reset();
      },
      onError: () => {
        toast.error('Ad could not be created');
        isSubmitting.value = false;
      },
      onFinish: () => {
        isSubmitting.value = false;
      },
    });
  }
};

onMounted(() => {
  if (ad && ad.image_url) {
    imagePreview.value = ad.image_url;
  }
});
</script> -->

<script setup>
import DashboardLayout from '@/layouts/DashboardLayout.vue';
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import { onMounted, ref, computed, watch } from 'vue';
import { useToast } from 'vue-toastification';
import dayjs from 'dayjs';

const { ad, adStatusTypes, costPerDay = 30 } = defineProps({
  ad: Object,
  adStatusTypes: Array,
  costPerDay: {
    type: Number,
    default: 30
  }
});

const toast = useToast();

// Form setup
const form = useForm({
  title: ad?.title || '',
  content: ad?.content || '',
  target_url: ad?.target_url || '',
  image: null,
  points_spent: ad?.points_spent || 0, // Will be calculated
  status: ad?.status || 'active',
  starts_at: ad?.starts_at ? dayjs(ad.starts_at).format('YYYY-MM-DDTHH:mm') : dayjs().format('YYYY-MM-DDTHH:mm'),
  ends_at: ad?.ends_at ? dayjs(ad.ends_at).format('YYYY-MM-DDTHH:mm') : dayjs().add(7, 'day').format('YYYY-MM-DDTHH:mm'),
  remove_image: false,
});

const imagePreview = ref(null);
const isSubmitting = ref(false);

// Computed properties for cost calculation
const lowBalance = computed(() => {
  return usePage().props.auth.user.points < totalCost.value && !ad;
})

const calculatedDays = computed(() => {
  if (!form.starts_at || !form.ends_at) return 0;

  const start = dayjs(form.starts_at);
  const end = dayjs(form.ends_at);

  if (end.isBefore(start)) return 0;

  return end.diff(start, 'day') + 1; // +1 to include both start and end days
});

const totalCost = computed(() => {
  return calculatedDays.value * costPerDay;
});

const formattedStartDate = computed(() => {
  return form.starts_at ? dayjs(form.starts_at).format('MMM D, YYYY') : '--';
});

const formattedEndDate = computed(() => {
  return form.ends_at ? dayjs(form.ends_at).format('MMM D, YYYY') : '--';
});

// Watch for changes and update the form points_spent
watch([calculatedDays, totalCost], () => {
  form.points_spent = totalCost.value;
});

// Handle image upload
const handleImageUpload = (event) => {
  const file = event.target.files[0];
  if (file) {
    form.image = file;

    // Create preview
    const reader = new FileReader();
    reader.onload = (e) => {
      imagePreview.value = e.target.result;
    };
    reader.readAsDataURL(file);
  }
};

// Remove image
const removeImage = () => {
  form.image = null;
  form.remove_image = true;
  imagePreview.value = null;
};

// Form submission
const submitAd = () => {
  isSubmitting.value = true;

  if (ad) {
    form
      .transform((data) => ({
        ...data,
        _method: 'put',
      }))
      .post(route('ad.update', { ad: ad.id }), {
        headers: {
          'Content-Type': 'multipart/form-data',
        },
        onSuccess: () => {
          toast.success('Ad updated successfully!');
        },
        onError: () => {
          toast.error('Ad could not be updated');
          isSubmitting.value = false;
        },
        onFinish: () => {
          isSubmitting.value = false;
        },
      });
  } else {
    form.post(route('ad.store'), {
      onSuccess: () => {
        toast.success('Ad created successfully!');
        form.reset();
      },
      onError: () => {
        toast.error('Ad could not be created');
        isSubmitting.value = false;
      },
      onFinish: () => {
        isSubmitting.value = false;
      },
    });
  }
};

onMounted(() => {
  if (ad && ad.image_url) {
    imagePreview.value = ad.image_url;
  }

  // Set initial points_spent value
  form.points_spent = totalCost.value;
});
</script>

<template>
  <Head :title="ad ? 'Edit Ad' : 'Create Ad'" />

  <DashboardLayout>
    <v-card class="mx-auto my-8 pa-6" max-width="900" rounded="lg" elevation="4">
      <v-card-title class="d-flex align-center">
        <v-icon
          size="36"
          :color="ad ? 'orange-darken-2' : 'green-darken-2'"
          class="mr-3"
        >
          {{ ad ? 'mdi-pencil-box' : 'mdi-plus-box' }}
        </v-icon>
        <span class="text-h4 font-weight-bold">
          {{ ad ? 'Edit Your Ad' : 'Create New Ad' }}
        </span>
      </v-card-title>
      <v-card-subtitle class="text-body-1 text-medium-emphasis mb-6">
        <v-divider class="my-2"></v-divider>
        Fill out the details below to {{ ad ? 'update' : 'create' }} your advertisement.
      </v-card-subtitle>

      <v-card-text class="px-0">
        <v-alert
          v-if="lowBalance"
          text="Your points are less than the amount of points required to run the ad!"
          title="Low On Points"
          type="warning"
        ></v-alert>
        <v-form @submit.prevent="submitAd">
          <v-container>
            <v-row>
              <v-col cols="12">
                <v-text-field
                  v-model="form.title"
                  label="Ad Title"
                  placeholder="Enter an attention-grabbing title"
                  prepend-inner-icon="mdi-format-title"
                  variant="outlined"
                  color="primary"
                  :disabled="isSubmitting"
                  required
                ></v-text-field>
                <div v-if="form.errors.title" class="text-caption text-error">
                  {{ form.errors.title }}
                </div>
              </v-col>
            </v-row>

            <v-row>
              <v-col cols="12">
                <v-text-field
                  v-model="form.target_url"
                  label="Target URL"
                  placeholder="https://example.com"
                  prepend-inner-icon="mdi-link"
                  variant="outlined"
                  color="primary"
                  :disabled="isSubmitting"
                  required
                ></v-text-field>
                <div v-if="form.errors.target_url" class="text-caption text-error">
                  {{ form.errors.target_url }}
                </div>
              </v-col>
            </v-row>

            <v-row>
              <v-col cols="12">
                <v-textarea
                  v-model="form.content"
                  label="Ad Content"
                  placeholder="Describe your advertisement here..."
                  :rows="4"
                  prepend-inner-icon="mdi-text"
                  variant="outlined"
                  color="primary"
                  :disabled="isSubmitting"
                  required
                ></v-textarea>
                <div v-if="form.errors.content" class="text-caption text-error">
                  {{ form.errors.content }}
                </div>
              </v-col>
            </v-row>

            <v-row>
              <v-col cols="12">
                <v-card flat class="pa-4 bg-grey-lighten-4 rounded-lg">
                  <v-card-title class="text-h6 pa-0 mb-2 font-weight-bold">
                    Ad Cost Calculation
                  </v-card-title>

                  <div class="d-flex align-center ga-4 flex-wrap">
                    <div class="d-flex align-center">
                      <span class="text-body-1 font-weight-medium">{{ calculatedDays }} days</span>
                      <v-icon color="primary" class="mx-2">mdi-close</v-icon>
                      <span class="text-body-1 font-weight-medium">{{ costPerDay }} points/day</span>
                      <v-icon color="primary" class="mx-2">mdi-equal</v-icon>
                      <span class="text-h6 font-weight-bold text-primary">{{ totalCost }} points</span>
                    </div>

                    <v-chip
                      v-if="calculatedDays > 0"
                      color="primary"
                      variant="tonal"
                      class="ml-auto"
                    >
                      Total Cost
                    </v-chip>
                  </div>

                  <div class="text-caption text-medium-emphasis mt-2">
                    Your ad will run from {{ formattedStartDate }} to {{ formattedEndDate }}
                  </div>

                  <!-- Hidden field to store the calculated points -->
                  <input type="hidden" v-model="form.points_spent" />

                  <div v-if="form.errors.points_spent" class="text-caption text-error mt-2">
                    {{ form.errors.points_spent }}
                  </div>
                </v-card>
              </v-col>

              <v-col cols="12" md="6">
                <v-select
                  v-model="form.status"
                  :items="adStatusTypes"
                  label="Status"
                  prepend-inner-icon="mdi-state-machine"
                  variant="outlined"
                  color="primary"
                  :disabled="isSubmitting"
                ></v-select>
                <div v-if="form.errors.status" class="text-caption text-error">
                  {{ form.errors.status }}
                </div>
              </v-col>
            </v-row>

            <v-row>
              <v-col cols="12" md="6">
                <v-text-field
                  v-model="form.starts_at"
                  label="Start Date & Time"
                  type="datetime-local"
                  prepend-inner-icon="mdi-calendar-start"
                  variant="outlined"
                  color="primary"
                  :disabled="isSubmitting"
                  required
                ></v-text-field>
                <div v-if="form.errors.starts_at" class="text-caption text-error">
                  {{ form.errors.starts_at }}
                </div>
              </v-col>

              <v-col cols="12" md="6">
                <v-text-field
                  v-model="form.ends_at"
                  label="End Date & Time"
                  type="datetime-local"
                  prepend-inner-icon="mdi-calendar-end"
                  variant="outlined"
                  color="primary"
                  :disabled="isSubmitting"
                  required
                ></v-text-field>
                <div v-if="form.errors.ends_at" class="text-caption text-error">
                  {{ form.errors.ends_at }}
                </div>
              </v-col>
            </v-row>

            <v-row>
              <v-col cols="12">
                <v-card flat class="pa-4 bg-grey-lighten-4 rounded-lg">
                  <v-card-title class="text-h6 pa-0 mb-2 font-weight-bold">
                    Ad Image
                  </v-card-title>
                  <v-file-input
                    label="Select Image"
                    hint="Choose an image for your ad (JPEG, PNG, GIF, WebP)"
                    prepend-icon="mdi-image"
                    :disabled="isSubmitting"
                    @change="handleImageUpload"
                    variant="outlined"
                    density="compact"
                    color="primary"
                  ></v-file-input>
                  <div v-if="form.errors.image" class="text-caption text-error mt-2">
                    {{ form.errors.image }}
                  </div>

                  <div v-if="imagePreview" class="mt-4">
                    <v-img
                      :src="imagePreview"
                      max-height="200"
                      max-width="300"
                      class="rounded-lg elevation-2"
                    ></v-img>
                    <v-btn
                      color="error"
                      variant="text"
                      size="small"
                      @click="removeImage"
                      class="mt-2"
                    >
                      Remove Image
                    </v-btn>
                  </div>
                </v-card>
              </v-col>
            </v-row>
          </v-container>
        </v-form>
      </v-card-text>

      <v-card-actions class="d-flex justify-end ga-3 px-6 pb-4">
        <v-btn
          type="button"
          color="grey-darken-1"
          variant="text"
          @click="router.visit(route('ad.index'))"
          :disabled="isSubmitting"
        >
          Cancel
        </v-btn>
        <v-btn
          v-if="!lowBalance"
          type="submit"
          color="primary"
          variant="flat"
          rounded="lg"
          @click="submitAd"
          :disabled="isSubmitting || !form.title || !form.content || !form.target_url || lowBalance"
        >
          <span v-if="isSubmitting">
            {{ ad ? 'Updating...' : 'Creating...' }}
          </span>
          <span v-else>
            {{ ad ? 'Update Ad' : 'Create Ad' }}
          </span>
        </v-btn>
      </v-card-actions>
    </v-card>
  </DashboardLayout>
</template>
