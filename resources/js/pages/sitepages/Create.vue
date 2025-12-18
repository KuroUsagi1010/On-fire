<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';

import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import InputError from '@/components/InputError.vue';
import { Spinner } from '@/components/ui/spinner';
import { Label } from '@/components/ui/label';
import { Switch } from '@/components/ui/switch';

import { index as sitesIndex } from '@/actions/App/Http/Controllers/Site/SiteController';
import { store } from '@/actions/App/Http/Controllers/SitePageController';
import type { BreadcrumbItem } from '@/types';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { ref, watch } from 'vue';

const props = defineProps<{
    site: { id: number | string; display_name?: string | null };
}>();

console.log(props);

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Sites', href: sitesIndex().url },
    { title: props.site.display_name ?? `Site #${props.site.id}`, href: '#' },
    { title: 'Pages', href: '#' },
    { title: 'Create', href: '#' },
];

const availableStatus = {
    200: '200 Ok',
    201: '201 Created',
    204: '204 No Content',
    301: '301 Moved Permanently',
    302: '302 Found',
    400: '400 Bad',
    401: '401 Unauthorized',
    403: '403 Forbidden',
    404: '404 Not Found',
    500: '500',
};

const selectedStatuses = ref([]);
</script>

<template>
    <Head title="Create Page" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <div
                class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 p-4 md:min-h-min dark:border-sidebar-border"
            >
                <div class="mx-auto w-full max-w-3xl">
                    <h1 class="mb-4 text-lg font-medium">Create Page</h1>

                    <Form
                        class="space-y-6"
                        v-bind="store.form(props.site.id)"
                        v-slot="{ errors, processing }"
                    >
                        <div class="grid gap-1">
                            <Label
                                for="display_name"
                                class="text-xs text-neutral-600 dark:text-neutral-300"
                                >Display name</Label
                            >
                            <Input
                                id="display_name"
                                name="display_name"
                                placeholder="Homepage"
                            />
                            <InputError :message="errors.display_name" />
                        </div>

                        <div class="grid gap-1">
                            <Label
                                for="url"
                                class="text-xs text-neutral-600 dark:text-neutral-300"
                                >URL</Label
                            >
                            <Input
                                id="url"
                                name="url"
                                type="url"
                                placeholder="https://example.com"
                            />
                            <InputError :message="errors.url" />
                        </div>

                        <div class="grid gap-1">
                            <label
                                for="description"
                                class="text-xs text-neutral-600 dark:text-neutral-300"
                                >Description</label
                            >
                            <textarea
                                id="description"
                                name="description"
                                rows="3"
                                class="rounded-md border border-neutral-300 bg-white p-2 text-sm outline-none focus:ring-2 focus:ring-blue-500 dark:border-white/10 dark:bg-transparent"
                                placeholder="Optional description of this page"
                            />
                            <InputError :message="errors.description" />
                        </div>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div class="grid gap-1">
                                <label
                                    for="check_interval_seconds"
                                    class="text-xs text-neutral-600 dark:text-neutral-300"
                                    >Check interval (seconds)</label
                                >
                                <Input
                                    id="check_interval_seconds"
                                    name="check_interval_seconds"
                                    type="number"
                                    min="0"
                                    placeholder="60"
                                />
                                <InputError
                                    :message="errors.check_interval_seconds"
                                />
                            </div>

                            <div class="grid gap-1">
                                <label
                                    for="timeout_seconds"
                                    class="text-xs text-neutral-600 dark:text-neutral-300"
                                    >Timeout (seconds)</label
                                >
                                <Input
                                    id="timeout_seconds"
                                    name="timeout_seconds"
                                    type="number"
                                    min="0"
                                    placeholder="30"
                                />
                                <InputError :message="errors.timeout_seconds" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div class="flex items-center gap-2">
                                <Switch
                                    id="paused"
                                    name="paused"
                                    data-state="unchecked"
                                />
                                <Label for="paused">Paused</Label>
                            </div>
                            <div class="flex items-center gap-2">
                                <Switch
                                    id="verify_ssl"
                                    name="verify_ssl"
                                    data-state="unchecked"
                                />
                                <Label for="verify_ssl">Verify SSL</Label>
                            </div>
                        </div>
                        <InputError :message="errors.paused" />
                        <InputError :message="errors.verify_ssl" />

                        <div class="grid gap-1">
                            <label
                                for="expected_status"
                                class="text-xs text-neutral-600 dark:text-neutral-300"
                                >Expected HTTP status codes</label
                            >
                            <Select v-model="selectedStatuses" :multiple="true">
                                <SelectTrigger>
                                    <SelectValue
                                        placeholder="Select a status"
                                    />
                                </SelectTrigger>
                                <SelectContent>
                                    <template
                                        v-for="(
                                            statusLabel, statusCode
                                        ) in availableStatus"
                                        :key="statusCode"
                                    >
                                        <SelectItem :value="statusCode">
                                            {{ statusLabel }}
                                        </SelectItem>
                                    </template>
                                </SelectContent>
                            </Select>
                            <!-- Send one hidden input per selected status so backend receives an array -->
                            <template v-for="status in selectedStatuses" :key="`expected-${status}`">
                                <input
                                    type="hidden"
                                    name="expected_status[]"
                                    :value="status"
                                />
                            </template>
                            <InputError :message="errors.expected_status" />
                        </div>

                        <div class="flex items-center justify-end gap-2 pt-2">
                            <a
                                :href="sitesIndex().url"
                                class="text-sm text-neutral-600 hover:underline dark:text-neutral-300"
                                >Cancel</a
                            >
                            <Button
                                type="submit"
                                class="inline-flex items-center"
                                :class="{ 'disabled': processing }"
                            >
                                <Spinner v-if="processing" /> Save
                            </Button>
                        </div>
                    </Form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped></style>
