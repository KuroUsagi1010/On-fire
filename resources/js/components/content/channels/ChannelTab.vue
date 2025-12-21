<script setup lang="ts">
import { TabsContent } from '@/components/ui/tabs';
import { Button } from '@/components/ui/button';
import { Mail, Slack, MessageSquare, Plus, Settings, Trash2, Power } from 'lucide-vue-next';
import {
    Table,
    TableHeader,
    TableBody,
    TableRow,
    TableHead,
    TableCell,
    TableCaption,
} from '@/components/ui/table';

type ChannelListItem = {
    id: string;
    type: 'email' | 'slack' | 'discord' | string;
    name: string;
    active: boolean;
    groups_count: number;
    created_at: string | null;
}

const props = defineProps<{ channels: ChannelListItem[] }>();

function typeIcon(type: string) {
    switch (type) {
        case 'email':
            return Mail;
        case 'slack':
            return Slack;
        case 'discord':
            return MessageSquare;
        default:
            return MessageSquare;
    }
}
</script>

<template>
    <TabsContent value="channels" class="mt-4">
        <div class="mb-4 flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold">Notification Channels</h2>
                <p class="text-sm text-muted-foreground">Configure where alerts are sent. All actions are placeholders for now.</p>
            </div>
            <div class="flex items-center gap-2">
                <Button size="sm" variant="default" disabled>
                    <Plus class="mr-2 h-4 w-4" /> Add Channel
                </Button>
            </div>
        </div>

        <div v-if="props.channels.length === 0" class="flex flex-col items-center justify-center rounded-lg border border-sidebar-border/70 p-10 text-center dark:border-sidebar-border">
            <div class="mb-2 rounded-lg bg-muted px-3 py-1 text-xs font-medium text-muted-foreground">No channels</div>
            <p class="max-w-md text-sm text-muted-foreground">You don't have any channels yet. Click "Add Channel" to create one. (Disabled for now)</p>
        </div>

        <div v-else class="overflow-hidden rounded-lg border border-sidebar-border/70 dark:border-sidebar-border">
            <Table>
                <TableHeader>
                    <TableRow class="bg-muted/40 text-xs font-medium text-muted-foreground">
                        <TableHead class="w-[45%]">Channel</TableHead>
                        <TableHead class="w-[15%]">Type</TableHead>
                        <TableHead class="w-[15%]">Groups</TableHead>
                        <TableHead class="w-[15%]">Created</TableHead>
                        <TableHead class="w-[10%] text-right">Actions</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="ch in props.channels" :key="ch.id" class="items-center">
                        <TableCell>
                            <div class="flex items-center gap-3">
                                <component :is="typeIcon(ch.type)" class="h-5 w-5 text-muted-foreground" />
                                <div class="flex items-center gap-2">
                                    <span class="font-medium">{{ ch.name || 'Untitled Channel' }}</span>
                                    <span
                                        class="rounded-full px-2 py-0.5 text-xs"
                                        :class="ch.active ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400' : 'bg-rose-500/10 text-rose-600 dark:text-rose-400'"
                                    >
                                        {{ ch.active ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                            </div>
                        </TableCell>
                        <TableCell class="capitalize text-sm text-muted-foreground">{{ ch.type }}</TableCell>
                        <TableCell class="text-sm">{{ ch.groups_count }}</TableCell>
                        <TableCell class="text-sm text-muted-foreground">{{ ch.created_at ? new Date(ch.created_at).toLocaleString() : '-' }}</TableCell>
                        <TableCell>
                            <div class="flex items-center justify-end gap-2">
                                <Button size="icon" variant="ghost" title="Configure" disabled>
                                    <Settings class="h-4 w-4" />
                                </Button>
                                <Button size="icon" variant="ghost" title="Enable/Disable" disabled>
                                    <Power class="h-4 w-4" />
                                </Button>
                                <Button size="icon" variant="ghost" title="Delete" disabled>
                                    <Trash2 class="h-4 w-4" />
                                </Button>
                            </div>
                        </TableCell>
                    </TableRow>
                </TableBody>
                <TableCaption class="text-left p-2">Listing of your team channels.</TableCaption>
            </Table>
        </div>
    </TabsContent>
</template>

<style scoped></style>
