<script setup lang="ts">
import { computed } from 'vue';

const props = defineProps<{
    score: number;
}>();

type Tier = {
    label: string;
    class: string;
};

const tier = computed<Tier>(() => {
    if (props.score >= 80) {
        return { label: 'High score', class: 'bg-success/10 text-success' };
    }

    if (props.score >= 60) {
        return { label: 'Medium score', class: 'bg-blue-500/10 text-blue-600 dark:text-blue-400' };
    }

    return { label: 'Low score', class: 'bg-warning/15 text-warning' };
});
</script>

<template>
    <span
        class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-semibold tabular-nums"
        :class="tier.class"
        :aria-label="`${score} percent, ${tier.label}`"
    >
        {{ score }}%
    </span>
</template>
