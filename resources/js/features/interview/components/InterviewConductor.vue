<script setup lang="ts">
import { Settings, Volume2 } from '@lucide/vue';
import { Button } from '@/components/ui/button';
import AiInterviewerChat from '@/features/interview/components/AiInterviewerChat.vue';
import ProgressPanel from '@/features/interview/components/ProgressPanel.vue';
import QuestionCard from '@/features/interview/components/QuestionCard.vue';
import type { InterviewSection, QuestionState } from '@/types/interview';

withDefaults(
    defineProps<{
        jobTitle: string;
        questionText: string;
        progressPercentage: number;
        sections: InterviewSection[];
        initialState?: QuestionState;
        loading?: boolean;
    }>(),
    {
        initialState: 'listening',
        loading: false,
    },
);
</script>

<template>
    <section
        class="rounded-xl border border-border bg-[#F9F7FF] p-4 dark:border-border/60 dark:bg-card/40 md:p-6"
        aria-label="Active AI interview"
    >
        <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_420px]">
            <div class="flex min-w-0 flex-col gap-6">
                <AiInterviewerChat />

                <QuestionCard
                    :question="questionText"
                    :initial-state="initialState"
                />

                <div class="flex items-center gap-1">
                    <Button
                        variant="ghost"
                        size="icon"
                        class="size-8 text-muted-foreground hover:text-foreground"
                        aria-label="Adjust interviewer volume"
                    >
                        <Volume2 class="size-4" />
                    </Button>
                    <Button
                        variant="ghost"
                        size="icon"
                        class="size-8 text-muted-foreground hover:text-foreground"
                        aria-label="Interviewer audio settings"
                    >
                        <Settings class="size-4" />
                    </Button>
                </div>
            </div>

            <ProgressPanel
                :job-title="jobTitle"
                :percentage="progressPercentage"
                :sections="sections"
                :loading="loading"
                class="min-h-[320px] xl:min-h-0"
            />
        </div>
    </section>
</template>
