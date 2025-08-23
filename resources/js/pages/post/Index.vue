<script setup lang="ts">
import DashboardLayout from '@/layouts/DashboardLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import dayjs from 'dayjs';

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

interface Props {
  posts: {
    data: Post[];
    links: any[];
    meta: any;
  };
}

const props = defineProps<Props>();

const deletePost = (postId: string) => {
  if (confirm('Are you sure you want to delete this post?')) {
    router.delete(route('post.destroy', postId), {
      preserveScroll: true,
    });
  }
};
</script>

<template>
  <Head title="Posts" />

  <DashboardLayout>
    <v-row class="align-center mb-4">
      <v-col cols="12" sm="6">
        <h1 class="text-h4 font-weight-bold">Latest Posts</h1>
      </v-col>
      <v-col cols="12" sm="6" class="text-sm-right">
        <v-btn color="primary" :href="route('post.create')"> Create New Post </v-btn>
      </v-col>
    </v-row>

    <v-row>
      <v-col v-for="post in posts.data" :key="post.id" cols="12">
        <v-card class="d-flex flex-column pa-4 h-100">
          <v-card-title class="d-flex justify-space-between align-start pa-0 pb-2">
            <div>
              <router-link :to="{ name: 'post.show', params: { id: post.id } }" class="text-decoration-none">
                <div class="text-h6 font-weight-bold text-primary">
                  {{ post.title }}
                </div>
              </router-link>
              <div class="text-subtitle-2 text-medium-emphasis mt-1">
                by {{ post.creator.name }} • {{ dayjs(post.created_at).format('hh:mm A, d MMM YYYY') }}
              </div>
            </div>
          </v-card-title>

          <v-card-text class="pa-0 text-body-1 text-truncate-3-lines py-4">
            <p>{{ post.content.substring(0, 200) }}{{ post.content.length > 200 ? '...' : '' }}</p>
          </v-card-text>

          <div v-if="post.tags && post.tags.length > 0" class="d-flex ga-2 mt-auto flex-wrap">
            <v-chip v-for="(tag, index) in post.tags" :key="index" size="small" label>
              {{ tag }}
            </v-chip>
          </div>

          <v-card-actions class="pa-0 pt-4">
            <v-btn variant="text" size="small" :href="route('post.show', post.id)"> Read More </v-btn>
            <div v-if="$page.props.auth.user?.id === post.creator.id" class="d-flex ga-2 justify-end">
              <v-btn variant="outlined" size="small" :href="route('post.edit', post.id)"> Edit </v-btn>
              <v-btn color="red-darken-2" variant="flat" size="small" @click="deletePost(post.id)"> Delete </v-btn>
            </div>
          </v-card-actions>
        </v-card>
      </v-col>
    </v-row>
  </DashboardLayout>
</template>
