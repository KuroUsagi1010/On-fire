<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';

import AppLayout from '@/layouts/AppLayout.vue'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import InputError from '@/components/InputError.vue'

import { index, store } from '@/actions/App/Http/Controllers/Site/SiteController';
import type { BreadcrumbItem, TeamListItem } from '@/types'
import { Spinner } from '@/components/ui/spinner';

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Sites', href: index().url },
  { title: 'Create', href: '#' },
]

const props = defineProps<{
  teams: TeamListItem[]
}>()

</script>

<template>
  <Head title="Create Site" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
      <div class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border p-4">
        <div class="mx-auto w-full max-w-3xl">
          <h1 class="text-lg font-medium mb-4">Create Site</h1>

          <Form class="space-y-6"
                v-bind="store.form()"
                v-slot="{errors, processing}"
            >
            <div class="grid gap-1">
              <label for="team_id" class="text-xs text-neutral-600 dark:text-neutral-300">Team</label>
              <select
                id="team_id"
                name="team_id"
                class="h-9 rounded-md border border-neutral-300 bg-white px-2 text-sm outline-none focus:ring-2 focus:ring-blue-500 dark:border-white/10 dark:bg-transparent"
              >
                <option value="" disabled>Select a team</option>
                <option v-for="t in props.teams" :key="t.id" :value="t.id">{{ t.name }}</option>
              </select>
              <InputError :message="errors.team_id" />
            </div>

            <div class="grid gap-1">
              <label for="display_name" class="text-xs text-neutral-600 dark:text-neutral-300">Display name</label>
              <Input id="display_name" name="display_name" placeholder="My Site" />
              <InputError :message="errors.display_name" />
            </div>

            <div class="flex items-center justify-end gap-2 pt-2">
              <a :href="index().url" class="text-sm text-neutral-600 hover:underline dark:text-neutral-300">Cancel</a>

              <Button type="submit"  class="inline-flex items-center" :class="{'disabled bg-red-900': processing}">
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
