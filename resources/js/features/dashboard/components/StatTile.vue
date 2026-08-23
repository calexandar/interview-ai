<script setup lang="ts">
import { TrendingDown, TrendingUp } from '@lucide/vue';
import type { Component } from 'vue';
import { computed } from 'vue';
import type { StatTrend } from '@/types/interview';

const props = withDefaults(
    defineProps<{
        icon: Component;
        label: string;
        value: number | string;
        trend?: StatTrend;
        tone?: 'primary' | 'success' | 'warning' | 'info';
    }>(),
    {
        trend: null,
        tone: 'primary',
    },
);

const TONES = {
    primary: {
        tile: 'bg-primary/8 text-primary',
        chip: 'bg-success/10 text-success',
    },
    success: {
        tile: 'bg-success/10 text-success',
        chip: 'bg-success/10 text-success',
    },
    warning: {
        tile: 'bg-warning/12 text-warning',
        chip: 'bg-success/10 text-success',
    },
    info: {
        tile: 'bg-blue-500/10 text-blue-600 dark:text-blue-400',
        chip: 'bg-success/10 text-success',
    },
} as const;

const classes = computed(() => TONES[props.tone]);
</script>

<template>
    <div class="rounded-lg border border-border bg-card p-3.5">
        <div class="flex items-center gap-3">
            <span
                class="flex size-9 shrink-0 items-center justify-center rounded-lg"
                :class="classes.tile"
            >
                <component
                    :is="icon"
                    class="size-4.5"
                    aria-hidden="true"
                />
            </span>
            <div class="min-w-0">
                <p class="text-xl leading-tight font-semibold tabular-nums text-foreground">
                    {{ value }}
                </p>
                <p class="truncate text-xs text-muted-foreground">
                    {{ label }}
                </p>
            </div>
            <span
                v-if="trend"
                class="ml-auto flex items-center gap-0.5 rounded-full px-1.5 py-0.5 text-[11px] font-medium"
                :class="
                    trend.direction === 'up'
                        ? 'bg-success/10 text-success'
                        : 'bg-destructive/10 text-destructive'
                "
            >
                <TrendingUp
                    v-if="trend.direction === 'up'"
                    class="size-3"
                    aria-hidden="true"
                />
                <TrendingDown
                    v-else
                    class="size-3"
                    aria-hidden="true"
                />
                {{ trend.value }}%
            </span>
        </div>
    </div>
</template>
