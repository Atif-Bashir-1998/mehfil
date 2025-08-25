<template>
  <v-card class="d-flex flex-column pa-4 h-100">
    <v-card-title class="d-flex justify-space-between align-start pa-0 pb-2">
      <div>
        <Link :href="route('post.show', { id: post.id })" class="text-decoration-none">
          <div class="text-h6 font-weight-bold text-primary">
            {{ post.title }}
          </div>
        </Link>
        <div class="text-subtitle-2 text-medium-emphasis mt-1">
          by <Link class="text-primary underline" :href="route('user.show', { user: post.creator.id })">{{ post.creator.name }}</Link> •
          {{ dayjs(post.created_at).format('hh:mm A, d MMM YYYY') }}
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
      <div v-if="usePage().props.auth.user?.id === post.creator.id" class="d-flex ga-2 justify-end">
        <v-btn variant="outlined" size="small" :href="route('post.edit', post.id)"> Edit </v-btn>
        <v-btn color="red-darken-2" variant="flat" size="small" @click="deletePost(post.id)"> Delete </v-btn>
      </div>
    </v-card-actions>
  </v-card>
</template>

<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import dayjs from 'dayjs';

const { post } = defineProps({
  post: {
    type: Object,
    required: true
  }
});

const deletePost = (postId) => {
  if (confirm('Are you sure you want to delete this post?')) {
    router.delete(route('post.destroy', postId), {
      preserveScroll: true,
    });
  }
};

</script>
