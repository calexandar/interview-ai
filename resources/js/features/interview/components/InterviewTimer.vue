<script setup lang="ts">
import { computed } from 'vue';
import { useInterviewTimer } from '@/features/interview/composables/useInterviewTimer';

const props = defineProps<{
    durationSeconds: number;
    remainingSeconds: number;
}>();

const { label, progressRatio, isWarning, isDanger, hasExpired } =
    useInterviewTimer({
        durationSeconds: props.durationSeconds,
        remainingSeconds: props.remainingSeconds,
    });

const RADIUS = 15;
const STROKE_WIDTH = 3;
const CIRCUMFERENCE = 2 * Math.PI * RADIUS;

const strokeDashoffset = computed(
    () => CIRCUMFERENCE * (1 - progressRatio.value),
);

const progressClass = computed(() => {
    if (isDanger.value || hasExpired.value) {
        return 'stroke-destructive';
    }

    if (isWarning.value) {
        return 'stroke-warning';
    }

    return 'stroke-primary';
});
</script>

<template>
    <div
        role="timer"
        class="flex items-center gap-2"
        :aria-label="`Interview time remaining: ${label}`"
    >
        <span class="relative inline-flex size-9 shrink-0">
            <svg viewBox="0 0 36 36" class="size-full -rotate-90">
                <circle
                    cx="18"
                    cy="18"
                    :r="RADIUS"
                    fill="none"
                    stroke-width="3"
                    class="stroke-muted"
                />
                <circle
                    cx="18"
                    cy="18"
                    :r="RADIUS"
                    fill="none"
                    :stroke-width="STROKE_WIDTH"
                    stroke-linecap="round"
                    :stroke-dasharray="CIRCUMFERENCE"
                    :stroke-dashoffset="strokeDashoffset"
                    :class="[progressClass, 'transition-[stroke-dashoffset] duration-1000 ease-linear']"
                />
            </svg>
        </span>
        <span class="hidden leading-tight sm:flex sm:flex-col">
            <span
                :class="[
                    'text-sm font-semibold tabular-nums',
                    isDanger || hasExpired ? 'text-destructive' : '',
                    isWarning && !isDanger ? 'text-warning' : '',
                ]"
            >
                {{ label }}
            </span>
            <span class="text-[11px] text-muted-foreground">
                Time Remaining
            </span>
        </span>
    </div>
</template>
