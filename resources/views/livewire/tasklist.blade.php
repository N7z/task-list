<?php

use App\Models\Task;
use Livewire\Volt\Component;

new class extends Component {
    public $tasks, $title = '';

    function mount() {
        $this->refreshTasks();
    }

    function newTask() {
        $task = Task::create([
            'title' => $this->title
        ]);
        Flux::modal('add-task')->close();
        $this->refreshTasks();
    }

    function toggle($id)
    {
        $task = Task::find($id);
        $task->completed = !$task->completed;
        $task->save();
        $this->refreshTasks();
    }

    function delete($id)
    {
        Task::find($id)?->delete();
        $this->refreshTasks();
    }

    function refreshTasks() {
        $this->tasks = Task::orderByDesc('created_at')->get();
    }
} ?>

<div>
    <flux:modal.trigger name="add-task">
        <flux:button variant="primary">Adicionar Tarefa</flux:button>
    </flux:modal.trigger>

    <flux:modal name="add-task" class="md:w-96">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Nova Tarefa</flux:heading>
                <flux:text class="mt-2">Crie uma nova tarefa para a lista.</flux:text>
            </div>

            <flux:input label="Título" type="text" wire:model="title" />

            <div class="flex mt-3">
                <flux:spacer />
                <flux:button wire:click="newTask" type="button" variant="primary">Salvar</flux:button>
            </div>
        </div>
    </flux:modal>

    <ul class="space-y-2 mt-6">
        @foreach($tasks as $task)
            <li class="flex justify-between items-center px-4 py-3 rounded-xl bg-zinc-100 dark:bg-zinc-700 shadow-sm">
                <div class="flex items-center gap-3">
                    <input type="checkbox" wire:click="toggle({{ $task->id }})" @checked($task->completed) class="w-5 h-5 text-blue-600 rounded dark:bg-zinc-700 border-gray-300 focus:ring-blue-500" />
                    <span class="text-base {{ $task->completed ? 'line-through text-gray-400 dark:text-gray-500' : 'text-gray-800 dark:text-gray-100' }}">
                        {{ $task->title }}
                    </span>
                </div>
                <button wire:click="delete({{ $task->id }})" class="text-red-600 hover:text-red-400 dark:text-red-400 dark:hover:text-red-300 text-lg font-bold">×</button>
            </li>
        @endforeach
    </ul>
</div>
