<x-layouts.app :title="__('Members')">
    <div class="min-h-screen bg-zinc-50 dark:bg-zinc-900">
        <div class="bg-white dark:bg-zinc-800 border-b border-zinc-200 dark:border-zinc-700">
            <div class="px-4 sm:px-6 lg:px-8 py-6">
                <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ __('Member Management') }}</h1>
                <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Manage SACCO member profiles and accounts') }}</p>
            </div>
        </div>
        <div class="px-4 sm:px-6 lg:px-8 py-8">
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-8 text-center">
                <flux:icon.users class="mx-auto h-12 w-12 text-zinc-400 mb-4" />
                <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100 mb-2">{{ __('Member Directory') }}</h3>
                <p class="text-zinc-600 dark:text-zinc-400">{{ __('Comprehensive member management with profiles, accounts, and relationship tracking.') }}</p>
            </div>
        </div>
    </div>
</x-layouts.app> 