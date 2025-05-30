<?php

use App\Models\Task;
use Livewire\Volt\Component;

new class extends Component {
    public $tasks, $public_tasks, $title = '';
    public $visibility = 'private';

    function mount() {
        $this->refreshTasks();
    }

    function newTask() {
        $task = Task::create([
            'user_id' => auth()->id(),
            'title' => $this->title,
            'visibility' => $this->visibility
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
        $task = Task::find($id);
        if (!$task || $task->user_id !== auth()->id()) {
            return;
        }

        $task->delete();
        $this->refreshTasks();
    }

    function refreshTasks() {
        // Mostrar todas as tarefas públicas ou as privadas do usuário
        $this->tasks = Task::query()
            ->orderByDesc('created_at')
            ->where('user_id', auth()->id())
            ->get();
        $this->public_tasks = Task::query()
            ->orderByDesc('created_at')
            ->where('visibility', 'public')
            ->get();
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

            <flux:radio.group wire:model="visibility" label="Visibilidade" variant="segmented">
                <flux:radio icon="lock-closed" value="private" label="Privado"  />
                <flux:radio icon="lock-open" value="public" label="Público" />
            </flux:radio.group>

            <div class="flex mt-3">
                <flux:spacer />
                <flux:button wire:click="newTask" type="button" variant="primary">Salvar</flux:button>
            </div>
        </div>
    </flux:modal>

    <p class="mt-6">Suas tarefas:</p>
    <flux:separator class="my-2"></flux:separator>
    <ul class="space-y-2">
        @foreach($tasks as $task)
            <li class="flex justify-between items-center px-4 py-3 rounded-xl bg-zinc-100 dark:bg-zinc-700 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="flex items-center">
                        <input id="default-checkbox" type="checkbox" wire:click="toggle({{ $task->id }})" @checked($task->completed) class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded-sm focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                        <label for="default-checkbox" class="{{ $task->completed ? 'line-through' : '' }} ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">{{ $task->title }}</label>
                    </div>
                </div>
                <button wire:click="delete({{ $task->id }})" class="text-red-600 hover:text-red-400 dark:text-red-400 dark:hover:text-red-300 text-lg font-bold">×</button>
            </li>
        @endforeach
    </ul>

    <p class="mt-6">Tarefas públicas:</p>
    <flux:separator class="my-2"></flux:separator>
    <ul class="space-y-2">
        @foreach($public_tasks as $task)
            <li class="flex justify-between items-center px-4 py-3 rounded-xl bg-zinc-100 dark:bg-zinc-700 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="flex items-center">
                        <input id="default-checkbox" type="checkbox" wire:click="toggle({{ $task->id }})" @checked($task->completed) class="w-4 h-4 text-blue-600 bg-gray-100 rounded-sm focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700">
                        <label for="default-checkbox" class="{{ $task->completed ? 'line-through' : '' }} ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">{{ $task->title }} — por {{ $task->user->name }}</label>
                    </div>
                </div>
                <button wire:click="delete({{ $task->id }})" class="text-red-600 hover:text-red-400 dark:text-red-400 dark:hover:text-red-300 text-lg font-bold">×</button>
            </li>
        @endforeach
    </ul>
</div>
