<script setup>
import DashboardLayout from '@/layouts/DashboardLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { computed } from 'vue';
import dayjs from 'dayjs';

const { ads, adStatusTypes } = defineProps({
  ads: Object,
  adStatusTypes: Array,
});

const statusColors = {
  active: 'success',
  paused: 'warning',
  expired: 'error',
  pending: 'info',
};

const formatDate = (date) => {
  return dayjs(date).format('MMM D, YYYY');
};

const isActive = (ad) => {
  const now = dayjs();
  const starts = dayjs(ad.starts_at);
  const ends = dayjs(ad.ends_at);
  return ad.status === 'active' && now.isAfter(starts) && now.isBefore(ends);
};

const redirectToCreate = () => {
  router.visit(route('ad.create'))
}

const redirectToEdit = (ad) => {
  router.visit(route('ad.edit', { ad: ad.id }))
}
</script>

<template>
  <Head title="My Ads" />

  <DashboardLayout>
    <v-container class="py-8">
      <v-row>
        <v-col cols="12">
          <div class="d-flex justify-space-between align-center mb-6">
            <div>
              <h1 class="text-h4 font-weight-bold">My Ads</h1>
              <p class="text-body-1 text-medium-emphasis mt-2">
                Manage your advertisements and track their performance
              </p>
            </div>
            <v-btn
              color="primary"
              prepend-icon="mdi-plus"
              @click="redirectToCreate"
            >
              Create New Ad
            </v-btn>
          </div>

          <v-card rounded="lg" elevation="2">
            <v-card-text class="pa-0">
              <v-table>
                <thead>
                  <tr>
                    <th class="text-left">Ad Title</th>
                    <th class="text-left">Status</th>
                    <th class="text-left">Duration</th>
                    <th class="text-center">Impressions</th>
                    <th class="text-center">Clicks</th>
                    <th class="text-center">CTR</th>
                    <th class="text-right">Points Spent</th>
                    <th class="text-right">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <tr
                    v-for="ad in ads.data"
                    :key="ad.id"
                    class="hover-row"
                  >
                    <td>
                      <div class="d-flex align-center">
                        <v-avatar
                          v-if="ad.image_url"
                          size="40"
                          rounded
                          class="mr-3"
                        >
                          <v-img :src="ad.image_url" :alt="ad.title" />
                        </v-avatar>
                        <div>
                          <div class="font-weight-medium text-body-1">
                            {{ ad.title }}
                          </div>
                          <div class="text-caption text-medium-emphasis">
                            {{ formatDate(ad.created_at) }}
                          </div>
                        </div>
                      </div>
                    </td>
                    <td>
                      <v-chip
                        :color="statusColors[ad.status]"
                        variant="tonal"
                        size="small"
                      >
                        {{ ad.status }}
                      </v-chip>
                      <v-chip
                        v-if="isActive(ad)"
                        color="success"
                        size="small"
                        class="ml-1"
                      >
                        Live
                      </v-chip>
                    </td>
                    <td>
                      <div class="text-body-2">
                        {{ formatDate(ad.starts_at) }}
                      </div>
                      <div class="text-caption text-medium-emphasis">
                        to {{ formatDate(ad.ends_at) }}
                      </div>
                    </td>
                    <td class="text-center">
                      <span class="font-weight-medium">
                        {{ ad.impressions.toLocaleString() }}
                      </span>
                    </td>
                    <td class="text-center">
                      <span class="font-weight-medium">
                        {{ ad.clicks.toLocaleString() }}
                      </span>
                    </td>
                    <td class="text-center">
                      <span
                        class="font-weight-medium"
                        :class="{
                          'text-success': (ad.clicks / ad.impressions).toFixed(2) > 2,
                          'text-warning': (ad.clicks / ad.impressions).toFixed(2) > 0.5 && (ad.clicks / ad.impressions).toFixed(2) <= 2,
                          'text-error': (ad.clicks / ad.impressions).toFixed(2) <= 0.5
                        }"
                      >
                        {{ (ad.clicks / ad.impressions).toFixed(2) }}%
                      </span>
                    </td>
                    <td class="text-right">
                      <span class="font-weight-bold text-primary">
                        {{ ad.points_spent.toLocaleString() }}
                      </span>
                    </td>
                    <td class="text-right">
                      <div class="d-flex justify-end ga-2">
                        <v-btn
                          icon="mdi-pencil"
                          variant="text"
                          size="small"
                          color="warning"
                          @click="redirectToEdit(ad)"
                        ></v-btn>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </v-table>

              <!-- Empty state -->
              <div
                v-if="ads.data.length === 0"
                class="text-center py-16"
              >
                <v-icon size="64" color="grey-lighten-1" class="mb-4">
                  mdi-advertisements
                </v-icon>
                <h3 class="text-h6 text-grey-darken-1 mb-2">
                  No ads created yet
                </h3>
                <p class="text-body-2 text-grey mb-4">
                  Create your first ad to reach our community
                </p>
                <v-btn
                  color="primary"
                  prepend-icon="mdi-plus"
                  @click="redirectToCreate"
                >
                  Create Your First Ad
                </v-btn>
              </div>
            </v-card-text>

            <!-- Pagination -->
            <v-card-actions v-if="ads.data.length > 0" class="pa-4">
              <div class="text-caption text-medium-emphasis">
                Showing {{ ads.from }} to {{ ads.to }} of {{ ads.total }} ads
              </div>
              <v-spacer></v-spacer>
              <v-pagination
                v-model="ads.current_page"
                :length="ads.last_page"
                :total-visible="7"
                @update:model-value="(page) => $inertia.visit(ads.path + '?page=' + page)"
              ></v-pagination>
            </v-card-actions>
          </v-card>
        </v-col>
      </v-row>
    </v-container>
  </DashboardLayout>
</template>

<style scoped>
.hover-row:hover {
  background-color: rgba(0, 0, 0, 0.02);
}
</style>
