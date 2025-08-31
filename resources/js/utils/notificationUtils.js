import { router } from '@inertiajs/vue3';
import { useToast } from 'vue-toastification';

export const markAsRead = (notificationId) => {
  let url = route('notification.mark-as-read', { notification: notificationId });

  router.post(
    url,
    {},
    {
      preserveScroll: true,
    },
  );
};

export const markAllAsRead = () => {
  const toast = useToast();

  let url = route('notification.mark-all-as-read');

  router.post(
    url,
    {},
    {
      preserveScroll: true,
      onSuccess: () => {
        toast.success("All comments are marked as read!");
      },
      onError: () => {
        toast.error("Could not mark all comments as read");
      }
    },
  );
};

export const removeNotification = (notificationId) => {
  const toast = useToast();

  let url = route('notification.destroy', { notification: notificationId });

  router.delete(
    url,
    {
      preserveScroll: true,
      onSuccess: () => {
        toast.success("Notification deleted successfully!");
      },
      onError: () => {
        toast.error("Could not remove notification");
      }
    },
  );
};
