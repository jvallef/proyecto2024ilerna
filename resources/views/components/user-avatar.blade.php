@props(['user'])

<div {{ $attributes->merge(['class' => 'flex items-center']) }}>
    <div class="flex-shrink-0 h-10 w-10">
        <img class="h-10 w-10 rounded-full" 
             src="{{ $user->profile_photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&color=7F9CF5&background=EBF4FF' }}" 
             alt="{{ $user->name }}">
    </div>
    <div class="ml-4">
        <div class="text-sm font-medium text-gray-900">
            {{ $user->name }}
        </div>
        <div class="text-sm text-gray-500">
            {{ $user->email }}
        </div>
    </div>
</div>
