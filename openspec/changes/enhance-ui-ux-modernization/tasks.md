# Tasks: enhance-ui-ux-modernization

## Task 1: Create new CSS partials — toasts, tooltips, loading, tabs
- **Agent**: ui-ux
- **Files**: resources/css/components/toasts.css, resources/css/components/tooltips.css, resources/css/components/loading.css, resources/css/components/tabs.css
- Create `toasts.css`: `.toast-container` (fixed top-right, z-60, flex column, gap-3), `.toast` (base card with shadow-lg, rounded-lg, padding, flex, max-width, border-l-4), `.toast-success` (border-green-500, green icon bg), `.toast-error` (border-red-500), `.toast-warning` (border-orange-500), `.toast-info` (border-blue-500), `.toast-close-btn` (absolute top-right, text-gray-400 hover:text-gray-600), keyframe animations for slide-in from right and fade-out
- Create `tooltips.css`: `.sidebar-tooltip` (absolute left-full, ml-2, px-3 py-1.5, bg-gray-900, text-white, text-sm, rounded-lg, whitespace-nowrap, opacity-0, pointer-events-none, transition), `.sidebar-tooltip-visible` (opacity-100)
- Create `loading.css`: `.btn-loading` (pointer-events-none, opacity-75, with inline spinner animation), `.skeleton` (bg-gray-200, rounded, animate-pulse), `.skeleton-text` (h-4 full-width), `.skeleton-circle` (rounded-full)
- Create `tabs.css`: `.tab-nav` (flex, -mb-px, border-b border-gray-200), `.tab-item` (px-6 py-3, text-sm font-medium, border-b-2 border-transparent, text-gray-500, hover styles, transition), `.tab-item-active` (border-indigo-500, text-indigo-600), `.tab-badge` (ml-1, px-2 py-0.5, rounded-full, text-xs), `.tab-badge-active` (bg-indigo-100 text-indigo-600), `.tab-badge-inactive` (bg-gray-100 text-gray-600)

## Task 2: Create sidebar and header CSS partials
- **Agent**: ui-ux
- **Files**: resources/css/modules/sidebar.css, resources/css/modules/header.css
- Create `sidebar.css`: Extract all inline Tailwind from `sidebar.blade.php` into semantic classes — `.sidebar` (fixed left-0 top-0, h-screen, bg-indigo-700, text-white, flex-col, transition-all, z-50, overflow-hidden), `.sidebar-expanded` (w-64), `.sidebar-collapsed` (w-20), `.sidebar-logo-area` (h-16, border-b border-indigo-600, px-5, flex items-center), `.sidebar-logo-icon` (w-10 h-10 bg-white rounded-lg, flex items-center justify-center), `.sidebar-brand` (font-bold text-lg, whitespace-nowrap, transition), `.sidebar-nav` (flex-1, overflow-y-auto, overflow-x-hidden, py-4), `.sidebar-section` (px-3 mb-6), `.sidebar-section-label` (px-3 text-xs font-semibold text-indigo-300 uppercase tracking-wider mb-2), `.sidebar-section-btn` (full width, flex, justify-between, same text styling), `.sidebar-link` (flex items-center px-3 py-2.5 rounded-lg mb-1 transition-colors text-indigo-100), `.sidebar-link-active` (bg-indigo-800 text-white), `.sidebar-link:hover` (bg-indigo-600), `.sidebar-link-icon` (w-6 h-6 flex-shrink-0), `.sidebar-link-text` (whitespace-nowrap, overflow-hidden, transition), `.sidebar-user` (border-t border-indigo-600, p-4, flex items-center), `.sidebar-mobile-overlay` (fixed inset-0 z-40 md:hidden), `.sidebar-mobile-backdrop` (fixed inset-0 bg-gray-600 bg-opacity-75)
- Create `header.css`: `.header-bar` (bg-white shadow-sm sticky top-0 z-40), `.header-inner` (flex items-center justify-between px-6 py-4), `.header-actions` (flex items-center space-x-4), `.header-action-btn` (p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors), `.header-hamburger` (md:hidden, p-2 text-gray-600)

## Task 3: Create dropdown and responsive-table CSS partials
- **Agent**: ui-ux
- **Files**: resources/css/components/dropdown.css, resources/css/modules/responsive-table.css
- Create `dropdown.css`: `.dropdown-link` (block w-full px-4 py-2 text-start text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:bg-gray-100 transition), `.dropdown-menu` (rounded-md ring-1 ring-black ring-opacity-5, bg-white, shadow-lg), `.dropdown-trigger` (cursor-pointer)
- Create `responsive-table.css`: `.table-responsive-wrapper` (overflow-x-auto), `.table-mobile-cards` (hidden on lg+, flex flex-col gap-4), `.table-mobile-card` (bg-white rounded-lg shadow-sm p-4 border border-gray-200), `.table-mobile-card-header` (flex justify-between items-center mb-2), `.table-mobile-card-row` (flex justify-between py-1 text-sm), `.table-mobile-card-label` (text-gray-500), `.table-mobile-card-value` (text-gray-900 font-medium), `.table-desktop-only` (hidden lg:table — for the desktop table element)

## Task 4: Update app.css with new imports
- **Agent**: ui-ux
- **Files**: resources/css/app.css
- Add new imports after existing ones, before the @tailwind directives: `@import 'components/toasts.css'`, `@import 'components/tooltips.css'`, `@import 'components/loading.css'`, `@import 'components/tabs.css'`, `@import 'components/dropdown.css'`, `@import 'modules/sidebar.css'`, `@import 'modules/header.css'`, `@import 'modules/responsive-table.css'`
- Keep @tailwind base/components/utilities at the end

## Task 5: Enhance existing CSS partials — buttons, forms, cards
- **Agent**: ui-ux
- **Files**: resources/css/components/buttons.css, resources/css/components/forms.css, resources/css/components/cards.css
- `buttons.css`: Add `.btn-icon` (@apply inline-flex items-center justify-center p-2 rounded-lg transition), add hover transform to `.btn-primary` `.btn-success` `.btn-danger` (hover:shadow-md, transform hover:-translate-y-0.5 transition-all duration-150), add `.btn-loading` (@apply pointer-events-none opacity-75 cursor-wait)
- `forms.css`: Add `.form-textarea` (@apply mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm), add `.form-input-icon` (position relative with left padding for icon), extend `.form-error` to include space-y-1. Add `.form-input-group` (@apply relative) for icon-wrapped inputs
- `cards.css`: Add `.card-hover` (@apply transition-all duration-200 hover:shadow-md hover:-translate-y-0.5), `.card-bordered-indigo` (@apply border-l-4 border-indigo-500), `.card-compact` (@apply p-4 space-y-4)

## Task 6: Enhance existing CSS partials — tables, empty-state, dashboard, badges
- **Agent**: ui-ux
- **Files**: resources/css/modules/tables.css, resources/css/modules/empty-state.css, resources/css/modules/dashboard.css, resources/css/components/badges.css
- `tables.css`: Add `.table-row:nth-child(even)` (bg-gray-50/50), enhance `.table-row` hover to bg-indigo-50/50, add `.table-sticky-header` (@apply sticky top-0 z-10) on `.table-header`
- `empty-state.css`: Add `.empty-state-icon` (@apply mx-auto h-16 w-16 text-gray-300), `.empty-state-action` (@apply mt-6), improve `.empty-state` padding to py-16
- `dashboard.css`: Add `.stat-card-hover` — extend `.stat-card` with hover:shadow-md hover:-translate-y-0.5 transition-all duration-200
- `badges.css`: Clean up redundant badge definitions — `.badge-success`, `.badge-info`, `.badge-blue` have duplicate base styles. Factor common base into `.badge` and only override colors in variants

## Task 7: Add nav-link and auth-status CSS classes, enhance modals
- **Agent**: ui-ux  
- **Files**: resources/css/layouts/page.css, resources/css/components/modals.css, resources/css/components/auth.css
- `page.css`: Add `.nav-link-active` (@apply inline-flex items-center px-1 pt-1 border-b-2 border-indigo-400 text-sm font-medium leading-5 text-gray-900), `.nav-link-inactive` (@apply inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 transition), `.responsive-nav-link-active` (block, border-l-4, border-indigo-400, bg-indigo-50, text-indigo-700), `.responsive-nav-link-inactive` (block, border-l-4, border-transparent, text-gray-600, hover styles)
- `modals.css`: Add `.modal-panel` (for the inner dialog panel — inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle), `.modal-sm` / `.modal-md` / `.modal-lg` / `.modal-xl` / `.modal-2xl` width variants
- `auth.css`: Add `.auth-status-message` (@apply font-medium text-sm text-green-600)

## Task 8: Refactor Blade components — buttons
- **Agent**: ui-ux
- **Files**: resources/views/components/primary-button.blade.php, resources/views/components/secondary-button.blade.php, resources/views/components/danger-button.blade.php
- `primary-button.blade.php`: Change to `$attributes->merge(['type' => 'submit', 'class' => 'btn-primary'])`
- `secondary-button.blade.php`: Change to `$attributes->merge(['type' => 'button', 'class' => 'btn-ghost'])`
- `danger-button.blade.php`: Change to `$attributes->merge(['type' => 'submit', 'class' => 'btn-danger'])`

## Task 9: Refactor Blade components — form elements
- **Agent**: ui-ux
- **Files**: resources/views/components/text-input.blade.php, resources/views/components/input-label.blade.php, resources/views/components/input-error.blade.php, resources/views/components/auth-session-status.blade.php
- `text-input.blade.php`: Change to `$attributes->merge(['class' => 'form-input'])`
- `input-label.blade.php`: Change to `$attributes->merge(['class' => 'form-label'])`
- `input-error.blade.php`: Change to `$attributes->merge(['class' => 'form-error'])`
- `auth-session-status.blade.php`: Change to `$attributes->merge(['class' => 'auth-status-message'])`

## Task 10: Refactor Blade components — navigation and dropdown
- **Agent**: ui-ux
- **Files**: resources/views/components/nav-link.blade.php, resources/views/components/responsive-nav-link.blade.php, resources/views/components/dropdown-link.blade.php
- `nav-link.blade.php`: Replace inline class strings with `$classes = ($active ?? false) ? 'nav-link-active' : 'nav-link-inactive'`
- `responsive-nav-link.blade.php`: Replace inline class strings with `$classes = ($active ?? false) ? 'responsive-nav-link-active' : 'responsive-nav-link-inactive'`
- `dropdown-link.blade.php`: Replace inline classes with `dropdown-link`

## Task 11: Refactor modal Blade component
- **Agent**: ui-ux
- **Files**: resources/views/components/modal.blade.php
- Replace inline `modal-overlay` and structural classes with `modal-overlay`, `modal-panel`, and width variant classes from `modals.css`
- Keep all Alpine.js logic intact (x-data, x-show, x-on, transitions)
- Extract remaining inline Tailwind to modal CSS classes

## Task 12: Create toast notification Blade component
- **Agent**: ui-ux
- **Files**: resources/views/components/toast.blade.php
- Create Alpine.js component with `x-data` that reads session flash messages
- Template: container with `toast-container`, individual toasts with type-specific classes
- Each toast: icon (SVG per type), message text, close button, auto-dismiss via `setTimeout`
- Support `session('success')`, `session('error')`, `session('warning')` out of the box
- Use CSS animation classes from `toasts.css` for enter/leave transitions

## Task 13: Update app layout — add toast component and mobile sidebar support
- **Agent**: ui-ux
- **Files**: resources/views/layouts/app.blade.php
- Add `<x-toast />` before closing `</body>` tag
- Add mobile hamburger button in header (visible on small screens): `.header-hamburger` button that dispatches `sidebar-toggle-mobile` event
- Replace inline header classes with `.header-bar`, `.header-inner`, `.header-actions`, `.header-action-btn`
- Replace inline main content area classes with appropriate semantic classes
- Keep `@stack('scripts')` at bottom

## Task 14: Refactor sidebar — extract inline Tailwind and add tooltips
- **Agent**: ui-ux
- **Files**: resources/views/layouts/sidebar.blade.php
- Replace ALL inline Tailwind utility classes with corresponding semantic classes from `sidebar.css`
- Use `.sidebar`, `.sidebar-expanded`, `.sidebar-collapsed`, `.sidebar-logo-area`, `.sidebar-nav`, `.sidebar-section`, `.sidebar-link`, `.sidebar-link-active`, `.sidebar-link-icon`, `.sidebar-link-text`, etc.
- Add tooltip for each nav link: a `<span>` with `.sidebar-tooltip` that shows the link label on hover when collapsed — controlled via Alpine: `x-show="!expanded"` with `@mouseenter`/`@mouseleave`
- Add mobile overlay behavior: sidebar has `sidebar-mobile-overlay` mode on small screens with backdrop
- Keep all existing Alpine.js state management (pinned, hovered, activeGroup, togglePin, toggleGroup)

## Task 15: Refactor navigation layout
- **Agent**: ui-ux
- **Files**: resources/views/layouts/navigation.blade.php
- Replace all inline Tailwind with semantic CSS classes
- Use `.nav-link-active` / `.nav-link-inactive` for nav links (from Blade component)
- Use `.dropdown-link` for dropdown items
- Use header/nav semantic classes throughout

## Task 16: Standardize auth views — forgot-password, reset-password, verify-email, confirm-password
- **Agent**: ui-ux
- **Files**: resources/views/auth/forgot-password.blade.php, resources/views/auth/reset-password.blade.php, resources/views/auth/verify-email.blade.php, resources/views/auth/confirm-password.blade.php
- Replace `block mt-1 w-full` on inputs with appropriate semantic class usage (rely on refactored `<x-text-input>` component which now uses `form-input`)
- Replace `mt-2` on errors with semantic class usage (rely on refactored `<x-input-error>`)
- Replace inline text styles with semantic classes
- Use `auth-form-*` classes from `auth.css` consistently (matching login/register pattern)
- Ensure consistent form structure: `auth-form-title`, `auth-form-subtitle`, `auth-form-fields`, `form-group`, `form-actions`

## Task 17: Standardize profile views
- **Agent**: ui-ux
- **Files**: resources/views/profile/edit.blade.php, resources/views/profile/partials/update-profile-information-form.blade.php, resources/views/profile/partials/update-password-form.blade.php, resources/views/profile/partials/delete-user-form.blade.php
- `profile/edit.blade.php`: Replace `py-12` / `max-w-7xl mx-auto sm:px-6 lg:px-8` with `.page-container` / `.page-content`. Replace `p-4 sm:p-8 bg-white shadow sm:rounded-lg` with `.card` / `.card-body`
- Profile partials: Replace `mt-1 block w-full` on inputs — rely on refactored `<x-text-input>` component. Replace `mt-2` on errors — rely on refactored `<x-input-error>`. Replace raw `text-lg font-medium text-gray-900` with `.card-title` or `.section-title`. Replace `mt-1 text-sm text-gray-600` with `.card-subtitle`
- Ensure delete account modal uses refactored `<x-modal>` with CSS classes

## Task 18: Standardize dashboard view
- **Agent**: ui-ux
- **Files**: resources/views/dashboard.blade.php
- Replace inline `flex items-center` within stat cards with appropriate CSS class (`.stat-card` already has flex, but inner `flex items-center` wrapper should use a semantic class or be removed if redundant)
- Add `.stat-card-hover` to stat cards for hover effect
- Ensure all header/page structure uses semantic classes
- Replace flash messages with toast component (remove inline alert divs if present)

## Task 19: Standardize attendance calendar view
- **Agent**: ui-ux
- **Files**: resources/views/attendance/calendar.blade.php
- This view has the MOST inline Tailwind usage. Replace ALL of it:
  - `bg-white overflow-hidden shadow-sm sm:rounded-lg` → `.card`
  - `p-6` → `.card-body`
  - `flex flex-wrap items-end gap-4` → `.filter-bar`
  - `mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm` → `.form-select`
  - `min-w-[280px]` → appropriate semantic or utility-in-CSS class
  - All inline table styling → data-table semantic classes
  - Search input with icon → `.form-input-group` + `.form-input-icon`
- Read the full file before editing to understand all inline usage

## Task 20: Standardize attendance DTR and history views
- **Agent**: ui-ux
- **Files**: resources/views/attendance/dtr.blade.php, resources/views/attendance/index.blade.php
- `dtr.blade.php`: Replace `grid grid-cols-1 lg:grid-cols-3 gap-6` with semantic class (add `.dtr-layout` to `dtr.css` if needed, or use `.dashboard-grid` if similar). Remove any remaining inline utilities
- `attendance/index.blade.php`: Already mostly uses semantic classes. Check for and replace any remaining inline Tailwind (primarily filter form area and `<x-primary-button>` which now uses semantic classes via component refactor)
- Replace alert divs with toast component usage

## Task 21: Standardize employee views
- **Agent**: ui-ux
- **Files**: resources/views/employees/index.blade.php, resources/views/employees/show.blade.php, resources/views/employees/create.blade.php, resources/views/employees/edit.blade.php
- `show.blade.php`: Replace inline `text-2xl font-bold text-gray-900` → add `.profile-name` or use existing class. Replace `text-indigo-600 font-medium` → `.profile-role` or similar. Replace `flex flex-col md:flex-row items-start md:items-center gap-6` → add to details.css as `.profile-header-layout`
- `index.blade.php`: Already mostly semantic. Remove any remaining inline `mt-1 block w-full` from form inputs
- `create.blade.php` and `edit.blade.php`: Remove remaining inline utilities — rely on refactored components for inputs/labels/errors. Any `mt-1 block w-full` or `mt-2` should come from the component defaults
- Replace alert divs with toast component usage

## Task 22: Standardize leave request views with tab CSS classes
- **Agent**: ui-ux
- **Files**: resources/views/leave-requests/index.blade.php, resources/views/leave-requests/show.blade.php, resources/views/leave-requests/create.blade.php
- `index.blade.php`: Replace inline tab navigation (`px-6 py-3 text-sm font-medium border-b-2 transition-colors` and conditional active class) with `.tab-nav`, `.tab-item`, `.tab-item-active`, `.tab-badge`, `.tab-badge-active` CSS classes
- `show.blade.php`: Extract inline action button area styling. Replace any remaining inline classes
- `create.blade.php`: Rely on component refactors for form inputs. Clean up any remaining inline
- Replace alert divs with toast component usage

## Task 23: Standardize payroll views with tab CSS classes
- **Agent**: ui-ux
- **Files**: resources/views/payroll/index.blade.php, resources/views/payroll/show.blade.php, resources/views/payroll/create.blade.php
- `index.blade.php`: Replace inline tab navigation with `.tab-nav`, `.tab-item`, `.tab-item-active`, `.tab-badge` classes (same pattern as leave-requests)
- `show.blade.php`: Clean up any remaining inline utilities, ensure stat cards use semantic classes
- `create.blade.php`: Rely on component refactors. Clean up remaining inline
- Replace alert divs with toast component usage

## Task 24: Standardize remaining views — leave types, leave balances, holidays, employee loans, payslips
- **Agent**: ui-ux
- **Files**: resources/views/leave-types/index.blade.php, resources/views/leave-types/create.blade.php, resources/views/leave-types/edit.blade.php, resources/views/leave-types/show.blade.php, resources/views/leave-balances/index.blade.php, resources/views/leave-balances/manage.blade.php, resources/views/holidays/index.blade.php, resources/views/holidays/create.blade.php, resources/views/holidays/edit.blade.php, resources/views/employee-loans/index.blade.php, resources/views/employee-loans/create.blade.php, resources/views/employee-loans/edit.blade.php, resources/views/employee-loans/show.blade.php, resources/views/payslips/index.blade.php, resources/views/payslips/show.blade.php
- Audit each view for remaining inline Tailwind. Replace with semantic classes
- Rely on component refactors for `<x-text-input>`, `<x-input-label>`, `<x-input-error>`, `<x-primary-button>`
- Replace all alert divs with toast component usage
- Ensure consistent `page-container` / `page-content` / `card` / `card-body` patterns

## Task 25: Add loading states to forms
- **Agent**: ui-ux
- **Files**: resources/views/components/primary-button.blade.php, resources/views/components/danger-button.blade.php
- Add Alpine.js form submission loading state: wrap submit buttons in `x-data` that listens to form submit, adds `.btn-loading` class and shows spinner SVG
- Can be done as a simple Alpine inline: `@click="$el.classList.add('btn-loading')"` on form submit, or use `x-on:submit` on parent form
- Only apply to submit-type buttons, not link buttons

## Task 26: Final CSS cleanup and verification
- **Agent**: ui-ux
- **Files**: resources/css/components/badges.css, resources/css/modules/alerts.css
- `badges.css`: Consolidate redundant badge definitions — ensure `.badge` base class is shared and variants only add color overrides. Remove duplicated `px-2 py-1 inline-flex text-xs font-semibold rounded-full` from each variant
- `alerts.css`: Keep as non-JS fallback. Add comment noting toast component is the primary flash message UI
- Final review: ensure all CSS partials use consistent `@apply` patterns, proper ordering, no orphaned classes
