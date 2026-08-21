<script setup lang="ts">
import { Sparkles } from '@lucide/vue';

interface Props {
    tagline?: boolean;
    stacked?: boolean;
    class?: string;
}

const props = withDefaults(defineProps<Props>(), {
    tagline: false,
    stacked: false,
    class: '',
});
</script>

<template>
    <div v-if="props.stacked" :class="['flex flex-col gap-1.5', props.class]">
        <div class="flex items-center gap-2">
            <Sparkles class="h-5 w-5 text-[#6D4AFF]" />
            <span class="text-xl font-semibold tracking-tight text-[#17182B]">
                Skillora
            </span>
        </div>
        <span class="text-sm text-gray-500">
            AI-powered technical interviews
        </span>
    </div>

    <div v-else :class="['flex items-center gap-2', props.class]">
        <Sparkles class="h-5 w-5 text-[#6D4AFF]" />
        <span class="text-lg font-semibold tracking-tight text-[#17182B]">
            Skillora
        </span>
        <span v-if="props.tagline" class="text-sm text-muted-foreground">
            AI-powered technical interviews
        </span>
    </div>
</template>
