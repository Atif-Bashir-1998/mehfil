import { router } from '@inertiajs/vue3';
import { useToast } from 'vue-toastification';
import axios from 'axios';

export const sendMessage = async (conversationId, messageText) => {
  const toast = useToast();

  let url = route('message.store', { conversation: conversationId });

  const payload = {
    body: messageText
  }

  return await axios.post(url, payload);

};

