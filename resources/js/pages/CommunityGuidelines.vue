<template>
  <v-container class="my-8" style="max-width: 900px">
    <v-card class="pa-6" rounded="lg" elevation="4">
      <v-card-title class="text-h4 font-weight-bold mb-4 text-center">
        Community Guidelines
      </v-card-title>

      <v-card-text class="text-body-1">
        <p class="mb-4">
          Our platform is built on trust, respect, and open communication. These guidelines are designed to ensure a safe, positive, and inclusive environment for everyone. By using our service, you agree to adhere to these rules.
        </p>

        <h3 class="text-h6 font-weight-bold mt-6 mb-2">1. Respectful Interaction</h3>
        <p>
          Treat all members of the community with courtesy and respect. Healthy debate is encouraged, but harassment, abuse, and hate speech are strictly prohibited.
        </p>
        <v-list dense>
          <v-list-item>
            <template #prepend><v-icon color="success">mdi-check-circle-outline</v-icon></template>
            <v-list-item-title class="font-weight-bold">Be Courteous:</v-list-item-title>
            <v-list-item-subtitle>Engage in conversations with civility and a welcoming attitude.</v-list-item-subtitle>
          </v-list-item>
          <v-list-item>
            <template #prepend><v-icon color="error">mdi-close-circle-outline</v-icon></template>
            <v-list-item-title class="font-weight-bold">No Harassment or Bullying:</v-list-item-title>
            <v-list-item-subtitle>Do not post content that attacks, mocks, or threatens other users.</v-list-item-subtitle>
          </v-list-item>
          <v-list-item>
            <template #prepend><v-icon color="error">mdi-close-circle-outline</v-icon></template>
            <v-list-item-title class="font-weight-bold">No Hate Speech:</v-list-item-title>
            <v-list-item-subtitle>Content that promotes discrimination or violence based on race, ethnicity, religion, gender, sexual orientation, disability, or nationality will be removed.</v-list-item-subtitle>
          </v-list-item>
        </v-list>

        <h3 class="text-h6 font-weight-bold mt-6 mb-2">2. Prohibited Content</h3>
        <p>
          The following types of content are not allowed on the platform and may result in immediate content removal and/or account suspension.
        </p>
        <v-list dense>
          <v-list-item>
            <template #prepend><v-icon>mdi-alert</v-icon></template>
            <v-list-item-title>Illegal Activities:</v-list-item-title>
            <v-list-item-subtitle>Posting content that advocates or depicts illegal acts.</v-list-item-subtitle>
          </v-list-item>
          <v-list-item>
            <template #prepend><v-icon>mdi-alert</v-icon></template>
            <v-list-item-title>Nudity or Sexual Content:</v-list-item-title>
            <v-list-item-subtitle>Strictly prohibited, especially content involving minors.</v-list-item-subtitle>
          </v-list-item>
          <v-list-item>
            <template #prepend><v-icon>mdi-alert</v-icon></template>
            <v-list-item-title>Private and Confidential Information:</v-list-item-title>
            <v-list-item-subtitle>Do not share private information (doxxing), including phone numbers, home addresses, or private financial details, without explicit consent.</v-list-item-subtitle>
          </v-list-item>
          <v-list-item>
            <template #prepend><v-icon>mdi-alert</v-icon></template>
            <v-list-item-title>Spam and Impersonation:</v-list-item-title>
            <v-list-item-subtitle>Do not engage in excessive spamming, flooding of comments, or impersonating other individuals or entities.</v-list-item-subtitle>
          </v-list-item>
        </v-list>

        <h3 class="text-h6 font-weight-bold mt-6 mb-2">3. Moderation and Enforcement</h3>
        <p>
          We rely on both automated tools and user reports (like the flag system you are working on!) to enforce these guidelines.
        </p>
        <v-list dense>
          <v-list-item>
            <template #prepend><v-icon>mdi-flag-outline</v-icon></template>
            <v-list-item-title>Reporting Content:</v-list-item-title>
            <v-list-item-subtitle>Use the 'Report' feature to notify moderators of content that violates these guidelines. Abuse of the reporting system is also a violation.</v-list-item-subtitle>
          </v-list-item>
          <v-list-item>
            <template #prepend><v-icon>mdi-gavel</v-icon></template>
            <v-list-item-title>Consequences:</v-list-item-title>
            <v-list-item-subtitle>Violations may result in warnings, content removal, temporary account suspension, or permanent banning, depending on the severity and frequency of the violation.</v-list-item-subtitle>
          </v-list-item>
        </v-list>

        <h3 class="text-h6 font-weight-bold mt-6 mb-2">4. Contact Us</h3>
        <p>
          If you have questions about these guidelines or wish to appeal a moderation decision, please contact our team:
        </p>
        <v-list dense>
          <v-list-item>
            <template #prepend><v-icon>mdi-email-outline</v-icon></template>
            <v-list-item-title>By email: <a href="mailto:moderation@example.com">moderation@example.com</a></v-list-item-title>
          </v-list-item>
        </v-list>
      </v-card-text>
    </v-card>
  </v-container>
</template>

<style scoped>
.my-8 {
  margin-top: 64px;
  margin-bottom: 64px;
}
</style>
