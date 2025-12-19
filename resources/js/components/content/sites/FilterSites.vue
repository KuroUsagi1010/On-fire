<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { X, ListFilterPlus } from 'lucide-vue-next';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { Form } from '@inertiajs/vue3';

import { index } from '@/actions/App/Http/Controllers/Site/SiteController';
import { ref } from 'vue';
import { Spinner } from '@/components/ui/spinner';

const site = ref('');

const formRef = ref();
const removeFilters = () => {
    site.value = '';
    formRef.value.reset();
    formRef.value.submit();
}
</script>

<template>
    <Form
        ref="formRef"
        :action="index().url"
        class="flex gap-2 space-y-6"
        v-slot="{ errors, processing }"
        :options="{
            preserveState: true,
        }"
    >
        <div class="grid gap-2">
            <Input
                v-model="site"
                id="site"
                class="block h-8 w-full rounded-sm"
                name="filters.site"
                required
                placeholder="Search Sites"
            />
            <InputError class="mt-2" :message="errors.site" />
        </div>
        <Button
            v-if="site"
            type="submit"
            variant="destructive"
            size="sm"
            @click="removeFilters"
            class="inline-flex gap-2 w-fit items-center"
        >
            <Spinner v-if="processing" class="size-4" />
            <X v-else :size="2" />
        </Button>
        <Button
            type="submit"
            variant="secondary"
            size="sm"
            class="inline-flex gap-2 w-fit items-center"
            :disabled="processing"
        >
            <Spinner v-if="processing" class="size-4" />
            <ListFilterPlus v-else :size="2" />
            Filter
        </Button>
    </Form>
</template>

<style scoped></style>
