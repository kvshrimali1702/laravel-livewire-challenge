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

<div>
    <h1 class="text-2xl font-bold mb-6">Task Manager</h1>

    {{-- Add Task Form --}}
    <form wire:submit="addTask" class="mb-6 flex gap-2">
        <div class="flex-1">
            <input
                type="text"
                wire:model="title"
                placeholder="Enter task title..."
                class="w-full border rounded px-3 py-2"
                maxlength="255"
            />
            @error('title')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            Add Task
        </button>
    </form>

    {{-- Task List --}}
    <ul class="space-y-2">
        @foreach ($tasks as $task)
            <li class="flex items-center gap-3 border rounded px-3 py-2">
                <input
                    type="checkbox"
                    wire:click="toggle({{ $task->id }})"
                    @checked($task->completed)
                    class="w-4 h-4 cursor-pointer"
                />
                <span class="{{ $task->completed ? 'line-through text-gray-400' : '' }}">
                    {{ $task->title }}
                </span>
            </li>
        @endforeach
    </ul>
</div>
