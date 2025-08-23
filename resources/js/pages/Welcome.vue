<script setup lang="ts"></script>

<template>
  <v-app>
    <v-main class="bg-blue-grey-lighten-5">
      <v-container fluid class="fill-height">
        <v-row class="align-center justify-center text-center">
          <v-col cols="12" md="8">
            <v-card class="pa-8 mx-auto" max-width="800" rounded="xl" elevation="4">
              <v-card-title class="text-h3 font-weight-bold text-blue-grey-darken-4 mb-4"> Welcome to Mehfil </v-card-title>
              <v-card-subtitle class="text-h6 text-blue-grey-lighten-1 mb-6"> Your Community. Your Culture. Your Voice. </v-card-subtitle>

              <v-card-text class="text-body-1 text-medium-emphasis">
                Mehfil is more than just a social platform; it's a digital gathering place designed for the Pakistani community. Connect with friends
                and family, share your stories, and explore the vibrant tapestry of culture. Whether you're celebrating traditions or discussing daily
                life, Mehfil is where your community comes to life.
              </v-card-text>

              <!-- Placeholder for a simple illustration or app screenshot -->
              <v-img
                class="mx-auto my-6"
                max-width="400"
                src="https://cdn.pixabay.com/photo/2018/02/23/12/38/flower-3175428_1280.jpg"
                alt="A representation of people gathering on a social platform."
                contain
              ></v-img>

              <div class="d-flex ga-4 mt-8 flex-wrap justify-center">
                <v-btn size="large" color="primary" rounded="xl" class="text-white"> Join Mehfil </v-btn>
                <v-btn size="large" variant="outlined" color="blue-grey-darken-4" rounded="xl"> Log In </v-btn>
              </div>
            </v-card>
          </v-col>
        </v-row>
      </v-container>
    </v-main>
  </v-app>
</template>
