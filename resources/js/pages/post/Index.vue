<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import dayjs from 'dayjs';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Posts',
        href: route('post.index'),
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
    posts: {
        data: Post[];
        links: any[];
        meta: any;
    };
}

const props = defineProps<Props>();

const deletePost = (postId: string) => {
    if (confirm('Are you sure you want to delete this post?')) {
        router.delete(route('post.destroy', postId), {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <Head title="Posts" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 overflow-x-auto">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-bold">Latest Posts</h1>
                <Button as-child>
                    <Link :href="route('post.create')">
                        Create New Post
                    </Link>
                </Button>
            </div>

            <div class="grid gap-4">
                <Card v-for="post in posts.data" :key="post.id">
                    <CardHeader>
                        <div class="flex justify-between items-start">
                            <div>
                                <CardTitle class="text-xl">
                                    <Link :href="route('post.show', post.id)" class="hover:underline">
                                        {{ post.title }}
                                    </Link>
                                </CardTitle>
                                <p class="text-sm text-muted-foreground mt-1">
                                    by {{ post.creator.name }} • {{ dayjs(post.created_at).format('hh:mm A, d MMM YYYY') }}
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
                        <div class="prose max-w-none mb-4">
                            <p>{{ post.content.substring(0, 200) }}{{ post.content.length > 200 ? '...' : '' }}</p>
                        </div>

                        <div v-if="post.tags && post.tags.length > 0" class="flex flex-wrap gap-2">
                            <Badge
                                v-for="(tag, index) in post.tags"
                                :key="index"
                                variant="secondary"
                            >
                                {{ tag }}
                            </Badge>
                        </div>

                        <div class="mt-4">
                            <Button variant="ghost" as-child>
                                <Link :href="route('post.show', post.id)">
                                    Read More
                                </Link>
                            </Button>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Pagination -->
            <!-- <div v-if="posts.meta.last_page > 1" class="flex justify-center mt-6">
                <div class="flex gap-1">
                    <Button
                        v-for="link in posts.links"
                        :key="link.label"
                        :variant="link.active ? 'default' : 'outline'"
                        :disabled="!link.url || link.active"
                        size="sm"
                        @click="link.url && router.visit(link.url)"
                    >
                        <span v-if="link.label === '&laquo; Previous'">Previous</span>
                        <span v-else-if="link.label === '&raquo; Next'">Next</span>
                        <span v-else v-html="link.label"></span>
                    </Button>
                </div>
            </div> -->
        </div>
    </AppLayout>
</template>
