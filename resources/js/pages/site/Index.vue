<script setup lang="ts">
import { ref } from 'vue';
import { Form, Head, Link, router } from '@inertiajs/vue3';
import { Plus, SquareArrowOutUpRight, Wrench } from 'lucide-vue-next';

import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import InputError from '@/components/InputError.vue';
import AppLayout from '@/layouts/AppLayout.vue';

import {
    index,
    create,
} from '@/actions/App/Http/Controllers/Site/SiteController';
import { create as createPage } from '@/actions/App/Http/Controllers/SitePageController';
import ProfileController from '@/actions/App/Http/Controllers/Settings/ProfileController';
import type { BreadcrumbItem, Site } from '@/types';
import EmptyPage from '@/components/content/sites/EmptyPage.vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Sites',
        href: index().url,
    },
];

defineProps<{ sites: Site[] }>();

const expandedSites = ref<Set<string>>(new Set());

function toggleExpanded(siteId: string) {
    if (expandedSites.value.has(siteId)) {
        expandedSites.value.delete(siteId);
    } else {
        expandedSites.value.add(siteId);
    }
}
</script>

<template>
    <Head title="Sites" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <div
                class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 p-4 md:min-h-min dark:border-sidebar-border"
            >
                <div class="flex flex-col gap-2">
                    <div class="mb-2 flex justify-between">
                        <div class="filters">
                            <Form
                                v-bind="ProfileController.update.form()"
                                class="space-y-6"
                                v-slot="{ errors }"
                            >
                                <div class="grid gap-2">
                                    <Input
                                        id="name"
                                        class="mt-1 block h-8 w-full rounded-sm"
                                        name="name"
                                        required
                                        autocomplete="name"
                                        placeholder="Search Sites"
                                    />
                                    <InputError
                                        class="mt-2"
                                        :message="errors.name"
                                    />
                                </div>
                            </Form>
                        </div>
                        <Button
                            @click="router.get(create().url)"
                            class="inline-flex w-fit items-center"
                        >
                            <Plus :size="2" />
                            Add New
                        </Button>
                    </div>

                    <template v-for="site in sites" :key="site.id">
                        <div
                            class="group rounded-lg border border-dashed border-neutral-200 border-neutral-300 bg-neutral-50 opacity-90 dark:border-white/5 dark:border-white/10 dark:bg-white/1"
                            :class="{
                                'border-solid bg-white shadow-xs dark:bg-white/5':
                                    expandedSites.has(site.id),
                            }"
                        >
                            <div
                                class="flex h-11 cursor-pointer items-center gap-3 rounded-lg pr-2.5 pl-4 hover:bg-white/50 dark:hover:bg-white/2"
                                @click="toggleExpanded(site.id)"
                            >
                                <div class="flex flex-1 items-center gap-3">
                                    <p
                                        class="inline-flex items-center gap-2 text-sm font-thin"
                                    >
                                        <SquareArrowOutUpRight size="16" />
                                        {{ site.display_name }}
                                    </p>
                                </div>
                                <p class="justify-self-end text-xs">
                                    {{ site.page_count }} Pages
                                </p>
                                <button
                                    type="button"
                                    class="flex h-6 w-6 cursor-pointer items-center justify-center rounded-md text-neutral-500 group-hover:text-blue-500 dark:border dark:border-white/8 dark:bg-white/3 dark:text-neutral-500 group-hover:dark:text-emerald-500"
                                    :class="{
                                        'text-blue-500 dark:bg-white/5 dark:text-emerald-500':
                                            expandedSites.has(site.id),
                                        'text-neutral-500 dark:bg-white/3 dark:text-neutral-500':
                                            !expandedSites.has(site.id),
                                    }"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        width="8"
                                        height="12"
                                        viewBox="0 0 8 12"
                                        fill="none"
                                        v-show="expandedSites.has(site.id)"
                                        style="display: none"
                                    >
                                        <g clip-path="url(#clip0_14550_6168)">
                                            <path
                                                d="M6.75 11.0001L4 8.25012L1.25 11.0001"
                                                stroke="currentColor"
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                            ></path>
                                            <path
                                                d="M6.75 1.50012L4 4.25012L1.25 1.50012"
                                                stroke="currentColor"
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                            ></path>
                                        </g>
                                        <defs>
                                            <clipPath id="clip0_14550_6168">
                                                <rect
                                                    width="8"
                                                    height="11"
                                                    fill="white"
                                                    style="
                                                        fill: white;
                                                        fill-opacity: 1;
                                                    "
                                                    transform="translate(0 0.500122)"
                                                ></rect>
                                            </clipPath>
                                        </defs>
                                    </svg>
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        width="12"
                                        height="12"
                                        viewBox="0 0 12 12"
                                        fill="none"
                                        v-show="!expandedSites.has(site.id)"
                                    >
                                        <g clip-path="url(#clip0_14550_6155)">
                                            <path
                                                d="M8.75 8.25012L6 11.0001L3.25 8.25012"
                                                stroke="currentColor"
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                            ></path>
                                            <path
                                                d="M8.75 3.75012L6 1.00012L3.25 3.75012"
                                                stroke="currentColor"
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                            ></path>
                                        </g>
                                        <defs>
                                            <clipPath id="clip0_14550_6155">
                                                <rect
                                                    width="12"
                                                    height="12"
                                                    fill="white"
                                                    style="
                                                        fill: white;
                                                        fill-opacity: 1;
                                                    "
                                                ></rect>
                                            </clipPath>
                                        </defs>
                                    </svg>
                                </button>
                            </div>
                            <div
                                class="expanded-state mx-4 border-t-1"
                                v-show="expandedSites.has(site.id)"
                            >
                                <div class="flex flex-col gap-2 p-1 sm:py-2">
                                    <template v-if="site?.pages?.length">
                                        <template
                                            v-for="page in site.pages"
                                            :key="page.id"
                                        >
                                            <div
                                                class="flex w-full cursor-pointer items-center gap-2 rounded-md border-b border-dashed p-2 pb-2 text-sm text-neutral-700 hover:bg-gray-50/70 dark:text-neutral-200 dark:hover:bg-white/20"
                                            >
                                                <div
                                                    class="h-2 w-2 rounded-full bg-red-500"
                                                    :class="{
                                                        '!bg-green-300':
                                                            !page.is_down,
                                                    }"
                                                ></div>
                                                {{ page.url }}
                                                <div
                                                    class="ml-auto flex items-center gap-6"
                                                >
                                                    <!-- Visit status bars: green/red based on has_met_expected_status; neutral when no data -->
                                                    <div
                                                        class="hidden h-10 items-center gap-1 sm:inline-flex"
                                                    >
                                                        <template
                                                            v-if="
                                                                page.latest_records &&
                                                                page
                                                                    .latest_records
                                                                    .length
                                                            "
                                                        >
                                                            <div
                                                                v-for="record in page.latest_records"
                                                                :key="record.id"
                                                                class="h-6 w-1"
                                                                :class="
                                                                    record.has_met_expected_status
                                                                        ? 'bg-emerald-400 dark:bg-emerald-400/60'
                                                                        : 'bg-red-400 dark:bg-red-400/60'
                                                                "
                                                            />
                                                        </template>
                                                        <template v-else>
                                                            <div
                                                                v-for="n in 30"
                                                                :key="n"
                                                                class="h-6 w-1 rounded-sm bg-neutral-300 opacity-60 dark:bg-white/20"
                                                            />
                                                        </template>
                                                    </div>
                                                    <div
                                                        class="rounded-md border p-1 dark:border-white/10"
                                                    >
                                                        <Wrench :size="16" />
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                        <Link
                                            :href="createPage(site.id).url"
                                            method="get"
                                            as="button"
                                            class="self-start"
                                        >
                                            <Button variant="default"
                                                >Add Pages</Button
                                            >
                                        </Link>
                                    </template>
                                    <template v-else>
                                        <EmptyPage :site="site.id" />
                                    </template>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped></style>
