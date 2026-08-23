<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import {
    BarChart3,
    Bot,
    BookOpen,
    Briefcase,
    LayoutGrid,
    MessageSquare,
    Settings,
    Sparkles,
    Users,
} from '@lucide/vue';
import AppLogo from '@/components/AppLogo.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import type { NavItem } from '@/types';

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: LayoutGrid,
    },
    {
        title: 'Interviews',
        href: '/interviews',
        icon: MessageSquare,
        disabled: true,
    },
    {
        title: 'Candidates',
        href: '/candidates',
        icon: Users,
        disabled: true,
    },
    {
        title: 'Jobs',
        href: '/jobs',
        icon: Briefcase,
        disabled: true,
    },
    {
        title: 'Question Bank',
        href: '/questions',
        icon: BookOpen,
        disabled: true,
    },
    {
        title: 'Skills',
        href: '/skills',
        icon: Sparkles,
        disabled: true,
    },
    {
        title: 'Reports',
        href: '/reports',
        icon: BarChart3,
        disabled: true,
    },
    {
        title: 'Settings',
        href: '/profile',
        icon: Settings,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="sidebar">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />

            <div class="mt-auto px-2 pb-2 group-data-[collapsible=icon]:hidden">
                <div class="rounded-xl bg-primary/5 p-3">
                    <div class="flex items-center gap-2.5">
                        <span
                            class="flex size-9 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-primary to-indigo-400 text-white"
                        >
                            <Bot class="size-4.5" />
                        </span>
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-foreground">
                                AI Agent
                            </p>
                            <p class="flex items-center gap-1.5 text-xs text-muted-foreground">
                                <span class="relative flex size-2">
                                    <span
                                        class="absolute inline-flex h-full w-full animate-ping rounded-full bg-success opacity-60"
                                    />
                                    <span
                                        class="relative inline-flex size-2 rounded-full bg-success"
                                    />
                                </span>
                                Online
                            </p>
                        </div>
                    </div>
                    <p class="mt-2.5 text-xs leading-relaxed text-muted-foreground">
                        Your AI interviewer is ready to conduct smart
                        interviews.
                    </p>
                </div>
            </div>
        </SidebarContent>

        <SidebarFooter>
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
