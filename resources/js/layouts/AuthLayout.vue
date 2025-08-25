<template>
  <Head v-if="title" :title="title" />
  <v-container fluid class="pa-0 h-screen">
    <v-row no-gutters class="fill-height">
      <v-col cols="12" md="6" class="d-none d-md-flex align-center bg-blue-grey-darken-4 pa-8 justify-center">
        <v-card class="bg-transparent text-center text-white" elevation="0">
          <v-icon size="100" class="mb-4">mdi-security</v-icon>
          <h2 class="text-h4 font-weight-bold mb-2">Welcome to Mehfil</h2>
          <p class="text-subtitle-1 text-grey-lighten-2">Your community for connections, thoughts, and creativity.</p>
        </v-card>
      </v-col>

      <v-col cols="12" md="6" class="d-flex align-center pa-4 pa-md-8 justify-center bg-white">
        <slot></slot>
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';

defineProps({
  title: {
    type: String,
    required: false
  }
})

</script>

<style scoped>

</style>
