<script setup>
import DashboardLayout from '@/layouts/DashboardLayout.vue';
import { Head } from '@inertiajs/vue3';
import PostCard from '@/components/PostCard.vue';

const { posts } = defineProps({
  posts: {
    type: Object,
    required: true
  },
  reaction_types: {
    type: Array,
    required: true
  }
});
</script>

<template>
  <Head title="Posts" />

  <DashboardLayout>
    <v-row class="align-right">
      <v-col cols="12" class="text-sm-right">
        <v-btn color="primary" :href="route('post.create')"> Create New Post </v-btn>
      </v-col>
    </v-row>

    <v-row justify="center">
      <v-col v-for="post in posts.data" :key="post.id" cols="8">
        <PostCard :post="post" />
      </v-col>
    </v-row>
  </DashboardLayout>
</template>
