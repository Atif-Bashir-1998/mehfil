import { router } from '@inertiajs/vue3';
import { useToast } from 'vue-toastification';

export const addReaction = (postId, reactionType) => {
  const toast = useToast();

  let url = route('post.reactions.store', {post: postId});

  let payload = {
    type: reactionType
  }

  router.post(
    url,
    payload,
    {
      preserveScroll: true,
      onSuccess: () => {
        toast.success("Reaction added successfully!");
      },
      onError: () => {
        toast.error("Could not add reaction");
      }
    },
  );
};

export const removeReaction = (postId) => {
  const toast = useToast();

  let url = route('post.reactions.destroy', {post: postId});

  router.delete(
    url,
    {
      preserveScroll: true,
      onSuccess: () => {
        toast.success("Reaction removed successfully!");
      },
      onError: () => {
        toast.error("Could not remove reaction");
      }
    },
  );
};
