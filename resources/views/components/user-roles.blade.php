@props(['roles'])

<div class="flex flex-wrap gap-2">
    @foreach($roles as $role)
        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
            {{ $role->name === 'admin' ? 'bg-red-100 text-red-800' : 
              ($role->name === 'teacher' ? 'bg-blue-100 text-blue-800' : 
               'bg-green-100 text-green-800') }}">
            {{ $role->name }}
        </span>
    @endforeach
</div>
