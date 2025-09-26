<template>
  <v-btn variant="text" size="small" color="error" @click="openFlagDialog">
    <v-icon left>mdi-flag</v-icon>
    Report
  </v-btn>

  <!-- Flag Dialog -->
  <v-dialog v-model="flagDialog" max-width="500px">
    <v-card>
      <v-card-title class="headline">
        Report {{ itemType }}
      </v-card-title>
      <v-card-text>
        <v-select
          v-model="flagReason"
          :items="flagReasons"
          label="Reason for reporting"
          required
        ></v-select>
        <v-textarea
          v-model="flagDescription"
          label="Additional details (optional)"
          rows="3"
        ></v-textarea>
      </v-card-text>
      <v-card-actions>
        <v-spacer></v-spacer>
        <v-btn color="grey" variant="text" @click="closeFlagDialog">Cancel</v-btn>
        <v-btn color="red" variant="flat" @click="submitFlag" :disabled="!flagReason" :loading="loading">
          Report
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script setup>
import { ref, computed } from 'vue';
import axios from 'axios';

const props = defineProps({
  flaggableType: {
    type: String,
    required: true,
    validator: (value) => ['post', 'comment', 'user', 'image'].includes(value)
  },
  flaggableId: {
    type: String,
    required: true
  }
});

const emit = defineEmits(['reported']);

const flagDialog = ref(false);
const flagReason = ref('');
const flagDescription = ref('');
const loading = ref(false);

const flagReasons = ref([
  'Spam',
  'Harassment',
  'Hate speech',
  'Inappropriate content',
  'False information',
  'Other'
]);

const itemType = computed(() => {
  const types = {
    post: 'Post',
    comment: 'Comment',
    user: 'User',
    image: 'Image'
  };
  return types[props.flaggableType] || 'Item';
});

const openFlagDialog = () => {
  flagDialog.value = true;
  flagReason.value = '';
  flagDescription.value = '';
};

const closeFlagDialog = () => {
  flagDialog.value = false;
};

const submitFlag = async () => {
  loading.value = true;

  try {
    const response = await axios.post(route('flag.store'), {
      flaggable_type: `App\\Models\\${itemType.value}`,
      flaggable_id: props.flaggableId,
      reason: flagReason.value,
      description: flagDescription.value || flagReason.value,
    });

    if (response.data.success) {
      // Show success message
      alert(`${itemType.value} reported successfully. Thank you for helping keep our community safe.`);
      emit('reported');
      closeFlagDialog();
    }
  } catch (error) {
    console.error('Error reporting item:', error);

    if (error.response?.data?.message) {
      alert(error.response.data.message);
    } else {
      alert('There was an error reporting this item. Please try again.');
    }
  } finally {
    loading.value = false;
  }
};
</script>
