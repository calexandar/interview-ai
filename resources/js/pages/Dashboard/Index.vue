<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { Deferred, Head } from '@inertiajs/vue3';
import { AlertTriangle, RotateCcw } from '@lucide/vue';
import { computed } from 'vue';
import { Button } from '@/components/ui/button';
import { Skeleton } from '@/components/ui/skeleton';
import InterviewOverviewCard from '@/features/dashboard/components/InterviewOverviewCard.vue';
import RecentInterviewsCard from '@/features/dashboard/components/RecentInterviewsCard.vue';
import SkillsAssessmentCard from '@/features/dashboard/components/SkillsAssessmentCard.vue';
import InterviewConductor from '@/features/interview/components/InterviewConductor.vue';
import { createDemoSession } from '@/features/interview/data/demoSession';
import { dashboard } from '@/routes';
import type { DashboardInterview } from '@/types/dashboard';
import type { AiAgentStatus, InterviewSession } from '@/types/interview';

interface Props {
    recentInterviews: DashboardInterview[];
    userName: string;
    aiAgentStatus: AiAgentStatus;
    session?: InterviewSession;
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

/**
 * Falls back to presentation-only sample data when no interview is
 * currently in progress so the full experience stays visible.
 */
const session = computed<InterviewSession>(() => {
    const loaded = props.session;

    if (loaded && loaded.activeInterview !== null) {
        return loaded;
    }

    return createDemoSession();
});

function retry(): void {
    router.reload();
}
</script>

<template>
    <Head title="Dashboard" />

    <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
        <Deferred data="session">
            <template #fallback>
                <div class="flex flex-col gap-6">
                    <Skeleton class="h-[520px] w-full rounded-xl" />
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 xl:grid-cols-3">
                        <Skeleton
                            v-for="index in 3"
                            :key="index"
                            class="h-[360px] rounded-xl"
                        />
                    </div>
                </div>
            </template>

            <template #rescue>
                <div
                    class="flex flex-col items-center gap-3 rounded-xl border border-destructive/30 bg-destructive/5 px-6 py-14 text-center"
                    role="alert"
                >
                    <AlertTriangle
                        class="size-8 text-destructive"
                        aria-hidden="true"
                    />
                    <p class="text-base font-semibold text-foreground">
                        Something went wrong.
                    </p>
                    <p class="max-w-sm text-sm text-muted-foreground">
                        We couldn't load the interview dashboard. Please try
                        again.
                    </p>
                    <Button
                        variant="outline"
                        class="mt-2"
                        @click="retry"
                    >
                        <RotateCcw
                            class="size-4"
                            aria-hidden="true"
                        />
                        Try again
                    </Button>
                </div>
            </template>

            <div class="flex flex-col gap-6">
                <InterviewConductor
                    :job-title="session.activeInterview?.jobTitle ?? ''"
                    :question-text="session.currentQuestion?.text ?? ''"
                    :initial-state="session.currentQuestion?.state"
                    :progress-percentage="session.progress.percentage"
                    :sections="session.progress.sections"
                />

                <div
                    class="grid grid-cols-1 gap-6 sm:grid-cols-2 xl:grid-cols-3"
                >
                    <InterviewOverviewCard
                        :statistics="session.statistics"
                        :interviews-over-time="session.interviewsOverTime"
                        class="sm:col-span-2 xl:col-span-1"
                    />

                    <RecentInterviewsCard
                        :interviews="recentInterviews"
                    />

                    <SkillsAssessmentCard :skills="session.skills" />
                </div>
            </div>
        </Deferred>
    </div>
</template>
