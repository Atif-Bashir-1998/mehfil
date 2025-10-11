<script setup>
import DashboardLayout from '@/layouts/DashboardLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';
import { useToast } from 'vue-toastification';

const { post } = defineProps({
  post: Object,
});

const toast = useToast();

// Form setup
const form = useForm({
  title: post?.title || '',
  content: post?.content || '',
  tags: post?.tags || [],
  images: [],
  deleted_image_ids: [],
});

const newTag = ref('');
const isSubmitting = ref(false);

// Ref to hold image previews. This will be an array of objects.
const imagePreviews =
 ref([]);

const addTag = () => {
  if (newTag.value.trim() && !form.tags.includes(newTag.value.trim())) {
    form.tags.push(newTag.value.trim());
    newTag.value = '';
  }
};

const removeTag = (index) => {
  form.tags.splice(index, 1);
};

// Form submission
const submitPost = () => {
  isSubmitting.value = true;
  if (post) {
    form
      .transform((data) => ({
        ...data,
        _method: 'put',
      }))
      .post(route('post.update', { post: post.id }), {
        headers: {
          'Content-Type': 'multipart/form-data',
        },
        onSuccess: () => {
          toast.success('Post updated successfully!');
        },
        onError: () => {
          toast.error('Post could not be updated');
          isSubmitting.value = false;
        },
        onFinish: () => {
          isSubmitting.value = false;
        },
        only: ['post'],
      });
  } else {
    // create new post
    form.post(route('post.store'), {
      onSuccess: () => {
        // Reset form after successful submission
        toast.success('Post created successfully!');
        form.reset();
      },
      onError: () => {
        toast.success('Post could not be created');
        isSubmitting.value = false;
      },
      onFinish: () => {
        isSubmitting.value = false;
      },
    });
  }
};

const handleImageUpload = (event) => {
  const files = event.target.files;

  if (files) {
    for (let i = 0; i < files.length; i++) {
      const file = files[i];

      // Add the file to the form data.
      form.images.push(file);

      // Create a URL for the image preview.
      const reader = new FileReader();
      reader.onload = (e) => {
        // Store the preview URL and file name.
        imagePreviews.value.push({
          url: e.target.result,
          name: file.name,
        });
      };
      reader.readAsDataURL(file);
    }
  }
};

const removeImage = (index, imageId = null) => {
  // if image is removed, make sure only the existing images from the post are removed
  if (imageId) {
    form.deleted_image_ids.push(imageId);
  }

  // Remove the image from the form data.
  form.images.splice(index, 1);
  // Remove the corresponding preview.
  imagePreviews.value.splice(index, 1);
};

onMounted(() => {
  if(post && post.images && post.images.length) {
    post.images.forEach(image => {
      imagePreviews.value.push({
        url: image.image_url,
        name: image.id,
        id: image.id
      });
    });
  }
})
</script>

<template>
  <Head title="Create a Post" />

  <DashboardLayout>
  <v-card class="mx-auto my-8 pa-6" max-width="900" rounded="lg" elevation="4">
    <v-card-title class="d-flex align-center">
      <v-icon
        size="36"
        :color="post ? 'orange-darken-2' : 'green-darken-2'"
        class="mr-3"
      >
        {{ post ? 'mdi-pencil-box' : 'mdi-plus-box' }}
      </v-icon>
      <span class="text-h4 font-weight-bold">
        {{ post ? 'Edit Your Post' : 'Create New Post' }}
      </span>
    </v-card-title>
    <v-card-subtitle class="text-body-1 text-medium-emphasis mb-6">
      <v-divider class="my-2"></v-divider>
      Fill out the details below to {{ post ? 'update' : 'create' }} your content for the Mehfil community.
    </v-card-subtitle>

    <v-card-text class="px-0">
      <v-form @submit.prevent="submitPost">
        <v-container>
          <v-row>
            <v-col cols="12">
              <v-text-field
                v-model="form.title"
                label="Post Title"
                placeholder="Enter a compelling title"
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
              <v-card flat class="pa-4 bg-grey-lighten-4 rounded-lg">
                <v-card-title class="text-h6 pa-0 mb-2 font-weight-bold">
                  Tags
                </v-card-title>
                <div class="d-flex align-center mt-2">
                  <v-text-field
                    v-model="newTag"
                    placeholder="Add a tag and press Enter"
                    :disabled="isSubmitting"
                    @keyup.enter="addTag"
                    density="compact"
                    hide-details
                    single-line
                    variant="outlined"
                    color="secondary"
                    class="mr-2"
                  ></v-text-field>
                  <v-btn type="button" @click="addTag" color="primary" variant="flat" :disabled="isSubmitting" icon="mdi-plus"></v-btn>
                </div>

                <div v-if="form.tags.length > 0" class="d-flex ga-2 mt-4 flex-wrap">
                  <v-chip
                    v-for="(tag, index) in form.tags"
                    :key="index"
                    closable
                    variant="tonal"
                    :color="form.errors[`tags.${index}`] ? 'error' : 'green-darken-1'"
                    @click:close="removeTag(index)"
                  >
                    {{ tag }}
                  </v-chip>
                </div>
                <div v-if="form.errors.tags" class="text-caption text-error mt-2">
                  {{ form.errors.tags }}
                </div>

                <!-- Show individual tag errors -->
                <div v-for="(error, index) in form.errors" :key="index" class="text-caption text-error mt-2">
                  <div v-if="index.startsWith('tags.')">
                    {{ error }}
                  </div>
                </div>
              </v-card>
            </v-col>
          </v-row>

          <v-row>
            <v-col cols="12">
              <v-textarea
                v-model="form.content"
                label="Post Content"
                placeholder="Share your story, thoughts, or ideas here..."
                :rows="8"
                prepend-inner-icon="mdi-pencil-box-multiple-outline"
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
                  Upload Images
                </v-card-title>
                <v-file-input
                  label="Select Images"
                  hint="Select one or more images (JPEG, PNG, GIF)"
                  prepend-icon="mdi-camera"
                  multiple
                  counter
                  show-size
                  :disabled="isSubmitting"
                  @change="handleImageUpload"
                  variant="outlined"
                  density="compact"
                  color="primary"
                ></v-file-input>
                <div v-if="form.errors.images" class="text-caption text-error mt-2">
                  {{ form.errors.images }}
                </div>

                <div v-for="index in (form.images ? form.images.length : 0)" :key="index" class="text-caption text-error mt-2">
                <div v-if="form.errors[`images.${index-1}`]">
                  Error with image#{{ index }}: {{ form.errors[`images.${index-1}`] }}
                </div>
              </div>
              </v-card>
            </v-col>
          </v-row>

          <v-row v-if="imagePreviews.length">
            <v-col cols="12">
              <v-carousel
                :key="imagePreviews.length"
                show-arrows="hover"
                color="white"
                hide-delimiters
                progress="primary"
                class="bg-blue-grey-lighten-5 rounded-lg elevation-2 mt-4"
                height="350"
              >
                <v-carousel-item v-for="(image, index) in imagePreviews" :key="index" :src="image.url" cover>
                  <div class="d-flex fill-height align-start pa-2 justify-end">
                    <v-btn
                      color="error"
                      icon="mdi-close-circle"
                      size="small"
                      variant="flat"
                      @click.stop="removeImage(index, image.id)"
                    ></v-btn>
                  </div>
                </v-carousel-item>
              </v-carousel>
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
        @click="router.visit(route('dashboard'))"
        :disabled="isSubmitting"
      >
        Cancel
      </v-btn>
      <v-btn
        type="submit"
        color="primary"
        variant="flat"
        rounded="lg"
        @click="submitPost"
        :disabled="isSubmitting || !form.title || !form.content"
      >
        <span v-if="isSubmitting">
          {{ post ? 'Updating...' : 'Creating...' }}
        </span>
        <span v-else>
          {{ post ? 'Update Post' : 'Create Post' }}
        </span>
      </v-btn>
    </v-card-actions>
  </v-card>
</DashboardLayout>
</template>
