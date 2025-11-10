<script setup>
import PostCard from '@/components/PostCard.vue';
import ReportButton from '@/components/ReportButton.vue';
import DashboardLayout from '@/layouts/DashboardLayout.vue';
import { router, usePage } from '@inertiajs/vue3'; // ADDED usePage import
import dayjs from 'dayjs';
import { ref } from 'vue';
import axios from 'axios';

const activeTab = ref('posts');
const dialogVisible = ref(false);
const messageBody = ref('');

const { user } = defineProps({
  user: Object,
});

const sendMessage = async () => {
  const url = route('message.start-conversation', { recipient: user.id });

  await axios.post(url, {
    body: messageBody.value,
  })
  .then(() => {
    // Success
    dialogVisible.value = false;
    messageBody.value = '';
  })
  .catch((error) => {
    // Handle error
    console.error('Failed to send message:', error);
  });
};
</script>

<template>
  <DashboardLayout>
    <v-container class="my-8" style="max-width: 900px">
      <v-card class="pa-0 mb-8" rounded="lg" elevation="4">
        <div class="relative">
          <v-img
            :src="
              user.cover_image?.image_url ||
              'https://images.unsplash.com/photo-1517849845537-4d257902454a?q=80&w=2940&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D'
            "
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
          <div class="d-flex justify-space-between align-center">
            <div class="text-h4 font-weight-bold">{{ user.name }}</div>

            <div v-if="usePage().props.auth.user?.id !== user.id" class="d-flex ga-2 align-center">
              <v-btn color="primary" variant="flat" prepend-icon="mdi-send" rounded="lg" @click="dialogVisible = true"> Message </v-btn>

              <ReportButton :flaggableId="user.id" flaggableType="user" />
            </div>
          </div>
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
            Posts ({{ user.posts.length }})
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
              <div v-if="user.slogan || user.occupation || user.location" class="py-4">
                <v-row>
                  <v-col v-if="user.slogan" cols="12">
                    <v-card flat class="pa-4 rounded-lg border">
                      <v-card-title class="text-h6 font-weight-bold pa-0 mb-2">Slogan</v-card-title>
                      <v-card-text class="text-subtitle-1 pa-0 text-medium-emphasis text-wrap">{{ user.slogan }}</v-card-text>
                    </v-card>
                  </v-col>

                  <v-col v-if="user.occupation || user.location" cols="12" class="d-flex ga-4 flex-wrap">
                    <v-card v-if="user.occupation" flat class="pa-4 flex-grow-1 rounded-lg border">
                      <div class="d-flex align-center">
                        <v-icon icon="mdi-briefcase-variant" size="x-large" class="mr-2"></v-icon>
                        <div>
                          <div class="text-h6 font-weight-bold">Occupation</div>
                          <div class="text-subtitle-1 text-medium-emphasis">{{ user.occupation }}</div>
                        </div>
                      </div>
                    </v-card>

                    <v-card v-if="user.location" flat class="pa-4 flex-grow-1 rounded-lg border">
                      <div class="d-flex align-center">
                        <v-icon icon="mdi-map-marker" size="x-large" class="mr-2"></v-icon>
                        <div>
                          <div class="text-h6 font-weight-bold">Location</div>
                          <div class="text-subtitle-1 text-medium-emphasis">{{ user.location }}</div>
                        </div>
                      </div>
                    </v-card>
                  </v-col>
                </v-row>
              </div>

              <div v-else class="text-medium-emphasis py-8 text-center">
                <p>No additional information available.</p>
              </div>
            </v-window-item>
          </v-window>
        </v-card-text>
      </v-card>
    </v-container>

    <v-dialog v-model="dialogVisible" persistent max-width="600">
      <v-card rounded="lg">
        <v-card-title class="text-h5 font-weight-bold pa-4 border-b"> Send a Message to {{ user.name }} </v-card-title>
        <v-card-text class="py-6">
          <v-textarea v-model="messageBody" label="Your message" variant="outlined" rows="3" hide-details></v-textarea>
        </v-card-text>
        <v-card-actions class="pa-4 d-flex ga-2 justify-end border-t">
          <v-btn color="grey-darken-1" variant="text" @click="dialogVisible = false" rounded="lg"> Cancel </v-btn>
          <v-btn color="primary" variant="flat" :disabled="!messageBody.trim()" @click="sendMessage" rounded="lg"> Send Message </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
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
