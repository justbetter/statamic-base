<template>
    <div class="max-w-7xl mx-auto" data-max-width-wrapper>
        <Head title="JustBetter Packages" />

        <Header title="JustBetter Packages" icon="addons" />

        <div v-if="!packagistAvailable" class="mb-6">
            <Card>
                <p class="text-sm">
                    Packagist could not be reached. Installed versions are shown, but update information is unavailable.
                </p>
            </Card>
        </div>

        <div class="space-y-8">
            <PackageSection
                title="Production packages"
                :packages="productionPackages"
            />

            <PackageSection
                v-if="devPackages.length > 0"
                title="Development packages"
                :packages="devPackages"
            />
        </div>
    </div>
</template>

<script setup>
import { Head } from '@statamic/cms/inertia';
import { Card, Header } from '@statamic/cms/ui';
import PackageSection from './PackageSection.vue';

defineProps({
    productionPackages: { type: Array, required: true },
    devPackages: { type: Array, required: true },
    packagistAvailable: { type: Boolean, required: true },
    icon: { type: String, default: null },
});
</script>
