<template>
  <v-card class="pa-4" variant="tonal">
    <div class="d-flex align-center">
      <v-avatar size="40" class="mr-3">
        <v-img :src="post.creator.avatar_url || 'https://eu.ui-avatars.com/api/?name=John+Doe&size=250'" alt="Avatar" />
      </v-avatar>
      <div>
        <div class="font-weight-medium">
          <Link :href="route('user.show', { user: post.creator.id })" class="text-decoration-none text-primary">
            {{ post.creator.name }}
          </Link>
        </div>
        <div class="text-caption text-medium-emphasis">
          {{ dayjs(post.created_at).format('MMM D, YYYY [at] h:mm A') }}
        </div>
      </div>
      <v-spacer />
      <v-menu v-if="usePage().props.auth.user?.id === post.creator.id">
        <template v-slot:activator="{ props }">
          <v-btn icon="mdi-dots-vertical" variant="text" size="small" v-bind="props" />
        </template>
        <v-list density="compact">
          <v-list-item prepend-icon="mdi-pencil" :href="route('post.edit', post.id)" title="Edit" />
          <v-list-item prepend-icon="mdi-delete" @click="deletePost(post.id)" title="Delete" />
        </v-list>
      </v-menu>
    </div>

    <v-card-title class="pa-0 mb-2">
      <Link :href="route('post.show', { id: post.id })" class="text-decoration-none">
        <div class="text-h6 font-weight-bold text-primary">
          {{ post.title }}
        </div>
      </Link>
    </v-card-title>

    <v-card-text class="pa-0 text-body-2">
      <p class="mb-0">{{ post.content.substring(0, 200) }}{{ post.content.length > 200 ? '...' : '' }}</p>
    </v-card-text>

    <v-row v-if="post.images && post.images.length > 0" class="mb-3" compact>
      <v-col v-if="post.images.length === 1" cols="12">
        <Link :href="route('post.show', { id: post.id })">
          <v-img
            :src="post.images[0].image_url"
            alt="Post Image"
            contain
            class="rounded-lg"
            height="300"
          ></v-img>
        </Link>
      </v-col>

      <v-col v-else-if="post.images.length >= 2" :cols="12">
        <v-row dense>
          <v-col
            v-for="(image, index) in post.images.slice(0, 3)"
            :key="index"
            :cols="post.images.length === 2 ? 6 : 4"
            >
            <Link :href="route('post.show', { id: post.id })">
              <v-img
                :src="image.image_url"
                :alt="`Image ${index + 1}`"
                contain
                class="rounded-lg"
                height="150"
              >
                <div
                  v-if="index === 2 && post.images.length > 3"
                  class="d-flex fill-height justify-center align-center overlay"
                >
                  <span class="text-h5 font-weight-bold text-white">+{{ post.images.length - 3 }}</span>
                </div>
              </v-img>
            </Link>
          </v-col>
        </v-row>
      </v-col>
    </v-row>
    <div v-if="post.tags && post.tags.length > 0" class="d-flex ga-1 mb-3 flex-wrap">
      <v-chip v-for="(tag, index) in post.tags" :key="index" size="small" variant="outlined" color="primary">
        {{ tag }}
      </v-chip>
    </div>

    <div class="d-flex align-center justify-space-between">
      <div class="d-flex align-center ga-3 text-caption text-medium-emphasis">
        <div v-if="post.reactions_count > 0" class="d-flex align-center">
          <v-icon size="small" color="blue" class="mr-1">mdi-thumb-up-outline</v-icon>
          {{ post.reactions_count }}
        </div>
        <div v-if="post.all_comments_count > 0" class="d-flex align-center">
          <v-icon size="small" class="mr-1">mdi-comment</v-icon>
          {{ post.all_comments_count }}
        </div>
        <div v-if="usePage().props.auth.user?.id !== post.creator.id && !post.is_flagged_by_current_user">
          <ReportButton :flaggableId="post.id" flaggableType="post" />
        </div>
      </div>

      <div class="d-flex align-center ga-2">
        <v-btn variant="text" size="small" @click="toggleReaction">
          <v-icon left>{{ userReaction ? userReaction.icon : 'mdi-emoticon-outline' }}</v-icon>
          {{ post.reactions_count || 0 }}
        </v-btn>
        <v-btn variant="text" size="small" :href="route('post.show', post.id)">
          <v-icon left>mdi-comment</v-icon>
          {{ post.all_comments_count || 0 }}
        </v-btn>
        <v-btn variant="text" size="small" :href="route('post.show', post.id)">
          Read More
        </v-btn>
      </div>
    </div>

    <v-menu v-model="reactionMenu" :close-on-content-click="false">
      <template v-slot:activator="{ props }">
        <div v-bind="props"></div>
      </template>
      <v-card>
        <v-card-text class="pa-2">
          <div class="d-flex ga-1">
            <v-btn
              v-for="reaction in reactionTypes"
              :key="reaction.value"
              icon
              :color="userReaction?.value === reaction.value ? 'primary' : ''"
              @click="handleAddReaction(reaction)"
            >
              <v-icon>{{ reaction.icon }}</v-icon>
            </v-btn>
          </div>
        </v-card-text>
      </v-card>
    </v-menu>
  </v-card>
</template>

<script setup>
import { Link, router, usePage } from '@inertiajs/vue3';
import { addReaction } from '@/utils/reactionUtils';
import ReportButton from './ReportButton.vue';
import dayjs from 'dayjs';
import { ref } from 'vue';

const { post } = defineProps({
  post: {
    type: Object,
    required: true,
  },
});

const reactionMenu = ref(false);
const userReaction = ref(null);
const reactionTypes = usePage().props.reaction_types;

const handleAddReaction = async (reaction) => {
  await addReaction(post.id, reaction.value)
  reactionMenu.value = false;
}

const toggleReaction = () => {
  if (userReaction.value) {
    userReaction.value = null;
  } else {
    reactionMenu.value = true;
  }
};

const deletePost = (postId) => {
  if (confirm('Are you sure you want to delete this post?')) {
    router.delete(route('post.destroy', postId), {
      preserveScroll: true,
    });
  }
};
</script>

<style scoped>
.overlay {
  background-color: rgba(0, 0, 0, 0.5);
  transition: background-color 0.3s ease;
}

.overlay:hover {
  background-color: rgba(0, 0, 0, 0.7);
}
</style>
