<script setup lang="ts">
import { computed } from 'vue'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import type { PagesOnFireWidgetData } from '@/types'
import { Flame, Link as LinkIcon, AlertTriangle } from 'lucide-vue-next'

const props = defineProps<{ data: PagesOnFireWidgetData }>()

const hasItems = computed(() => (props.data?.items?.length ?? 0) > 0)
const total = computed(() => props.data?.count ?? props.data?.items?.length ?? 0)

function statusColor(status?: number | null) {
  if (status == null) return 'text-muted-foreground'
  if (status >= 500) return 'text-red-600 dark:text-red-400'
  if (status >= 400) return 'text-amber-600 dark:text-amber-400'
  return 'text-foreground'
}

function expectedText(expected?: number[] | null) {
  if (!expected || expected.length === 0) return '—'
  return expected.join(', ')
}
</script>

<template>
  <Card
    class="relative w-full sm:w-[28rem] md:w-[32rem] aspect-auto border-dashed hover:border-solid transition rounded-xs bg-background/70 backdrop-blur-[2px] hover:bg-background/60"
  >
    <CardHeader class="space-y-1 pb-2">
      <div class="flex items-center justify-between">
        <CardTitle class="flex items-center gap-2 text-base font-semibold tracking-tight">
          <Flame class="h-4 w-4 text-red-600 dark:text-red-400" />
          Pages on fire
        </CardTitle>
        <div
          class="inline-flex items-center gap-1 rounded-full border px-2 py-0.5 text-xs font-medium border-red-300/50 text-red-700 dark:text-red-300 dark:border-red-400/30"
          v-if="hasItems"
        >
          <Flame class="h-3.5 w-3.5" />
          {{ total }} down
        </div>
        <div v-else class="text-xs text-muted-foreground">All clear</div>
      </div>
    </CardHeader>
    <CardContent class="pt-0">
      <div v-if="hasItems" class="max-h-64 overflow-auto pr-1">
        <ul class="divide-y">
          <li v-for="item in data.items" :key="item.id" class="flex items-center gap-3 py-2">
            <div class="shrink-0 rounded-md bg-destructive/10 p-1.5">
              <AlertTriangle class="h-4 w-4 text-destructive" />
            </div>
            <div class="min-w-0 flex-1">
              <div class="flex items-center gap-2">
                <LinkIcon class="h-3.5 w-3.5 text-muted-foreground" />
                <a :href="item.url" target="_blank" rel="noopener" class="truncate hover:underline">
                  {{ item.url }}
                </a>
              </div>
              <div class="mt-0.5 text-xs text-muted-foreground">
                Expected: <span class="tabular-nums">{{ expectedText(item.expected) }}</span>
              </div>
            </div>
            <div class="ml-auto text-sm font-medium tabular-nums" :class="statusColor(item.status)">
              {{ item.status ?? '—' }}
            </div>
          </li>
        </ul>
      </div>

      <div v-else class="flex h-40 flex-col items-center justify-center gap-2 text-center">
        <div class="rounded-full bg-emerald-500/10 p-3">
          <Flame class="h-5 w-5 text-emerald-500" />
        </div>
        <div class="text-sm font-medium">No pages are on fire</div>
        <div class="text-xs text-muted-foreground">Everything is healthy right now.</div>
      </div>
    </CardContent>
  </Card>

</template>

<style scoped></style>
