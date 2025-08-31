<template>
  <v-app :theme="theme">
    <v-app-bar class="px-4" app>
      <v-app-bar-nav-icon @click.stop="drawer = !drawer"></v-app-bar-nav-icon>

      <v-app-bar-title>
        <v-img
          :width="60"
          cover
          :src="Logo"
        ></v-img>
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

          <v-btn icon variant="tonal" class="mr-2" title="Notifications">
            <v-icon>mdi-bell</v-icon>
          </v-btn>

          <v-menu>
            <template v-slot:activator="{ props }">
              <v-btn icon="mdi-account" variant="tonal" v-bind="props"></v-btn>
            </template>

            <v-list>
              <v-list-item title="Setting & Privacy" @click="console.log('i')"></v-list-item>
              <v-list-item title="Help & Support" @click="console.log('i')"></v-list-item>
              <v-list-item title="Logout" @click="console.log('i')"></v-list-item>
            </v-list>
          </v-menu>
        </template>
        <template v-else>
          <v-btn variant="outlined" color="secondary" class="mr-2">
            Login
          </v-btn>
          <v-btn variant="flat" color="primary" class="mr-2">
            Signup
          </v-btn>
        </template>
      </template>
    </v-app-bar>

    <v-navigation-drawer v-model="drawer" app>
      <v-list>
        <v-list-item :href="route('post.index')" prepend-icon="mdi-rss" title="Feed" :active="route().current() === 'post.index'" color="primary"></v-list-item>
        <v-list-item :href="route('post.create')" prepend-icon="mdi-pencil" title="Create a post" :active="['post.create', 'post.edit'].includes(route().current())" color="primary"></v-list-item>
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
    </v-main>
  </v-app>
</template>

<script setup>
import Logo from '@/assets/images/logo.png';
import { usePage } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';
import { handleLogout } from '@/utils/logout';
import { route } from 'ziggy-js';

const theme = ref('light');
const drawer = ref(true);

// Toggle between light and dark themes
const toggleTheme = () => {
  theme.value = theme.value === 'light' ? 'dark' : 'light';
  localStorage.setItem('theme', theme.value);
};

// Load saved theme preference on component mount
onMounted(() => {
  const savedTheme = localStorage.getItem('theme');
  if (savedTheme) {
    theme.value = savedTheme;
  }
});
</script>
