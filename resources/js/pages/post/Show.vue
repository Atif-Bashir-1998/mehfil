<script setup>
import DashboardLayout from '@/layouts/DashboardLayout.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
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

// Full-screen image dialog
const dialog = ref(false);
const selectedImage = ref(null);
const form = useForm({
  content: '',
  image: null,
});

const showImage = (imageUrl) => {
  selectedImage.value = imageUrl;
  dialog.value = true;
};

const handleAddComment = () => {
  let payload = {
    parent_id: null,
    content: form.content,
    image: form.image
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
    <v-container class="my-8" style="max-width: 900px">
      <v-card class="mx-auto pa-6 mb-8" rounded="lg" elevation="4">
        <v-card-title class="pa-0 mb-4">
          <h1 class="text-h4 font-weight-bold text-blue-grey-darken-4">{{ post.title }}</h1>
          <div class="d-flex align-center text-subtitle-1 text-medium-emphasis mt-2">
            by
            <Link :href="route('user.show', { user: post.creator.id })" class="font-weight-bold text-primary ml-1 mr-2">
              {{ post.creator.name }}
            </Link>
            • {{ dayjs(post.created_at).format('MMMM D, YYYY') }}
          </div>
        </v-card-title>

        <v-card-text class="pa-0">
          <div v-if="post.images && post.images.length > 0" class="my-6">
            <v-carousel
              cycle
              show-arrows="hover"
              hide-delimiters
              height="400"
              class="rounded-lg elevation-2"
              color="white"
            >
              <v-carousel-item
                v-for="(image, index) in post.images"
                :key="index"
                :src="image.image_url"
                cover
                @click="showImage(image.image_url)"
                class="cursor-pointer"
              ></v-carousel-item>
            </v-carousel>
          </div>

          <div v-if="post.tags && post.tags.length > 0" class="d-flex ga-2 mt-4 mb-6 flex-wrap">
            <v-chip
              v-for="(tag, index) in post.tags"
              :key="index"
              size="small"
              label
              color="green-darken-1"
              variant="tonal"
            >
              {{ tag }}
            </v-chip>
          </div>

          <div class="text-body-1 text-medium-emphasis whitespace-pre-line">
            {{ post.content }}
          </div>
        </v-card-text>

        <v-divider class="my-6"></v-divider>

        <div class="d-flex justify-space-between align-center">
          <div class="d-flex align-center ga-4 text-medium-emphasis">
            <div class="d-flex align-center ga-1" v-if="post.reactions_count > 0">
              <v-icon size="small" color="red">mdi-heart</v-icon>
              <span class="text-caption">{{ post.reactions_count }} reactions</span>
            </div>
            <div class="d-flex align-center ga-1" v-if="post.comments_count > 0">
              <v-icon size="small">mdi-comment</v-icon>
              <span class="text-caption">{{ post.comments_count }} comments</span>
            </div>
          </div>

          <div class="d-flex ga-2" v-if="$page.props.auth.user?.id === post.creator.id">
            <v-btn variant="outlined" size="small" :href="route('post.edit', post.id)" prepend-icon="mdi-pencil"> Edit </v-btn>
            <v-btn color="red-darken-2" variant="flat" size="small" @click="deletePost(post.id)" prepend-icon="mdi-delete"> Delete </v-btn>
          </div>
        </div>

        <div class="d-flex ga-2 mt-4">
          <v-btn variant="text" size="small" prepend-icon="mdi-heart-outline">React</v-btn>
          <v-btn variant="text" size="small" prepend-icon="mdi-comment-outline">Comment</v-btn>
        </div>
      </v-card>

      <v-card class="mx-auto pa-6" rounded="lg" elevation="4">
        <v-card-title class="text-h5 font-weight-bold mb-4">
          Comments ({{ post.comments_count || 0 }})
        </v-card-title>

        <v-card-text class="pa-0">
          <v-form @submit.prevent="handleAddComment" class="mb-8">
            <v-textarea
              label="Add a comment..."
              v-model="form.content"
              rows="3"
              variant="outlined"
              color="primary"
              :error-messages="form.errors.content"
              required
            ></v-textarea>

            <v-file-input
              v-model="form.image"
              label="Upload Image (Optional)"
              prepend-icon="mdi-camera"
              accept="image/*"
              variant="outlined"
              density="compact"
              class="my-4"
              :error-messages="form.errors.image"
            ></v-file-input>

            <v-btn
              type="submit"
              :loading="form.processing"
              color="primary"
              variant="flat"
              :disabled="!form.content"
              prepend-icon="mdi-comment-plus-outline"
            >
              Post Comment
            </v-btn>
          </v-form>

          <div v-if="post.comments && post.comments.length > 0">
            <Comment v-for="comment in post.comments" :key="comment.id" :comment="comment" :is-parent="true" />
          </div>

          <div v-else class="text-center text-medium-emphasis py-8">
            <v-icon size="48" class="mb-2">mdi-comment-outline</v-icon>
            <p class="text-body-1">No comments yet. Be the first to comment!</p>
          </div>
        </v-card-text>
      </v-card>
    </v-container>

    <v-dialog v-model="dialog" fullscreen>
      <v-card>
        <v-img :src="selectedImage" contain></v-img>
        <v-btn
          icon="mdi-close"
          size="large"
          color="white"
          variant="flat"
          style="position: absolute; top: 16px; right: 16px; z-index: 1;"
          @click="dialog = false"
        ></v-btn>
      </v-card>
    </v-dialog>
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
