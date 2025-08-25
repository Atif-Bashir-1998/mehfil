<!-- <script setup>
import DashboardLayout from '@/layouts/DashboardLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import dayjs from 'dayjs';

defineProps({
  post: {
    type: Object,
    required: true
  }
});

const deletePost = (postId) => {
  if (confirm('Are you sure you want to delete this post?')) {
    router.delete(route('post.destroy', postId), {
      onSuccess: () => {
        router.visit(route('post.index'));
      },
    });
  }
};
</script>

<template>
  <Head :title="post.title" />

  <DashboardLayout>
    <v-card class="mx-auto">
      <v-card-title class="d-flex justify-space-between align-start">
        <div>
          <h1 class="text-h4 font-weight-bold">{{ post.title }}</h1>
          <div class="text-subtitle-1 text-medium-emphasis mt-2">
            by <Link :href="route('user.show', {user: post.creator.id})" class="font-weight-bold text-primary">{{ post.creator.name }}</Link> •
            {{ dayjs(post.created_at).format('MMMM d, YYYY h:mm a') }}
          </div>
        </div>
        <div class="d-flex ga-2" v-if="$page.props.auth.user?.id === post.creator.id">
          <v-btn variant="outlined" size="small" :href="route('post.edit', post.id)"> Edit </v-btn>
          <v-btn color="red-darken-2" variant="flat" size="small" @click="deletePost(post.id)"> Delete </v-btn>
        </div>
      </v-card-title>

      <v-card-text>
        <div v-if="post.tags && post.tags.length > 0" class="d-flex ga-2 mb-6 flex-wrap">
          <v-chip v-for="(tag, index) in post.tags" :key="index" size="small" label>
            {{ tag }}
          </v-chip>
        </div>

        <div class="prose text-body-1 mt-4 mb-6">
          <p class="whitespace-pre-line">{{ post.content }}</p>
        </div>
      </v-card-text>

      <v-card-actions>
        <v-btn variant="outlined" :href="route('post.index')"> ← Back to Posts </v-btn>
      </v-card-actions>
    </v-card>
  </DashboardLayout>
</template> -->

<script setup>
import DashboardLayout from '@/layouts/DashboardLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import dayjs from 'dayjs';
import relativeTime from 'dayjs/plugin/relativeTime'
import Comment from '@/components/Comment.vue';
import { addComment } from '@/utils/commentUtils';
import { ref } from 'vue';

dayjs.extend(relativeTime)

const { post } = defineProps({
  post: {
    type: Object,
    required: true
  }
});

const commentText = ref('');

const handleAddComment = () => {
  let payload = {
    parent_id: null,
    content: commentText.value
  }
  addComment(post.id, payload)
}

const deletePost = (postId) => {
  if (confirm('Are you sure you want to delete this post?')) {
    router.delete(route('post.destroy', postId), {
      onSuccess: () => {
        router.visit(route('post.index'));
      },
    });
  }
};
</script>

<template>
  <Head :title="post.title" />

  <DashboardLayout>
    <!-- Main Post Card -->
    <v-card class="mx-auto">
      <v-card-title class="d-flex justify-space-between align-start">
        <div>
          <h1 class="text-h4 font-weight-bold">{{ post.title }}</h1>
          <div class="text-subtitle-1 text-medium-emphasis mt-2">
            by <Link :href="route('user.show', {user: post.creator.id})" class="font-weight-bold text-primary">{{ post.creator.name }}</Link> •
            {{ dayjs(post.created_at).format('MMMM d, YYYY h:mm a') }}
          </div>
        </div>

        <div class="d-flex ga-2" v-if="$page.props.auth.user?.id === post.creator.id">
          <v-btn variant="outlined" size="small" :href="route('post.edit', post.id)"> Edit </v-btn>
          <v-btn color="red-darken-2" variant="flat" size="small" @click="deletePost(post.id)"> Delete </v-btn>
        </div>
      </v-card-title>

      <v-card-text>
        <!-- Tags -->
        <div v-if="post.tags && post.tags.length > 0" class="d-flex ga-2 mb-6 flex-wrap">
          <v-chip v-for="(tag, index) in post.tags" :key="index" size="small" label>
            {{ tag }}
          </v-chip>
        </div>

        <!-- Post Content -->
        <div class="prose text-body-1 mt-4 mb-6">
          <p class="whitespace-pre-line">{{ post.content }}</p>
        </div>

        <!-- Reaction Stats -->
        <div class="d-flex align-center ga-4 text-medium-emphasis mb-4">
          <div v-if="post.reactions_count > 0" class="d-flex align-center">
            <v-icon size="small" color="red" class="mr-1">mdi-heart</v-icon>
            {{ post.reactions_count }} reactions
          </div>
          <div v-if="post.comments_count > 0" class="d-flex align-center">
            <v-icon size="small" class="mr-1">mdi-comment</v-icon>
            {{ post.comments_count }} comments
          </div>
        </div>
      </v-card-text>

      <v-card-actions class="d-flex justify-space-between">
        <v-btn variant="outlined" :href="route('post.index')"> ← Back to Posts </v-btn>

        <!-- Reaction Buttons -->
        <div class="d-flex ga-2">
          <v-btn variant="text" size="small" prepend-icon="mdi-heart-outline">
            React
          </v-btn>
          <v-btn variant="text" size="small" prepend-icon="mdi-comment-outline">
            Comment
          </v-btn>
        </div>
      </v-card-actions>
    </v-card>

    <!-- Comments Section -->
    <v-card class="mx-auto">
      <v-card-title>
        <h2 class="text-h5">Comments ({{ post.comments_count || 0 }})</h2>
      </v-card-title>

      <v-card-text>
        <!-- Add Comment Form -->
        <form class="mb-6">
          <v-textarea
            label="Add a comment..."
            name="content"
            v-model="commentText"
            rows="3"
            variant="outlined"
            required
          ></v-textarea>
          <v-btn @click="handleAddComment" color="primary" variant="flat">
            Post Comment
          </v-btn>
        </form>

        <!-- Comments List -->
        <div v-if="post.comments && post.comments.length > 0">
          <Comment
            v-for="comment in post.comments"
            :key="comment.id"
            :comment="comment"
            :is-parent="true"
          />
        </div>

        <!-- No Comments Message -->
        <div v-else class="text-center text-medium-emphasis py-8">
          <v-icon size="large" class="mb-2">mdi-comment-outline</v-icon>
          <p>No comments yet. Be the first to comment!</p>
        </div>
      </v-card-text>
    </v-card>
  </DashboardLayout>
</template>

<style scoped>
.comments-container {
  border-left: 3px solid #e0e0e0;
  padding-left: 16px;
}

.replies-container {
  border-left: 2px solid #f0f0f0;
  padding-left: 16px;
}

.comment-item {
  padding: 12px 0;
  border-bottom: 1px solid #f5f5f5;
}

.comment-item:last-child {
  border-bottom: none;
}

.reply-item {
  padding: 8px 0;
}
</style>
