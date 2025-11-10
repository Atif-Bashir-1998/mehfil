<template>
  <div class="ad-banner" @click="handleAdClick">
    <v-card variant="flat" class="ad-card" color="primary" rounded="md">
      <v-img
        v-if="ad.image_url"
        :src="ad.image_url"
        contain
        height="90"
        class="position-relative"
      >
        <v-container class="fill-height pa-4">
          <v-row align="center" justify="space-between" class="fill-height">
            <v-col class="flex-grow-1">
              <div class="text-white">
                <div class="text-body-1 font-weight-bold">{{ ad.title }}</div>
                <div class="text-caption my-2">{{ ad.content }}</div>
                <div class="text-caption font-weight-medium">Learn more →</div>
              </div>
            </v-col>
            <v-col cols="auto">
              <v-chip size="small" variant="outlined" color="white" class="bg-grey-darken-1">
                Sponsored
              </v-chip>
            </v-col>
          </v-row>
        </v-container>
      </v-img>

      <!-- Fallback when no image -->
      <div v-else>
        <v-container class="pa-4">
          <v-row align="center" justify="space-between">
            <v-col class="flex-grow-1">
              <div>
                <div class="text-body-1 font-weight-bold">{{ ad.title }}</div>
                <div class="text-caption text-medium-emphasis my-2">{{ ad.content }}</div>
                <div class="text-caption font-weight-medium text-primary">Learn more →</div>
              </div>
            </v-col>
            <v-col cols="auto">
              <v-chip size="small" variant="outlined" color="info">
                Sponsored
              </v-chip>
            </v-col>
          </v-row>
        </v-container>
      </div>
    </v-card>
  </div>
</template>

<script setup>
import { router } from '@inertiajs/vue3';

const props = defineProps({
  ad: {
    type: Object,
    required: true
  }
});

const handleAdClick = () => {
  let url = route('api.ad.click', {ad: props.ad.id})

  router.visit(url);

  window.open(props.ad.target_url, '_blank', 'noopener,noreferrer');
};
</script>

<style scoped>
.ad-banner {
  cursor: pointer;
  height: 90px;
}

.ad-card {
  height: 100%;
  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;
  transition: all 0.3s ease;
  position: relative;
}

.ad-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.3);
  opacity: 0;
  transition: opacity 0.3s ease;
  border-radius: inherit;
}

.ad-card.has-image::before {
  opacity: 1;
}

.ad-card:hover::before {
  opacity: 0.5;
}

.ad-content {
  height: 100%;
  display: flex;
  flex-direction: column;
  justify-content: center;
  position: relative;
  z-index: 1;
}

.ad-title {
  line-height: 1.2;
  margin-bottom: 4px;
  color: white;
  text-shadow: 0 1px 2px rgba(0, 0, 0, 0.7);
}

.ad-description {
  line-height: 1.3;
  margin-bottom: 8px;
  display: -webkit-box;
  -webkit-box-orient: vertical;
  overflow: hidden;
  color: rgba(255, 255, 255, 0.9);
  text-shadow: 0 1px 1px rgba(0, 0, 0, 0.5);
}

.ad-cta {
  transition: all 0.2s ease;
  color: white;
  text-shadow: 0 1px 1px rgba(0, 0, 0, 0.5);
}

.ad-banner:hover .ad-cta {
  transform: translateX(4px);
}

.sponsored-badge {
  font-size: 0.7rem;
  background-color: rgba(255, 255, 255, 0.2);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.3);
  position: relative;
  z-index: 1;
}
</style>
