<script setup>
import { ref } from 'vue';
import Logo from '@/assets/images/logo.png';
import Group from '@/assets/images/group.jpg';

const drawer = ref(false); // Used for mobile navigation if you want to add it later
const navItems = [
  { title: 'Home', link: '#home' },
  { title: 'Our Mission', link: '#mission' },
  { title: 'About Us', link: '#about' },
  { title: 'Contact Us', link: '#contact' },
];
</script>

<template>
  <v-app>
    <v-app-bar app class="px-4" color="white" flat>
      <img :src="Logo" alt="Mehfil Logo" style="height: 40px; margin-right: 16px;">

      <v-spacer></v-spacer>

      <div class="d-none d-md-flex ga-4">
        <v-btn v-for="item in navItems" :key="item.title" :href="item.link" variant="text" color="blue-grey-darken-4">
          {{ item.title }}
        </v-btn>
      </div>

      <v-app-bar-nav-icon class="d-md-none" @click.stop="drawer = !drawer"></v-app-bar-nav-icon>
    </v-app-bar>

    <v-main class="bg-blue-grey-lighten-5">
      <v-container fluid class="py-16" id="home">
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

              <v-img
                class="mx-auto my-6"
                max-width="400"
                src="https://cdn.pixabay.com/photo/2019/08/21/11/48/social-media-4421043_1280.png"
                alt="A representation of people gathering on a social platform."
                contain
              ></v-img>

              <div class="d-flex ga-4 mt-8 flex-wrap justify-center">
                <v-btn :href="route('register')" size="large" color="primary" rounded="xl" class="text-white"> Join Mehfil </v-btn>
                <v-btn :href="route('login')" size="large" variant="outlined" color="blue-grey-darken-4" rounded="xl"> Log In </v-btn>
              </div>
            </v-card>
          </v-col>
        </v-row>
      </v-container>

      <div class="bg-white">
        <v-container class="py-16" id="mission">
          <v-row class="text-center">
            <v-col cols="12">
              <h2 class="text-h4 font-weight-bold text-blue-grey-darken-4 mb-4">Our Mission</h2>
              <v-divider class="my-4 mx-auto" style="width: 80px;"></v-divider>
              <p class="text-body-1 text-medium-emphasis mx-auto" style="max-width: 700px;">
                Our mission is to bridge the cultural and geographical divides within the Pakistani diaspora. We aim to create a safe, vibrant, and
                inclusive space where individuals can share, connect, and celebrate their heritage with pride. We are dedicated to fostering a sense of
                community that transcends borders.
              </p>
            </v-col>
          </v-row>
        </v-container>

        <v-container class="py-16" id="about">
          <v-row class="align-center">
            <v-col cols="12" md="6">
              <img :src="Group" alt="A diverse group of people discussing ideas." class="w-100 rounded-lg elevation-4">
            </v-col>
            <v-col cols="12" md="6" class="mt-8 mt-md-0">
              <h2 class="text-h4 font-weight-bold text-blue-grey-darken-4 mb-4">About Us</h2>
              <p class="text-body-1 text-medium-emphasis mb-4">
                Founded by a team of passionate individuals, Mehfil was born from the desire to create a digital home for the Pakistani community. We understand the value of connection and the importance of preserving cultural traditions in an increasingly globalized world.
              </p>
              <p class="text-body-1 text-medium-emphasis">
                Our team is committed to building a platform that is not only powerful and secure but also reflects the warmth and hospitality central to Pakistani culture. We believe in the power of community to uplift and inspire.
              </p>
            </v-col>
          </v-row>
        </v-container>

        <v-container class="py-16" id="contact">
          <v-row class="text-center">
            <v-col cols="12" md="8" class="mx-auto">
              <h2 class="text-h4 font-weight-bold text-blue-grey-darken-4 mb-4">Get in Touch</h2>
              <p class="text-body-1 text-medium-emphasis mb-8">
                Have questions or feedback? We'd love to hear from you. Reach out to our team via email or follow us on social media.
              </p>
              <v-btn href="mailto:contact@mehfil.com" size="large" color="primary" rounded="xl" class="text-white">
                <v-icon start>mdi-email</v-icon> Email Us
              </v-btn>
              <div class="d-flex ga-4 justify-center mt-6">
                <v-btn icon color="blue-grey-darken-4" size="small"><v-icon>mdi-facebook</v-icon></v-btn>
                <v-btn icon color="blue-grey-darken-4" size="small"><v-icon>mdi-twitter</v-icon></v-btn>
                <v-btn icon color="blue-grey-darken-4" size="small"><v-icon>mdi-instagram</v-icon></v-btn>
              </div>
            </v-col>
          </v-row>
        </v-container>
      </div>

      <v-footer class="bg-blue-grey-darken-4 text-white py-8">
        <v-container>
          <v-row justify="center">
            <v-col cols="12" class="text-center">
              <p class="text-caption">&copy; {{ new Date().getFullYear() }} Mehfil. All Rights Reserved.</p>
            </v-col>
          </v-row>
        </v-container>
      </v-footer>
    </v-main>
  </v-app>
</template>
