<script setup>
import DashboardLayout from '@/layouts/DashboardLayout.vue';
import { Head } from '@inertiajs/vue3';
import PostCard from '@/components/PostCard.vue';
import { ref, onMounted, useTemplateRef } from 'vue';
import { useInfiniteScroll } from '@vueuse/core';
import axios from 'axios';

const props = defineProps({
  reaction_types: {
    type: Array,
    required: true
  }
});

const scrollEl = useTemplateRef('scrollEl');
const postList = ref([]);
const loading = ref(false);
const hasMorePosts = ref(true);
const currentPage = ref(1);

const loadPosts = async (page = 1) => {
  if (loading.value) return;

  loading.value = true;

  try {
    const response = await axios.get(`/api/posts?page=${page}`);
    const posts = response.data;

    if (page === 1) {
      postList.value = posts.data;
    } else {
      postList.value = [...postList.value, ...posts.data];
    }

    currentPage.value = posts.current_page;
    hasMorePosts.value = !!posts.next_page_url;
  } catch (error) {
    console.error('Error loading posts:', error);
  } finally {
    loading.value = false;
  }
};

// Handle reaction updates from PostCard
const handleReactionUpdate = (updateData) => {
  const { postId, action, reactionType, reactionsCount, reactions } = updateData;

  // Find the post in postList and update it
  const postIndex = postList.value.findIndex(post => post.id === postId);
  if (postIndex !== -1) {
    // Create a new array to trigger reactivity
    const updatedPosts = [...postList.value];

    // Update the specific post
    updatedPosts[postIndex] = {
      ...updatedPosts[postIndex],
      reactions_count: reactionsCount,
      reactions: reactions
    };

    postList.value = updatedPosts;
  }
};

// Load initial posts when component mounts
onMounted(() => {
  loadPosts(1);
});

// Setup infinite scroll
const { reset } = useInfiniteScroll(
  scrollEl,
  () => {
    if (!loading.value && hasMorePosts.value) {
      loadPosts(currentPage.value + 1);
    }
  },
  {
    distance: 100,
    canLoadMore: () => {
      return !loading.value && hasMorePosts.value;
    },
  }
);

const resetPosts = () => {
  postList.value = [];
  currentPage.value = 1;
  hasMorePosts.value = true;
  reset();
  loadPosts(1);
};
</script>

<template>
  <Head title="Posts" />

  <DashboardLayout>
    <v-row class="align-right">
      <v-col cols="12" class="text-sm-right">
        <v-btn color="primary" :href="route('post.create')"> Create New Post </v-btn>
      </v-col>
    </v-row>

    <v-row justify="center">
      <v-col cols="8">
        <!-- Scrollable container -->
        <div
          ref="scrollEl"
          style="height: 80vh; overflow-y: auto;"
          class="scroll-container"
        >
          <!-- Posts list -->
          <div v-for="post in postList" :key="post.id" class="mb-4">
            <PostCard
              :post="post"
              @reaction-updated="handleReactionUpdate"
            />
          </div>

          <!-- Loading indicator -->
          <div v-if="loading" class="text-center py-4">
            <v-progress-circular indeterminate color="primary"></v-progress-circular>
            <div class="text-caption mt-2">Loading more posts...</div>
          </div>

          <!-- End of feed message -->
          <div v-if="!hasMorePosts && postList.length > 0" class="text-center py-4">
            <div class="text-caption text-medium-emphasis">You've reached the end of the feed</div>
          </div>

          <!-- Empty state -->
          <div v-if="postList.length === 0 && !loading" class="text-center py-8">
            <div class="text-body-1 text-medium-emphasis">No posts yet. Be the first to post!</div>
          </div>
        </div>
      </v-col>
    </v-row>
  </DashboardLayout>
</template>

<style scoped>
.scroll-container {
  scrollbar-width: thin;
  scrollbar-color: #ccc transparent;
}

.scroll-container::-webkit-scrollbar {
  width: 6px;
}

.scroll-container::-webkit-scrollbar-track {
  background: transparent;
}

.scroll-container::-webkit-scrollbar-thumb {
  background-color: #ccc;
  border-radius: 3px;
}
</style>
