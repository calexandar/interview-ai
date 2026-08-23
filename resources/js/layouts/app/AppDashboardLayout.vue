<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import AppContent from '@/components/AppContent.vue';
import AppShell from '@/components/AppShell.vue';
import AppSidebar from '@/components/AppSidebar.vue';
import TopHeader from '@/components/layout/TopHeader.vue';
import { Toaster } from '@/components/ui/sonner';
import type { ActiveInterview } from '@/types/interview';

const page = usePage<{ session?: { activeInterview: ActiveInterview | null } }>();

const activeInterview = computed<ActiveInterview | null>(
    () => page.props.session?.activeInterview ?? null,
);
</script>

<template>
    <AppShell variant="sidebar">
        <AppSidebar />
        <AppContent variant="sidebar" class="overflow-x-hidden bg-background">
            <slot name="header">
                <TopHeader :active-interview="activeInterview" />
            </slot>
            <slot />
        </AppContent>
        <Toaster />
    </AppShell>
</template>
