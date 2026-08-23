<script setup lang="ts">
import type { Component, HTMLAttributes } from "vue"
import { cn } from "@/lib/utils"

const props = defineProps<{
  class?: HTMLAttributes["class"]
  icon?: Component
  title: string
  description?: string
}>()
</script>

<template>
  <div
    data-slot="empty-state"
    :class="
      cn(
        'flex flex-col items-center justify-center gap-2 rounded-lg border border-dashed border-border bg-muted/30 px-6 py-10 text-center',
        props.class,
      )
    "
    role="status"
  >
    <div
      v-if="icon || $slots.icon"
      class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary/10 text-primary"
    >
      <slot name="icon">
        <component :is="icon" class="h-5 w-5" />
      </slot>
    </div>
    <p class="text-sm font-medium text-foreground">
      {{ title }}
    </p>
    <p v-if="description" class="max-w-sm text-sm text-muted-foreground">
      {{ description }}
    </p>
    <div v-if="$slots.action" class="mt-2">
      <slot name="action" />
    </div>
  </div>
</template>
