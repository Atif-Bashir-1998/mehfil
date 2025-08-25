import { router } from '@inertiajs/vue3';

export const handleLogout = () => {
  let url = route('logout');

  console.log({url})

  router.post(
    url,
    {},
    {
      onSuccess: () => {
        router.flushAll();
      },
    },
  );
};
