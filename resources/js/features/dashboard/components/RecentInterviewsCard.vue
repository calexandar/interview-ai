<script setup lang="ts">
import { MessageSquare } from '@lucide/vue';
import { computed } from 'vue';
import InterviewStatusBadge from '@/components/Status/InterviewStatusBadge.vue';
import { Avatar, AvatarFallback } from '@/components/ui/avatar';
import {
    Card,
    CardContent,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { EmptyState } from '@/components/ui/empty-state';
import { useInitials } from '@/composables/useInitials';
import ScoreBadge from '@/features/dashboard/components/ScoreBadge.vue';
import type { DashboardInterview } from '@/types/dashboard';

const props = defineProps<{
    interviews: DashboardInterview[];
}>();

const { getInitials } = useInitials();

const visibleInterviews = computed(() => props.interviews.slice(0, 5));

function formatDate(dateString: string): string {
    const date = new Date(dateString);
    const now = new Date();
    const diffDays = Math.floor(
        (now.setHours(0, 0, 0, 0) - new Date(date).setHours(0, 0, 0, 0)) /
            (1000 * 60 * 60 * 24),
    );

    if (diffDays === 0) {
        return `Today, ${date.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' })}`;
    }

    if (diffDays === 1) {
        return 'Yesterday';
    }

    return date.toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
    });
}

function toPercent(score: number | null): number | null {
    return score === null ? null : Math.round(score * 10);
}
</script>

<template>
    <Card class="gap-4 rounded-xl border-border shadow-sm">
        <CardHeader class="flex flex-row items-center justify-between">
            <CardTitle class="text-base font-semibold">
                Recent Interviews
            </CardTitle>
            <span
                class="inline-flex h-8 items-center rounded-md border border-input bg-card px-3 text-xs font-medium text-muted-foreground"
                title="Full interview list coming soon"
                aria-disabled="true"
            >
                View All
            </span>
        </CardHeader>
        <CardContent class="px-0 pb-2">
            <EmptyState
                v-if="visibleInterviews.length === 0"
                class="mx-6 mb-4"
                :icon="MessageSquare"
                title="No recent interviews."
                description="Start your first AI interview to see results here."
            />

            <ul
                v-else
                class="divide-y divide-border"
            >
                <li
                    v-for="interview in visibleInterviews"
                    :key="interview.id"
                    class="flex items-center gap-3 px-5 py-3 transition-colors hover:bg-muted/40"
                >
                    <Avatar class="size-9">
                        <AvatarFallback
                            class="bg-primary/10 text-xs font-semibold text-primary"
                        >
                            {{ getInitials(interview.candidate_name) }}
                        </AvatarFallback>
                    </Avatar>

                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-medium text-foreground">
                            {{ interview.candidate_name }}
                        </p>
                        <p class="truncate text-xs text-muted-foreground">
                            {{ interview.position_title }}
                        </p>
                        <p class="mt-0.5 text-[11px] text-muted-foreground">
                            {{ formatDate(interview.created_at) }}
                        </p>
                    </div>

                    <div class="flex shrink-0 flex-col items-end gap-1.5">
                        <ScoreBadge
                            v-if="toPercent(interview.score) !== null"
                            :score="toPercent(interview.score) ?? 0"
                        />
                        <InterviewStatusBadge :status="interview.status" />
                    </div>
                </li>
            </ul>
        </CardContent>
    </Card>
</template>
