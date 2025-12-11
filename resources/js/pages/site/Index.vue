<script setup lang="ts">
import { ref } from 'vue';
import { Form, Head, router } from '@inertiajs/vue3';
import { Plus, SquareArrowOutUpRight, Wrench } from 'lucide-vue-next';

import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import InputError from '@/components/InputError.vue';
import AppLayout from '@/layouts/AppLayout.vue';

import { index, create } from '@/actions/App/Http/Controllers/Site/SiteController';
import ProfileController from '@/actions/App/Http/Controllers/Settings/ProfileController';
import type { BreadcrumbItem, Site, SitePage } from '@/types';
import EmptyPage from '@/components/content/sites/EmptyPage.vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Sites',
        href: index().url,
    },
];

const props = defineProps<{ sites: Site[], pages?: SitePage[] }>()

const expandedSites = ref<Set<string>>(new Set());

const isLoading = ref(false);
// Placeholder bar heights (px) for visit-statuses until VisitRecord data exists
function toggleExpanded(siteId: string) {
    if (expandedSites.value.has(siteId)) {
        expandedSites.value.delete(siteId);
    } else {
        expandedSites.value.add(siteId);
        const siteIdsArray = Array.from(expandedSites.value);
        router.reload({
            only: ['pages'],
            data: { 'site_ids': siteIdsArray},
            preserveState: true,
            preserveScroll: true,
            onStart: () => isLoading.value = true,
            onFinish: () => { isLoading.value = false },
        })
    }
}

function pagesFor(siteId: string) {
    return (props.pages ?? []).filter((item) => siteId === item.site_id)
}


</script>

<template>
    <Head title="Sites" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <div
                class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border p-4"
            >
                <div class="flex flex-col gap-2">
                    <div class="flex justify-between mb-2">
                        <div class="filters">

                            <Form
                                v-bind="ProfileController.update.form()"
                                class="space-y-6"
                                v-slot="{ errors }"
                            >
                                <div class="grid gap-2">
                                    <Input
                                        id="name"
                                        class="mt-1 block w-full h-8 rounded-sm"
                                        name="name"
                                        required
                                        autocomplete="name"
                                        placeholder="Search Sites"
                                    />
                                    <InputError class="mt-2" :message="errors.name" />
                                </div>
                            </Form>
                        </div>
                        <Button @click="router.get(create().url)" class="w-fit inline-flex items-center">
                            <Plus :size="2" />
                            Add New
                        </Button>
                    </div>

                    <template v-for="site in sites" :key="site.id">
                        <div class="group rounded-lg border border-neutral-200 dark:border-white/5 border-dashed border-neutral-300 bg-neutral-50 opacity-90 dark:border-white/10 dark:bg-white/1"
                             :class="{
                                    'border-solid bg-white dark:bg-white/5 shadow-xs': expandedSites.has(site.id),
                                }">
                            <div class="flex h-11 cursor-pointer items-center gap-3 rounded-lg pr-2.5 pl-4 hover:bg-white/50 dark:hover:bg-white/2"    @click="toggleExpanded(site.id)">
                               <div class="flex flex-1 items-center gap-3">

                                   <p class="text-sm font-thin inline-flex gap-2 items-center">{{site.display_name}} <SquareArrowOutUpRight size="16" /></p>
                               </div>
                                <p class="justify-self-end text-xs">{{site.page_count}} Pages</p>
                                <button type="button" class="flex h-6 w-6 cursor-pointer items-center justify-center rounded-md dark:border dark:border-white/8 group-hover:text-blue-500 group-hover:dark:text-emerald-500 text-neutral-500 dark:text-neutral-500 dark:bg-white/3" :class="{
                                        'text-blue-500 dark:text-emerald-500 dark:bg-white/5': expandedSites.has(site.id),
                                        'text-neutral-500 dark:text-neutral-500 dark:bg-white/3': !expandedSites.has(site.id),
                                    }">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="8" height="12" viewBox="0 0 8 12" fill="none" v-show="expandedSites.has(site.id)" style="display: none;">
                                        <g clip-path="url(#clip0_14550_6168)">
                                            <path d="M6.75 11.0001L4 8.25012L1.25 11.0001" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"></path>
                                            <path d="M6.75 1.50012L4 4.25012L1.25 1.50012" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"></path>
                                        </g>
                                        <defs>
                                            <clipPath id="clip0_14550_6168">
                                                <rect width="8" height="11" fill="white" style="fill:white;fill-opacity:1;" transform="translate(0 0.500122)"></rect>
                                            </clipPath>
                                        </defs>
                                    </svg>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12" fill="none" v-show="!expandedSites.has(site.id)">
                                        <g clip-path="url(#clip0_14550_6155)">
                                            <path d="M8.75 8.25012L6 11.0001L3.25 8.25012" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"></path>
                                            <path d="M8.75 3.75012L6 1.00012L3.25 3.75012" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"></path>
                                        </g>
                                        <defs>
                                            <clipPath id="clip0_14550_6155">
                                                <rect width="12" height="12" fill="white" style="fill:white;fill-opacity:1;"></rect>
                                            </clipPath>
                                        </defs>
                                    </svg>
                                </button>
                            </div>
                            <div class="expanded-state border-t-1 mx-4" v-show="expandedSites.has(site.id)">
                                <div class="flex p-1 sm:py-2 flex-col gap-2">
                                    <template v-if="pagesFor(site.id).length">
                                        <template v-for="page in pagesFor(site.id)" :key="page.id">
                                            <div class="text-sm text-neutral-700 dark:text-neutral-200 flex items-center border-b border-dashed pb-2 cursor-pointer hover:bg-gray-50/70 dark:hover:bg-white/20 rounded-md p-2 w-full gap-2">
                                                <div class="h-2 w-2 rounded-full bg-red-500" :class="{ '!bg-green-300': !page.is_down }"></div>
                                                {{ page.url }}
                                                <div class="ml-auto flex items-center gap-6">
                                                    <!-- Visit status bars: green/red based on has_met_expected_status; neutral when no data -->
                                                    <div class="items-center gap-1 h-10 hidden sm:inline-flex">
                                                        <template v-if="page.records && page.records.length">
                                                            <div
                                                                v-for="record in page.records"
                                                                :key="record.id"
                                                                class="w-1 h-6"
                                                                :class="record.has_met_expected_status ? 'bg-emerald-400 dark:bg-emerald-400/60' : 'bg-red-400 dark:bg-red-400/60'"
                                                            />
                                                        </template>
                                                        <template v-else>
                                                            <div
                                                                v-for="n in 30"
                                                                :key="n"
                                                                class="w-1 h-6 rounded-sm opacity-60 bg-neutral-300 dark:bg-white/20"
                                                            />
                                                        </template>
                                                    </div>
                                                    <div class="p-1 rounded-md border dark:border-white/10">
                                                        <Wrench  :size="16" />
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
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
