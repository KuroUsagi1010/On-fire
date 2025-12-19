<script setup lang="ts">
import { computed } from 'vue'
import { Card, CardContent } from '@/components/ui/card'
import type { PagesTotalWidgetData } from '@/types'
import { Flame } from 'lucide-vue-next'

const props = defineProps<{ data: PagesTotalWidgetData }>()

const percentUp = computed(() => {
  if (!props.data.total) return 0
  return Math.round((props.data.up / props.data.total) * 100)
})

// SVG circular progress geometry (modern, thick ring with gradient)
const r = 46
const cx = 60
const cy = 60
const stroke = 9
const circumference = computed(() => 2 * Math.PI * r)
const dashOffset = computed(() => ((100 - percentUp.value) / 100) * circumference.value)

const percentColor = computed(() => {
  const p = percentUp.value
  if (p >= 99) return 'text-emerald-600 dark:text-emerald-400'
  if (p >= 90) return 'text-amber-600 dark:text-amber-400'
  return 'text-red-600 dark:text-red-400'
})

// Show a small fire icon when uptime is not perfect
const showFire = computed(() => percentUp.value < 100)
</script>

<template>
  <Card
    class="relative aspect-square w-52 border-dashed sm:w-60 md:w-64 mx-auto overflow-hidden rounded-xs border bg-gradient-to-br from-background to-muted/40 p-0 transition
           hover:bg-gradient-to-br hover:from-background hover:to-muted/60 hover:border-foreground/20
           dark:from-slate-950 dark:to-slate-900/40"
  >
    <CardContent class="flex h-full flex-col items-center justify-between p-4 text-center">
      <!-- Header -->
      <div class="flex w-full items-start justify-between">
        <div class="text-xs uppercase tracking-wider text-muted-foreground">Pages</div>
        <!-- Percent chip -->
        <div
          class="rounded-full px-2 py-0.5 text-xs font-medium bg-muted/60 backdrop-blur-sm"
          :class="percentColor"
          :aria-label="`Uptime ${percentUp}%`"
        >
          <span class="inline-flex items-center gap-1">
            <Flame v-if="showFire" class="h-3.5 w-3.5" aria-hidden="true" />
            {{ percentUp }}%
          </span>
        </div>
      </div>

      <!-- Center value with halo ring -->
      <div class="relative flex flex-col items-center justify-center">
        <svg viewBox="0 0 120 120" class="h-32 w-32 md:h-36 md:w-36">
          <defs>
            <linearGradient id="grad" x1="0%" y1="0%" x2="100%" y2="0%">
              <stop offset="0%" stop-color="#34d399" />
              <stop offset="100%" stop-color="#38bdf8" />
            </linearGradient>
            <filter id="glow" x="-50%" y="-50%" width="200%" height="200%">
              <feGaussianBlur stdDeviation="2.5" result="coloredBlur" />
              <feMerge>
                <feMergeNode in="coloredBlur" />
                <feMergeNode in="SourceGraphic" />
              </feMerge>
            </filter>
          </defs>
          <!-- Track -->
          <circle :r="r" :cx="cx" :cy="cy" :stroke-width="stroke" class="fill-none stroke-muted/30" />
          <!-- Progress with gradient + subtle glow -->
          <circle
            :r="r"
            :cx="cx"
            :cy="cy"
            :stroke-width="stroke"
            :stroke-dasharray="circumference"
            :stroke-dashoffset="dashOffset"
            stroke="url(#grad)"
            class="fill-none transition-[stroke-dashoffset] duration-700 ease-out"
            style="transform: rotate(-90deg); transform-origin: 50% 50%; filter: url(#glow);"
            stroke-linecap="round"
          />
        </svg>
        <!-- Number in the middle -->
        <div class="absolute text-2xl font-semibold leading-none tracking-tight md:text-3xl">
          {{ data.up }}/{{ data.total }}
        </div>
      </div>

      <!-- Footer helper text -->
      <div class="w-full">
        <div class="text-left text-xs text-muted-foreground">Monitored pages currently up</div>
      </div>
    </CardContent>
  </Card>
</template>

<style scoped></style>
