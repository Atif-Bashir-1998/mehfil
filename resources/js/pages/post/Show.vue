<script setup lang="ts">
import DashboardLayout from '@/layouts/DashboardLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import dayjs from 'dayjs';

interface Props {
  post: Post;
}

const props = defineProps<Props>();

interface Post {
  id: string;
  title: string;
  content: string;
  tags: string[] | null;
  created_at: string;
  creator: {
    id: number;
    name: string;
    username: string;
    profile_photo_path: string | null;
  };
}

const deletePost = (postId: string) => {
  if (confirm('Are you sure you want to delete this post?')) {
    router.delete(route('post.destroy', postId), {
      onSuccess: () => {
        router.visit(route('post.index'));
      },
    });
  }
};
</script>

<template>
  <Head :title="post.title" />

  <DashboardLayout>
    <v-card class="mx-auto">
      <v-card-title class="d-flex justify-space-between align-start">
        <!-- Post Title and Subtitle -->
        <div>
          <h1 class="text-h4 font-weight-bold">{{ post.title }}</h1>
          <div class="text-subtitle-1 text-medium-emphasis mt-2">
            by <span class="font-weight-bold">{{ post.creator.name }}</span> •
            {{ dayjs(post.created_at).format('MMMM d, YYYY h:mm a') }}
          </div>
        </div>

        <!-- Edit and Delete buttons, only visible to the post creator -->
        <div class="d-flex ga-2" v-if="$page.props.auth.user?.id === post.creator.id">
          <v-btn variant="outlined" size="small" :href="route('post.edit', post.id)"> Edit </v-btn>
          <v-btn color="red-darken-2" variant="flat" size="small" @click="deletePost(post.id)"> Delete </v-btn>
        </div>
      </v-card-title>

      <v-card-text>
        <!-- Tags -->
        <div v-if="post.tags && post.tags.length > 0" class="d-flex ga-2 mb-6 flex-wrap">
          <v-chip v-for="(tag, index) in post.tags" :key="index" size="small" label>
            {{ tag }}
          </v-chip>
        </div>

        <!-- Post Content -->
        <div class="prose text-body-1 mt-4 mb-6">
          <p class="whitespace-pre-line">{{ post.content }}</p>
        </div>
      </v-card-text>

      <v-card-actions>
        <v-btn variant="outlined" :href="route('post.index')"> ← Back to Posts </v-btn>
      </v-card-actions>
    </v-card>
  </DashboardLayout>
</template>
