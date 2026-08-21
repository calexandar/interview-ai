<script setup lang="ts">
withDefaults(
    defineProps<{
        label?: string;
    }>(),
    {
        label: 'or continue with',
    },
);
</script>

<template>
    <div class="relative my-2">
        <div class="absolute inset-0 flex items-center">
            <div class="w-full border-t border-gray-200" />
        </div>
        <div class="relative flex justify-center text-sm">
            <span class="bg-white px-4 text-gray-400">{{ label }}</span>
        </div>
    </div>
</template>
