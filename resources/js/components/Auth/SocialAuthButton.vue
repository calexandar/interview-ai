<script setup lang="ts">
defineProps<{
    label: string;
    disabled?: boolean;
}>();
</script>

<template>
    <button
        type="button"
        :disabled="disabled"
        class="flex h-12 flex-1 items-center justify-center gap-2 rounded-xl border border-gray-200 bg-white text-sm font-medium text-gray-700 transition-colors duration-200 hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-50 disabled:hover:bg-white"
    >
        <slot />
        {{ label }}
    </button>
</template>
