<script setup lang="ts">
import type { Component } from 'vue';
import type { HTMLAttributes } from 'vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import InputError from '@/components/InputError.vue';
import { cn } from '@/lib/utils';

const props = withDefaults(defineProps<{
    label: string;
    icon?: Component;
    error?: string;
    type?: string;
    placeholder?: string;
    autocomplete?: string;
    autofocus?: boolean;
    required?: boolean;
    tabindex?: number;
    class?: HTMLAttributes['class'];
}>(), {
    type: 'text',
    placeholder: '',
    autocomplete: undefined,
    autofocus: false,
    required: false,
    tabindex: undefined,
    class: undefined,
});
</script>

<template>
    <div class="grid gap-2">
        <Label :for="$attrs.id as string" class="text-sm font-medium text-gray-700">
            {{ label }}
        </Label>
        <div class="relative">
            <component
                v-if="icon"
                :is="icon"
                class="pointer-events-none absolute left-3 top-1/2 h-[18px] w-[18px] -translate-y-1/2 text-gray-400"
            />
            <Input
                :type="type"
                :placeholder="placeholder"
                :autocomplete="autocomplete"
                :autofocus="autofocus"
                :required="required"
                :tabindex="tabindex"
                :class="cn(
                    'h-12 rounded-xl border-gray-200 bg-white pl-10 text-sm placeholder:text-gray-400 focus:border-purple-500 focus:ring-purple-500/10',
                    error && 'border-red-500 focus:border-red-500 focus:ring-red-500/10',
                    props.class,
                )"
                v-bind="$attrs"
            />
        </div>
        <InputError :message="error" />
    </div>
</template>
