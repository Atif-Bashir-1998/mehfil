<script setup>
import DashboardLayout from '@/layouts/DashboardLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import dayjs from 'dayjs';
import relativeTime from 'dayjs/plugin/relativeTime';
import { markAsRead, markAllAsRead, removeNotification } from '../utils/notificationUtils';

dayjs.extend(relativeTime);

const props = defineProps({
  notifications: Object,
});

const viewPost = (notification) => {
  let url = route('post.show', { post: notification.data.post_id });
  router.visit(url)
}

</script>

<template>
  <Head title="Notifications" />

  <DashboardLayout>
    <v-container class="my-8">
      <v-card class="pa-6" rounded="lg" elevation="4">
        <div class="d-flex align-center justify-space-between mb-4">
          <v-card-title class="text-h4 font-weight-bold pa-0">Notifications</v-card-title>
          <v-btn variant="text" color="primary" @click="markAllAsRead"> Mark all as read </v-btn>
        </div>

        <v-divider class="my-4"></v-divider>

        <div v-if="notifications.data.length">
          <v-list class="pa-0">
            <v-list-item
              v-for="notification in notifications.data"
              :key="notification.id"
              :class="{ 'bg-grey-lighten-4': notification.read_at === null }"
              class="mb-2 rounded-lg"
            >
              <template v-slot:prepend>
                <v-icon
                  :color="notification.read_at ? 'grey-lighten-1' : 'primary'"
                  :icon="notification.read_at ? 'mdi-bell-outline' : 'mdi-bell-badge'"
                ></v-icon>
              </template>

              <v-list-item-title class="font-weight-regular text-wrap">
                {{ notification.data.message }}
              </v-list-item-title>

              <v-list-item-subtitle class="text-caption text-grey-darken-1">
                {{ dayjs(notification.created_at).fromNow() }}
              </v-list-item-subtitle>

              <template v-slot:append>
                <div class="d-flex ga-2">
                  <v-btn
                    variant="tonal"
                    size="small"
                    :color="notification.read_at ? 'grey' : 'primary'"
                    :title="notification.read_at ? 'Mark as unread' : 'Mark as read'"
                    @click="markAsRead(notification.id)"
                  >
                    <v-icon>{{ notification.read_at ? 'mdi-email-open-outline' : 'mdi-email-outline' }}</v-icon>
                  </v-btn>
                  <v-btn
                    v-if="notification.data.post_id"
                    variant="tonal"
                    size="small"
                    color="secondary"
                    title="View Post"
                    @click="viewPost(notification)"
                  >
                    <v-icon>mdi-eye</v-icon>
                  </v-btn>
                  <v-btn variant="tonal" size="small" color="error" title="Delete" @click="removeNotification(notification.id)">
                    <v-icon>mdi-delete-outline</v-icon>
                  </v-btn>
                </div>
              </template>
            </v-list-item>
          </v-list>

          <v-pagination
            v-if="notifications.last_page > 1"
            class="mt-4"
            :length="notifications.last_page"
            :total-visible="5"
            :model-value="notifications.current_page"
            @update:model-value="(page) => router.visit(notifications.links[page].url, { preserveScroll: true })"
          ></v-pagination>
        </div>
        <div v-else class="text-medium-emphasis py-8 text-center">
          <v-icon size="64" color="grey-lighten-2">mdi-bell-off</v-icon>
          <p class="mt-2">No notifications found.</p>
        </div>
      </v-card>
    </v-container>
  </DashboardLayout>
</template>
