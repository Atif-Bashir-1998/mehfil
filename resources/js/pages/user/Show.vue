<script setup>
import DashboardLayout from '@/layouts/DashboardLayout.vue';
import dayjs from 'dayjs';
import { ref } from 'vue';

const activeTab = ref('posts');

const { user } = defineProps({
  user: Object
});

</script>

<template>
  <DashboardLayout>
    <div class="user-profile-container">
      <v-card class="pa-6 mb-8" rounded="xl" elevation="2">
        <v-row no-gutters class="align-center">
          <!-- Profile Picture and Info -->
          <v-col cols="12" md="auto" class="text-md-left text-center">
            <v-avatar size="128" color="grey-lighten-2">
              <v-img :src="'https://placehold.co/128x128/9e9e9e/FFFFFF?text=Mehfil'" alt="User Profile Avatar"></v-img>
            </v-avatar>
          </v-col>

          <!-- User Details -->
          <v-col cols="12" md="grow" class="text-md-left pl-md-6 mt-md-0 mt-4 text-center">
            <h1 class="text-h4 font-weight-bold text-blue-grey-darken-4">{{ 'UserName' }}</h1>
            <p class="text-subtitle-1 text-medium-emphasis mt-1">{{ 'No bio available.' }}</p>
            <div class="text-body-2 text-grey-darken-1 mt-2">
              <v-icon size="small" icon="mdi-map-marker" class="mr-1"></v-icon>
              {{ 'Location not specified' }}
            </div>
            <div class="text-body-2 text-grey-darken-1 mt-1">
              <v-icon size="small" icon="mdi-calendar-month" class="mr-1"></v-icon>
              Joined: {{ dayjs() }}
            </div>
          </v-col>
        </v-row>
      </v-card>

      <!-- Tabbed Interface for Posts and Other Content -->
      <v-card class="pa-4" rounded="xl" elevation="2">
        <v-tabs v-model="activeTab" grow color="primary">
          <v-tab value="posts">
            <v-icon start icon="mdi-post-outline"></v-icon>
            Posts ({{ 15 }})
          </v-tab>
          <v-tab value="about">
            <v-icon start icon="mdi-information-outline"></v-icon>
            About
          </v-tab>
        </v-tabs>

        <v-card-text>
          <v-window v-model="activeTab">
            <!-- Posts Tab Content -->
            <v-window-item value="posts">
              <div class="text-medium-emphasis py-8 text-center">
                <v-icon size="64" color="grey-lighten-2">mdi-inbox</v-icon>
                <p class="mt-2">No posts yet.</p>
              </div>
              <!-- <v-list v-else>
                <v-list-item v-for="post in user.posts" :key="post.id" class="my-4">
                  <v-card class="pa-4" rounded="lg" variant="tonal">
                    <div class="d-flex justify-space-between align-start">
                      <v-card-title class="text-h6 font-weight-bold pa-0">{{ post.title }}</v-card-title>
                      <v-card-subtitle class="text-caption pa-0 mt-1">
                        {{ formatDate(post.created_at) }}
                      </v-card-subtitle>
                    </div>
                    <v-card-text class="text-body-2 text-medium-emphasis pa-0 mt-2">{{ truncate(post.content, 150) }}</v-card-text>
                  </v-card>
                </v-list-item>
              </v-list> -->
            </v-window-item>

            <!-- About Tab Content -->
            <v-window-item value="about">
              <div class="text-medium-emphasis py-8 text-center">
                <p>This is the "About" section. You can add more details here.</p>
              </div>
            </v-window-item>
          </v-window>
        </v-card-text>
      </v-card>
    </div>
  </DashboardLayout>
</template>
