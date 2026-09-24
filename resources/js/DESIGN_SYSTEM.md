# Project Tracker - Frontend UI Design System

## Overview
This document describes the comprehensive frontend UI design system implemented for the Laravel Inertia + Vue3 Project Tracker application.

## Color Palette

### Primary Colors
- **Primary Green**: `rgb(22, 163, 74)` - Main brand color
- **Primary Green Dark**: `rgb(21, 128, 61)` - Hover states, accents
- **Primary Green Light**: `rgb(134, 239, 172)` - Light backgrounds, highlights

### Emerald Accents
- **Emerald 500**: `rgb(16, 185, 129)`
- **Emerald 600**: `rgb(5, 150, 105)`
- **Emerald 700**: `rgb(4, 120, 87)`

### Semantic Colors
- **Success**: `rgb(34, 197, 94)` - Green 500
- **Warning**: `rgb(234, 179, 8)` - Yellow 500
- **Danger**: `rgb(239, 68, 68)` - Red 500
- **Info**: `rgb(59, 130, 246)` - Blue 500

### Dark Mode Colors
- **Primary Background**: `rgb(15, 23, 42)` - Slate 900
- **Secondary Background**: `rgb(30, 41, 59)` - Slate 800
- **Surface**: `rgb(30, 41, 59)` - Card backgrounds

## Typography

### Font Family
- Primary: `Poppins, sans-serif`
- Fallback: System fonts

### Font Sizes
- **xs**: 0.75rem (12px)
- **sm**: 0.875rem (14px)
- **base**: 1rem (16px)
- **lg**: 1.125rem (18px)
- **xl**: 1.25rem (20px)
- **2xl**: 1.5rem (24px)
- **3xl**: 1.875rem (30px)
- **4xl**: 2.25rem (36px)

### Font Weights
- Regular: 400
- Medium: 500
- Semibold: 600
- Bold: 700

## Spacing Scale (8px Grid System)

```css
--spacing-xs:   0.25rem;  /* 4px */
--spacing-sm:   0.5rem;   /* 8px */
--spacing-md:   1rem;     /* 16px */
--spacing-lg:   1.5rem;   /* 24px */
--spacing-xl:   2rem;     /* 32px */
--spacing-2xl:  3rem;     /* 48px */
--spacing-3xl:  4rem;     /* 64px */
```

## Border Radius

```css
--radius-sm:   0.5rem;    /* 8px */
--radius-md:   0.75rem;   /* 12px */
--radius-lg:   1rem;      /* 16px */
--radius-xl:   1.25rem;   /* 20px */
--radius-2xl:  1.5rem;    /* 24px */
--radius-pill: 9999px;    /* Fully rounded */
```

## Shadows

```css
--shadow-soft:    0 2px 8px rgba(0, 0, 0, 0.04);
--shadow-soft-md: 0 4px 16px rgba(0, 0, 0, 0.06);
--shadow-soft-lg: 0 8px 24px rgba(0, 0, 0, 0.08);
--shadow-green:   0 4px 12px rgba(34, 197, 94, 0.2);
```

## Component Library

### Button Component (`Button.vue`)
**Variants**: primary, secondary, light, success, danger, warning, ghost  
**Sizes**: sm, md, lg  
**Features**: Loading state, full width option, icon support

```vue
<Button variant="primary" size="md" :loading="isLoading">
  <i class="ri-save-line mr-2"></i>
  Save Changes
</Button>
```

### Card Component (`Card.vue`)
**Features**: Header slot, body, footer slot, actions slot, hover effects

```vue
<Card title="Project Statistics" subtitle="Monthly overview">
  <template #actions>
    <Button variant="ghost" size="sm">View All</Button>
  </template>
  <!-- Card content -->
</Card>
```

### StatsCard Component (`StatsCard.vue`)
**Features**: Large icons, trend indicators, badges, subtitles

```vue
<StatsCard
  title="Total Projects"
  value="248"
  icon="ri-folder-line"
  icon-bg="bg-green-600"
  badge="+12.5%"
  badge-class="badge-success"
  trend="up"
/>
```

### MetricCard Component (`MetricCard.vue`)
**Colors**: green, blue, purple, orange, red, emerald  
**Features**: Gradient backgrounds, trend indicators, descriptions

```vue
<MetricCard
  title="Active Tasks"
  value="1,429"
  icon="ri-checkbox-circle-line"
  color="blue"
  trend="up"
  trend-value="+8.2%"
  description="In progress"
/>
```

### Badge Component (`Badge.vue`)
**Variants**: success, warning, danger, info, primary, secondary, neutral  
**Sizes**: sm, md, lg  
**Features**: Dot indicator option

```vue
<Badge variant="success" size="sm" dot>
  Active
</Badge>
```

### ProgressBar Component (`ProgressBar.vue`)
**Variants**: primary, success, warning, danger  
**Sizes**: sm, md, lg  
**Features**: Show label option, gradient fill

```vue
<ProgressBar :value="75" show-label>
  <template #label>
    <span>Progress</span>
  </template>
</ProgressBar>
```

### Avatar Component (`Avatar.vue`)
**Sizes**: xs, sm, md, lg, xl  
**Features**: Image support, initials, status indicators (online, offline, away, busy)

```vue
<Avatar
  :src="user.avatar"
  :name="user.name"
  size="md"
  status="online"
/>
```

### Input Component (`Input.vue`)
**Types**: text, email, password, number, etc.  
**Features**: Label, error states, icons, hints, required indicator

```vue
<Input
  v-model="form.email"
  type="email"
  label="Email Address"
  icon="ri-mail-line"
  :error="form.errors.email"
  hint="We'll never share your email"
  required
/>
```

### EmptyState Component (`EmptyState.vue`)
**Features**: Icon, title, description, action button

```vue
<EmptyState
  icon="ri-inbox-line"
  title="No tasks assigned"
  description="Check back later for new assignments"
  action-text="Refresh"
  @action="handleRefresh"
/>
```

### PageHeader Component (`PageHeader.vue`)
**Features**: Breadcrumbs, title, subtitle, actions slot

```vue
<PageHeader
  title="Dashboard"
  subtitle="Welcome back! Here's your overview"
>
  <template #actions>
    <Button variant="primary">New Project</Button>
  </template>
</PageHeader>
```

## Layout Components

### AppHeader
- Fixed position with backdrop blur
- 64px height
- Responsive hamburger menu
- Dark mode toggle
- Profile dropdown
- Notification bell with pulse animation
- Search bar (desktop)
- Auto-hide on scroll down

### AppSidebar
- 280px width
- Collapsible menu with smooth animations
- Gradient active states
- Icon + label navigation
- Nested submenus support
- Custom green scrollbar
- Mobile overlay

### AppLayout
- Responsive layout system
- Automatic sidebar margin adjustment
- Dark mode support
- Smooth transitions
- Mobile-friendly overlay

## Design Principles

### 1. Visual Language
- **Large border-radius**: 16-20px for cards and containers
- **Pill-shaped buttons**: Fully rounded borders
- **Soft shadows**: Subtle elevation with rgba
- **Generous whitespace**: Breathing room between elements
- **Consistent padding**: 8px grid system

### 2. Color Usage
- **Primary actions**: Green (22, 163, 74)
- **Secondary actions**: Outlined or light background
- **Danger actions**: Red
- **Success states**: Green
- **Warning states**: Yellow/Orange
- **Info states**: Blue

### 3. Dark Mode
- Enhanced contrast for better visibility
- Darker backgrounds: rgb(15, 23, 42)
- Lighter borders: rgba(255, 255, 255, 0.08)
- Adjusted shadows for depth
- Green colors adjusted for dark backgrounds

### 4. Animations
- **Duration**: 200-300ms for most transitions
- **Easing**: cubic-bezier(0.4, 0, 0.2, 1) for smooth feel
- **Stagger**: 100ms delay between items
- **Types**: fadeIn, fadeInUp, slideIn, scaleIn
- **Respect prefers-reduced-motion**

### 5. Accessibility
- **Focus states**: 2px green outline with offset
- **Color contrast**: WCAG AA compliant
- **Keyboard navigation**: Full support
- **Screen readers**: Proper ARIA labels
- **Touch targets**: Minimum 44x44px

### 6. Responsive Design
- **Mobile-first approach**
- **Breakpoints**:
  - Mobile: < 768px
  - Tablet: 768px - 991px
  - Desktop: >= 992px
  - Large: >= 1200px
- **Flexible grids**: CSS Grid and Flexbox
- **Touch-friendly**: Larger hit areas on mobile

## Implementation Guidelines

### CSS Variables
Use CSS variables for consistent theming:
```css
background-color: rgb(var(--primary-green));
border-radius: var(--radius-xl);
padding: var(--spacing-lg);
box-shadow: var(--shadow-soft-md);
```

### Tailwind Classes
Prefer Tailwind utility classes for rapid development:
```html
<div class="rounded-2xl shadow-soft-md p-6 bg-white dark:bg-gray-800">
```

### Component Composition
Build complex UIs by composing smaller components:
```vue
<Card>
  <StatsCard />
  <ProgressBar />
  <Button />
</Card>
```

### Dark Mode Toggle
Dark mode is controlled at the document root:
```javascript
document.documentElement.classList.toggle('dark', isDarkMode)
```

## File Structure

```
resources/
├── css/
│   ├── app.css              # Main entry point
│   ├── pm-custom.css        # Custom styles and design system
│   └── styles.css           # Base styles (icons, fonts)
├── js/
│   ├── Components/
│   │   ├── layout/
│   │   │   ├── AppHeader.vue
│   │   │   ├── AppSidebar.vue
│   │   │   └── AppFooter.vue
│   │   └── ui/
│   │       ├── Button.vue
│   │       ├── Card.vue
│   │       ├── Badge.vue
│   │       ├── StatsCard.vue
│   │       ├── MetricCard.vue
│   │       ├── ProgressBar.vue
│   │       ├── Avatar.vue
│   │       ├── Input.vue
│   │       ├── EmptyState.vue
│   │       └── PageHeader.vue
│   ├── Layouts/
│   │   └── AppLayout.vue
│   └── Pages/
│       ├── Dashboard.Enhanced.vue
│       ├── Auth/
│       │   └── Login.vue
│       └── TeamMembers/
│           └── Dashboard.vue
└── images/                   # Static assets
```

## Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)
- Mobile Safari (iOS 13+)
- Chrome Mobile (latest)

## Performance Considerations

1. **GPU Acceleration**: Transform and opacity for animations
2. **Lazy Loading**: Images and components
3. **CSS Containment**: Layout, style, and paint containment
4. **Will-change**: Used sparingly for frequently animated elements
5. **Debounced Scroll**: Optimized scroll event handlers

## Future Enhancements

- [ ] Additional component variants
- [ ] Animation library expansion
- [ ] Icon system standardization
- [ ] Theme customization tool
- [ ] Component playground/documentation site
- [ ] A11y audit and improvements
- [ ] Performance monitoring
- [ ] Component unit tests

## Resources

- [Tailwind CSS Documentation](https://tailwindcss.com)
- [Vue 3 Documentation](https://vuejs.org)
- [Inertia.js Documentation](https://inertiajs.com)
- [Remix Icons](https://remixicon.com)
- [WCAG Guidelines](https://www.w3.org/WAI/WCAG21/quickref/)

---

**Last Updated**: {{ new Date().toLocaleDateString() }}  
**Version**: 1.0.0  
**Maintained by**: Frontend Team
