<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import dayjs from 'dayjs';

interface Props {
    post: Post;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Posts',
        href: route('post.index'),
    },
    {
        title: 'View Post',
        href: route('post.show', props.post.id),
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

const deletePost = (postId: string) => {
    if (confirm('Are you sure you want to delete this post?')) {
        router.delete(route('post.destroy', postId), {
            onSuccess: () => {
                router.visit(route('post.index'));
            }
        });
    }
};
</script>

<template>
    <Head :title="post.title" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 overflow-x-auto">
            <Card class="w-full max-w-3xl mx-auto">
                <CardHeader>
                    <div class="flex justify-between items-start">
                        <div>
                            <CardTitle class="text-2xl">{{ post.title }}</CardTitle>
                            <p class="text-sm text-muted-foreground mt-2">
                                by <span class="font-medium">{{ post.creator.name }}</span> •
                                {{ dayjs(post.created_at).format('MMMM d, YYYY h:mm a') }}
                            </p>
                        </div>
                        <div class="flex gap-2" v-if="$page.props.auth.user?.id === post.creator.id">
                            <Button
                                variant="outline"
                                size="sm"
                                as-child
                            >
                                <Link :href="route('post.edit', post.id)">
                                    Edit
                                </Link>
                            </Button>
                            <Button
                                variant="destructive"
                                size="sm"
                                @click="deletePost(post.id)"
                            >
                                Delete
                            </Button>
                        </div>
                    </div>
                </CardHeader>

                <CardContent>
                    <div class="prose max-w-none mb-6">
                        <p class="whitespace-pre-line">{{ post.content }}</p>
                    </div>

                    <div v-if="post.tags && post.tags.length > 0" class="flex flex-wrap gap-2 mb-6">
                        <Badge
                            v-for="(tag, index) in post.tags"
                            :key="index"
                            variant="secondary"
                        >
                            {{ tag }}
                        </Badge>
                    </div>

                    <div class="flex justify-between items-center pt-4 border-t">
                        <Button variant="outline" as-child>
                            <Link :href="route('post.index')">
                                ← Back to Posts
                            </Link>
                        </Button>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
