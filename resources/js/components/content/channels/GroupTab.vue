<script setup lang="ts">
import { TabsContent } from '@/components/ui/tabs';
import { Button } from '@/components/ui/button';
import { Layers, Plus, Settings, Trash2 } from 'lucide-vue-next';
import {
    Table,
    TableHeader,
    TableBody,
    TableRow,
    TableHead,
    TableCell,
    TableCaption,
} from '@/components/ui/table';

type GroupListItem = {
    id: string;
    name: string;
    channels_count: number;
    created_at: string | null;
};

// For now, no backend: provide placeholder UI data
const groups: GroupListItem[] = [
    { id: 'g1', name: 'Tier 1 Support', channels_count: 2, created_at: new Date().toISOString() },
    { id: 'g2', name: 'LP Product Alerts', channels_count: 1, created_at: new Date(Date.now() - 86400000).toISOString() },
];
</script>

<template>
    <TabsContent value="groups" class="mt-4">
        <div class="mb-4 flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold">Notification Groups</h2>
                <p class="text-sm text-muted-foreground">Bundle channels into groups. All actions are placeholders for now.</p>
            </div>
            <div class="flex items-center gap-2">
                <Button size="sm" variant="default" disabled>
                    <Plus class="mr-2 h-4 w-4" /> Add Group
                </Button>
            </div>
        </div>

        <div v-if="groups.length === 0" class="flex flex-col items-center justify-center rounded-lg border border-sidebar-border/70 p-10 text-center dark:border-sidebar-border">
            <div class="mb-2 rounded-lg bg-muted px-3 py-1 text-xs font-medium text-muted-foreground">No groups</div>
            <p class="max-w-md text-sm text-muted-foreground">You don't have any groups yet. Click "Add Group" to create one. (Disabled for now)</p>
        </div>

        <div v-else class="overflow-hidden rounded-lg border border-sidebar-border/70 dark:border-sidebar-border">
            <Table>
                <TableHeader>
                    <TableRow class="bg-muted/40 text-xs font-medium text-muted-foreground">
                        <TableHead class="w-[50%]">Group</TableHead>
                        <TableHead class="w-[15%]">Channels</TableHead>
                        <TableHead class="w-[25%]">Created</TableHead>
                        <TableHead class="w-[10%] text-right">Actions</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="g in groups" :key="g.id" class="items-center">
                        <TableCell>
                            <div class="flex items-center gap-3">
                                <Layers class="h-5 w-5 text-muted-foreground" />
                                <span class="font-medium">{{ g.name }}</span>
                            </div>
                        </TableCell>
                        <TableCell class="text-sm">{{ g.channels_count }}</TableCell>
                        <TableCell class="text-sm text-muted-foreground">{{ g.created_at ? new Date(g.created_at).toLocaleString() : '-' }}</TableCell>
                        <TableCell>
                            <div class="flex items-center justify-end gap-2">
                                <Button size="icon" variant="ghost" title="Configure" disabled>
                                    <Settings class="h-4 w-4" />
                                </Button>
                                <Button size="icon" variant="ghost" title="Delete" disabled>
                                    <Trash2 class="h-4 w-4" />
                                </Button>
                            </div>
                        </TableCell>
                    </TableRow>
                </TableBody>
                <TableCaption class="text-left p-2">Listing of your notification groups.</TableCaption>
            </Table>
        </div>
    </TabsContent>
</template>

<style scoped></style>
