<template>
  <v-card class="pa-4" variant="outlined">
    <!-- Header with author info -->
    <div class="d-flex align-center mb-3">
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

    <!-- Post content -->
    <v-card-title class="pa-0 mb-2">
      <Link :href="route('post.show', { id: post.id })" class="text-decoration-none">
        <div class="text-h6 font-weight-bold text-primary">
          {{ post.title }}
        </div>
      </Link>
    </v-card-title>

    <v-card-text class="pa-0 text-body-2 mb-3">
      <p class="mb-0">{{ post.content.substring(0, 200) }}{{ post.content.length > 200 ? '...' : '' }}</p>
    </v-card-text>

    <!-- Tags -->
    <div v-if="post.tags && post.tags.length > 0" class="d-flex ga-1 mb-3 flex-wrap">
      <v-chip v-for="(tag, index) in post.tags" :key="index" size="small" variant="outlined" color="primary">
        {{ tag }}
      </v-chip>
    </div>

    <!-- Stats and Actions -->
    <div class="d-flex align-center justify-space-between">
      <!-- Reaction and comment stats -->
      <div class="d-flex align-center ga-3 text-caption text-medium-emphasis">
        <div v-if="post.reactions_count > 0" class="d-flex align-center">
          <v-icon size="small" color="blue" class="mr-1">mdi-thumb-up-outline</v-icon>
          {{ post.reactions_count }}
        </div>
        <div v-if="post.all_comments_count > 0" class="d-flex align-center">
          <v-icon size="small" class="mr-1">mdi-comment</v-icon>
          {{ post.all_comments_count }}
        </div>
      </div>

      <!-- Action buttons -->
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

    <!-- Quick reaction menu -->
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
