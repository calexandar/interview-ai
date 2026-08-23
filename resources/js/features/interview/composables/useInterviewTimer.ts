import { useIntervalFn } from '@vueuse/core';
import { computed, ref } from 'vue';

const WARNING_THRESHOLD_SECONDS = 5 * 60;
const DANGER_THRESHOLD_SECONDS = 60;

export interface UseInterviewTimerOptions {
    durationSeconds: number;
    remainingSeconds: number;
}

export function useInterviewTimer({
    durationSeconds,
    remainingSeconds: initialRemainingSeconds,
}: UseInterviewTimerOptions) {
    const remaining = ref(Math.max(0, Math.floor(initialRemainingSeconds)));

    const { pause } = useIntervalFn(() => {
        if (remaining.value <= 0) {
            pause();

            return;
        }

        remaining.value -= 1;
    }, 1000);

    const minutes = computed(() => Math.floor(remaining.value / 60));
    const seconds = computed(() => remaining.value % 60);

    const label = computed(
        () =>
            `${String(minutes.value).padStart(2, '0')}:${String(seconds.value).padStart(2, '0')}`,
    );

    const progressRatio = computed(() =>
        durationSeconds > 0
            ? Math.min(1, Math.max(0, remaining.value / durationSeconds))
            : 0,
    );

    const hasExpired = computed(() => remaining.value === 0);

    const isWarning = computed(
        () =>
            !hasExpired.value && remaining.value <= WARNING_THRESHOLD_SECONDS,
    );

    const isDanger = computed(
        () => !hasExpired.value && remaining.value <= DANGER_THRESHOLD_SECONDS,
    );

    return {
        remaining,
        label,
        progressRatio,
        isWarning,
        isDanger,
        hasExpired,
    };
}
