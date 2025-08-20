<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Label } from '@/components/ui/label';
import { Badge } from '@/components/ui/badge';
import { X } from 'lucide-vue-next';
import { ref } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Create a Post',
        href: route('post.create'),
    },
];

interface Post {
    id: string;
    title: string;
    content: string;
    tags: string[] | null;
    created_at: string;
    creator: {
        id: number;
        name: string;
        username: string;
        profile_photo_path: string | null;
    };
}

interface Props {
    post: Post;
}

const { post } = defineProps<Props>();

// Form setup
const form = useForm({
    title: post.title || '',
    content: post.content || '',
    tags: post.tags || [] as string[],
});

const newTag = ref('');
const isSubmitting = ref(false);

const addTag = () => {
    if (newTag.value.trim() && !form.tags.includes(newTag.value.trim())) {
        form.tags.push(newTag.value.trim());
        newTag.value = '';
    }
};

const removeTag = (index: number) => {
    form.tags.splice(index, 1);
};

// Form submission
const submitPost = () => {
    isSubmitting.value = true;
    if(post) {
        // update the post
        form.put(route('post.update', {post: post.id}), {
            onSuccess: () => {
                // Reset form after successful submission
                // form.reset();
            },
            onError: () => {
                isSubmitting.value = false;
            },
            onFinish: () => {
                isSubmitting.value = false;
            },
            only: ['post']
        });
    } else {
        // create new post
        form.post(route('post.store'), {
            onSuccess: () => {
                // Reset form after successful submission
                form.reset();
            },
            onError: () => {
                isSubmitting.value = false;
            },
            onFinish: () => {
                isSubmitting.value = false;
            }
        });
    }
};
</script>

<template>
    <Head title="Create a Post" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 overflow-x-auto">
            <Card class="w-full max-w-2xl mx-auto">
                <CardHeader>
                    <CardTitle>
                        {{ post ? 'Edit Post' : 'Create New Post' }}
                    </CardTitle>
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="submitPost" class="space-y-6">
                        <!-- Post Title -->
                        <div class="space-y-2">
                            <Label for="title">Post Title *</Label>
                            <Input
                                id="title"
                                v-model="form.title"
                                placeholder="Enter a title for your post"
                                :disabled="isSubmitting"
                                required
                            />
                            <p v-if="form.errors.title" class="text-sm text-red-500">{{ form.errors.title }}</p>
                        </div>

                        <!-- Tags Section -->
                        <div class="space-y-2">
                            <Label for="tags">Tags</Label>
                            <div class="flex gap-2">
                                <Input
                                    id="tags"
                                    v-model="newTag"
                                    placeholder="Add a tag and press Enter"
                                    :disabled="isSubmitting"
                                    @keyup.enter="addTag"
                                />
                                <Button type="button" @click="addTag" variant="outline" :disabled="isSubmitting">
                                    Add
                                </Button>
                            </div>

                            <!-- Display added tags -->
                            <div v-if="form.tags.length > 0" class="flex flex-wrap gap-2 mt-2">
                                <Badge
                                    v-for="(tag, index) in form.tags"
                                    :key="index"
                                    variant="secondary"
                                    class="gap-1"
                                >
                                    {{ tag }}
                                    <button
                                        type="button"
                                        @click="removeTag(index)"
                                        class="ml-1 hover:bg-secondary-foreground/10 rounded-full p-0.5"
                                        :disabled="isSubmitting"
                                    >
                                        <X class="h-3 w-3" />
                                    </button>
                                </Badge>
                            </div>
                            <p v-if="form.errors.tags" class="text-sm text-red-500">{{ form.errors.tags }}</p>
                        </div>

                        <!-- Post Content -->
                        <div class="space-y-2">
                            <Label for="content">Post Content *</Label>
                            <Textarea
                                id="content"
                                v-model="form.content"
                                placeholder="Write your detailed post here..."
                                :rows="8"
                                :disabled="isSubmitting"
                                required
                            />
                            <p v-if="form.errors.content" class="text-sm text-red-500">{{ form.errors.content }}</p>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex justify-end gap-3 pt-4">
                            <Button
                                type="button"
                                variant="outline"
                                @click="router.visit(route('dashboard'))"
                                :disabled="isSubmitting"
                            >
                                Cancel
                            </Button>
                            <Button
                                type="submit"
                                :disabled="isSubmitting || !form.title || !form.content"
                            >
                                <span v-if="isSubmitting">{{ post ? 'Updating...' : 'Creating...' }}</span>
                                <span v-else>
                                    {{ post ? 'Update' : 'Create' }}
                                </span>
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
