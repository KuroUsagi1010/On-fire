<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import type { BreadcrumbItem, SitePage, VisitRecord } from '@/types'
import { Site } from '@/types'
import { index as sitesIndex } from '@/actions/App/Http/Controllers/Site/SiteController'
import { Button } from '@/components/ui/button'
import { ref } from 'vue'
import {
  Drawer,
  DrawerContent,
  DrawerHeader,
  DrawerTitle,
  DrawerDescription,
  DrawerFooter,
} from '@/components/ui/drawer'

const props = defineProps<{
  site: Site
  page: SitePage
  records: VisitRecord[]
  uptime_percentage: number | null
}>()

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Sites', href: sitesIndex().url },
  { title: props.site.display_name ?? `Site #${props.site.id}`, href: '#' },
  { title: props.page.display_name ?? 'Page', href: '#' },
]

function statusBadgeClasses(isUp: boolean) {
  return isUp
    ? 'inline-flex items-center rounded-md bg-emerald-100 px-2 py-0.5 text-xs font-medium text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400'
    : 'inline-flex items-center rounded-md bg-rose-100 px-2 py-0.5 text-xs font-medium text-rose-700 dark:bg-rose-500/10 dark:text-rose-400'
}

function httpStatusClasses(status: number) {
  if (status >= 200 && status < 300) return 'text-emerald-600'
  if (status >= 300 && status < 400) return 'text-amber-600'
  return 'text-rose-600'
}

function fmtDateTime(iso: string) {
  try { return new Date(iso).toLocaleString() } catch { return iso }
}

// Drawer state for viewing a record's payload
const drawerOpen = ref(false)
const selectedRecord = ref<VisitRecord | null>(null)

function openPayload(record: VisitRecord) {
  selectedRecord.value = record
  drawerOpen.value = true
}

function closeDrawer() {
  drawerOpen.value = false
  selectedRecord.value = null
}

function fmtHeaders(headers: Record<string, string> | null | undefined): string {
  if (!headers || Object.keys(headers).length === 0) return '—'
  return Object.entries(headers).map(([k, v]) => `${k}: ${v}`).join('\n')
}

// Visual helpers for red/green bars
function statusBarClass(r: VisitRecord) {
  return r.has_met_expected_status
    ? 'bg-emerald-500/80 hover:bg-emerald-500'
    : 'bg-rose-500/80 hover:bg-rose-500'
}
</script>

<template>
  <Head :title="props.page.display_name ?? props.page.url ?? 'Page'" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
      <!-- Header / Summary -->
      <div class="rounded-xl border border-sidebar-border/70 dark:border-sidebar-border p-4">
        <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
          <div class="space-y-1">
            <div class="flex items-center gap-2">
              <h1 class="text-lg font-medium">{{ props.page.display_name ?? 'Page' }}</h1>
              <span v-if="props.page.paused"
                    class="inline-flex items-center rounded-md bg-neutral-200 px-2 py-0.5 text-xs font-medium text-neutral-700 dark:bg-white/10 dark:text-neutral-300">Paused</span>
              <span v-else :class="statusBadgeClasses(!props.page.is_down)">
                {{ props.page.is_down ? 'Down' : 'Up' }}
              </span>
            </div>
            <a :href="props.page.url" target="_blank" rel="noopener"
               class="block text-sm text-blue-600 hover:underline dark:text-blue-400">{{ props.page.url }}</a>

            <div class="flex flex-wrap items-center gap-3 pt-1 text-sm text-neutral-700 dark:text-neutral-300">
              <div>
                Uptime (7d):
                <span v-if="props.uptime_percentage !== null" class="font-medium">
                  {{ props.uptime_percentage?.toFixed(2) }}%
                </span>
                <span v-else class="italic">No data</span>
              </div>
              <div v-if="props.records?.length">
                Last check: <span class="font-medium">{{ fmtDateTime(props.records[0].created_at) }}</span>
              </div>
            </div>
          </div>

          <div class="flex items-center gap-2">
            <!-- Edit button placeholder -->
            <Button variant="secondary" type="button" disabled title="Edit coming soon">
              Edit Page
            </Button>
          </div>
        </div>
      </div>

      <!-- Uptime Bars (red/green timeline) -->
      <div class="rounded-xl border border-sidebar-border/70 dark:border-sidebar-border p-4">
        <div class="mb-2 flex items-center justify-between">
          <div class="text-sm font-medium">Status timeline</div>
          <div class="flex items-center gap-3 text-xs text-neutral-600 dark:text-neutral-400">
            <div class="flex items-center gap-1">
              <span class="inline-block h-2 w-3 rounded-sm bg-emerald-500" /> Up
            </div>
            <div class="flex items-center gap-1">
              <span class="inline-block h-2 w-3 rounded-sm bg-rose-500" /> Down
            </div>
          </div>
        </div>
        <div class="flex items-stretch gap-0.5">
          <template v-if="props.records && props.records.length">
            <div
              v-for="r in props.records"
              :key="`bar-${r.id}`"
              :title="`${fmtDateTime(r.created_at)} • ${r.status} • ${r.duration_ms} ms`"
              :aria-label="`Visit at ${fmtDateTime(r.created_at)} was ${r.has_met_expected_status ? 'up' : 'down'}`"
              class="w-2"
              :class="['h-10 transition-colors', statusBarClass(r)]"
            />
          </template>
          <div v-else class="text-xs text-neutral-500">No records yet</div>
        </div>
      </div>

      <!-- Recent Checks Table -->
      <div class="rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
        <div class="border-b border-sidebar-border/70 p-3 text-sm font-medium dark:border-sidebar-border">
          Recent checks
        </div>
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-neutral-200 dark:divide-white/10">
            <thead>
              <tr class="text-left text-xs text-neutral-500 dark:text-neutral-400">
                <th class="px-4 py-2 font-normal">Time</th>
                <th class="px-4 py-2 font-normal">Status</th>
                <th class="px-4 py-2 font-normal">Duration</th>
                <th class="px-4 py-2 font-normal">Expected</th>
                <th class="px-4 py-2 font-normal">Error</th>
                <th class="px-4 py-2 font-normal text-right">Payload</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-neutral-100 dark:divide-white/5">
              <tr v-for="r in props.records" :key="r.id" class="text-sm align-top">
                <td class="px-4 py-2 whitespace-nowrap">{{ fmtDateTime(r.created_at) }}</td>
                <td class="px-4 py-2 whitespace-nowrap">
                  <span :class="httpStatusClasses(r.status)" class="font-medium">{{ r.status }}</span>
                </td>
                <td class="px-4 py-2 whitespace-nowrap">{{ r.duration_ms }} ms</td>
                <td class="px-4 py-2 whitespace-nowrap">
                  <span :class="r.has_met_expected_status ? 'text-emerald-600' : 'text-rose-600'">
                    {{ r.has_met_expected_status ? 'Met' : 'Missed' }}
                  </span>
                </td>
                <td class="px-4 py-2 whitespace-nowrap">
                  <span :class="r.has_error ? 'text-rose-600' : 'text-neutral-500'">
                    {{ r.has_error ? 'Yes' : 'No' }}
                  </span>
                </td>
                <td class="px-4 py-2 whitespace-nowrap text-right">
                  <Button size="sm" variant="outline" @click="openPayload(r)">
                    View payload
                  </Button>
                </td>
              </tr>
              <tr v-if="!props.records || props.records.length === 0">
                <td colspan="6" class="px-4 py-6 text-center text-sm text-neutral-500">No records yet</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Payload Drawer (single instance) -->
      <Drawer v-model:open="drawerOpen">
        <DrawerContent class="p-0">
          <DrawerHeader class="px-4 pb-0 pt-3">
            <DrawerTitle>Visit payload</DrawerTitle>
            <DrawerDescription>
              <span v-if="selectedRecord">
                {{ fmtDateTime(selectedRecord.created_at) }} · status {{ selectedRecord.status }}
              </span>
            </DrawerDescription>
          </DrawerHeader>

          <div class="px-4 py-3">
            <div class="mb-2 text-xs font-medium uppercase tracking-wide text-neutral-500 dark:text-neutral-400">Response Headers</div>
            <pre class="mb-3 max-h-48 overflow-auto rounded bg-neutral-50 p-2 text-xs dark:bg-white/5"><code>{{ fmtHeaders(selectedRecord?.payload?.response_headers) }}</code></pre>

            <div class="mb-2 text-xs font-medium uppercase tracking-wide text-neutral-500 dark:text-neutral-400">Response Body</div>
            <pre class="max-h-80 overflow-auto rounded bg-neutral-50 p-2 text-xs dark:bg-white/5"><code>{{ selectedRecord?.payload?.response_body ?? '—' }}</code></pre>
          </div>

          <DrawerFooter class="px-4 pb-4">
            <div class="flex items-center justify-end gap-2">
              <Button variant="secondary" @click="closeDrawer">Close</Button>
            </div>
          </DrawerFooter>
        </DrawerContent>
      </Drawer>
    </div>
  </AppLayout>
</template>

<style scoped></style>
