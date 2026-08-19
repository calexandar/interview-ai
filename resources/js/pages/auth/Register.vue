<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import { Building2, Lock, Mail, Phone, User } from '@lucide/vue';
import AuthInput from '@/components/Auth/AuthInput.vue';
import AuthDivider from '@/components/Auth/AuthDivider.vue';
import SocialAuthButton from '@/components/Auth/SocialAuthButton.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { login } from '@/routes';
import { store } from '@/routes/register';

defineProps<{
    passwordRules: string;
}>();
</script>

<template>
    <Head title="Register" />

    <Form
        v-bind="store.form()"
        :reset-on-success="['password', 'password_confirmation']"
        v-slot="{ errors, processing }"
        class="flex flex-col gap-5"
    >
        <AuthInput
            id="name"
            name="name"
            label="Full name"
            :icon="User"
            placeholder="Enter your full name"
            autocomplete="name"
            autofocus
            required
            :tabindex="1"
            :error="errors.name"
        />

        <AuthInput
            id="email"
            name="email"
            type="email"
            label="Work email"
            :icon="Mail"
            placeholder="Enter your work email"
            autocomplete="email"
            required
            :tabindex="2"
            :error="errors.email"
        />

        <AuthInput
            id="company"
            name="company"
            label="Company name"
            :icon="Building2"
            placeholder="Enter your company name"
            autocomplete="organization"
            required
            :tabindex="3"
            :error="errors.company"
        />

        <PasswordInput
            id="password"
            name="password"
            label="Password"
            :icon="Lock"
            placeholder="Create a password"
            autocomplete="new-password"
            required
            :tabindex="4"
            :passwordrules="passwordRules"
            :error="errors.password"
        />

        <PasswordInput
            id="password_confirmation"
            name="password_confirmation"
            label="Confirm password"
            :icon="Lock"
            placeholder="Confirm your password"
            autocomplete="new-password"
            required
            :tabindex="5"
            :passwordrules="passwordRules"
            :error="errors.password_confirmation"
        />

        <AuthInput
            id="phone"
            name="phone"
            type="tel"
            label="Phone number (optional)"
            :icon="Phone"
            placeholder="Enter your phone number"
            autocomplete="tel"
            :tabindex="6"
            :error="errors.phone"
        />

        <div class="flex items-start gap-3 pt-1">
            <Checkbox id="terms" name="terms" :tabindex="7" class="mt-0.5" />
            <Label for="terms" class="text-sm leading-snug text-gray-600">
                I agree to the
                <Link href="#" class="font-medium text-purple-600 hover:underline">Terms of Service</Link>
                and
                <Link href="#" class="font-medium text-purple-600 hover:underline">Privacy Policy</Link>
            </Label>
        </div>

        <Button
            type="submit"
            class="mt-2 h-12 w-full rounded-xl bg-[#6D5DF5] text-sm font-medium hover:bg-[#5E4DE3]"
            :tabindex="8"
            :disabled="processing"
            data-test="register-user-button"
        >
            <Spinner v-if="processing" class="mr-2" />
            {{ processing ? 'Creating account...' : 'Create account' }}
        </Button>

        <AuthDivider />

        <div class="flex gap-3">
            <SocialAuthButton label="Google" disabled />
            <SocialAuthButton label="Microsoft" disabled />
        </div>

        <div class="pt-2 text-center text-sm text-gray-500">
            Already have an account?
            <TextLink :href="login()" class="font-medium text-purple-600" :tabindex="9">
                Sign in
            </TextLink>
        </div>
    </Form>
</template>
