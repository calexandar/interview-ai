<script setup lang="ts">
import { CheckCircle2 } from '@lucide/vue';
import { computed, onBeforeUnmount, ref, watch } from 'vue';
import AudioWaveform from '@/features/interview/components/AudioWaveform.vue';
import RecordingControls from '@/features/interview/components/RecordingControls.vue';
import type { QuestionState, RecordingState } from '@/types/interview';

const props = withDefaults(
    defineProps<{
        question: string;
        initialState?: QuestionState;
    }>(),
    {
        initialState: 'listening',
    },
);

const state = ref<RecordingState>(
    props.initialState === 'processing'
        ? 'processing'
        : props.initialState === 'answered'
          ? 'completed'
          : 'recording',
);

const elapsedSeconds = ref(0);
let processingTimeout: ReturnType<typeof setTimeout> | undefined;
let elapsedInterval: ReturnType<typeof setInterval> | undefined;

function clearTimers(): void {
    if (elapsedInterval) {
        clearInterval(elapsedInterval);
        elapsedInterval = undefined;
    }
}

function startElapsedTimer(): void {
    clearTimers();
    elapsedInterval = setInterval(() => {
        elapsedSeconds.value += 1;
    }, 1000);
}

watch(
    () => props.question,
    () => {
        state.value = 'recording';
        elapsedSeconds.value = 0;
        startElapsedTimer();
    },
);

startElapsedTimer();

onBeforeUnmount(() => {
    clearTimers();

    if (processingTimeout) {
        clearTimeout(processingTimeout);
    }
});

function toggleRecording(): void {
    if (state.value === 'recording') {
        state.value = 'paused';
        clearTimers();
    } else if (state.value === 'paused') {
        state.value = 'recording';
        startElapsedTimer();
    }
}

function stopRecording(): void {
    clearTimers();
    state.value = 'processing';

    processingTimeout = setTimeout(() => {
        state.value = 'completed';
    }, 1600);
}

const recordingLabel = computed(() => {
    const minutes = String(Math.floor(elapsedSeconds.value / 60)).padStart(
        2,
        '0',
    );
    const seconds = String(elapsedSeconds.value % 60).padStart(2, '0');

    return `${minutes}:${seconds}`;
});
</script>

<template>
    <div
        class="flex w-full flex-col items-center gap-6 rounded-2xl border border-border bg-card p-8 shadow-sm"
    >
        <h2
            class="max-w-xl text-center text-lg leading-relaxed font-semibold text-foreground sm:text-xl"
        >
            {{ question }}
        </h2>

        <AudioWaveform :active="state === 'recording'" />

        <div class="flex h-5 items-center justify-center gap-2 text-sm text-muted-foreground">
            <template v-if="state === 'recording'">
                <span
                    class="relative flex size-2"
                    aria-hidden="true"
                >
                    <span
                        class="absolute inline-flex h-full w-full animate-ping rounded-full bg-destructive opacity-60"
                    />
                    <span
                        class="relative inline-flex size-2 rounded-full bg-destructive"
                    />
                </span>
                <span>Listening to your answer...</span>
                <span
                    class="font-medium tabular-nums text-foreground"
                    :aria-label="`Recording time elapsed: ${recordingLabel}`"
                >
                    {{ recordingLabel }}
                </span>
            </template>
            <template v-else-if="state === 'paused'"> Paused </template>
            <template v-else-if="state === 'processing'">
                Processing your answer...
            </template>
            <template v-else>
                <CheckCircle2
                    class="size-4 text-success"
                    aria-hidden="true"
                />
                <span>Answer recorded</span>
            </template>
        </div>

        <RecordingControls
            :state="state"
            @toggle-recording="toggleRecording"
            @stop="stopRecording"
        />
    </div>
</template>
