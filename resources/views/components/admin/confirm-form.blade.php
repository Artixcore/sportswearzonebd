@props(['action', 'method' => 'POST', 'message' => 'Are you sure?', 'confirmLabel' => 'Confirm'])
<form action="{{ $action }}" method="POST" class="inline" onsubmit="return confirm('{{ addslashes($message) }}');">
    @csrf
    @method($method)
    <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-medium">{{ $confirmLabel }}</button>
</form>
