<script setup>
import DashboardLayout from '@/layouts/DashboardLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { useToast } from "vue-toastification";

const { post } = defineProps({
  post: Object
});
const toast = useToast();

// Form setup
const form = useForm({
  title: post?.title || '',
  content: post?.content || '',
  tags: post?.tags || [],
});

const newTag = ref('');
const isSubmitting = ref(false);

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
    // update the post
    form.put(route('post.update', { post: post.id }), {
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
</script>

<template>
  <Head title="Create a Post" />

  <DashboardLayout>
    <v-card>
      <v-card-title class="text-h5 font-weight-bold">
        {{ post ? 'Edit Post' : 'Create New Post' }}
      </v-card-title>
      <v-card-subtitle> Fill out the form to {{ post ? 'edit' : 'create' }} your post. </v-card-subtitle>

      <v-card-text>
        <v-form @submit.prevent="submitPost">
          <v-container>
            <!-- Post Title -->
            <v-row>
              <v-col cols="12">
                <v-text-field
                  v-model="form.title"
                  label="Post Title"
                  placeholder="Enter a title for your post"
                  :disabled="isSubmitting"
                  required
                ></v-text-field>
                <div v-if="form.errors.title" class="text-caption text-error">
                  {{ form.errors.title }}
                </div>
              </v-col>
            </v-row>

            <!-- Tags Section -->
            <v-row>
              <v-col cols="12">
                <v-label>Tags</v-label>
                <div class="d-flex align-center mt-2">
                  <v-text-field
                    v-model="newTag"
                    placeholder="Add a tag and press Enter"
                    :disabled="isSubmitting"
                    @keyup.enter="addTag"
                    density="compact"
                    hide-details
                    single-line
                    class="mr-2"
                  ></v-text-field>
                  <v-btn type="button" @click="addTag" variant="tonal" :disabled="isSubmitting"> Add </v-btn>
                </div>

                <!-- Display added tags -->
                <div v-if="form.tags.length > 0" class="d-flex ga-2 mt-4 flex-wrap">
                  <v-chip v-for="(tag, index) in form.tags" :key="index" closable variant="tonal" @click:close="removeTag(index)">
                    {{ tag }}
                  </v-chip>
                </div>
                <div v-if="form.errors.tags" class="text-caption text-error">
                  {{ form.errors.tags }}
                </div>
              </v-col>
            </v-row>

            <!-- Post Content -->
            <v-row>
              <v-col cols="12">
                <v-textarea
                  v-model="form.content"
                  label="Post Content"
                  placeholder="Write your detailed post here..."
                  :rows="8"
                  :disabled="isSubmitting"
                  required
                ></v-textarea>
                <div v-if="form.errors.content" class="text-caption text-error">
                  {{ form.errors.content }}
                </div>
              </v-col>
            </v-row>
          </v-container>
        </v-form>
      </v-card-text>

      <v-card-actions class="d-flex justify-end gap-3">
        <v-btn type="button" color="error" variant="outlined" @click="router.visit(route('dashboard'))" :disabled="isSubmitting"> Cancel </v-btn>
        <v-btn type="submit" color="primary" variant="flat" @click="submitPost" :disabled="isSubmitting || !form.title || !form.content">
          <span v-if="isSubmitting">
            {{ post ? 'Updating...' : 'Creating...' }}
          </span>
          <span v-else>
            {{ post ? 'Update' : 'Create' }}
          </span>
        </v-btn>
      </v-card-actions>
    </v-card>
  </DashboardLayout>
</template>
