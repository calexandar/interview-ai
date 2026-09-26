<script setup lang="ts">
import { router, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { Button } from '@/components/ui/button';
import { Spinner } from '@/components/ui/spinner';
import AiInterviewerChat from '@/features/interview/components/AiInterviewerChat.vue';
import EndInterviewDialog from '@/features/interview/components/EndInterviewDialog.vue';
import InterviewTimer from '@/features/interview/components/InterviewTimer.vue';
import ProgressPanel from '@/features/interview/components/ProgressPanel.vue';
import QuestionCard from '@/features/interview/components/QuestionCard.vue';
import {
    answer,
    next,
    pause,
    resume,
    retryEvaluation as retryEvaluationRoute,
    skip,
} from '@/routes/interviews';
import type { ConductPageProps } from '@/types/interview';

const props = defineProps<ConductPageProps>();

const answerForm = useForm({
    question_id: props.currentQuestion?.id ?? 0,
    content: '',
    duration_seconds: 0,
});

const isSubmitting = ref(false);
const isRetrying = ref(false);
const answerText = ref('');

const questionState = computed(() => {
    if (!props.currentQuestion) {
        return 'pending';
    }

    if (props.currentQuestion.status === 'answered') {
        return 'answered';
    }

    if (props.currentQuestion.status === 'asking') {
        return 'listening';
    }

    return 'pending';
});

/**
 * The interviewer's line. While a question is being generated there is no
 * current question yet, so the panel says it is thinking rather than showing a
 * stale question.
 */
const interviewerMessage = computed(() =>
    props.currentQuestion ? props.currentQuestion.text : null,
);

const isThinking = computed(() => !props.currentQuestion);

/**
 * Only an answer that exists, is not completed, and is in a state Laravel will
 * accept a retry for can be retried. The server enforces all of this anyway;
 * this only keeps the button honest.
 */
const canRetryEvaluation = computed(() => {
    const question = props.currentQuestion;

    if (!question?.answerId || isRetrying.value) {
        return false;
    }

    return question.evaluationState !== 'completed';
});

/**
 * Tells the candidate where their answer's review stands. Deliberately carries
 * no score, confidence or rubric: Laravel never sends those to this page.
 */
const evaluationNotice = computed<{
    message: string;
    tone: string;
} | null>(() => {
    const question = props.currentQuestion;

    if (!question) {
        return null;
    }

    if (
        question.status === 'answered' &&
        question.evaluationState === 'failed'
    ) {
        return {
            message:
                'We could not review your last answer just now. Your answer was saved, and you can try again.',
            tone: 'border-warning/30 bg-warning/5 text-warning',
        };
    }

    if (
        question.status === 'answered' &&
        question.evaluationState === 'pending'
    ) {
        return {
            message: 'Your answer was saved and is waiting to be reviewed.',
            tone: 'border-border bg-muted/30 text-muted-foreground',
        };
    }

    return null;
});

function submitAnswer(): void {
    if (!props.currentQuestion || !answerText.value.trim()) {
        return;
    }

    isSubmitting.value = true;

    answerForm.question_id = props.currentQuestion.id;
    answerForm.content = answerText.value.trim();

    // A failed evaluation flashes evaluation_failed and the server has left us
    // on this question so the candidate can retry. Advancing in that case would
    // move past an answer that was never reviewed.
    let evaluationFailed = false;

    answerForm.post(answer.url({ interview: props.interview.id }), {
        onFlash: (flash) => {
            evaluationFailed = flash.evaluation_failed === true;
        },
        onFinish: () => {
            isSubmitting.value = false;
            answerText.value = '';
        },
        onSuccess: () => {
            if (evaluationFailed) {
                return;
            }

            router.post(next({ interview: props.interview.id }));
        },
    });
}

function retryEvaluation(): void {
    const question = props.currentQuestion;

    if (!question?.answerId) {
        return;
    }

    isRetrying.value = true;

    router.post(
        retryEvaluationRoute({ interview: props.interview.id }),
        { answer_id: question.answerId },
        {
            preserveScroll: true,
            onFinish: () => {
                isRetrying.value = false;
            },
        },
    );
}

function skipQuestion(): void {
    if (!props.currentQuestion) {
        return;
    }

    router.post(
        skip({ interview: props.interview.id }),
        { question_id: props.currentQuestion.id },
        { preserveState: true },
    );
}

function pauseInterview(): void {
    router.post(pause({ interview: props.interview.id }));
}

function resumeInterview(): void {
    router.post(resume({ interview: props.interview.id }));
}
</script>

<template>
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-foreground">
                    Interview in Progress
                </h1>
                <p class="mt-1 text-sm text-muted-foreground">
                    {{ candidate.name }} — {{ position.title }}
                </p>
            </div>
            <div class="flex items-center gap-4">
                <InterviewTimer
                    v-if="interview.startedAt"
                    :duration-seconds="interview.durationSeconds"
                    :remaining-seconds="interview.remainingSeconds"
                />
                <Button
                    v-if="interview.status === 'in_progress'"
                    variant="outline"
                    size="sm"
                    @click="pauseInterview"
                >
                    Pause
                </Button>
                <Button
                    v-else-if="interview.status === 'paused'"
                    variant="outline"
                    size="sm"
                    @click="resumeInterview"
                >
                    Resume
                </Button>
                <EndInterviewDialog :interview-id="interview.id" />
            </div>
        </div>

        <section
            class="rounded-xl border border-border bg-[#F9F7FF] p-4 md:p-6 dark:border-border/60 dark:bg-card/40"
            aria-label="Active AI interview"
        >
            <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_420px]">
                <div class="flex min-w-0 flex-col gap-6">
                    <AiInterviewerChat
                        :agent-status="isThinking ? 'thinking' : 'online'"
                        :candidate-name="candidate.name"
                        :job-title="position.title"
                        :message="interviewerMessage"
                        :thinking="isThinking"
                    />

                    <QuestionCard
                        v-if="currentQuestion"
                        :question="currentQuestion.text"
                        :initial-state="questionState"
                    />

                    <div
                        v-if="evaluationNotice"
                        class="rounded-lg border p-4"
                        :class="evaluationNotice.tone"
                        role="status"
                        aria-live="polite"
                    >
                        <div
                            class="flex flex-wrap items-center justify-between gap-3"
                        >
                            <p class="text-sm">
                                {{ evaluationNotice.message }}
                            </p>

                            <Button
                                v-if="canRetryEvaluation"
                                variant="outline"
                                size="sm"
                                :disabled="isRetrying"
                                @click="retryEvaluation"
                            >
                                <Spinner
                                    v-if="isRetrying"
                                    class="mr-2 size-4"
                                />
                                {{ isRetrying ? 'Retrying...' : 'Try again' }}
                            </Button>
                        </div>
                    </div>

                    <div
                        v-if="
                            currentQuestion &&
                            interview.status === 'in_progress'
                        "
                        class="flex flex-col gap-3"
                    >
                        <textarea
                            v-model="answerText"
                            class="min-h-[120px] w-full resize-y rounded-lg border border-input bg-background p-3 text-sm text-foreground placeholder:text-muted-foreground focus-visible:outline-2 focus-visible:outline-offset-1 focus-visible:outline-ring"
                            placeholder="Type your answer here..."
                            :disabled="isSubmitting"
                        />
                        <div class="flex items-center gap-3">
                            <Button
                                :disabled="!answerText.trim() || isSubmitting"
                                @click="submitAnswer"
                            >
                                <Spinner
                                    v-if="isSubmitting"
                                    class="mr-2 size-4"
                                />
                                {{
                                    isSubmitting
                                        ? 'Submitting...'
                                        : 'Submit Answer'
                                }}
                            </Button>
                            <Button
                                variant="ghost"
                                :disabled="isSubmitting"
                                @click="skipQuestion"
                            >
                                Skip Question
                            </Button>
                        </div>
                    </div>

                    <div
                        v-else-if="interview.status === 'paused'"
                        class="rounded-lg border border-warning/30 bg-warning/5 p-4 text-center"
                    >
                        <p class="text-sm text-warning">
                            Interview is paused. Click Resume to continue.
                        </p>
                    </div>

                    <div
                        v-else-if="interview.status === 'expired'"
                        class="rounded-lg border border-destructive/30 bg-destructive/5 p-4 text-center"
                    >
                        <p class="text-sm text-destructive">
                            This interview has expired.
                        </p>
                    </div>

                    <div
                        v-else-if="interview.status === 'completed'"
                        class="rounded-lg border border-success/30 bg-success/5 p-4 text-center"
                    >
                        <p class="text-sm text-success">
                            Interview completed. Thank you for your time.
                        </p>
                    </div>
                </div>

                <ProgressPanel
                    :job-title="position.title"
                    :percentage="progress.percentage"
                    :sections="progress.sections"
                    class="min-h-[320px] xl:min-h-0"
                />
            </div>
        </section>
    </div>
</template>
