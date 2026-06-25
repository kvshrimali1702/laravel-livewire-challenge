<?php

use App\Models\Task;
use Livewire\Volt\Component;
use Livewire\Attributes\Validate;

new class extends Component {
    #[Validate('required|string|max:255')]
    public string $title = '';

    public function addTask(): void
    {
        $this->validate();

        Task::create([
            'title' => $this->title,
            'completed' => false,
        ]);

        $this->reset('title');
    }

    public function toggle(int $id): void
    {
        $task = Task::findOrFail($id);
        $task->update(['completed' => ! $task->completed]);
    }

    public function with(): array
    {
        return [
            'tasks' => Task::latest()->get(),
        ];
    }
}; ?>

<div class="space-y-6">

    {{-- Add Task Card --}}
    <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl p-6 shadow-xl">
        <h2 class="text-sm font-semibold text-slate-300 uppercase tracking-widest mb-4">New Task</h2>

        <form wire:submit="addTask" class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1">
                <input
                    type="text"
                    wire:model="title"
                    placeholder="What needs to be done?"
                    maxlength="255"
                    class="w-full bg-white/10 border border-white/15 text-white placeholder-slate-500 rounded-xl px-4 py-3 text-sm font-medium
                           focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent
                           transition duration-200"
                />
                @error('title')
                    <p class="mt-2 flex items-center gap-1.5 text-rose-400 text-xs font-medium">
                        <svg class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <button
                type="submit"
                class="inline-flex items-center justify-center gap-2 bg-gradient-to-r from-purple-600 to-indigo-600
                       hover:from-purple-500 hover:to-indigo-500 text-white font-semibold text-sm
                       px-6 py-3 rounded-xl shadow-lg shadow-purple-500/25
                       transition-all duration-200 hover:scale-[1.02] active:scale-[0.98]
                       focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 focus:ring-offset-transparent
                       whitespace-nowrap"
            >
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                Add Task
            </button>
        </form>
    </div>

    {{-- Task List Card --}}
    <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl shadow-xl overflow-hidden">

        {{-- Card Header --}}
        <div class="px-6 py-4 border-b border-white/10 flex items-center justify-between">
            <h2 class="text-sm font-semibold text-slate-300 uppercase tracking-widest">Tasks</h2>
            <span class="text-xs font-semibold text-slate-400 bg-white/10 px-2.5 py-1 rounded-full">
                {{ $tasks->count() }} {{ Str::plural('task', $tasks->count()) }}
            </span>
        </div>

        {{-- Empty State --}}
        @if($tasks->isEmpty())
            <div class="flex flex-col items-center justify-center py-16 px-6 text-center">
                <div class="w-16 h-16 rounded-full bg-white/5 flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
                <p class="text-slate-400 font-medium text-sm">No tasks yet</p>
                <p class="text-slate-600 text-xs mt-1">Add your first task above to get started.</p>
            </div>

        {{-- Task Items --}}
        @else
            <ul class="divide-y divide-white/5">
                @foreach ($tasks as $task)
                    <li
                        wire:key="task-{{ $task->id }}"
                        class="group flex items-center gap-4 px-6 py-4 transition-colors duration-150
                               hover:bg-white/5 {{ $task->completed ? 'opacity-60' : '' }}"
                    >
                        {{-- Custom Checkbox --}}
                        <button
                            wire:click="toggle({{ $task->id }})"
                            type="button"
                            class="flex-shrink-0 w-5 h-5 rounded-full border-2 flex items-center justify-center transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-1 focus:ring-offset-transparent
                                   {{ $task->completed
                                       ? 'bg-gradient-to-br from-purple-500 to-indigo-600 border-transparent shadow-md shadow-purple-500/30'
                                       : 'border-slate-600 hover:border-purple-400 bg-transparent' }}"
                            aria-label="{{ $task->completed ? 'Mark incomplete' : 'Mark complete' }}"
                        >
                            @if($task->completed)
                                <svg class="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                            @endif
                        </button>

                        {{-- Task Title --}}
                        <span class="flex-1 text-sm font-medium transition-all duration-200
                                     {{ $task->completed ? 'line-through text-slate-500' : 'text-slate-200' }}">
                            {{ $task->title }}
                        </span>

                        {{-- Status Badge --}}
                        @if($task->completed)
                            <span class="hidden sm:inline-flex items-center gap-1 text-xs font-semibold text-emerald-400 bg-emerald-400/10 px-2.5 py-1 rounded-full">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                Done
                            </span>
                        @else
                            <span class="hidden sm:inline-flex items-center text-xs font-semibold text-slate-500 bg-white/5 px-2.5 py-1 rounded-full">
                                Pending
                            </span>
                        @endif
                    </li>
                @endforeach
            </ul>

            {{-- Footer Stats --}}
            @php
                $completed = $tasks->where('completed', true)->count();
                $total = $tasks->count();
                $percent = $total > 0 ? round(($completed / $total) * 100) : 0;
            @endphp
            <div class="px-6 py-4 border-t border-white/10 bg-white/[0.02]">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs text-slate-500 font-medium">Progress</span>
                    <span class="text-xs font-semibold text-slate-400">{{ $completed }}/{{ $total }} completed</span>
                </div>
                <div class="w-full bg-white/10 rounded-full h-1.5 overflow-hidden">
                    <div
                        class="h-full bg-gradient-to-r from-purple-500 to-indigo-500 rounded-full transition-all duration-500"
                        style="width: {{ $percent }}%"
                    ></div>
                </div>
            </div>
        @endif
    </div>
</div>
