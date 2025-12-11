<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { usePage } from '@inertiajs/vue3'
import type { PageProps } from '@inertiajs/core'
import { computed } from 'vue';

const page = usePage<PageProps>()

const auth = page.props.auth;

// First team in the list (or null if none)
const currentTeam = auth.teams[0]

</script>

<template>
    <div
        class="flex aspect-square size-8 items-center justify-center rounded-md bg-sidebar-primary text-sidebar-primary-foreground"
    >
        <AppLogoIcon class="size-5 fill-current text-white dark:text-black" />
    </div>
    <div class="ml-1 grid flex-1 text-left text-sm">
        <span class="mb-0.5 truncate leading-tight font-semibold"
            >{{currentTeam.name}}</span
        >
    </div>
</template>
