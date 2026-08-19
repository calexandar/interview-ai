# OpenCode Prompt — Recreate the Skillora Registration Page

## Objective

Replace the default Laravel registration page with a polished, production-ready registration experience for **Skillora**, an AI-powered technical interviewing platform.

The goal is to recreate the provided registration design as closely as possible while keeping the implementation clean, responsive, accessible, and aligned with the existing Laravel + Inertia + Vue + Tailwind architecture.

The registration page should feel like a premium SaaS application rather than a default Laravel authentication screen.

---

# 1. Existing Stack

The project uses:

- Laravel 13
- PHP 8.4+
- Inertia.js 3
- Vue 3
- TypeScript
- Tailwind CSS 4
- Laravel authentication
- Vite

Use the project's existing authentication implementation.

**Do not replace the authentication system.**

Only replace/rework the registration UI.

---

# 2. Reference Design

Use the provided registration screenshot as the visual reference.

The design consists of:

```text
┌───────────────────────────────────────────────────────────────┐
│                                                               │
│  LEFT MARKETING PANEL             RIGHT REGISTRATION CARD     │
│                                                               │
│  Skillora                         Create your account         │
│  AI-powered technical interviews                             │
│                                                               │
│  Create your account             Full name                   │
│  Start smarter hiring today      [____________________]      │
│                                                               │
│  Description                      Work email                 │
│                                   [____________________]      │
│  ✦ AI-Powered Interviews          Company name               │
│    Conduct technical              [____________________]      │
│    interviews that adapt...       Password                   │
│                                   [____________________]      │
│  ✦ Data-Driven Insights          Confirm password            │
│    Get detailed assessments...    [____________________]      │
│                                                               │
│  ✓ Secure & Trustworthy           Phone number (optional)    │
│                                   [____________________]      │
│  Dashboard preview                                           │
│                                   ☑ I agree to Terms...       │
│                                   [ Create account ]          │
│                                   ─── or sign up with ───     │
│                                   [ Google ] [ Microsoft ]   │
│                                   Already have an account?   │
│                                   Sign in                    │
│                                                               │
└───────────────────────────────────────────────────────────────┘
The implementation should preserve this overall composition.

3. Page Layout

Use a full-screen layout.

The page should have:

min-height: 100vh

Use a very subtle off-white/lavender background.

Suggested:

background: #F7F7FB

The page should not look like a standard centered Laravel authentication card.

Instead use a two-column layout on desktop.

4. Desktop Layout

On large screens:

┌───────────────────────┬─────────────────────────────┐
│                       │                             │
│   Marketing           │      Registration           │
│   Section             │      Form                   │
│                       │                             │
│   ~45% width          │      ~55% width              │
│                       │                             │
└───────────────────────┴─────────────────────────────┘

Recommended:

grid-cols-[0.9fr_1.1fr]

or an equivalent responsive grid.

The right registration section should have enough width for the form without becoming excessively wide.

Maximum form/card width:

~600px
5. Overall Spacing

Use generous whitespace.

The page should feel spacious.

Recommended desktop padding:

px-8
lg:px-12
xl:px-16

Vertical padding:

py-8
lg:py-12

Do not let the content touch the viewport edges.

6. Left Marketing Panel

The left side should communicate the value of Skillora.

It should NOT look like a normal login form.

Use a subtle lavender background treatment.

Suggested background:

#F7F5FF

or a very subtle radial gradient.

Avoid a strong purple gradient.

7. Skillora Logo / Brand

At the top-left create a simple brand lockup.

Example:

✦ Skillora
  AI-powered technical interviews

The icon should be a small sparkle/AI symbol.

Use:

text-purple-600

for the icon.

Brand name:

Skillora

Typography:

font-semibold
text-xl

Subtitle:

text-sm
text-gray-500

The logo should be reusable.

Create:

resources/js/Components/Brand/SkilloraLogo.vue

Do not duplicate the logo markup across pages.

8. Left Hero Heading

Use:

Create your
account

with a large heading.

Recommended:

text-4xl
lg:text-5xl
font-semibold
tracking-tight

Color:

#17171C

Below it:

Start smarter hiring today

Use the primary purple.

Recommended:

text-lg
font-semibold
text-purple-600
9. Marketing Description

Use:

Join Skillora and transform the way you interview and hire top technical talent with the power of AI.

Style:

text-base
leading-7
text-gray-600

Maximum width:

max-w-md

Do not make this paragraph too wide.

10. Feature List

Create three feature blocks.

Feature 1

Title:

AI-Powered Interviews

Description:

Conduct technical interviews that adapt to each candidate in real-time.

Icon:

Users / UserRound
Feature 2

Title:

Data-Driven Insights

Description:

Get detailed assessments and skill breakdowns to make confident decisions.

Icon:

BarChart3
Feature 3

Title:

Secure & Trustworthy

Description:

Enterprise-grade security to keep your data and candidates safe.

Icon:

ShieldCheck

Use Lucide icons if the project already has them.

If not, install/use the project's existing icon solution rather than introducing multiple icon libraries.

11. Feature Icon Design

Each feature should have a small rounded square icon container.

Example:

┌──────────┐
│    ✦     │
└──────────┘

Dimensions:

48px × 48px

Border radius:

12px

Background:

#F0EBFF

Icon:

#6D5DF5

The third security icon can use a subtle green treatment.

Do not make the feature cards visually heavy.

12. Dashboard Preview

At the bottom-left include a small decorative dashboard preview.

This is not a functional dashboard.

It is a visual marketing element.

Create:

DashboardPreview.vue

The preview should resemble the Skillora recruiter dashboard.

Example:

┌───────────────────────────────────┐
│ ▣   Dashboard                     │
│                                   │
│ ┌───────┐ ┌───────┐ ┌───────┐     │
│ │ 128   │ │ 342   │ │ 56    │     │
│ │Interv.│ │Cand.  │ │Hires  │     │
│ └───────┘ └───────┘ └───────┘     │
│                                   │
│ Recent Interviews                 │
│                                   │
│ ● Candidate        ━━━━━━━ 8.5    │
│ ● Candidate        ━━━━━━  7.8    │
│ ● Candidate        ━━━━━━━ 9.1    │
└───────────────────────────────────┘

Use fake static data.

Clearly keep this component decorative.

Do not connect it to the backend.

13. Dashboard Preview Styling

The preview should be:

background: white
border: 1px solid #E8E8EF
border-radius: 16px
box-shadow: subtle

Use slightly smaller typography.

It should look like a real application screenshot.

Do not make it too large.

Recommended:

max-w-md
14. Decorative Background

Add subtle decorative elements around the dashboard preview.

For example:

Small dots
Soft radial glow
Small circles
Very subtle lavender shapes

Do not use excessive decoration.

The reference uses a subtle dotted pattern near the lower-left area.

Recreate this with CSS/Tailwind if possible.

Do not add a large image asset just for the dots.

15. Right Registration Card

The registration form is the primary interactive element.

Place it inside a large white card.

Recommended:

background: white
border: 1px solid #E8E8EF
border-radius: 18px
box-shadow: 0 10px 30px rgba(...)

Keep the shadow subtle.

The card should feel elevated from the page background.

16. Registration Card Width

Use:

w-full
max-w-[600px]

The form should have comfortable horizontal padding.

Desktop:

px-10
lg:px-12

Mobile:

px-6
17. Form Header

At the top:

Create your account

Use:

text-3xl
font-semibold
tracking-tight

Below:

Fill in the details below to get started.

Style:

text-sm
text-gray-500
18. Form Fields

Use the following fields.

Full Name

Label:

Full name

Placeholder:

Enter your full name
Work Email

Label:

Work email

Placeholder:

Enter your work email
Company Name

Label:

Company name

Placeholder:

Enter your company name
Password

Label:

Password

Placeholder:

Create a password

Include an eye icon to toggle visibility.

Confirm Password

Label:

Confirm password

Placeholder:

Confirm your password

Include an eye icon to toggle visibility.

Phone Number

Label:

Phone number

Add:

(optional)

Placeholder:

Enter your phone number
19. Input Design

Inputs should look modern and lightweight.

Example:

height: 50-52px
border-radius: 10-12px
border: 1px solid #E2E2EA
background: white

Default:

border-gray-200

Focus:

border-purple-500
ring-purple-500/10

The focus state should be clearly visible.

20. Input Icons

Use subtle icons inside inputs.

Suggested:

Full name:
User


Work email:
Mail


Company:
Building2


Password:
Lock


Confirm password:
Lock


Phone:
Phone

Icon style:

size: 18px
text-gray-400

Icons should not overpower the field.

21. Password Hint

Under the password input display:

Minimum 8 characters with number and special character

Use:

text-xs
text-gray-500

Only display this when appropriate.

Do not clutter the form with unnecessary validation messages.

22. Validation

Use Laravel/Inertia validation.

Errors should appear directly below the relevant field.

Example:

Work email
[________________________]


Please enter a valid work email address.

Error style:

text-sm
text-red-600

The input should also receive an error border.

Do not use browser-only validation as the primary validation mechanism.

Server-side Laravel validation remains authoritative.

23. Terms Checkbox

Below the form fields:

☑ I agree to the Terms of Service and Privacy Policy

Use a custom styled checkbox compatible with the existing design.

Terms links:

Terms of Service
Privacy Policy

Use the primary purple.

Do not make the entire sentence purple.

24. Create Account Button

Primary CTA:

Create account

Button should be full width.

Height:

50-52px

Border radius:

10-12px

Background:

#6D5DF5

Hover:

#5E4DE3

Text:

white
font-medium

Include a subtle transition.

25. Button Loading State

When the registration request is processing:

Creating account...

Show a small spinner.

The button must be disabled.

Do not allow multiple submissions.

Use Inertia's existing form processing state.

26. Social Registration

Below the primary button:

────────── or sign up with ──────────

Then:

[ Google ]    [ Microsoft ]

The buttons should have:

white background
border
rounded

They should not compete visually with the primary CTA.

If social authentication is not implemented in the backend:

Do not create fake working buttons.

Either:

hide them until available, or
render them as clearly disabled/coming-soon controls.

Do not implement fake authentication.

27. Login Link

At the bottom:

Already have an account?
Sign in

Use:

text-gray-500

for the first part.

Use:

text-purple-600
font-medium

for:

Sign in

Link to the existing Laravel login route.

Use Inertia navigation.

28. Bottom Legal Text

Under the registration card, include:

By signing up, you agree to our Terms of Service and Privacy Policy.

This should be subtle.

Use:

text-xs
text-gray-500

Center it below the card.

On mobile, ensure it wraps naturally.

29. Responsive Behavior
Desktop

Show:

Left marketing panel
+
Right registration form
Tablet

Keep the two-column layout if there is enough room.

Otherwise collapse into:

Marketing
↓
Registration
Mobile

The registration experience should become a single-column page.

Hide the decorative dashboard preview.

Simplify the marketing section.

Suggested order:

Skillora logo


Create your account
Start smarter hiring today


Registration card

Do not force the large marketing hero onto a small screen.

30. Mobile Form

On mobile:

px-4

Card:

rounded-xl

Inputs should remain at least:

48px

high.

The CTA should remain full width.

Avoid horizontal scrolling.

31. Accessibility

The form must be accessible.

Implement:

Proper <label> elements
for/id relationships
Keyboard navigation
Visible focus state
autocomplete attributes
Accessible password visibility buttons
Accessible validation errors
aria-invalid
aria-describedby
Screen-reader-friendly error messages

Suggested autocomplete:

name
email
organization
new-password
tel
32. Password Visibility

Implement reusable:

PasswordInput.vue

Props should support:

label
modelValue
error
autocomplete
placeholder

The eye button must:

Toggle password visibility
Be keyboard accessible
Have an accessible label
Not submit the form
33. Form Component Architecture

Create a reusable registration form component.

Suggested structure:

resources/js/
├── Components/
│   ├── Brand/
│   │   └── SkilloraLogo.vue
│   │
│   ├── Auth/
│   │   ├── AuthInput.vue
│   │   ├── PasswordInput.vue
│   │   ├── SocialAuthButton.vue
│   │   └── AuthDivider.vue
│   │
│   └── Marketing/
│       ├── FeatureItem.vue
│       └── DashboardPreview.vue
│
└── Pages/
    └── Auth/
        └── Register.vue

Adapt this to the project's existing structure if it already has equivalent components.

Do not create duplicates.

34. Inertia Form

Use the existing Inertia form mechanism.

The Vue component should handle:

form.name
form.email
form.company
form.password
form.password_confirmation
form.phone

Use the existing Laravel registration endpoint.

Do not move registration logic into Vue.

35. Backend Compatibility

Before changing the frontend:

Inspect the current registration route.
Inspect the current registration controller/action.
Inspect the current validation rules.
Inspect the User model.
Inspect whether the application already has an Organization model.
Inspect how organization/company registration is currently handled.

Do not assume that:

company_name
phone

already exist in the database.

If the current backend does not support these fields, determine the smallest backend change required.

Do not silently create fields that are not persisted.

36. Organization Handling

This is an important part of the product architecture.

Skillora is a multi-tenant SaaS application.

The registration flow should eventually create:

User
Organization
OrganizationMembership

Conceptually:

Registration
      ↓
Create User
      ↓
Create Organization
      ↓
Create Owner Membership

However, first inspect the existing application architecture.

If organization/tenant support already exists, use it.

Do not create a second organization system.

37. Do Not Break Authentication

The following must continue to work:

Registration
Login
Logout
Forgot Password
Reset Password
Email Verification

Only modify what is necessary for the new registration experience.

38. Error States

Handle:

Email already exists
An account with this email already exists.
Sign in instead.
Password mismatch
Passwords do not match.
Server error
We couldn't create your account.
Please try again.
Validation errors

Display them inline.

Do not display raw Laravel exception messages.

39. Visual Details

Pay attention to the following small details:

Cards

Use:

rounded-2xl
border
shadow-sm

not heavy shadows.

Buttons

Use:

transition
duration-200
Inputs

Use subtle focus rings.

Labels

Keep them close to their inputs.

Sections

Use consistent vertical rhythm:

space-y-5

or similar.

Heading

Use tighter letter spacing.

40. Avoid Overengineering

Do not introduce:

A new UI framework
Bootstrap
Vuetify
Element Plus
Material UI
A new form library
A new state management library
A new authentication system

Use:

Vue
Inertia
Tailwind
existing Laravel authentication
41. Do Not Use Hardcoded Fake Backend Data

The decorative dashboard preview may use static data.

The actual registration form must use real backend state.

Do not create fake:

users
organizations
authentication responses

just to make the design appear functional.

42. Animation Guidelines

Use subtle animations.

Page:

fade-in

Dashboard preview:

subtle fade/slide

Button:

hover transition

Validation:

small fade-in

Do not animate every element.

The page should feel fast.

43. Final Acceptance Criteria

The implementation is complete when:

[ ] Default Laravel registration UI is replaced.


[ ] Page matches the provided visual reference.


[ ] Skillora branding is present.


[ ] Two-column desktop layout works.


[ ] Mobile layout works.


[ ] Marketing section is implemented.


[ ] Feature blocks are implemented.


[ ] Dashboard preview is implemented.


[ ] Registration card matches the design.


[ ] Full name field works.


[ ] Work email field works.


[ ] Company field works if supported by backend.


[ ] Password field works.


[ ] Password confirmation works.


[ ] Password visibility toggle works.


[ ] Phone field works if supported by backend.


[ ] Terms checkbox works.


[ ] Laravel validation errors are displayed.


[ ] Registration loading state works.


[ ] Duplicate submission is prevented.


[ ] Login link works.


[ ] Existing Laravel authentication remains functional.


[ ] Accessibility requirements are met.


[ ] No fake authentication functionality exists.


[ ] No unnecessary dependencies are introduced.


[ ] Tailwind CSS 4 is used.


[ ] Vue 3 + TypeScript is used.


[ ] Components are reusable.


[ ] No business logic is placed inside UI components.
44. Most Important Instruction

Do not simply make the default Laravel registration form look purple.

Recreate the complete product experience shown in the reference:

                SKILLORA
                   │
        AI-powered technical interviews
                   │
        ┌──────────┴──────────┐
        │                     │
        ▼                     ▼
  Product positioning     Registration
  + features              experience
  + dashboard preview

The result should look like the first page of a real commercial SaaS product.

The user should immediately understand:

Skillora helps companies conduct better technical interviews with AI.

And the registration form should make signing up feel simple, trustworthy, and professional.