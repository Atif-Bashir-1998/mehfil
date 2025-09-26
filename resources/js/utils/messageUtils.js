import axios from 'axios';

export const sendMessage = async (conversationId, messageText) => {
  let url = route('message.store', { conversation: conversationId });

  const payload = {
    body: messageText
  }

  return await axios.post(url, payload);

};

