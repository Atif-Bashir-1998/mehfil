<script setup>
import AuthLayout from '@/layouts/AuthLayout.vue';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps({
  status: {
    required: false,
    type: String,
  },
  canResetPassword: {
    type: Boolean,
  },
});

const form = useForm({
  email: '',
  password: '',
  remember: false,
});

const showPassword = ref(false);

const submit = () => {
  form.post(route('login'), {
    onFinish: () => form.reset('password'),
  });
};
</script>

<!-- <script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthBase from '@/layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';

defineProps<{
    status?: string;
    canResetPassword: boolean;
}>();

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <AuthBase title="Log in to your account" description="Enter your email and password below to log in">
        <Head title="Log in" />

        <div v-if="status" class="mb-4 text-center text-sm font-medium text-green-600">
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="flex flex-col gap-6">
            <div class="grid gap-6">
                <div class="grid gap-2">
                    <Label for="email">Email address</Label>
                    <Input
                        id="email"
                        type="email"
                        required
                        autofocus
                        :tabindex="1"
                        autocomplete="email"
                        v-model="form.email"
                        placeholder="email@example.com"
                    />
                    <InputError :message="form.errors.email" />
                </div>

                <div class="grid gap-2">
                    <div class="flex items-center justify-between">
                        <Label for="password">Password</Label>
                        <TextLink v-if="canResetPassword" :href="route('password.request')" class="text-sm" :tabindex="5">
                            Forgot password?
                        </TextLink>
                    </div>
                    <Input
                        id="password"
                        type="password"
                        required
                        :tabindex="2"
                        autocomplete="current-password"
                        v-model="form.password"
                        placeholder="Password"
                    />
                    <InputError :message="form.errors.password" />
                </div>

                <div class="flex items-center justify-between">
                    <Label for="remember" class="flex items-center space-x-3">
                        <Checkbox id="remember" v-model="form.remember" :tabindex="3" />
                        <span>Remember me</span>
                    </Label>
                </div>

                <Button type="submit" class="mt-4 w-full" :tabindex="4" :disabled="form.processing">
                    <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                    Log in
                </Button>
            </div>

            <div class="text-center text-sm text-muted-foreground">
                Don't have an account?
                <TextLink :href="route('register')" :tabindex="5">Sign up</TextLink>
            </div>
        </form>
    </AuthBase>
</template> -->

<template>
  <AuthLayout title="Login">
    <v-card class="pa-4 pa-md-8" rounded="xl" elevation="4">
      <v-card-title class="text-h5 font-weight-bold mb-2 text-center"> Log in to your account </v-card-title>
      <v-card-subtitle class="text-medium-emphasis mb-4 text-center"> Enter your email and password below to log in </v-card-subtitle>

      <v-alert v-if="status" type="success" variant="tonal" class="mb-4" rounded="lg">
        {{ status }}
      </v-alert>

      <v-form @submit.prevent="submit" ref="formRef">
        <v-text-field
          label="Email"
          v-model="form.email"
          type="email"
          :error-messages="form.errors.email"
          :loading="form.processing"
          required
          autofocus
        ></v-text-field>

        <v-text-field
          label="Password"
          v-model="form.password"
          :type="showPassword ? 'text' : 'password'"
          :append-inner-icon="showPassword ? 'mdi-eye-off' : 'mdi-eye'"
          @click:append-inner="showPassword = !showPassword"
          :error-messages="form.errors.password"
          :loading="form.processing"
          required
        ></v-text-field>

        <v-checkbox v-model="form.remember" label="Remember me" color="primary" density="compact" hide-details></v-checkbox>

        <v-btn color="primary" size="large" block type="submit" :disabled="form.processing">
          <v-progress-circular v-if="form.processing" size="20" width="2" color="white" indeterminate class="mr-2"></v-progress-circular>
          Log in
        </v-btn>
      </v-form>

      <v-card-actions class="flex-column mt-4">
        <div>
          Don't have an account?
          <v-btn variant="text" :href="route('register')"> Sign up </v-btn>
        </div>
        <div>
          Forgot Password?
          <v-btn variant="text" :href="route('password.request')"> Reset your password </v-btn>
        </div>
      </v-card-actions>
    </v-card>
  </AuthLayout>
</template>
