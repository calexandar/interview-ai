<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { ArrowRight, Lock, Mail } from '@lucide/vue';
import AuthDivider from '@/components/Auth/AuthDivider.vue';
import AuthInput from '@/components/Auth/AuthInput.vue';
import SocialAuthButton from '@/components/Auth/SocialAuthButton.vue';
import PasskeyVerify from '@/components/PasskeyVerify.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { register } from '@/routes';
import { store } from '@/routes/login';
import { request } from '@/routes/password';

defineOptions({
    layout: {
        title: 'Log in to your account',
        description: 'Enter your email and password below to log in',
    },
});

defineProps<{
    status?: string;
    canResetPassword: boolean;
}>();
</script>

<template>
    <Head title="Log in" />

    <div>
        <div class="mb-8 text-center">
            <h2 class="text-3xl font-semibold tracking-tight text-[#17182B]">
                Welcome back
            </h2>
            <p class="mt-2 text-sm text-gray-500">
                Log in to your account to continue.
            </p>
        </div>

        <div
            v-if="status"
            class="mb-4 rounded-xl bg-[#EAF8F1] px-4 py-3 text-center text-sm font-medium text-[#22A06B]"
        >
            {{ status }}
        </div>

        <PasskeyVerify
            label="Sign in with a passkey"
            hide-separator
            button-class="h-11 rounded-xl border-[#6D4AFF]! bg-white! text-sm font-medium text-[#6D4AFF]! transition-colors duration-200 hover:bg-[#F0EBFF]!"
        />

        <AuthDivider label="or continue with email" class="my-5" />

        <Form
            v-bind="store.form()"
            :reset-on-success="['password']"
            v-slot="{ errors, processing }"
            class="flex flex-col gap-5"
        >
            <AuthInput
                id="email"
                name="email"
                type="email"
                label="Work email"
                :icon="Mail"
                placeholder="Enter your work email"
                autocomplete="email"
                required
                autofocus
                :tabindex="1"
                :error="errors.email"
            />

            <div class="grid gap-2">
                <div class="flex items-center justify-between">
                    <Label
                        for="password"
                        class="text-sm font-medium text-gray-700"
                    >
                        Password
                    </Label>
                    <TextLink
                        v-if="canResetPassword"
                        :href="request()"
                        class="text-sm font-medium text-[#6D4AFF]! no-underline!"
                        :tabindex="5"
                    >
                        Forgot your password?
                    </TextLink>
                </div>
                <PasswordInput
                    id="password"
                    name="password"
                    required
                    :icon="Lock"
                    :tabindex="2"
                    placeholder="Enter your password"
                    autocomplete="current-password"
                    hide-hint
                    :error="errors.password"
                />
            </div>

            <Label
                for="remember"
                class="flex items-center gap-2.5 text-sm font-normal text-gray-700"
            >
                <Checkbox
                    id="remember"
                    name="remember"
                    :tabindex="3"
                    class="data-[state=checked]:border-[#6D4AFF] data-[state=checked]:bg-[#6D4AFF]"
                />
                Keep me signed in
            </Label>

            <Button
                type="submit"
                class="mt-1 h-[52px] w-full rounded-xl bg-[#6D4AFF] text-base font-medium text-white transition-colors duration-200 hover:bg-[#5E3DE5]"
                :tabindex="4"
                :disabled="processing"
                data-test="login-button"
            >
                <Spinner v-if="processing" />
                <template v-if="processing">Signing in...</template>
                <template v-else>
                    Log in
                    <ArrowRight class="h-4 w-4" />
                </template>
            </Button>
        </Form>

        <AuthDivider label="or continue with" />

        <div class="grid grid-cols-2 gap-3">
            <SocialAuthButton label="Google" disabled tabindex="-1">
                <svg viewBox="0 0 24 24" class="h-4 w-4" aria-hidden="true">
                    <path
                        fill="#4285F4"
                        d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 0 1-2.2 3.32v2.77h3.57c2.08-1.92 3.27-4.74 3.27-8.1Z"
                    />
                    <path
                        fill="#34A853"
                        d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84A11 11 0 0 0 12 23Z"
                    />
                    <path
                        fill="#FBBC05"
                        d="M5.84 14.1a6.6 6.6 0 0 1 0-4.2V7.06H2.18a11 11 0 0 0 0 9.88l3.66-2.84Z"
                    />
                    <path
                        fill="#EA4335"
                        d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15A11 11 0 0 0 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52Z"
                    />
                </svg>
            </SocialAuthButton>

            <SocialAuthButton label="Microsoft" disabled tabindex="-1">
                <svg viewBox="0 0 24 24" class="h-4 w-4" aria-hidden="true">
                    <path fill="#F25022" d="M1 1h10.5v10.5H1z" />
                    <path fill="#7FBA00" d="M12.5 1H23v10.5H12.5z" />
                    <path fill="#00A4EF" d="M1 12.5h10.5V23H1z" />
                    <path fill="#FFB900" d="M12.5 12.5H23V23H12.5z" />
                </svg>
            </SocialAuthButton>
        </div>
        <p class="mt-2 text-center text-xs text-gray-400">
            Social sign-in is coming soon.
        </p>

        <div class="mt-6 text-center text-sm text-gray-500">
            Don't have an account?
            <TextLink
                :href="register()"
                :tabindex="5"
                class="font-medium text-[#6D4AFF]! no-underline!"
            >
                Create one
            </TextLink>
        </div>
    </div>
</template>
