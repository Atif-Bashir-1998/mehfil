<script setup>
import DashboardLayout from '@/layouts/DashboardLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted, nextTick } from 'vue';
import dayjs from 'dayjs';
import relativeTime from 'dayjs/plugin/relativeTime';
import axios from 'axios';
import { sendMessage } from '@/utils/messageUtils';

dayjs.extend(relativeTime);

const props = defineProps({
  conversations: {
    type: Object,
    required: true,
  },
});

const user = usePage().props.auth.user;
const selectedConversation = ref(null);
const messages = ref([]);
const messagesPagination = ref({});
const isLoadingMessages = ref(false);
const newMessage = ref('');
const messagesContainer = ref(null);

const breadcrumbs = [
  { title: 'Home', href: route('dashboard') },
  { title: 'Messages', disabled: true },
];

const loadMessages = async (conversationId) => {
  if (selectedConversation.value && selectedConversation.value.id !== conversationId) {
    window.Echo.leave(`conversations.${selectedConversation.value.id}`);
  }

  isLoadingMessages.value = true;
  selectedConversation.value = props.conversations.find(conv => conv.id === conversationId);

  try {
    const { data } = await axios.get(route('message.show', { conversation: conversationId }));
    messages.value = data.messages.data.reverse();
    messagesPagination.value = data.messages;

    await nextTick();
    scrollToBottom();

    // Start listening for new messages on the selected conversation's channel
    window.Echo.private(`conversations.${conversationId}`)
      .listen('NewMessageSent', (e) => {
        if (e.message.user_id !== user.id) {
          messages.value.push(e.message);
          scrollToBottom();
        }
      });

  } catch (error) {
    console.error('Failed to load messages:', error);
  } finally {
    isLoadingMessages.value = false;
  }
};

const handleSendMessage = async () => {
  if (!selectedConversation.value || !newMessage.value.trim()) return;

  try {
    await sendMessage(selectedConversation.value.id, newMessage.value)

    await loadMessages(selectedConversation.value.id)
    newMessage.value = '';
    await nextTick();
    scrollToBottom();
  } catch (error) {
    console.error('Failed to send message:', error);
  }
};

const scrollToBottom = () => {
  if (messagesContainer.value) {
    messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight;
  }
};

onUnmounted(() => {
  if (selectedConversation.value) {
    window.Echo.leave(`conversations.${selectedConversation.value.id}`);
  }
});
</script>

<template>
  <Head title="Messages" />

  <DashboardLayout :breadcrumbs="breadcrumbs">
    <v-container class="h-100 pa-0" fluid>
      <v-row no-gutters class="h-100">
        <v-col cols="12" md="4" class="d-flex flex-column h-100 border-e" v-if="conversations.length">
          <v-card class="flex-grow-1" flat rounded="0">
            <v-card-title class="pa-4 border-b">
              <v-text-field
                label="Search or start new chat"
                prepend-inner-icon="mdi-magnify"
                variant="outlined"
                density="compact"
                hide-details
                single-line
                rounded="lg"
              ></v-text-field>
            </v-card-title>
            <v-list class="pa-0 h-100 overflow-y-auto">
              <v-list-item
                v-for="conversation in conversations"
                :key="conversation.id"
                :title="conversation.participants[0]?.name"
                class="border-b py-3"
                @click="loadMessages(conversation.id)"
                :active="selectedConversation?.id === conversation.id"
              >
                <template v-slot:prepend>
                  <v-avatar color="primary">
                    <v-img
                      :src="
                        conversation.participants[0]?.profile_image?.image_url ||
                        `https://eu.ui-avatars.com/api/?name=${conversation.participants[0]?.name}&size=64`
                      "
                    ></v-img>
                  </v-avatar>
                </template>
                <v-list-item-subtitle class="text-truncate">
                  {{ conversation.messages[0]?.body }}
                </v-list-item-subtitle>
                <template v-slot:append>
                  <span class="text-caption text-medium-emphasis">
                    {{ dayjs(conversation.messages[0]?.created_at).fromNow() }}
                  </span>
                </template>
              </v-list-item>
            </v-list>
          </v-card>
        </v-col>

        <v-col cols="12" :md="conversations.length ? 8 : 12" class="d-flex flex-column h-100">
          <v-card class="h-100" flat rounded="0">
            <v-card-title class="pa-4 border-b" v-if="selectedConversation">
              <div class="d-flex align-center">
                <v-avatar color="primary" class="mr-3">
                  <v-img :src="selectedConversation.participants[0]?.profile_image?.image_url || `https://eu.ui-avatars.com/api/?name=${selectedConversation.participants[0]?.name}&size=64`"></v-img>
                </v-avatar>
                <div class="d-flex flex-column">
                  <span class="text-subtitle-1 font-weight-bold">{{ selectedConversation.participants[0]?.name }}</span>
                </div>
              </div>
            </v-card-title>

            <v-card-text class="d-flex flex-column h-100 justify-end pa-4" v-if="selectedConversation && !isLoadingMessages">
              <div ref="messagesContainer" class="flex-grow-1 overflow-y-auto mb-4">
                <div v-for="message in messages" :key="message.id" class="my-2 d-flex" :class="{'justify-end': message.user_id === user.id}">
                  <v-card class="pa-3" rounded="lg" :color="message.user_id === user.id ? 'primary' : 'grey-lighten-2'" :class="{'text-white': message.user_id === user.id}">
                    <div class="d-flex align-end ga-2">
                      <span>{{ message.body }}</span>
                      <span class="text-caption" :class="{'text-white opacity-50': message.user_id === user.id, 'text-medium-emphasis': message.user_id !== user.id}">{{ dayjs(message.created_at).format('h:mm A') }}</span>
                    </div>
                  </v-card>
                </div>
              </div>
              <v-text-field
                label="Type your message... Press `ENTER` to send"
                v-model="newMessage"
                variant="outlined"
                density="compact"
                hide-details
                single-line
                rounded="lg"
                append-inner-icon="mdi-send"
                @keyup.enter="handleSendMessage"
                @click:append-inner="handleSendMessage"
              ></v-text-field>
            </v-card-text>

            <div v-else-if="isLoadingMessages" class="h-100 d-flex align-center justify-center text-center text-medium-emphasis">
              <v-progress-circular indeterminate color="primary"></v-progress-circular>
            </div>

            <v-card-text class="d-flex align-center text-medium-emphasis h-100 justify-center text-center" v-else>
              <div v-if="conversations.length">
                <p>Select a conversation to start chatting.</p>
              </div>
              <div v-else>
                <v-icon size="64" color="grey-lighten-2">mdi-chat-plus-outline</v-icon>
                <p class="text-h6 font-weight-bold mt-4">No Conversations Yet</p>
                <p class="text-subtitle-1 mt-2">Start a new conversation to begin.</p>
                <v-btn class="mt-4" color="primary" variant="flat" rounded="lg">Start a New Chat</v-btn>
              </div>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>
    </v-container>
  </DashboardLayout>
</template>

<style scoped>
.h-100 {
  height: calc(100vh - 64px); /* Adjust based on your app-bar height */
}

.messages-container {
  flex-grow: 1;
  overflow-y: auto;
  margin-bottom: 16px;
}
</style>
