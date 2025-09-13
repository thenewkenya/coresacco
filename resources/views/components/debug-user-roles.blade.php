@if(config('app.debug'))
<div class="fixed bottom-4 right-4 bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded z-50 text-xs max-w-sm">
    <strong>Debug Info:</strong><br>
    User: {{ auth()->user()->name }}<br>
    Email: {{ auth()->user()->email }}<br>
    Roles: {{ auth()->user()->roles->pluck('name')->join(', ') ?: 'None' }}<br>
    Has admin role: {{ auth()->user()->hasRole('admin') ? 'Yes' : 'No' }}<br>
    Is admin: {{ auth()->user()->isAdmin() ? 'Yes' : 'No' }}<br>
    Has view-members permission: {{ auth()->user()->hasPermission('view-members') ? 'Yes' : 'No' }}
</div>
@endif
