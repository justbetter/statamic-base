<template>
    <section>
        <h2 class="text-lg font-semibold mb-3" v-text="title" />

        <Card v-if="packages.length === 0">
            <p class="text-sm text-gray-600">No packages found in this section.</p>
        </Card>

        <Card v-else class="overflow-x-auto">
            <Table>
                <TableColumns>
                    <TableColumn>Package</TableColumn>
                    <TableColumn>Addon</TableColumn>
                    <TableColumn>Type</TableColumn>
                    <TableColumn>Installed</TableColumn>
                    <TableColumn>Latest</TableColumn>
                    <TableColumn>Update</TableColumn>
                </TableColumns>

                <TableRows>
                    <TableRow v-for="pkg in packages" :key="pkg.name">
                        <TableCell>
                            <div class="font-medium" v-text="pkg.name" />
                            <p
                                v-if="pkg.description"
                                class="text-xs text-gray-500 mt-0.5"
                                v-text="pkg.description"
                            />
                        </TableCell>
                        <TableCell v-text="pkg.addonName ?? '—'" />
                        <TableCell v-text="formatType(pkg.type)" />
                        <TableCell v-text="pkg.installedVersion" />
                        <TableCell v-text="pkg.latestVersion ?? '—'" />
                        <TableCell>
                            <Badge :variant="badgeVariant(pkg.updateStatus)" :text="badgeLabel(pkg.updateStatus)" />
                        </TableCell>
                    </TableRow>
                </TableRows>
            </Table>
        </Card>
    </section>
</template>

<script setup>
import { Badge, Card, Table, TableCell, TableColumn, TableColumns, TableRow, TableRows } from '@statamic/cms/ui';

defineProps({
    title: { type: String, required: true },
    packages: { type: Array, required: true },
});

const formatType = (type) => {
    if (type === 'statamic-addon') {
        return 'Statamic addon';
    }

    return type;
};

const badgeVariant = (status) => {
    const variants = {
        up_to_date: 'success',
        patch: 'info',
        minor: 'warning',
        major: 'danger',
        unknown: 'default',
    };

    return variants[status] ?? 'default';
};

const badgeLabel = (status) => {
    const labels = {
        up_to_date: 'Up to date',
        patch: 'Patch update',
        minor: 'Minor update',
        major: 'Major update',
        unknown: 'Unknown',
    };

    return labels[status] ?? 'Unknown';
};
</script>
