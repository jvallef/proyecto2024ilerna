@props(['status'])

<div class="flex flex-wrap gap-2">
    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
        {{ $status === 'published' ? 'bg-green-100 text-green-800' : 
           ($status === 'draft' ? 'bg-yellow-100 text-yellow-800' : 
            'bg-red-100 text-red-800') }}">
        {{ $status === 'published' ? 'Publicada' : 
           ($status === 'draft' ? 'Borrador' : 'Inactiva') }}
    </span>
</div>
