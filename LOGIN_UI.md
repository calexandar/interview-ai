# OpenCode Prompt — Recreate the Skillora Login Page

## Objective

Replace the default Laravel login page with a polished, production-ready login experience for **Skillora**, an AI-powered technical interviewing platform.

The goal is to recreate the provided login-page design as closely as possible.

The page should feel like a premium SaaS product used by professional hiring teams.

Do **not** create a generic centered Laravel login form.

The login page should combine:

- Skillora branding
- Product positioning
- AI-focused messaging
- Feature highlights
- A small dashboard preview
- Login form
- Social authentication options
- Security messaging
- Responsive behavior

The final result should visually match the provided reference image.

---

# 1. Existing Technology Stack

The project uses:

- Laravel 13
- PHP 8.4+
- Inertia.js 3
- Vue 3
- TypeScript
- Tailwind CSS 4
- Laravel authentication
- Vite

Use the existing project architecture.

The login page should be implemented with:

```text
Vue 3
+
TypeScript
+
Inertia.js
+
Tailwind CSS 4

2. Reference Design

Use the provided login screenshot as the primary visual reference.

The overall composition is:

┌─────────────────────────────────────────────────────────────────────┐
│                                                                     │
│  SKILLORA                                       Testimonial         │
│                                                                     │
│  Welcome back                                      ┌──────────────┐ │
│  Better interviews. Better hires.                 │ Quote        │ │
│                                                   │ Person       │ │
│  Product description                    ┌─────────┴──────────────┐ │
│                                        │                         │ │
│  ✦ Smarter Interviews                 │       Welcome back      │ │
│    Adaptive AI interviews             │                          │ │
│                                        │ Work email              │ │
│  ▣ Clearer Insights                   │ [______________________] │ │
│    Objective assessments              │                          │ │
│                                        │ Password                 │ │
│  ✓ A More Confident Hire              │ [______________________] │ │
│    Data-driven hiring                │                          │ │
│                                        │ ☑ Keep me signed in     │ │
│  ┌───────────────────────────────┐    │        Forgot password? │ │
│  │ Dashboard Preview             │    │                          │ │
│  │                               │    │ [       Log in →       ] │ │
│  │ 12  184  46  18               │    │                          │ │
│  │                               │    │ ─── or continue with ──  │ │
│  │ Recent interviews             │    │                          │ │
│  │ John Doe             8.4      │    │ [ Google ] [ Microsoft ] │ │
│  │ Sarah Miller         7.8      │    │                          │ │
│  │ Robert Johnson       9.1      │    │ Don't have an account?   │ │
│  └───────────────────────────────┘    │ Create one                │ │
│                                        └───────────────────────────┘ │
│  Trusted by innovative companies                                      │
│                                                                     │
└─────────────────────────────────────────────────────────────────────┘

The implementation should preserve this visual hierarchy.

3. Overall Design Direction

The design should feel:

Modern
Premium
Minimal
Professional
AI-native
Trustworthy
Calm
Spacious

Visual references:

Linear
+
Notion
+
Modern AI SaaS
+
Professional recruiting software

Avoid making it look like:

Bootstrap
Default Laravel Breeze
Generic authentication template
Cyberpunk AI
Excessive glassmorphism
Cryptocurrency dashboard
Gaming interface
4. Color Palette

Use a primarily light interface.

Suggested colors:

Background:
#F8F7FC


Primary:
#6D4AFF


Primary hover:
#5E3DE5


Primary light:
#F0EBFF


Text:
#17182B


Secondary text:
#667085


Muted:
#98A2B3


Border:
#E6E6EF


Card:
#FFFFFF


Success:
#22A06B


Success background:
#EAF8F1


Warning:
#E7A928


Warning background:
#FFF7E6

Purple should be the primary brand color.

Do not cover the entire page with purple.

Purple should primarily be used for:

Brand icon
Primary CTA
Active states
AI-related elements
Links
Small accents
5. Typography

Use the project's existing font if one is already configured.

Otherwise use:

Inter

or a similar modern sans-serif.

Typography:

Hero:
48-56px
font-semibold
tracking-tight


Form heading:
32-36px
font-semibold


Feature heading:
15-16px
font-semibold


Body:
15-16px


Metadata:
12-13px

The typography should have strong hierarchy but remain understated.

6. Page Background

The entire page should have a very subtle lavender/off-white background.

Use:

#F8F7FC

or a subtle equivalent.

Add a very soft radial purple glow behind the dashboard preview.

Example concept:

background:
  radial-gradient(
    circle at 35% 75%,
    rgba(109, 74, 255, 0.08),
    transparent 30%
  );

Keep it extremely subtle.

Do not create a strong gradient background.

7. Main Layout

Desktop layout:

min-height: 100vh


grid:
left section ≈ 48%
right section ≈ 52%

Recommended:

grid-cols-1
lg:grid-cols-[0.95fr_1.05fr]

The left side contains the marketing content.

The right side contains the login card.

8. Desktop Spacing

Use generous whitespace.

Recommended:

px-6
sm:px-8
lg:px-12
xl:px-16
2xl:px-20

Top/bottom:

py-8
lg:py-12

The content should never touch the viewport edge.

9. Skillora Brand

Create a reusable brand component:

resources/js/Components/Brand/SkilloraLogo.vue

Display:

✦ Skillora
AI-powered technical interviews

The sparkle icon should be purple.

Example layout:

┌──────────────────────────────┐
│ ✦ Skillora                   │
│   AI-powered technical       │
│   interviews                 │
└──────────────────────────────┘

Brand name:

font-semibold
text-xl

Subtitle:

text-sm
text-gray-500

Do not duplicate the logo markup.

10. Left Hero Section

Create a strong hero heading:

Welcome back

Recommended:

text-5xl
font-semibold
tracking-tight

Below it:

Better interviews.
Better hires.

The second line should use the primary purple.

Example:

Welcome back


Better interviews.
Better hires.

The second statement is the main product message.

11. Hero Description

Use:

Log in to your Skillora account and continue building strong technical teams with the power of AI.

Style:

text-base
leading-7
text-gray-600

Maximum width:

max-w-md

Do not make the paragraph too wide.

12. Feature List

Create three feature items.

Use a reusable:

resources/js/Components/Marketing/FeatureItem.vue

component.

Props should support:

icon
title
description
variant
Feature 1

Title:

Smarter Interviews

Description:

Adaptive, AI-powered interviews for every candidate.

Icon:

Zap
Feature 2

Title:

Clearer Insights

Description:

Objective assessments and skill breakdowns, all in one place.

Icon:

BarChart3
Feature 3

Title:

A More Confident Hire

Description:

Make hiring decisions with real data, not guesswork.

Icon:

ShieldCheck

Use Lucide icons if already installed.

Do not install a second icon library if the project already has one.

13. Feature Item Layout

Each item should be:

icon container
+
content

Example:

┌──────┐
│  ⚡  │   Smarter Interviews
└──────┘   Adaptive, AI-powered interviews
           for every candidate.

Icon container:

48px × 48px
rounded-xl
bg-purple-50

Icon:

text-purple-600

The third feature can use a subtle green accent.

14. Dashboard Preview

The dashboard preview is an important visual element.

Create:

resources/js/Components/Marketing/DashboardPreview.vue

This is decorative.

It should not use real backend data.

Use static example data.

15. Dashboard Preview Structure

Create a small application window:

┌─────────────────────────────────────────┐
│ ✦   Dashboard                            │
│                                         │
│ ┌───────┐ ┌───────┐ ┌───────┐ ┌───────┐ │
│ │  12   │ │  184  │ │  46   │ │  18   │ │
│ │Active │ │Cand.  │ │Interv.│ │Strong │ │
│ └───────┘ └───────┘ └───────┘ └───────┘ │
│                                         │
│ Recent Interviews                       │
│                                         │
│ ● John Doe       Senior Laravel  8.4    │
│ ● Sarah Miller   Frontend Dev    7.8    │
│ ● Robert Johnson Backend Dev     9.1    │
│                                         │
└─────────────────────────────────────────┘
16. Dashboard Statistics

Use:

Active Positions
12
+20%


Total Candidates
184
+12%


Interviews
46
+8%


Strong Candidates
18
+28%

Use small green trend indicators.

The preview should look realistic but remain visually simplified.

17. Dashboard Recent Interviews

Rows:

John Doe
Senior Laravel Developer
8.4
Completed


Sarah Miller
Frontend Developer
7.8
Completed


Robert Johnson
Backend Developer
9.1
Completed

Use initials instead of profile photos:

JD
SM
RJ

Avatar:

32px
rounded-full
bg-purple-50
text-purple-700
18. Dashboard Preview Styling

Use:

background: white
border: 1px solid #E5E7EB
border-radius: 18px
box-shadow: subtle

The preview should appear slightly elevated.

Do not use heavy shadows.

Recommended:

shadow-[0_20px_60px_rgba(...)]

with a very low opacity.

19. Dashboard Preview Glow

Place a subtle lavender glow behind the dashboard.

The glow should extend slightly outside the card.

Example:

absolute
blur-3xl
bg-purple-200/30

Do not allow it to dominate the page.

20. Decorative Dots

Near the lower-left corner of the dashboard preview add a dotted pattern.

Example:

• • • • •
• • • • •
• • • • •
• • • • •

Create it with CSS or a reusable decorative component.

Do not use an external image.

Keep opacity low.

21. Trusted Companies

Below the dashboard preview:

Trusted by innovative companies

Then show subtle technology/company references:

Laravel
Vue.js
AWS
Google

These should be treated as decorative examples.

Do not imply that these companies officially endorse Skillora.

If the product does not have real customers, use a safer label:

Built with technologies trusted by modern teams

Prefer this wording.

Use muted grayscale logos/icons.

22. Right Login Section

The right side should contain a large white login card.

Recommended:

w-full
max-w-[540px]

The card should be vertically centered.

23. Login Card

Style:

background: white
border: 1px solid #E7E7EF
border-radius: 18px
shadow: subtle

Padding:

Desktop:

px-10
py-10

Large desktop:

px-12
py-12

Mobile:

px-6
py-8
24. Login Header

Use:

Welcome back

as the form heading.

Below:

Log in to your account to continue.

Heading:

text-3xl
font-semibold
tracking-tight
text-gray-900

Subtitle:

text-sm
text-gray-500

Center-align the form heading.

25. Work Email

Label:

Work email

Placeholder:

Enter your work email

Input should include a mail icon.

Example:

┌───────────────────────────────────────────┐
│ ✉  Enter your work email                  │
└───────────────────────────────────────────┘

Use:

type="email"
autocomplete="email"
26. Password

Label:

Password

Placeholder:

Enter your password

Input includes:

Lock icon
Eye icon

Use:

type="password"
autocomplete="current-password"

The eye button must toggle visibility.

It must be keyboard accessible.

27. Password Visibility Component

Create:

resources/js/Components/Auth/PasswordInput.vue

Support:

label
modelValue
error
placeholder
autocomplete

Features:

Password visibility toggle
Accessible button
Error state
Focus state
Disabled state

The visibility button must not submit the form.

28. Remember Me

Below the password:

☑ Keep me signed in

Use a custom checkbox.

Style:

accent-purple

Keep the checkbox small.

29. Forgot Password

Align to the right:

Forgot your password?

Use the existing Laravel password reset route.

Style:

text-purple-600
font-medium

Do not hard-code the URL if the application already exposes the route.

Use Laravel route helpers or the project's existing route mechanism.

30. Login Button

Primary CTA:

Log in →

Full width.

Height:

50-52px

Style:

bg-purple-600
hover:bg-purple-700
text-white
rounded-xl
font-medium

Use a subtle hover transition.

Do not use a giant gradient.

31. Login Loading State

When submitting:

Signing in...

Display a spinner.

The button must become disabled.

Prevent multiple submissions.

Use Inertia's processing state.

Do not implement custom loading state management if Inertia already provides it.

32. Divider

Below the login button:

────────────  or continue with  ────────────

The divider should be subtle.

Use:

border-gray-200
text-gray-400
33. Social Login

Show:

[ Google ]    [ Microsoft ]

Buttons:

white
border-gray-200
rounded-xl
height: 48-50px

Use the official recognizable icons.

Do not create fake social authentication.

If Google/Microsoft authentication is not currently implemented:

either hide these buttons
or render them disabled with an appropriate implementation note

Do not pretend they work.

34. Account Creation Link

At the bottom:

Don't have an account?
Create one

Use the existing Laravel registration route.

Example visual:

Don't have an account?  Create one
                         ^ purple

The link should use Inertia navigation.

35. Security Message

Below the login card add:

Your data is secure with enterprise-grade security
and encryption.

Add a small shield icon.

Use muted text.

This should be subtle and should not make unsupported security claims if the backend does not actually provide enterprise-grade security.

If the application is not yet enterprise-grade, use:

Your account and interview data are protected with secure authentication.

Prefer truthful wording.

36. Testimonial

In the upper-right area of the page create a small floating testimonial card.

Example:

“Skillora has completely changed
how we screen technical talent.”


Emily Carter
Head of Engineering, Pixelate

Use a small avatar.

The testimonial is decorative placeholder content.

Make it easy to remove later.

Create:

resources/js/Components/Marketing/TestimonialCard.vue

Do not hard-code it directly inside Login.vue.

37. Testimonial Styling

Card:

background: rgba(255,255,255,0.65)
border: 1px solid rgba(...)
border-radius: 14px
backdrop-blur

Keep the effect subtle.

Quote mark:

text-purple-300

Avatar:

36px
rounded-full

Text:

text-sm

The testimonial should not distract from the login form.

38. Footer

At the bottom of the page use:

Skillora
•
AI-powered technical interviews
•
Smarter hiring for a stronger tomorrow.

Keep it very subtle.

Desktop:

centered

Mobile:

wrapped
text-center
39. Responsive Design

The page must be fully responsive.

Desktop

Show:

Marketing section
+
Login card
+
Testimonial
Tablet

Use a reduced marketing section.

If necessary:

Marketing
      ↓
Login

Do not allow the login form to become too narrow.

Mobile

The priority is the login form.

Use:

Skillora logo
↓
Login card
↓
Sign-up link

The following can be hidden or simplified:

Dashboard preview
Testimonial
Trusted technology section
Decorative dots

Keep a small version of:

Welcome back

and the product tagline.

40. Mobile Layout

Use:

px-4

for the page.

Login card:

rounded-xl

Inputs:

min-height: 48px

Button:

min-height: 50px

Avoid horizontal scrolling.

41. Accessibility

Implement:

Semantic HTML
Proper <label> elements
for / id relationships
Keyboard navigation
Visible focus states
Accessible password visibility button
aria-invalid
aria-describedby
Screen-reader-friendly errors
autocomplete attributes

Recommended:

email:
autocomplete="email"


password:
autocomplete="current-password"

Remember-me checkbox:

autocomplete / browser-compatible
42. Validation

Use Laravel/Inertia validation.

Display errors below the relevant input.

Example:

Work email


[ Enter your work email ]


Please enter a valid email address.

Error styling:

text-red-600

Input:

border-red-400

Do not rely only on browser validation.

Laravel validation remains authoritative.

43. Authentication Compatibility

Before modifying the UI, inspect:

routes/web.php


login route


login controller/action


authentication request


User model


password reset routes


registration route

Do not change authentication behavior unless necessary.

The page must continue to work with the existing Laravel authentication system.

44. Existing Laravel Login

If the project currently uses Laravel Breeze or a similar authentication implementation:

Keep its backend behavior.

Only replace the presentation layer.

For example:

POST /login

should continue to use the existing authentication endpoint.

Do not create a new:

/api/login

unless the project already uses that architecture.

45. Suggested Vue Structure

Use:

resources/js/
├── Components/
│   ├── Auth/
│   │   ├── AuthInput.vue
│   │   ├── PasswordInput.vue
│   │   ├── SocialAuthButton.vue
│   │   └── AuthDivider.vue
│   │
│   ├── Brand/
│   │   └── SkilloraLogo.vue
│   │
│   └── Marketing/
│       ├── FeatureItem.vue
│       ├── DashboardPreview.vue
│       └── TestimonialCard.vue
│
└── Pages/
    └── Auth/
        └── Login.vue

Adapt this to the existing project structure if equivalent components already exist.

Do not duplicate existing components.

46. Login.vue Responsibilities

Login.vue should primarily compose the page.

It should contain:

Page layout
Marketing section
Login card

It should NOT contain:

AI business logic
Database logic
Authentication implementation
API clients
Large reusable UI components
Dashboard data logic

Keep the component readable.

47. Form State

Use the project's existing Inertia form approach.

Conceptually:

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

Submit using the existing login route.

Example conceptual flow:

Submit
  ↓
Inertia POST
  ↓
Laravel authentication
  ↓
Success → dashboard
  ↓
Validation error → display errors

Do not reinvent this flow.

48. Error States

Handle:

Invalid credentials

Display:

The email or password you entered is incorrect.

Do not expose sensitive authentication details.

Validation errors

Display inline.

Rate limiting

If Laravel returns a throttling error, show a friendly message:

Too many login attempts.
Please wait a moment and try again.
Server error

Show:

We couldn't sign you in right now.
Please try again.

Do not expose stack traces.

49. Empty / Loading States

Login should not need an empty state.

Loading state is important.

Use:

Logging in...

or:

Signing in...

with a spinner.

50. Animation

Keep animations subtle.

Use:

200-300ms

for:

Button hover
Input focus
Card appearance
Error appearance

Optional page entrance:

opacity 0 → 1
translateY 4px → 0

Avoid:

Large sliding animations
Continuous animations
Flashing
Animated gradients
Excessive parallax
51. Performance

Do not add heavy assets.

The dashboard preview should be built with HTML/CSS/Vue components.

Do not use a screenshot image for the dashboard preview.

Do not load large images for decorative purposes.

The page should remain fast.

52. Avoid Unnecessary Dependencies

Do not install:

Bootstrap
Vuetify
Element Plus
Material UI
another CSS framework
another form library
another state management library

Use the existing stack.

If an icon library already exists, use it.

If Lucide is already installed, use Lucide.

53. Do Not Fake Functionality

Do not create fake:

Google authentication
Microsoft authentication
Dashboard data
Authentication responses
Security guarantees

The dashboard preview may contain static visual data because it is explicitly decorative.

Actual authentication must use Laravel.

54. Visual Hierarchy

The visual priority must be:

1. Login form
2. Welcome back heading
3. Log in CTA
4. Product branding
5. Marketing message
6. Feature benefits
7. Dashboard preview
8. Testimonial
9. Decorative elements

The user should immediately understand where to log in.

55. Final Visual Target

The final page should communicate:

Welcome back. Continue building better technical teams with AI.

It should look like a real commercial SaaS product.

The overall visual language:

Soft lavender background
        +
White elevated card
        +
Purple primary CTA
        +
Dark navy typography
        +
Subtle shadows
        +
Rounded corners
        +
Generous whitespace
        +
Small AI accents
56. Acceptance Criteria

The implementation is complete when:

[ ] Default Laravel login UI is replaced.


[ ] Login page matches the supplied reference image.


[ ] Skillora branding is present.


[ ] Two-column desktop layout works.


[ ] Responsive mobile layout works.


[ ] Left marketing section is implemented.


[ ] "Welcome back" hero is implemented.


[ ] Product tagline is implemented.


[ ] Three feature blocks are implemented.


[ ] Dashboard preview is implemented.


[ ] Dashboard preview uses static decorative data only.


[ ] Login card is implemented.


[ ] Work email field works.


[ ] Password field works.


[ ] Password visibility toggle works.


[ ] Remember-me checkbox works.


[ ] Forgot password link works.


[ ] Login button works with Laravel authentication.


[ ] Loading state works.


[ ] Validation errors work.


[ ] Authentication errors work.


[ ] Registration link works.


[ ] Social login buttons are only shown if actually supported.


[ ] Testimonial is implemented as a reusable component.


[ ] Security message is truthful.


[ ] Footer is implemented.


[ ] Accessibility requirements are satisfied.


[ ] No unnecessary dependencies are added.


[ ] Existing Laravel authentication remains intact.


[ ] No authentication logic is moved into Vue.


[ ] No business logic is placed inside reusable UI components.


[ ] Tailwind CSS 4 is used.


[ ] Vue 3 + TypeScript is used.


[ ] Inertia.js is used.


[ ] Components are reusable and reasonably sized.
57. Important Final Instruction

Do not simply change the colors of the existing Laravel authentication page.

Recreate the complete Skillora product experience from the reference image.

The finished page should feel like:

A premium AI hiring SaaS

rather than:

A Laravel application with a custom login form.

The candidate/recruiter should immediately recognize the product as:

Skillora — AI-powered technical interviews.