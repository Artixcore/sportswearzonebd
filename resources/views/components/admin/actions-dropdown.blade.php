@props(['align' => 'right'])
<div class="relative inline-block text-left group">
    <button type="button" class="inline-flex items-center justify-center w-8 h-8 rounded-md border border-slate-300 bg-white text-slate-600 hover:bg-slate-50">
        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"/></svg>
    </button>
    <div class="absolute z-10 mt-1 w-48 rounded-md bg-white shadow-lg border border-slate-200 py-1 opacity-0 invisible group-hover:opacity-100 group-hover:visible {{ $align === 'right' ? 'right-0' : 'left-0' }}">
        {{ $slot }}
    </div>
</div>
