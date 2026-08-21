<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import InterviewStatusBadge from '@/components/Status/InterviewStatusBadge.vue';
import RecommendationBadge from '@/components/Status/RecommendationBadge.vue';
import { Avatar, AvatarFallback } from '@/components/ui/avatar';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { useInitials } from '@/composables/useInitials';

interface Interview {
    id: number;
    candidate_name: string;
    position_title: string;
    status: string;
    score: number | null;
    recommendation: string | null;
    created_at: string;
}

defineProps<{
    interviews: Interview[];
}>();

const { getInitials } = useInitials();

function formatDate(dateString: string): string {
    const date = new Date(dateString);
    const now = new Date();
    const diffMs = now.getTime() - date.getTime();
    const diffDays = Math.floor(diffMs / (1000 * 60 * 60 * 24));

    if (diffDays === 0) {
return 'Today';
}

    if (diffDays === 1) {
return 'Yesterday';
}

    if (diffDays < 7) {
return `${diffDays} days ago`;
}

    return date.toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
    });
}

function formatScore(score: number | null): string {
    if (score === null) {
return '—';
}

    return `${score.toFixed(1)} / 10`;
}
</script>

<template>
    <Card class="border-0 shadow-sm">
        <CardHeader class="flex flex-row items-center justify-between pb-4">
            <CardTitle class="text-base font-semibold">
                Recent Interviews
            </CardTitle>
            <Link
                href="/interviews"
                class="text-sm font-medium text-primary hover:text-primary/80 transition-colors"
            >
                View all
            </Link>
        </CardHeader>
        <CardContent class="p-0">
            <div v-if="interviews.length === 0" class="px-6 pb-6 text-center">
                <p class="text-sm text-muted-foreground">
                    No interviews yet. Start your first candidate interview to see results here.
                </p>
            </div>
            <table v-else class="w-full">
                <thead>
                    <tr class="border-b border-border">
                        <th
                            class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted-foreground"
                        >
                            Candidate
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted-foreground"
                        >
                            Position
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted-foreground"
                        >
                            Date
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted-foreground"
                        >
                            Score
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted-foreground"
                        >
                            Recommendation
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted-foreground"
                        >
                            Status
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    <tr
                        v-for="interview in interviews"
                        :key="interview.id"
                        class="transition-colors hover:bg-muted/50"
                    >
                        <td class="whitespace-nowrap px-6 py-3.5">
                            <div class="flex items-center gap-3">
                                <Avatar class="h-8 w-8">
                                    <AvatarFallback
                                        class="bg-primary/10 text-xs font-medium text-primary"
                                    >
                                        {{ getInitials(interview.candidate_name) }}
                                    </AvatarFallback>
                                </Avatar>
                                <span class="text-sm font-medium text-foreground">
                                    {{ interview.candidate_name }}
                                </span>
                            </div>
                        </td>
                        <td class="whitespace-nowrap px-6 py-3.5">
                            <span class="text-sm text-foreground">
                                {{ interview.position_title }}
                            </span>
                        </td>
                        <td class="whitespace-nowrap px-6 py-3.5">
                            <span class="text-sm text-muted-foreground">
                                {{ formatDate(interview.created_at) }}
                            </span>
                        </td>
                        <td class="whitespace-nowrap px-6 py-3.5">
                            <span
                                :class="[
                                    'text-sm font-medium',
                                    interview.score && interview.score >= 7
                                        ? 'text-success'
                                        : interview.score && interview.score >= 5
                                            ? 'text-warning'
                                            : interview.score
                                                ? 'text-destructive'
                                                : 'text-muted-foreground',
                                ]"
                            >
                                {{ formatScore(interview.score) }}
                            </span>
                        </td>
                        <td class="whitespace-nowrap px-6 py-3.5">
                            <RecommendationBadge
                                v-if="interview.recommendation"
                                :recommendation="interview.recommendation"
                            />
                            <span v-else class="text-sm text-muted-foreground">
                                —
                            </span>
                        </td>
                        <td class="whitespace-nowrap px-6 py-3.5">
                            <InterviewStatusBadge :status="interview.status" />
                        </td>
                    </tr>
                </tbody>
            </table>
        </CardContent>
    </Card>
</template>
