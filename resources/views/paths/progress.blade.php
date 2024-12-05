@extends('layouts.app')

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-6">
                        <h2 class="text-3xl font-semibold mb-2">{{ $path->name }} - Progress</h2>
                        <p class="text-gray-600">Track your learning progress in this path</p>
                    </div>

                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="text-xl font-semibold">{{ $path->name }}</h3>
                                <p class="text-gray-600">{{ $path->description }}</p>
                            </div>
                            @php
                                $enrollment = $path->enrollments->where('user_id', auth()->id())->first();
                                $progress = $enrollment ? $enrollment->progress : 0;
                            @endphp
                            <div class="text-right">
                                <div class="text-2xl font-bold text-blue-600">{{ $progress }}%</div>
                                <div class="text-sm text-gray-500">Complete</div>
                            </div>
                        </div>

                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ $progress }}%"></div>
                        </div>

                        @if($path->modules->count() > 0)
                            <div class="mt-6 space-y-4">
                                @foreach($path->modules as $module)
                                    <div class="border rounded-lg p-4">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <h4 class="font-semibold">{{ $module->name }}</h4>
                                                <p class="text-sm text-gray-600">{{ $module->description }}</p>
                                            </div>
                                            @php
                                                $completed = $enrollment ? $enrollment->completedModules->contains($module->id) : false;
                                            @endphp
                                            <div>
                                                @if($completed)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        Completed
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                        Pending
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <p class="text-gray-600">No modules available for this path yet.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
