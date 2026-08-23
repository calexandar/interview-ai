<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import {
    SidebarGroup,
    SidebarGroupLabel,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { useCurrentUrl } from '@/composables/useCurrentUrl';
import type { NavItem } from '@/types';

defineProps<{
    items: NavItem[];
}>();

const { isCurrentUrl } = useCurrentUrl();
</script>

<template>
    <SidebarGroup class="px-2 py-0">
        <SidebarGroupLabel>Platform</SidebarGroupLabel>
        <SidebarMenu>
            <SidebarMenuItem v-for="item in items" :key="item.title">
                <SidebarMenuButton
                    as-child
                    :is-active="!item.disabled && isCurrentUrl(item.href)"
                    :tooltip="item.title"
                    :aria-disabled="item.disabled || undefined"
                    :disabled="item.disabled"
                >
                    <Link
                        v-if="!item.disabled"
                        :href="item.href"
                    >
                        <component :is="item.icon" />
                        <span>{{ item.title }}</span>
                    </Link>
                    <span
                        v-else
                        class="flex w-full items-center gap-2"
                    >
                        <component :is="item.icon" />
                        <span>{{ item.title }}</span>
                    </span>
                </SidebarMenuButton>
            </SidebarMenuItem>
        </SidebarMenu>
    </SidebarGroup>
</template>
