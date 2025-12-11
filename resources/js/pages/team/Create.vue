<script setup lang="ts">
import AuthBase from '@/layouts/AuthLayout.vue';
import {Head, Form } from '@inertiajs/vue3';
import { store } from '@/routes/team/team';
import Card from '@/components/ui/card/Card.vue';
import { Input } from '@/components/ui/input';
import { Spinner } from '@/components/ui/spinner';
import { Button } from '@/components/ui/button';
import InputError from '@/components/InputError.vue';
import { Label } from '@/components/ui/label';

</script>

<template>
    <AuthBase title="Create your team" description="Start your journey today by creating a team">
        <Head title="Create"></Head>

        <Card class="p-4">
            <Form
                v-bind="store.form()"
                v-slot="{errors, processing}"
                class="flex flex-col gap-6"
            >
                <div class="grid gap-6">
                    <div class="grid gap-2">
                        <Label for="name">Name</Label>
                        <Input
                            id="name"
                            type="text"
                            autofocus
                            :tabindex="1"
                            name="name"
                            placeholder="My Acme Team."
                        />

                        <InputError :message="errors.name" />
                    </div>

                    <Button
                        type="submit"
                        class="mt-2 w-fit justify-self-end"
                        tabindex="2"
                        :disabled="processing"
                        data-test="create-team-button"
                        >
                        <Spinner v-if="processing" />
                        Create Team
                    </Button>
                </div>
            </Form>
        </Card>
    </AuthBase>
</template>

<style scoped></style>
