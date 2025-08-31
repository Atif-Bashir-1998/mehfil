<script setup>
import DashboardLayout from '@/layouts/DashboardLayout.vue';
import dayjs from 'dayjs';
import { ref } from 'vue';
import PostCard from '@/components/PostCard.vue';

const activeTab = ref('posts');

const { user } = defineProps({
  user: Object
});
</script>

<template>
  <DashboardLayout>
    <v-container class="my-8" style="max-width: 900px">
      <v-card class="pa-0 mb-8" rounded="lg" elevation="4">
        <div class="relative">
          <v-img
            :src="user.cover_image?.image_url || 'https://images.unsplash.com/photo-1517849845537-4d257902454a?q=80&w=2940&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D'"
            height="200"
            cover
            class="bg-grey-lighten-2"
          ></v-img>

          <v-avatar
            size="128"
            class="profile-avatar elevation-6"
            :image="user.profile_image?.image_url || 'https://eu.ui-avatars.com/api/?name=' + user.name + '&size=128'"
          ></v-avatar>
        </div>

        <div class="pa-6 mt-8">
          <div class="text-h4 font-weight-bold">{{ user.name }}</div>
          <div class="text-subtitle-1 text-medium-emphasis mt-2">No bio available.</div>

          <v-divider class="my-4"></v-divider>

          <div class="d-flex ga-4 text-body-2 text-medium-emphasis">
            <div v-if="user.location" class="d-flex align-center">
              <v-icon size="small" icon="mdi-map-marker" class="mr-1"></v-icon>
              <span>{{ user.location }}</span>
            </div>
            <div class="d-flex align-center">
              <v-icon size="small" icon="mdi-calendar-month" class="mr-1"></v-icon>
              <span>Joined: {{ dayjs(user.created_at).format('MMMM YYYY') }}</span>
            </div>
          </div>
        </div>
      </v-card>

      <v-card rounded="lg" elevation="4">
        <v-tabs v-model="activeTab" grow color="primary">
          <v-tab value="posts">
            <v-icon start icon="mdi-post-outline"></v-icon>
            Posts ({{ user.posts_count }})
          </v-tab>
          <v-tab value="about">
            <v-icon start icon="mdi-information-outline"></v-icon>
            About
          </v-tab>
        </v-tabs>

        <v-card-text>
          <v-window v-model="activeTab">
            <v-window-item value="posts">
              <div v-if="!user.posts.length" class="text-medium-emphasis py-8 text-center">
                <v-icon size="64" color="grey-lighten-2">mdi-inbox</v-icon>
                <p class="mt-2">No posts yet.</p>
              </div>
              <div v-else class="post-list-container">
                <div v-for="post in user.posts" :key="post.id" class="post-item">
                  <PostCard :post="post" />
                </div>
              </div>
            </v-window-item>

            <v-window-item value="about">
              <div class="text-medium-emphasis py-8 text-center">
                <p>This is the "About" section. You can add more details here.</p>
              </div>
            </v-window-item>
          </v-window>
        </v-card-text>
      </v-card>
    </v-container>
  </DashboardLayout>
</template>

<style scoped>
.relative {
  position: relative;
}

.profile-avatar {
  position: absolute;
  top: 100%;
  left: 32px;
  transform: translateY(-50%);
  border: 4px solid rgb(var(--v-theme-surface));
}

.post-list-container {
    display: grid;
    gap: 24px;
}
</style>
