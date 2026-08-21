<script setup lang="ts">
import { TrendingUp, TrendingDown } from '@lucide/vue';
import { computed  } from 'vue';
import type {Component} from 'vue';
import { Card, CardContent } from '@/components/ui/card';

interface Props {
    icon: Component;
    label: string;
    value: number | string;
    trend?: {
        value: number;
        direction: 'up' | 'down';
    } | null;
    color?: 'primary' | 'success' | 'warning' | 'destructive';
}

const props = withDefaults(defineProps<Props>(), {
    trend: null,
    color: 'primary',
});

const iconBgClass = computed(() => {
    switch (props.color) {
        case 'primary':
            return 'bg-primary/10 text-primary';
        case 'success':
            return 'bg-success/10 text-success';
        case 'warning':
            return 'bg-warning/10 text-warning';
        case 'destructive':
            return 'bg-destructive/10 text-destructive';
        default:
            return 'bg-primary/10 text-primary';
    }
});
</script>

<template>
    <Card class="border-0 shadow-sm">
        <CardContent class="p-5">
            <div class="flex items-start justify-between">
                <div
                    :class="[
                        'flex h-10 w-10 items-center justify-center rounded-lg',
                        iconBgClass,
                    ]"
                >
                    <component :is="icon" class="h-5 w-5" />
                </div>
                <div
                    v-if="trend"
                    class="flex items-center gap-1 text-xs font-medium"
                >
                    <TrendingUp
                        v-if="trend.direction === 'up'"
                        class="h-3.5 w-3.5 text-success"
                    />
                    <TrendingDown
                        v-if="trend.direction === 'down'"
                        class="h-3.5 w-3.5 text-destructive"
                    />
                    <span
                        :class="{
                            'text-success': trend.direction === 'up',
                            'text-destructive': trend.direction === 'down',
                        }"
                    >
                        +{{ trend.value }}
                    </span>
                </div>
            </div>
            <div class="mt-4">
                <p class="text-2xl font-semibold tracking-tight text-foreground">
                    {{ value }}
                </p>
                <p class="mt-0.5 text-sm text-muted-foreground">{{ label }}</p>
            </div>
        </CardContent>
    </Card>
</template>
