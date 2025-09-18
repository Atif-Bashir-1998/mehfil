<!-- <script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthBase from '@/layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <AuthBase title="Create an account" description="Enter your details below to create your account">
        <Head title="Register" />

        <form @submit.prevent="submit" class="flex flex-col gap-6">
            <div class="grid gap-6">
                <div class="grid gap-2">
                    <Label for="name">Name</Label>
                    <Input id="name" type="text" required autofocus :tabindex="1" autocomplete="name" v-model="form.name" placeholder="Full name" />
                    <InputError :message="form.errors.name" />
                </div>

                <div class="grid gap-2">
                    <Label for="email">Email address</Label>
                    <Input id="email" type="email" required :tabindex="2" autocomplete="email" v-model="form.email" placeholder="email@example.com" />
                    <InputError :message="form.errors.email" />
                </div>

                <div class="grid gap-2">
                    <Label for="password">Password</Label>
                    <Input
                        id="password"
                        type="password"
                        required
                        :tabindex="3"
                        autocomplete="new-password"
                        v-model="form.password"
                        placeholder="Password"
                    />
                    <InputError :message="form.errors.password" />
                </div>

                <div class="grid gap-2">
                    <Label for="password_confirmation">Confirm password</Label>
                    <Input
                        id="password_confirmation"
                        type="password"
                        required
                        :tabindex="4"
                        autocomplete="new-password"
                        v-model="form.password_confirmation"
                        placeholder="Confirm password"
                    />
                    <InputError :message="form.errors.password_confirmation" />
                </div>

                <Button type="submit" class="mt-2 w-full" tabindex="5" :disabled="form.processing">
                    <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                    Create account
                </Button>
            </div>

            <div class="text-center text-sm text-muted-foreground">
                Already have an account?
                <TextLink :href="route('login')" class="underline underline-offset-4" :tabindex="6">Log in</TextLink>
            </div>
        </form>
    </AuthBase>
</template> -->

<script setup>
import AuthLayout from '@/layouts/AuthLayout.vue';
import { useForm } from '@inertiajs/vue3';

const form = useForm({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
});

const submit = () => {
  form.post(route('register'), {
    onFinish: () => form.reset('password', 'password_confirmation'),
  });
};
</script>

<template>
  <AuthLayout>
    <v-card class="pa-4 pa-md-8 w-75" rounded="xl" elevation="4">
      <v-card-title class="text-h5 font-weight-bold mb-2 text-center">
        Create your account
      </v-card-title>
      <v-card-subtitle class="text-medium-emphasis mb-4 text-center">
        Sign up to get started
      </v-card-subtitle>

      <v-form @submit.prevent="submit">
        <div class="mb-4">
          <v-text-field
            id="name"
            v-model="form.name"
            label="Name"
            type="text"
            placeholder="Full name"
            :error-messages="form.errors?.name"
            variant="outlined"
            rounded="lg"
            required
            autofocus
          ></v-text-field>
        </div>

        <div class="mb-4">
          <v-text-field
            id="email"
            v-model="form.email"
            label="Email address"
            type="email"
            placeholder="email@example.com"
            :error-messages="form.errors?.email"
            variant="outlined"
            rounded="lg"
            required
          ></v-text-field>
        </div>

        <div class="mb-4">
          <v-text-field
            id="password"
            v-model="form.password"
            label="Password"
            type="password"
            placeholder="Password"
            :error-messages="form.errors?.password"
            variant="outlined"
            rounded="lg"
            required
          ></v-text-field>
        </div>

        <div class="mb-6">
          <v-text-field
            id="password_confirmation"
            v-model="form.password_confirmation"
            label="Confirm password"
            type="password"
            placeholder="Confirm password"
            :error-messages="form.errors?.password_confirmation"
            variant="outlined"
            rounded="lg"
            required
          ></v-text-field>
        </div>

        <v-btn
          color="primary"
          size="large"
          rounded="lg"
          block
          type="submit"
          class="mb-4"
          :disabled="form.processing"
        >
          <v-progress-circular
            v-if="form.processing"
            size="20"
            width="2"
            color="white"
            indeterminate
            class="mr-2"
          ></v-progress-circular>
          Create account
        </v-btn>

        <div class="text-center text-body-2 text-medium-emphasis">
          Already have an account?
          <v-btn
            variant="text"
            class="pa-0 text-blue-grey-darken-2 font-weight-bold"
            :href="route('login')"
            tabindex="-1"
          >
            Log in
          </v-btn>
        </div>
      </v-form>
    </v-card>
  </AuthLayout>
</template>
