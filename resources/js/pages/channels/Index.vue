<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { Tabs, TabsList, TabsTrigger } from '@/components/ui/tabs';

import AppLayout from '@/layouts/AppLayout.vue';

import { index } from '@/actions/App/Http/Controllers/Channels/NotificationsChannelController';
import type { BreadcrumbItem } from '@/types';
import ChannelTab from '@/components/content/channels/ChannelTab.vue';
import GroupTab from '@/components/content/channels/GroupTab.vue';

type ChannelListItem = {
    id: string;
    type: 'email' | 'slack' | 'discord' | string;
    name: string;
    active: boolean;
    groups_count: number;
    created_at: string | null;
};

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Channels', href: index().url },
];

defineProps<{ channels: ChannelListItem[] }>();
</script>

<template>
    <Head title="Channels" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <div
                class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 p-4 md:min-h-min dark:border-sidebar-border"
            >
                <div class="flex flex-col gap-2">
                    <Tabs default-value="channels">
                        <TabsList>
                            <TabsTrigger value="channels">
                                Channels
                            </TabsTrigger>
                            <TabsTrigger value="groups">
                                Groups
                            </TabsTrigger>
                        </TabsList>
                        <ChannelTab :channels="channels" />
                        <GroupTab/>
                    </Tabs>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped></style>
