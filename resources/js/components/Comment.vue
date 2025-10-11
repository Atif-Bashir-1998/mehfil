<template>
  <div>
    <div class="comment-item" :class="{ 'is-reply': !isParent }">
      <div class="d-flex align-start mb-2">
        <v-avatar size="36" class="mt-1 mr-3">
          <v-img :src="comment.user.avatar_url || defaultAvatar" />
        </v-avatar>

        <div class="flex-grow-1">
          <div class="d-flex align-center">
            <span class="font-weight-bold text-body-1">{{ comment.user.name }}</span>
            <span class="text-caption text-medium-emphasis ml-2">
              {{ dayjs(comment.created_at).fromNow() }}
            </span>
          </div>

          <p class="text-body-2 mt-1 mb-2 whitespace-pre-line">{{ comment.content }}</p>

          <v-card v-if="comment.image" flat class="my-3 overflow-hidden rounded-lg">
            <v-img :src="comment.image.image_url" max-height="250" cover @click="showImage(comment.image.image_url)" class="cursor-pointer"></v-img>
          </v-card>

          <div class="d-flex ga-2">
            <v-btn size="x-small" variant="text" color="primary" prepend-icon="mdi-reply" class="text-caption" @click="toggleReply"> Reply </v-btn>

            <ReportButton v-if="!comment.is_flagged_by_current_user" :flaggableId="comment.id" flaggableType="comment" />

            <v-btn
              v-if="$page.props.auth.user?.id === comment.user_id"
              size="x-small"
              variant="text"
              prepend-icon="mdi-delete"
              class="text-caption text-error"
              @click="deleteComment"
            >
              Delete
            </v-btn>
          </div>
        </div>
      </div>

      <div class="comment-children" :class="{ 'ml-8': isParent }">
        <div v-if="showReplyForm" class="reply-form mt-4">
          <v-textarea
            v-model="replyContent"
            label="Write a reply..."
            variant="outlined"
            rows="2"
            auto-grow
            hide-details
            color="primary"
            class="mb-2"
            :error-messages="usePage().props.errors?.content"
          ></v-textarea>
          <div class="d-flex ga-2">
            <v-btn size="small" color="primary" variant="flat" @click="handleReplyAdded" :disabled="!replyContent.trim()"> Post Reply </v-btn>
            <v-btn size="small" variant="text" color="grey-darken-1" @click="cancelReply"> Cancel </v-btn>
          </div>
        </div>

        <div v-if="comment.replies && comment.replies.length > 0" class="replies">
          <Comment v-for="reply in comment.replies" :key="reply.id" :comment="reply" :is-parent="false" class="mt-4" />
        </div>
      </div>
    </div>

    <v-dialog v-model="dialog" max-width="800">
      <v-card>
        <v-img :src="selectedImage" contain></v-img>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="primary" variant="text" @click="dialog = false">Close</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>

<script setup>
import ReportButton from './ReportButton.vue'
import { addComment } from '@/utils/commentUtils';
import { router, usePage } from '@inertiajs/vue3';
import dayjs from 'dayjs';
import relativeTime from 'dayjs/plugin/relativeTime';
import { ref } from 'vue';

dayjs.extend(relativeTime);

const { comment } = defineProps({
  comment: {
    type: Object,
    required: true,
  },
  isParent: {
    type: Boolean,
    default: true,
  },
});

const showReplyForm = ref(false);
const replyContent = ref('');
const dialog = ref(false);
const selectedImage = ref(null);
const defaultAvatar = 'https://eu.ui-avatars.com/api/?name=User&size=64';

const toggleReply = () => {
  showReplyForm.value = !showReplyForm.value;
  if (!showReplyForm.value) {
    replyContent.value = '';
  }
};

const cancelReply = () => {
  showReplyForm.value = false;
  replyContent.value = '';
};

const deleteComment = () => {
  if (confirm('Are you sure you want to delete this comment?')) {
    router.delete(route('comment.destroy', comment.id), {
      preserveScroll: true,
    });
  }
};

const handleReplyAdded = async () => {
  const payload = {
    parent_id: comment.id,
    content: replyContent.value,
  };
  await addComment(comment.post_id, payload);

  showReplyForm.value = false;
  replyContent.value = '';
};

const showImage = (imageUrl) => {
  selectedImage.value = imageUrl;
  dialog.value = true;
};
</script>

<style scoped>
.comment-item {
  padding: 16px 0;
  border-bottom: 1px solid #e0e0e0;
}

.comment-item:last-child {
  border-bottom: none;
}

.is-reply {
  padding: 16px 0;
}

.comment-children {
  border-left: 2px solid #e0e0e0;
  padding-left: 16px;
}

.reply-form {
  background-color: #fafafa;
  padding: 12px;
  border-radius: 8px;
  border: 1px solid #e0e0e0;
}
</style>
