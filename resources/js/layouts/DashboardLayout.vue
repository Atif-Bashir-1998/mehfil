<template>
  <v-app :theme="theme">
    <v-app-bar class="px-4" app>
      <v-app-bar-nav-icon @click.stop="drawer = !drawer"></v-app-bar-nav-icon>

      <v-app-bar-title>
        <v-img :width="60" cover :src="Logo"></v-img>
      </v-app-bar-title>

      <template v-slot:append>
        <v-btn icon variant="tonal" class="mr-2" @click="toggleTheme" :title="theme === 'dark' ? 'Switch to light theme' : 'Switch to dark theme'">
          <v-icon>
            {{ theme === 'dark' ? 'mdi-weather-sunny' : 'mdi-weather-night' }}
          </v-icon>
        </v-btn>
        <template v-if="usePage().props.auth.user">
          <v-btn icon variant="tonal" class="mr-2" title="Messages">
            <v-icon>mdi-chat</v-icon>
          </v-btn>

          <v-menu offset="12" :close-on-content-click="false">
            <template v-slot:activator="{ props }">
              <v-badge :content="unreadNotificationCount" :model-value="unreadNotificationCount > 0" color="error" class="mr-2">
                <v-btn icon variant="tonal" v-bind="props" title="Notifications">
                  <v-icon>mdi-bell</v-icon>
                </v-btn>
              </v-badge>
            </template>

            <v-card min-width="350">
              <v-list>
                <v-list-item class="d-flex justify-space-between align-center">
                  <v-list-item-title class="font-weight-bold text-h6"> Notifications </v-list-item-title>
                  <v-btn v-if="unreadNotificationCount > 0" variant="text" color="primary" size="x-small" @click="markAllAsRead">
                    Mark all as read
                  </v-btn>
                </v-list-item>

                <template v-if="latestNotifications.length">
                  <v-list-item
                    v-for="notification in latestNotifications"
                    :key="notification.id"
                    :class="{ 'bg-grey-lighten-4': notification.read_at === null }"
                    class="border-b"
                    :to="notification.data.link"
                  >
                    <v-list-item-title class="text-body-2 font-weight-regular text-wrap">
                      {{ notification.data.message }}
                    </v-list-item-title>
                    <v-list-item-subtitle class="mt-1">
                      <div class="text-caption text-grey-darken-1">
                        {{ dayjs(notification.created_at).fromNow() }}
                      </div>
                      <div class="d-flex align-center ga-2 mt-2">
                        <v-btn v-if="!notification.read_at" variant="tonal" color="primary" size="x-small" @click.stop="markAsRead(notification.id)">
                          Mark as Read
                        </v-btn>
                        <v-btn v-if="notification.data.post_id" variant="tonal" color="secondary" size="x-small" @click="viewPost(notification.data.post_id)"> Visit Post </v-btn>
                      </div>
                    </v-list-item-subtitle>
                  </v-list-item>
                </template>
                <v-list-item v-else>
                  <v-list-item-subtitle class="text-medium-emphasis py-4 text-center"> You have no new notifications. </v-list-item-subtitle>
                </v-list-item>
              </v-list>

              <v-divider></v-divider>

              <v-card-actions>
                <v-btn text block color="primary" @click="viewAllNotifications"> View All </v-btn>
              </v-card-actions>
            </v-card>
          </v-menu>

          <v-menu>
            <template v-slot:activator="{ props }">
              <v-btn icon variant="tonal" v-bind="props" size="small">
                <v-avatar size="36">
                  <v-img :src="user.profile_image?.image_url || 'https://eu.ui-avatars.com/api/?name=' + user.name + '&size=64'"></v-img>
                </v-avatar>
              </v-btn>
            </template>

            <v-list>
              <v-list-item title="Setting & Privacy" @click="console.log('i')"></v-list-item>
              <v-list-item title="Help & Support" @click="console.log('i')"></v-list-item>
              <v-list-item title="Logout" @click="console.log('i')"></v-list-item>
            </v-list>
          </v-menu>
        </template>
        <template v-else>
          <v-btn variant="outlined" color="secondary" class="mr-2"> Login </v-btn>
          <v-btn variant="flat" color="primary" class="mr-2"> Signup </v-btn>
        </template>
      </template>
    </v-app-bar>

    <v-navigation-drawer v-model="drawer" app>
      <v-list>
        <v-list-item
          :href="route('post.index')"
          prepend-icon="mdi-rss"
          title="Feed"
          :active="route().current() === 'post.index'"
          color="primary"
        ></v-list-item>
        <v-list-item
          :href="route('post.create')"
          prepend-icon="mdi-pencil"
          title="Create a post"
          :active="['post.create', 'post.edit'].includes(route().current())"
          color="primary"
        ></v-list-item>
      </v-list>

      <template v-slot:append>
        <v-list>
          <v-list-item @click="handleLogout" prepend-icon="mdi-logout" title="Logout" base-color="error"></v-list-item>
        </v-list>
      </template>
    </v-navigation-drawer>

    <v-main>
      <v-container fluid class="pa-4">
        <slot />
      </v-container>
      <v-snackbar-queue v-model="snackbarNotifications" color="primary" timeout="8000"></v-snackbar-queue>
    </v-main>
  </v-app>
</template>

<script setup>
import Logo from '@/assets/images/logo.png';
import { handleLogout } from '@/utils/logout';
import { router, usePage } from '@inertiajs/vue3';
import dayjs from 'dayjs';
import relativeTime from 'dayjs/plugin/relativeTime';
import { onMounted, ref } from 'vue';
import { route } from 'ziggy-js';
import { markAllAsRead, markAsRead } from '@/utils/notificationUtils';

dayjs.extend(relativeTime);

const { user } = usePage().props.auth;
const notifications = usePage().props.notifications;

const latestNotifications = ref(notifications.latest);
const unreadNotificationCount = ref(notifications.unread_count);

const theme = ref('light');
const drawer = ref(true);
const snackbarNotifications = ref([]);

// Toggle between light and dark themes
const toggleTheme = () => {
  theme.value = theme.value === 'light' ? 'dark' : 'light';
  localStorage.setItem('theme', theme.value);
};

const viewAllNotifications = () => {
  let url = route('notification.index');

  router.visit(url);
};

const viewPost = (postId) => {
  let url = route('post.show', { post: postId })

  router.visit(url)
}

// Load saved theme preference on component mount
onMounted(() => {
  const savedTheme = localStorage.getItem('theme');
  if (savedTheme) {
    theme.value = savedTheme;
  }

  if (user && window.Echo) {
    window.Echo.private(`App.Models.User.${user.id}`).notification((notification) => {
      snackbarNotifications.value.push(notification.message);
      console.log({ notification });
      router.reload();

      // Update the unread count in real-time
      unreadNotificationCount.value++;
    });
  }
});
</script>
