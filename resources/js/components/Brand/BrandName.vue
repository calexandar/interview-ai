<script setup lang="ts">
interface Props {
    tagline?: boolean;
    class?: string;
}

const props = withDefaults(defineProps<Props>(), {
    tagline: false,
    class: '',
});
</script>

<template>
    <div :class="['flex items-center gap-2', props.class]">
        <span class="text-lg font-semibold tracking-tight text-foreground">
            Skillora
        </span>
        <span v-if="props.tagline" class="text-sm text-muted-foreground">
            AI-powered technical interviews
        </span>
    </div>
</template>
