<template>
  <div class="comment" :class="{ 'is-reply': !isParent }">
    <!-- Comment Header -->
    <div class="comment-header">
      <v-avatar size="24" class="mr-2">
        <v-img :src="comment.user.avatar_url || defaultAvatar" />
      </v-avatar>
      <span class="comment-author font-weight-medium">{{ comment.user.name }}</span>
      <span class="comment-time text-caption text-medium-emphasis ml-2">
        {{ dayjs(comment.created_at).fromNow() }}
      </span>
    </div>

    <!-- Comment Content -->
    <div class="comment-content">
      <p class="comment-text text-body-2 mb-2">{{ comment.content }}</p>

      <!-- Comment Actions -->
      <div class="comment-actions">
        <v-btn
          size="x-small"
          variant="text"
          prepend-icon="mdi-reply"
          class="text-caption"
          @click="toggleReply"
        >
          Reply
        </v-btn>

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

      <!-- Reply Form -->
      <div v-if="showReplyForm" class="reply-form mt-3">
        <v-textarea
          v-model="replyContent"
          label="Write a reply..."
          variant="outlined"
          rows="2"
          hide-details
          auto-grow
        ></v-textarea>
        <div class="d-flex ga-2 mt-2">
          <v-btn
            size="small"
            color="primary"
            variant="flat"
            @click="handleReplyAdded"
            :disabled="!replyContent.trim()"
          >
            Post Reply
          </v-btn>
          <v-btn
            size="small"
            variant="text"
            @click="cancelReply"
          >
            Cancel
          </v-btn>
        </div>
      </div>
    </div>

    <!-- Nested Replies -->
    <div v-if="comment.replies && comment.replies.length > 0" class="replies">
      <Comment
        v-for="reply in comment.replies"
        :key="reply.id"
        :comment="reply"
        :is-parent="false"
        @reply-added="handleReplyAdded"
      />
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import dayjs from 'dayjs';
import { addComment } from '@/utils/commentUtils';

const {comment} = defineProps({
  comment: {
    type: Object,
    required: true
  },
  isParent: {
    type: Boolean,
    default: true
  }
});

const emit = defineEmits(['reply-added']);

const showReplyForm = ref(false);
const replyContent = ref('');
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
  let payload = {
    parent_id: comment.id,
    content: replyContent.value
  }
  await addComment(comment.post_id, payload)

  showReplyForm.value = false
  replyContent.value = ''
};
</script>

<style scoped>
.comment {
  padding: 12px 0;
  border-bottom: 1px solid #f0f0f0;
}

.comment:last-child {
  border-bottom: none;
}

.is-reply {
  margin-left: 32px;
  padding-left: 12px;
  border-left: 2px solid #e0e0e0;
}

.comment-header {
  display: flex;
  align-items: center;
  margin-bottom: 8px;
}

.comment-author {
  font-size: 0.875rem;
}

.comment-time {
  font-size: 0.75rem;
}

.comment-text {
  line-height: 1.4;
  margin: 0;
}

.comment-actions {
  display: flex;
  gap: 8px;
  margin-top: 4px;
}

.replies {
  margin-top: 16px;
}

.reply-form {
  background-color: #fafafa;
  padding: 12px;
  border-radius: 8px;
  border: 1px solid #e0e0e0;
}
</style>
