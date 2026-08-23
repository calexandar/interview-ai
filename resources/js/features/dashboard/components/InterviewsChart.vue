<script setup lang="ts">
import { computed } from 'vue';
import type { DailyInterviewCount } from '@/types/interview';

const props = defineProps<{
    data: DailyInterviewCount[];
}>();

const WIDTH = 320;
const HEIGHT = 130;
const PADDING_X = 14;
const PADDING_TOP = 12;
const PADDING_BOTTOM = 18;

const maxValue = computed(() =>
    Math.max(1, ...props.data.map((point) => point.count)),
);

const points = computed(() => {
    const count = props.data.length;

    if (count < 2) {
        return [];
    }

    const stepX = (WIDTH - PADDING_X * 2) / (count - 1);
    const usableHeight = HEIGHT - PADDING_TOP - PADDING_BOTTOM;

    return props.data.map((point, index) => ({
        x: PADDING_X + index * stepX,
        y:
            PADDING_TOP +
            usableHeight -
            (point.count / maxValue.value) * usableHeight,
        label: point.label,
        count: point.count,
    }));
});

const linePath = computed(() =>
    points.value
        .map((point, index) => `${index === 0 ? 'M' : 'L'}${point.x},${point.y}`)
        .join(' '),
);

const areaPath = computed(() => {
    if (points.value.length === 0) {
        return '';
    }

    const first = points.value[0];
    const last = points.value[points.value.length - 1];
    const baseline = HEIGHT - PADDING_BOTTOM;

    return `${linePath.value} L${last.x},${baseline} L${first.x},${baseline} Z`;
});

const gridLines = [0.25, 0.5, 0.75, 1].map((fraction) => ({
    y:
        PADDING_TOP +
        (HEIGHT - PADDING_TOP - PADDING_BOTTOM) * (1 - fraction),
}));
</script>

<template>
    <figure class="m-0">
        <svg
            :viewBox="`0 0 ${WIDTH} ${HEIGHT}`"
            class="h-auto w-full"
            role="img"
            aria-label="Interviews over time"
        >
            <title>Interviews conducted per day over the last week</title>

            <line
                v-for="(gridLine, index) in gridLines"
                :key="index"
                :x1="PADDING_X"
                :x2="WIDTH - PADDING_X"
                :y1="gridLine.y"
                :y2="gridLine.y"
                class="stroke-border"
                stroke-width="1"
                stroke-dasharray="3 4"
            />

            <path
                :d="areaPath"
                class="fill-primary/8"
            />
            <path
                :d="linePath"
                fill="none"
                stroke-width="2.5"
                stroke-linecap="round"
                stroke-linejoin="round"
                class="stroke-primary"
            />

            <circle
                v-for="point in points"
                :key="point.label"
                :cx="point.x"
                :cy="point.y"
                r="3.5"
                class="fill-primary"
                stroke="white"
                stroke-width="1.5"
            >
                <title>{{ `${point.label}: ${point.count}` }}</title>
            </circle>
        </svg>

        <div class="mt-1 flex justify-between px-1">
            <span
                v-for="point in points"
                :key="point.label"
                class="text-[11px] text-muted-foreground"
            >
                {{ point.label }}
            </span>
        </div>

        <figcaption class="sr-only">
            Interviews per day:
            <span v-for="point in points" :key="point.label">
                {{ point.label }} {{ point.count }};
            </span>
        </figcaption>
    </figure>
</template>
