<script setup lang="ts">
import { Eye, EyeOff, type LucideIcon } from '@lucide/vue';
import { ref, useTemplateRef } from 'vue';
import type { HTMLAttributes } from 'vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import InputError from '@/components/InputError.vue';
import { cn } from '@/lib/utils';

defineOptions({ inheritAttrs: false });

const props = withDefaults(defineProps<{
    class?: HTMLAttributes['class'];
    label?: string;
    error?: string;
    icon?: LucideIcon;
    placeholder?: string;
    autocomplete?: string;
    passwordrules?: string;
}>(), {
    label: undefined,
    error: undefined,
    icon: undefined,
    placeholder: 'Password',
    autocomplete: 'new-password',
    passwordrules: undefined,
});

const showPassword = ref(false);
const inputRef = useTemplateRef('inputRef');

defineExpose({
    $el: inputRef,
    focus: () => inputRef.value?.$el?.focus(),
});
</script>

<template>
    <div class="grid gap-2">
        <Label v-if="label" :for="$attrs.id as string" class="text-sm font-medium text-gray-700">
            {{ label }}
        </Label>
        <div class="relative">
            <component
                v-if="icon"
                :is="icon"
                class="pointer-events-none absolute left-3 top-1/2 h-[18px] w-[18px] -translate-y-1/2 text-gray-400"
            />
            <Input
                ref="inputRef"
                :type="showPassword ? 'text' : 'password'"
                :placeholder="placeholder"
                :autocomplete="autocomplete"
                :passwordrules="passwordrules"
                :class="cn(
                    'h-12 rounded-xl border-gray-200 bg-white dark:bg-white text-sm placeholder:text-gray-400 focus:border-purple-500 focus:ring-purple-500/10',
                    icon ? 'pl-10' : 'pl-3',
                    'pr-10',
                    error && 'border-red-500 focus:border-red-500 focus:ring-red-500/10',
                    props.class,
                )"
                v-bind="$attrs"
            />
            <button
                type="button"
                @click="showPassword = !showPassword"
                class="absolute inset-y-0 right-0 flex items-center rounded-r-md px-3 text-gray-400 hover:text-gray-600 focus-visible:ring-[3px] focus-visible:ring-purple-500/20 focus-visible:outline-none"
                :aria-label="showPassword ? 'Hide password' : 'Show password'"
                :tabindex="-1"
            >
                <EyeOff v-if="showPassword" class="h-[18px] w-[18px]" />
                <Eye v-else class="h-[18px] w-[18px]" />
            </button>
        </div>
        <p v-if="!error" class="text-xs text-gray-500">
            Minimum 8 characters with number and special character
        </p>
        <InputError :message="error" />
    </div>
</template>
