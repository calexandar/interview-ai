<script setup lang="ts">
import type { HTMLAttributes } from 'vue';
import { cn } from '@/lib/utils';

const props = defineProps<{
    class?: HTMLAttributes['class'];
}>();
</script>

<template>
    <div
        :class="cn('pointer-events-none h-24 w-40 opacity-40', props.class)"
        :style="{
            backgroundImage:
                'radial-gradient(circle, #C7BFFF 1.5px, transparent 1.5px)',
            backgroundSize: '16px 16px',
        }"
    />
</template>
