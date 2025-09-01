<template>
  <v-container class="my-8" style="max-width: 900px">
    <v-card class="pa-6" rounded="lg" elevation="4">
      <v-card-title class="text-h4 font-weight-bold mb-4">
        Privacy Policy
      </v-card-title>

      <v-card-text class="text-body-1">
        <p class="mb-4">
          This Privacy Policy describes how your personal information is collected, used, and shared when you use our social media platform.
          We are committed to protecting your privacy and ensuring you have a safe and positive experience.
        </p>

        <h3 class="text-h6 font-weight-bold mt-6 mb-2">1. Information We Collect</h3>
        <p>
          We collect various types of information to provide and improve our service to you.
        </p>
        <v-list dense>
          <v-list-item>
            <template #prepend><v-icon>mdi-chevron-right</v-icon></template>
            <template #default>
              <v-list-item-title class="font-weight-bold">Personal Information:</v-list-item-title>
              <v-list-item-subtitle>
                When you create an account, you provide us with personal details such as your name, email address, password, profile image, and cover image. You may also choose to provide additional information like your slogan, occupation, and location.
              </v-list-item-subtitle>
            </template>
          </v-list-item>
          <v-list-item>
            <template #prepend><v-icon>mdi-chevron-right</v-icon></template>
            <template #default>
              <v-list-item-title class="font-weight-bold">User-Generated Content:</v-list-item-title>
              <v-list-item-subtitle>
                This includes any content you post to the platform, such as text, images, comments, and messages. This content is associated with your account.
              </v-list-item-subtitle>
            </template>
          </v-list-item>
          <v-list-item>
            <template #prepend><v-icon>mdi-chevron-right</v-icon></template>
            <template #default>
              <v-list-item-title class="font-weight-bold">Usage Data:</v-list-item-title>
              <v-list-item-subtitle>
                We automatically collect information about how you interact with our platform, including your IP address, browser type, pages visited, and the time and date of your visit.
              </v-list-item-subtitle>
            </template>
          </v-list-item>
        </v-list>

        <h3 class="text-h6 font-weight-bold mt-6 mb-2">2. How We Use Your Information</h3>
        <p>
          We use the information we collect for the following purposes:
        </p>
        <v-list dense>
          <v-list-item>
            <template #prepend><v-icon>mdi-account-circle</v-icon></template>
            <template #default>
              <v-list-item-title>To create and manage your account.</v-list-item-title>
            </template>
          </v-list-item>
          <v-list-item>
            <template #prepend><v-icon>mdi-post-outline</v-icon></template>
            <template #default>
              <v-list-item-title>To provide, operate, and maintain our platform and its features.</v-list-item-title>
            </template>
          </v-list-item>
          <v-list-item>
            <template #prepend><v-icon>mdi-chat-processing-outline</v-icon></template>
            <template #default>
              <v-list-item-title>To facilitate communication and private messaging between users.</v-list-item-title>
            </template>
          </v-list-item>
          <v-list-item>
            <template #prepend><v-icon>mdi-bell-outline</v-icon></template>
            <template #default>
              <v-list-item-title>To send you notifications and updates about your activity and our service.</v-list-item-title>
            </template>
          </v-list-item>
          <v-list-item>
            <template #prepend><v-icon>mdi-chart-line</v-icon></template>
            <template #default>
              <v-list-item-title>To analyze usage and trends to improve our service and develop new features.</v-list-item-title>
            </template>
          </v-list-item>
        </v-list>

        <h3 class="text-h6 font-weight-bold mt-6 mb-2">3. Data Security</h3>
        <p>
          We implement a variety of security measures to maintain the safety of your personal information. Your account password is encrypted and we use secure technologies to protect your data from unauthorized access, use, or disclosure.
        </p>

        <h3 class="text-h6 font-weight-bold mt-6 mb-2">4. Your Rights</h3>
        <p>
          You have the right to access, update, and delete your personal information. If you wish to exercise these rights, please contact us at the address provided below.
        </p>
        <v-list dense>
          <v-list-item>
            <template #prepend><v-icon>mdi-account-edit</v-icon></template>
            <template #default>
              <v-list-item-title>The right to access your personal data.</v-list-item-title>
            </template>
          </v-list-item>
          <v-list-item>
            <template #prepend><v-icon>mdi-account-remove</v-icon></template>
            <template #default>
              <v-list-item-title>The right to request deletion of your account and data.</v-list-item-title>
            </template>
          </v-list-item>
        </v-list>

        <h3 class="text-h6 font-weight-bold mt-6 mb-2">5. Changes to This Privacy Policy</h3>
        <p>
          We may update our Privacy Policy from time to time. We will notify you of any changes by posting the new Privacy Policy on this page.
        </p>

        <h3 class="text-h6 font-weight-bold mt-6 mb-2">6. Contact Us</h3>
        <p>
          If you have any questions about this Privacy Policy, please contact us:
        </p>
        <v-list dense>
          <v-list-item>
            <template #prepend><v-icon>mdi-email-outline</v-icon></template>
            <template #default>
              <v-list-item-title>By email: <a href="mailto:privacy@example.com">privacy@example.com</a></v-list-item-title>
            </template>
          </v-list-item>
        </v-list>
      </v-card-text>
    </v-card>
  </v-container>
</template>

<script setup></script>

<style scoped>
.my-8 {
  margin-top: 64px;
  margin-bottom: 64px;
}
</style>
