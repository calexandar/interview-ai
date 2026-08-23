<script setup lang="ts">
withDefaults(
    defineProps<{
        active?: boolean;
    }>(),
    {
        active: false,
    },
);

const BAR_COUNT = 32;

const STATIC_HEIGHTS = [
    8, 14, 10, 18, 12, 22, 9, 26, 11, 16, 20, 13, 28, 10, 17, 24, 9, 21, 14,
    27, 12, 18, 23, 11, 19, 15, 25, 10, 16, 22, 13, 20,
];

const barHeight = (index: number): number => STATIC_HEIGHTS[index] ?? 12;
const animationDelay = (index: number): string =>
    `${(index % 9) * -110}ms`;
</script>

<template>
    <div
        class="flex h-10 items-center justify-center gap-[3px]"
        aria-hidden="true"
    >
        <span
            v-for="index in BAR_COUNT"
            :key="index"
            class="w-[3px] shrink-0 origin-center rounded-full bg-primary/80"
            :class="active ? 'animate-waveform' : ''"
            :style="
                active
                    ? { height: '28px', animationDelay: animationDelay(index) }
                    : { height: `${barHeight(index)}px` }
            "
        />
    </div>
</template>
