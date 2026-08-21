<script setup lang="ts">
defineProps<{
    quote: string;
    name: string;
    role: string;
    initials: string;
}>();
</script>

<template>
    <div
        class="max-w-[280px] rounded-[14px] border border-white/60 bg-white/65 p-4 shadow-[0_8px_30px_rgba(23,24,43,0.06)] backdrop-blur-sm"
    >
        <div class="font-serif text-2xl leading-none text-purple-300">
            &ldquo;
        </div>
        <p class="mt-1 text-sm leading-relaxed text-gray-700">
            {{ quote }}
        </p>
        <div class="mt-3 flex items-center gap-2.5">
            <div
                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-[#F0EBFF] text-[11px] font-semibold text-[#6D4AFF]"
            >
                {{ initials }}
            </div>
            <div>
                <div class="text-sm font-semibold text-[#17182B]">
                    {{ name }}
                </div>
                <div class="text-xs text-gray-500">{{ role }}</div>
            </div>
        </div>
    </div>
</template>
