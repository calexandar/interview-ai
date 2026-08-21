<script setup lang="ts">
import type { Component } from 'vue';

defineProps<{
    icon: Component;
    title: string;
    description: string;
    iconVariant?: 'purple' | 'green';
}>();
</script>

<template>
    <div class="flex items-start gap-4">
        <div
            :class="[
                'flex h-12 w-12 shrink-0 items-center justify-center rounded-xl',
                iconVariant === 'green' ? 'bg-[#EAF8F1]' : 'bg-[#F0EBFF]',
            ]"
        >
            <component
                :is="icon"
                :class="[
                    'h-5 w-5',
                    iconVariant === 'green'
                        ? 'text-[#22A06B]'
                        : 'text-[#6D4AFF]',
                ]"
            />
        </div>
        <div>
            <h3 class="text-sm font-semibold text-[#17182B]">{{ title }}</h3>
            <p class="mt-1 text-sm leading-relaxed text-gray-500">
                {{ description }}
            </p>
        </div>
    </div>
</template>
