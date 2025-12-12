<script setup lang="ts">
import {
    Empty,
    EmptyContent,
    EmptyDescription,
    EmptyHeader,
    EmptyMedia,
    EmptyTitle,
} from '@/components/ui/empty';
import { create } from '@/actions/App/Http/Controllers/SitePageController';
import { Link as LinkIcon } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Link } from '@inertiajs/vue3';

defineProps<{
    site: string
}>()
</script>

<template>
    <Empty>
        <EmptyHeader>
            <EmptyMedia variant="icon">
                <LinkIcon />
            </EmptyMedia>
            <EmptyTitle>No Pages Yet</EmptyTitle>
            <EmptyDescription>
                This site doesn't have any pages yet.
                Get started by adding page URLs
            </EmptyDescription>
        </EmptyHeader>
        <EmptyContent>
            <div class="flex gap-2">
                <Link :href="create(site).url" method="get" as="button">
                   <Button variant="secondary">Add Pages</Button>
                </Link>
            </div>
        </EmptyContent>
    </Empty>
</template>

<style scoped></style>
