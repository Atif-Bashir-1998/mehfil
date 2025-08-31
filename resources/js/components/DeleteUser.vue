<script setup>
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const passwordInput = ref<HTMLInputElement | null>(null);
const dialog = ref(false);

const form = useForm({
    password: '',
});

const deleteUser = (e) => {
    e.preventDefault();

    form.delete(route('profile.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value?.focus(),
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    form.clearErrors();
    form.reset();
    dialog.value = false;
};
</script>

<template>
    <v-card class="pa-6" rounded="lg" elevation="4">
        <v-card-title class="text-h5 font-weight-bold">Delete Account</v-card-title>
        <v-card-subtitle class="text-body-1">Permanently delete your account and all of its resources.</v-card-subtitle>

        <v-card-text>
            <v-alert
                type="error"
                variant="tonal"
                class="mb-4"
                density="compact"
            >
                <div class="d-flex flex-column ga-1">
                    <span class="font-weight-medium">Warning: Please proceed with caution, this cannot be undone.</span>
                </div>
            </v-alert>

            <v-btn
                color="red-darken-2"
                variant="flat"
                @click="dialog = true"
            >
                Delete Account
            </v-btn>
        </v-card-text>
    </v-card>

    <v-dialog v-model="dialog" max-width="500">
        <v-card>
            <v-card-title class="text-h6 font-weight-bold">Are you sure you want to delete your account?</v-card-title>
            <v-card-text class="text-body-2">
                Once your account is deleted, all of its resources and data will be permanently erased.
                Please enter your password to confirm you would like to permanently delete your account.
            </v-card-text>

            <v-card-text>
                <v-form @submit.prevent="deleteUser">
                    <v-text-field
                        label="Password"
                        type="password"
                        variant="outlined"
                        placeholder="Enter your password"
                        v-model="form.password"
                        :error-messages="form.errors.password"
                        ref="passwordInput"
                        required
                    ></v-text-field>
                </v-form>
            </v-card-text>

            <v-card-actions class="d-flex justify-end ga-2">
                <v-btn variant="text" @click="closeModal">Cancel</v-btn>
                <v-btn
                    color="red-darken-2"
                    variant="flat"
                    @click="deleteUser"
                    :loading="form.processing"
                    :disabled="form.processing"
                >
                    Delete Account
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>
