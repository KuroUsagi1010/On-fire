<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { dashboard } from '@/routes'
import type { BreadcrumbItem, DashboardWidgetTuple, PagesTotalWidgetData, PagesOnFireWidgetData } from '@/types'
import { Head, router } from '@inertiajs/vue3'
import PagesTotalWidget from '@/components/dashboard/widgets/PagesTotalWidget.vue'
import PagesOnFireWidget from '@/components/dashboard/widgets/PagesOnFireWidget.vue'
import { RefreshCw } from 'lucide-vue-next'
import { ref } from 'vue'

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Dashboard',
    href: dashboard().url,
  },
]

const props = defineProps<{ widgets: DashboardWidgetTuple[] }>()

const refreshing = ref(false)

async function refreshWidgets() {
  if (refreshing.value) return
  refreshing.value = true
  try {
    await router.post('/dashboard/refresh', {}, {
      preserveScroll: true,
      preserveState: false,
      onFinish: () => {
        refreshing.value = false
      },
    })
  } catch {
    refreshing.value = false
  }
}

function resolveWidget([id, data]: DashboardWidgetTuple) {
  switch (id) {
    case 'pages-total-widget':
      return {
        is: PagesTotalWidget,
        data: data as PagesTotalWidgetData,
      }
    case 'pages-on-fire-widget':
      return {
        is: PagesOnFireWidget,
        data: data as PagesOnFireWidgetData,
      }
    default:
      return {
        is: null,
        data,
      }
  }
}
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
      <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
        <!-- Header actions -->
        <div class="flex items-center justify-end">
          <button
            type="button"
            class="inline-flex items-center gap-2 rounded-md border border-dashed px-3 py-1.5 text-sm font-medium transition hover:border-solid disabled:opacity-50"
            :disabled="refreshing"
            @click="refreshWidgets"
            title="Refresh dashboard widgets"
            aria-label="Refresh dashboard widgets"
          >
            <RefreshCw :class="['h-4 w-4', refreshing ? 'animate-spin' : '']" />
            <span class="hidden sm:inline">Refresh</span>
          </button>
        </div>
        <div class="grid auto-rows-min gap-2 md:grid-cols-6">
          <template v-for="(tuple, idx) in props.widgets" :key="idx">
            <component
              :is="resolveWidget(tuple).is || 'div'"
              v-bind="{ data: resolveWidget(tuple).data }"
            >
              <template v-if="!resolveWidget(tuple).is">
                <div class="p-4 text-sm text-muted-foreground">Unknown widget: {{ tuple[0] }}</div>
              </template>
            </component>
          </template>
        </div>
      </div>
    </AppLayout>
</template>
