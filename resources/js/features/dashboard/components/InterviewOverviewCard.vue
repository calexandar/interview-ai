<script setup lang="ts">
import {
    Award,
    ChevronDown,
    MessageSquare,
    PlayCircle,
    CheckCircle2,
} from '@lucide/vue';
import {
    Card,
    CardContent,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Skeleton } from '@/components/ui/skeleton';
import InterviewsChart from '@/features/dashboard/components/InterviewsChart.vue';
import StatTile from '@/features/dashboard/components/StatTile.vue';
import type {
    DailyInterviewCount,
    InterviewStatistics,
} from '@/types/interview';

withDefaults(
    defineProps<{
        statistics: InterviewStatistics;
        interviewsOverTime: DailyInterviewCount[];
        loading?: boolean;
    }>(),
    {
        loading: false,
    },
);
</script>

<template>
    <Card class="gap-4 rounded-xl border-border shadow-sm">
        <CardHeader class="flex flex-row items-center justify-between">
            <CardTitle class="text-base font-semibold">
                Interview Overview
            </CardTitle>
            <span
                class="inline-flex items-center gap-1 rounded-md border border-input bg-card px-2.5 py-1 text-xs font-medium text-muted-foreground"
                title="More ranges coming soon"
                aria-disabled="true"
            >
                This Week
                <ChevronDown
                    class="size-3.5"
                    aria-hidden="true"
                />
            </span>
        </CardHeader>

        <CardContent class="space-y-5">
            <div v-if="loading" class="grid grid-cols-2 gap-3 lg:grid-cols-4">
                <Skeleton
                    v-for="index in 4"
                    :key="index"
                    class="h-[68px]"
                />
            </div>

            <div
                v-else
                class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4"
            >
                <StatTile
                    :icon="MessageSquare"
                    label="Interviews"
                    :value="statistics.interviews.value"
                    :trend="statistics.interviews.trend"
                    tone="primary"
                />
                <StatTile
                    :icon="CheckCircle2"
                    label="Completed"
                    :value="statistics.completed.value"
                    :trend="statistics.completed.trend"
                    tone="success"
                />
                <StatTile
                    :icon="PlayCircle"
                    label="In Progress"
                    :value="statistics.inProgress.value"
                    :trend="statistics.inProgress.trend"
                    tone="warning"
                />
                <StatTile
                    :icon="Award"
                    label="Avg. Score"
                    :value="
                        statistics.averageScorePercent.value === null
                            ? '—'
                            : `${statistics.averageScorePercent.value}%`
                    "
                    tone="info"
                />
            </div>

            <div v-if="loading" class="space-y-2">
                <p class="text-sm font-semibold">Interviews Over Time</p>
                <Skeleton class="h-[150px] w-full" />
            </div>

            <div v-else-if="interviewsOverTime.length > 1" class="space-y-1">
                <p class="text-sm font-semibold text-foreground">
                    Interviews Over Time
                </p>
                <InterviewsChart :data="interviewsOverTime" />
            </div>
        </CardContent>
    </Card>
</template>
