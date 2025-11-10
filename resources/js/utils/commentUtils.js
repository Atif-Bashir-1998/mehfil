import { router } from '@inertiajs/vue3';
import { useToast } from 'vue-toastification';
import axios from 'axios';

export const addComment = (postId, payload) => {
  const toast = useToast();

  let url = route('post.comment.store', { post: postId });

  router.post(
    url,
    payload,
    {
      preserveScroll: true,
      onSuccess: async() => {
        toast.success("Comment added successfully!");

        await axios.post(`/posts/${postId}/interaction`, { type: 'comment' });
      },
      onError: () => {
        toast.error("Could not add comment");
      }
    },
  );
};

export const updateComment = (commentId, payload) => {
  const toast = useToast();

  let url = route('comment.update', { comment: commentId });

  router.patch(
    url,
    payload,
    {
      preserveScroll: true,
      onSuccess: () => {
        toast.success("Comment updated successfully!");
      },
      onError: () => {
        toast.error("Could not update comment");
      }
    },
  );
};

export const removeComment = (commentId) => {
  const toast = useToast();

  let url = route('comment.destroy', { comment: commentId });

  router.delete(
    url,
    {
      preserveScroll: true,
      onSuccess: () => {
        toast.success("Comment removed successfully!");
      },
      onError: () => {
        toast.error("Could not remove comment");
      }
    },
  );
};
