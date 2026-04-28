<div
    x-data="{
        toasts: [],
        addToast(type, message) {
            const id = Date.now();
            this.toasts.push({ id, type, message, leaving: false });
            setTimeout(() => this.removeToast(id), 5000);
        },
        removeToast(id) {
            const toast = this.toasts.find(t => t.id === id);
            if (toast) {
                toast.leaving = true;
                setTimeout(() => {
                    this.toasts = this.toasts.filter(t => t.id !== id);
                }, 300);
            }
        },
        iconPath(type) {
            const icons = {
                success: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
                error: 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z',
                warning: 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z',
                info: 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'
            };
            return icons[type] || icons.info;
        }
    }"
    x-init="
        @if(session('success'))
            addToast('success', '{{ session('success') }}');
        @endif
        @if(session('error'))
            addToast('error', '{{ session('error') }}');
        @endif
        @if(session('warning'))
            addToast('warning', '{{ session('warning') }}');
        @endif
        @if(session('info'))
            addToast('info', '{{ session('info') }}');
        @endif
    "
    @toast.window="addToast($event.detail.type, $event.detail.message)"
    class="toast-container"
>
    <template x-for="toast in toasts" :key="toast.id">
        <div
            :class="[
                'toast',
                'toast-' + toast.type,
                toast.leaving ? 'toast-leave' : 'toast-enter'
            ]"
        >
            <svg class="toast-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="iconPath(toast.type)"></path>
            </svg>
            <p class="toast-message" x-text="toast.message"></p>
            <button @click="removeToast(toast.id)" class="toast-close-btn">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    </template>
</div>
