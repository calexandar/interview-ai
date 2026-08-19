<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import { dashboard } from '@/routes';
import { Button } from '@/components/ui/button';
import { Briefcase, Users, MessageSquare, Award, Plus } from '@lucide/vue';
import StatCard from '@/components/Dashboard/StatCard.vue';
import RecentInterviewsTable from '@/components/Dashboard/RecentInterviewsTable.vue';
import type { DashboardInterview } from '@/types/dashboard';

interface Props {
    activePositionsCount: number;
    candidatesCount: number;
    interviewsCount: number;
    strongCandidatesCount: number;
    recentInterviews: DashboardInterview[];
    userName: string;
}

const props = defineProps<Props>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: dashboard(),
            },
        ],
    },
});

const greeting = computed(() => {
    const hour = new Date().getHours();
    if (hour < 12) return 'Good morning';
    if (hour < 18) return 'Good afternoon';
    return 'Good evening';
});

const firstName = computed(() => props.userName.split(' ')[0]);
</script>

<template>
    <Head title="Dashboard" />

    <div class="flex h-full flex-1 flex-col gap-6 overflow-x-auto p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold tracking-tight text-foreground">
                    {{ greeting }}, {{ firstName }}
                </h1>
                <p class="mt-1 text-sm text-muted-foreground">
                    Here's what's happening with your hiring pipeline.
                </p>
            </div>
            <Button as-child class="bg-primary text-primary-foreground hover:bg-primary/90">
                <Link href="/positions/create">
                    <Plus class="mr-1.5 h-4 w-4" />
                    Create Position
                </Link>
            </Button>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <StatCard
                :icon="Briefcase"
                label="Active Positions"
                :value="activePositionsCount"
                color="primary"
            />
            <StatCard
                :icon="Users"
                label="Candidates"
                :value="candidatesCount"
                color="primary"
            />
            <StatCard
                :icon="MessageSquare"
                label="Interviews"
                :value="interviewsCount"
                color="primary"
            />
            <StatCard
                :icon="Award"
                label="Strong Candidates"
                :value="strongCandidatesCount"
                color="success"
            />
        </div>

        <RecentInterviewsTable :interviews="recentInterviews" />
    </div>
</template>
