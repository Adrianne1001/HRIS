# Design: Enhance UI/UX Modernization

## Overview

This design covers the systematic modernization of the FortiTech HRIS UI/UX layer. It affects all Blade views, Blade components, CSS partials, and Alpine.js interactions. No backend changes are required — this is purely a frontend enhancement.

The approach is divided into five pillars: (1) CSS Architecture Cleanup, (2) Blade Component Refactoring, (3) Blade View Standardization, (4) New Interactive Components, and (5) Visual Polish Enhancements.

## Pillar 1: CSS Architecture Cleanup

### Current CSS Structure

```
resources/css/app.css          # Entry point — imports all partials
├── layouts/page.css           # Page layout utilities
├── layouts/grid.css           # Grid helpers
├── components/buttons.css     # Button styles
├── components/forms.css       # Form element styles
├── components/cards.css       # Card styles
├── components/badges.css      # Badge/tag styles
├── components/modals.css      # Modal overlay styles
├── components/auth.css        # Auth page styles
├── modules/tables.css         # Data table styles
├── modules/alerts.css         # Alert message styles
├── modules/empty-state.css    # Empty state styles
├── modules/details.css        # Detail/show page styles
├── modules/schedule.css       # Schedule-specific styles
├── modules/dashboard.css      # Dashboard widget styles
├── modules/dtr.css            # Daily Time Record styles
├── modules/landing.css        # Landing page styles
├── modules/leave-balance.css  # Leave balance card styles
└── modules/payslip.css        # Payslip layout styles
```

### New CSS Partials to Add

| File | Purpose |
|------|---------|
| `components/tooltips.css` | Sidebar tooltip styles for collapsed state |
| `components/toasts.css` | Toast notification styles and animations |
| `components/loading.css` | Loading spinners and skeleton loaders |
| `components/tabs.css` | Status tab navigation styles (currently inline in views) |
| `modules/sidebar.css` | Extract sidebar styles from inline Tailwind in `sidebar.blade.php` |
| `modules/header.css` | Extract header bar styles from inline Tailwind in `app.blade.php` |
| `modules/responsive-table.css` | Mobile card-view alternative for data tables |

### CSS Updates to Existing Partials

**`components/buttons.css`** — Add:
- `.btn-icon` — Icon-only button variant (square, centered icon)
- `.btn-loading` — State class that shows spinner and disables interaction
- Hover micro-animation: subtle scale transform (`transform: scale(1.02)`) on `.btn-primary`, `.btn-success`, `.btn-danger`

**`components/forms.css`** — Add:
- `.form-select-styled` — Styled select with custom dropdown arrow (if not already done via `.form-select`)
- `.form-textarea` — Textarea styling to match form-input
- `.form-input-icon` — Input with left icon support (for search inputs)

**`components/cards.css`** — Add:
- `.card-hover` — Card with hover elevation effect
- `.card-bordered` — Card variant with left color accent border
- `.card-compact` — Reduced padding card variant

**`modules/tables.css`** — Add:
- `.table-row-alt` — Alternating row background colors
- `.table-responsive-cards` — Wrapper class for mobile card view
- `.table-mobile-card` — Individual table row rendered as card on mobile

**`modules/empty-state.css`** — Enhance:
- `.empty-state-icon` — Larger, styled icon wrapper
- `.empty-state-action` — CTA button area within empty state

**`modules/alerts.css`** — Keep current classes for non-JS fallback, but views will switch to using the new toast component when JS is available.

**`modules/dashboard.css`** — Add:
- `.stat-card-trend` — Trend indicator (up/down arrow with color)
- `.stat-card-hover` — Hover lift effect on stat cards

## Pillar 2: Blade Component Refactoring

### Components to Refactor

Each component must switch from inline Tailwind to referencing semantic CSS classes. The CSS class definitions remain in the CSS partials.

**`components/primary-button.blade.php`**
- Current: `bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 ...`
- Change to: Reference `btn-primary` class
- The `.btn-primary` CSS class already exists in `components/buttons.css` with the correct indigo-600 styling

**`components/secondary-button.blade.php`**
- Current: `bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 ...`
- Change to: Reference `btn-ghost` class (which matches the visual intent)

**`components/danger-button.blade.php`**
- Current: `bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 ...`
- Change to: Reference `btn-danger` class

**`components/text-input.blade.php`**
- Current: `border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm`
- Change to: Reference `form-input` class (already defined in `components/forms.css`)

**`components/input-label.blade.php`**
- Current: `block font-medium text-sm text-gray-700`
- Change to: Reference `form-label` class

**`components/input-error.blade.php`**
- Current: `text-sm text-red-600 space-y-1`
- Change to: Reference `form-error` class (extend if needed to include `space-y-1`)

**`components/nav-link.blade.php`**
- Current: Two long inline class strings for active/inactive states
- Add CSS classes: `.nav-link-active`, `.nav-link-inactive` in `layouts/page.css`

**`components/responsive-nav-link.blade.php`**
- Current: Two very long inline class strings for active/inactive states
- Add CSS classes: `.responsive-nav-link-active`, `.responsive-nav-link-inactive` in `layouts/page.css`

**`components/dropdown.blade.php`**
- Mostly fine with Alpine.js logic, but extract alignment/width classes to CSS where possible

**`components/dropdown-link.blade.php`**
- Current: `block w-full px-4 py-2 text-start text-sm leading-5 text-gray-700 hover:bg-gray-100 ...`
- Add CSS class: `.dropdown-link` in `components/buttons.css` or a new `components/dropdown.css`

**`components/modal.blade.php`**
- Currently uses mix of inline classes and `.modal-overlay` / `.modal-content` from CSS
- Ensure all inline classes are extracted to `components/modals.css`

**`components/auth-session-status.blade.php`**
- Current: `font-medium text-sm text-green-600`
- Add CSS class: `.auth-status-message` in `components/auth.css`

### New Component

**`components/toast.blade.php`** — Alpine.js toast notification component
- Props: none (listens to dispatched events or reads from session)
- Reads `session('success')`, `session('error')`, `session('warning')` on page load
- Shows slide-in toast from top-right corner
- Auto-dismisses after 5 seconds
- Support types: success (green), error (red), warning (orange), info (blue)
- Uses CSS classes from new `components/toasts.css`

## Pillar 3: Blade View Standardization

### Views Requiring Inline Tailwind Cleanup

These views currently use inline Tailwind utilities that must be replaced with semantic CSS classes:

**High Priority (significant inline usage):**

| View | Issues |
|------|--------|
| `attendance/calendar.blade.php` | Heavy inline: `bg-white overflow-hidden shadow-sm sm:rounded-lg`, `mt-1 block w-full border-gray-300 ...`, raw `flex flex-wrap items-end gap-4` instead of `filter-bar` |
| `profile/edit.blade.php` | Uses `py-12`, `max-w-7xl mx-auto sm:px-6 lg:px-8`, `p-4 sm:p-8 bg-white shadow sm:rounded-lg` instead of `page-container`, `page-content`, `card` |
| `profile/partials/*.blade.php` | Uses `mt-1 block w-full` on inputs, `mt-2` on errors, raw layout classes instead of semantic |
| `auth/forgot-password.blade.php` | Uses `block mt-1 w-full` on input, `mt-2` on error, raw text classes |
| `auth/reset-password.blade.php` | Same raw Tailwind as forgot-password |
| `auth/verify-email.blade.php` | Raw button and text styles |
| `auth/confirm-password.blade.php` | Raw input and button styles |
| `dashboard.blade.php` | Has inline `flex items-center` inside stat cards that could use semantic class |
| `layouts/navigation.blade.php` | Heavy inline Tailwind throughout — largely unused now in favor of sidebar layout but should be cleaned up if kept |

**Medium Priority (partial inline usage):**

| View | Issues |
|------|--------|
| `employees/show.blade.php` | Some inline `flex`, `text-2xl font-bold`, `text-indigo-600 font-medium` |
| `leave-requests/index.blade.php` | Tab navigation uses inline `px-6 py-3 text-sm font-medium border-b-2` instead of tab CSS class |
| `leave-requests/show.blade.php` | Action buttons area and reject form use inline styles |
| `payroll/index.blade.php` | Same tab navigation inline pattern |
| `leave-balances/manage.blade.php` | Some inline utilities in toolbar area |
| `attendance/dtr.blade.php` | Grid layout `grid grid-cols-1 lg:grid-cols-3 gap-6` inline |

### Standardization Rules

1. **All page views** must use: `<div class="page-container"><div class="page-content">` (or `page-content-md`/`page-content-sm`)
2. **All cards** must use: `<div class="card"><div class="card-body">` 
3. **All form inputs** must use `form-input` or `form-select` classes (not raw Tailwind)
4. **All buttons** must use `btn-*` classes
5. **All tables** must use `data-table`, `table-header`, `table-th`, `table-row`, `table-cell` classes
6. **All flash messages** must use the new `<x-toast />` component
7. **All headers** must use `header-title`, `header-row`, `header-row-between` classes
8. **Tab navigations** must use new `.tab-nav`, `.tab-item`, `.tab-item-active` classes

## Pillar 4: New Interactive Components

### Toast Notification System

**Component:** `resources/views/components/toast.blade.php`
**CSS:** `resources/css/components/toasts.css`

Behavior:
- Included once in `layouts/app.blade.php` before `</body>`
- On page load, checks `session('success')`, `session('error')`, `session('warning')`
- Renders a toast for each flash message
- Toast slides in from top-right with CSS animation
- Auto-dismisses after 5 seconds (configurable via data attribute)
- Dismissible via close button
- Stacks multiple toasts vertically

CSS classes:
- `.toast-container` — Fixed positioning, top-right, z-60, flex column gap
- `.toast` — Base toast card styling 
- `.toast-success` — Green left border, green icon
- `.toast-error` — Red left border, red icon
- `.toast-warning` — Orange left border, orange icon
- `.toast-info` — Blue left border, blue icon
- `.toast-enter` — Slide-in animation from right
- `.toast-leave` — Fade-out animation
- `.toast-close-btn` — Close button positioning

### Sidebar Tooltips

**Update:** `resources/views/layouts/sidebar.blade.php`
**CSS:** `resources/css/components/tooltips.css`

When sidebar is collapsed, hovering over a nav item shows a tooltip with the label text.

Implementation:
- Each nav link gets `x-tooltip` Alpine directive or a manual tooltip div
- Tooltip appears to the right of the sidebar item
- Only shown when `!expanded` (sidebar is collapsed)
- CSS classes: `.sidebar-tooltip`, `.sidebar-tooltip-visible`

### Loading States

**CSS:** `resources/css/components/loading.css`

- `.btn-loading` — Added to button on form submit via Alpine.js. Shows inline spinner SVG, disables button
- `.skeleton` — Skeleton loader base class (rectangular placeholder)
- `.skeleton-text` — Text-line skeleton
- `.skeleton-circle` — Circular skeleton (avatar placeholder)

### Mobile Sidebar Overlay

**Update:** `resources/views/layouts/app.blade.php` + `sidebar.blade.php`

On mobile viewports (< md):
- Sidebar is hidden by default (off-screen left)
- Hamburger button in header triggers overlay mode
- Sidebar slides in from left over page content
- Semi-transparent backdrop overlay
- Close on backdrop click or X button
- CSS classes: `.sidebar-mobile-overlay`, `.sidebar-mobile-backdrop`

## Pillar 5: Visual Polish Enhancements

### Sidebar Refinements
- Active state: Stronger visual indicator (left accent bar + background)
- Section headers: Slightly larger spacing, subtle top border separator
- Collapsed icons: Add tooltip labels
- Bottom section: User avatar/info area with dropdown for profile/logout

### Card Enhancements
- Default cards: `border border-gray-200` for crisper edges instead of shadow-only
- Hover cards (dashboard widgets): Subtle `translateY(-2px)` on hover with shadow transition
- Section title cards: Left colored border accent (indigo-500)

### Table Enhancements
- Alternating row colors: `bg-gray-50` on even rows
- Improved hover: `bg-indigo-50` instead of `bg-gray-50`
- Sticky table header on scroll
- Better action column alignment

### Button Micro-Animations
- All `btn-*` classes: `transition-all duration-150` with subtle `transform scale` on hover
- Loading state: Spinner replaces button text, button maintains width

### Empty State Improvements
- Larger, more illustrative icons (SVG illustrations)
- Clearer action text and CTA buttons
- Consistent across all modules

### Form Refinements
- Input focus: Smoother ring transition
- Label: Slightly bolder weight
- Error messages: Slide-in animation
- Select dropdowns: Custom styled arrow icon

### Status Tabs
- Underline style with smooth transition between tabs
- Badge counts with proper semantic colors
- Extract from inline Tailwind to `.tab-nav`, `.tab-item`, `.tab-item-active`, `.tab-badge` classes

## File Inventory

### New Files
| File | Type | Purpose |
|------|------|---------|
| `resources/css/components/toasts.css` | CSS | Toast notification styles |
| `resources/css/components/tooltips.css` | CSS | Sidebar tooltip styles |
| `resources/css/components/loading.css` | CSS | Loading spinners and skeletons |
| `resources/css/components/tabs.css` | CSS | Tab navigation styles |
| `resources/css/components/dropdown.css` | CSS | Dropdown menu styles |
| `resources/css/modules/sidebar.css` | CSS | Sidebar semantic styles |
| `resources/css/modules/header.css` | CSS | Header bar semantic styles |
| `resources/css/modules/responsive-table.css` | CSS | Mobile table card view |
| `resources/views/components/toast.blade.php` | Blade | Toast notification component |

### Modified Files
| File | Changes |
|------|---------|
| `resources/css/app.css` | Add imports for all new CSS partials |
| `resources/css/components/buttons.css` | Add hover animations, icon button, loading state |
| `resources/css/components/forms.css` | Add textarea, input-icon, form-error extensions |
| `resources/css/components/cards.css` | Add hover, bordered, compact variants |
| `resources/css/components/badges.css` | Minor cleanup of redundant definitions |
| `resources/css/components/modals.css` | Extract inline styles from modal component |
| `resources/css/modules/tables.css` | Add alternating rows, sticky header, hover improvements |
| `resources/css/modules/empty-state.css` | Enhanced icon and action area |
| `resources/css/modules/dashboard.css` | Add hover effects, trend indicators |
| `resources/css/modules/alerts.css` | Keep as fallback; primary UX moves to toast |
| `resources/css/layouts/page.css` | Add nav-link, responsive-nav-link classes |
| `resources/views/components/primary-button.blade.php` | Use `btn-primary` class |
| `resources/views/components/secondary-button.blade.php` | Use `btn-ghost` class |
| `resources/views/components/danger-button.blade.php` | Use `btn-danger` class |
| `resources/views/components/text-input.blade.php` | Use `form-input` class |
| `resources/views/components/input-label.blade.php` | Use `form-label` class |
| `resources/views/components/input-error.blade.php` | Use `form-error` class |
| `resources/views/components/nav-link.blade.php` | Use semantic nav classes |
| `resources/views/components/responsive-nav-link.blade.php` | Use semantic nav classes |
| `resources/views/components/dropdown.blade.php` | Extract inline to CSS |
| `resources/views/components/dropdown-link.blade.php` | Use `dropdown-link` class |
| `resources/views/components/modal.blade.php` | Extract remaining inline to CSS |
| `resources/views/components/auth-session-status.blade.php` | Use `auth-status-message` class |
| `resources/views/layouts/app.blade.php` | Add toast component, mobile sidebar trigger |
| `resources/views/layouts/sidebar.blade.php` | Extract inline Tailwind, add tooltips, mobile overlay |
| `resources/views/layouts/navigation.blade.php` | Extract inline Tailwind to CSS |
| `resources/views/dashboard.blade.php` | Replace inline Tailwind with semantic classes |
| `resources/views/profile/edit.blade.php` | Standardize to page layout pattern |
| `resources/views/profile/partials/update-profile-information-form.blade.php` | Use semantic classes |
| `resources/views/profile/partials/update-password-form.blade.php` | Use semantic classes |
| `resources/views/profile/partials/delete-user-form.blade.php` | Use semantic classes |
| `resources/views/auth/forgot-password.blade.php` | Use semantic classes |
| `resources/views/auth/reset-password.blade.php` | Use semantic classes |
| `resources/views/auth/verify-email.blade.php` | Use semantic classes |
| `resources/views/auth/confirm-password.blade.php` | Use semantic classes |
| `resources/views/attendance/calendar.blade.php` | Replace all inline Tailwind |
| `resources/views/attendance/dtr.blade.php` | Replace inline grid/layout classes |
| `resources/views/attendance/index.blade.php` | Minor cleanup |
| `resources/views/employees/index.blade.php` | Minor cleanup |
| `resources/views/employees/show.blade.php` | Replace inline typography/layout |
| `resources/views/leave-requests/index.blade.php` | Extract tab nav to CSS classes |
| `resources/views/leave-requests/show.blade.php` | Extract inline action area |
| `resources/views/leave-balances/index.blade.php` | Minor cleanup |
| `resources/views/leave-balances/manage.blade.php` | Minor cleanup |
| `resources/views/leave-types/index.blade.php` | Minor cleanup |
| `resources/views/payroll/index.blade.php` | Extract tab nav to CSS classes |
| `resources/views/payroll/show.blade.php` | Minor cleanup |
| `resources/views/employee-loans/index.blade.php` | Minor cleanup |
| `resources/views/holidays/index.blade.php` | Minor cleanup |
| `resources/views/payslips/index.blade.php` | Minor cleanup |
