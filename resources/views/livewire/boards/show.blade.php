<div class="py-12 h-96">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 h-96">

        @if($createListModalIsOpen)
            @include('livewire.lists.create')
        @endif

        @if($createTaskModalIsOpen)
            @include('livewire.tasks.create')
        @endif

        @if($editTaskModalIsOpen)
            @include('livewire.tasks.edit')
        @endif

        @if($deleteListModalIsOpen)
            @include('livewire.lists.delete')
        @endif

        @if (session()->has('message'))
            <div class="bg-teal-100 border-t-4 border-teal-500 rounded-b text-teal-900 px-4 py-3 shadow-md my-3"
                 role="alert">
                <div class="flex">
                    <div>
                        <p class="text-sm">{{ session('message') }}</p>
                    </div>
                </div>
            </div>
        @endif


        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg px-4 py-4">

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>

                    <input type="text"
                           class="text-2xl mb-2 border-none bg-transparent focus:outline-none flex-grow w-full text-center sm:text-left"
                           wire:model="name"
                           wire:blur="updateBoardName()"
                           wire:keydown.enter="updateBoardName()">

                    <p></p>

                    @error('name') <span class="text-red-500">{{ $message }}</span>@enderror

                </div>

                <div class="text-center sm:text-right" wire:ignore>
                    <input type="text" id="color-input" value="{{ $board->color_hash }}">

                    <button wire:click="openModal()"
                            class="h-11 bg-green-600 hover:bg-green-600 text-white py-1 mb-6 px-3 rounded my-3 mt-1">
                        + New List
                    </button>
                </div>
            </div>


            <div class="overflow-hidden shadow-xl sm:rounded-lg px-4 py-4 bg-indigo-600 min-h-96"
                 style="background-color: {{ $board->color_hash }}">

                <div>
                    <div class="container mx-auto py-8">
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4"
                             wire:sortable="updateTaskOrder" wire:sortable-group="updateTaskStatus">
                            @foreach($lists as $list)

                                <div class=" rounded-lg bg-gray-50 p-3" wire:key="group-{{ $list->id }}">

                                    <div class="flex justify-between bg-white rounded-lg mb-2">
                                        <input type="text"
                                               class="text-lg font-bold mb-2 border-none bg-transparent focus:outline-none flex-grow text-left w-4"
                                               value="{{ $list->name }}"
                                               wire:blur="updateListName('{{ $list->id }}', $event.target.value)"
                                               wire:keydown.enter="updateListName('{{ $list->id }}', $event.target.value)">


                                        <div class="grid grid-cols-2 w-auto mt-3">
                                            <div class="mr-1">
                                                <button class="text-gray-400 hover:text-green-500"
                                                        wire:click="openCreateTaskModal('{{ $list->id }}')">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </div>
                                            <div class="mr-1">
                                                <button class="text-gray-400 hover:text-red-700"
                                                        wire:click="openDeleteListModal('{{ $list->id }}')">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="bg-white rounded-lg shadow-md p-4"
                                         wire:sortable-group.item-group="{{ $list->id }}">


                                        @foreach($data[$list->name] as $task)
                                            <div wire:click="openTaskDetails('{{ $task->id }}')"
                                                 wire:key="task-{{ $task->id }}"
                                                 wire:sortable-group.handle
                                                 wire:sortable-group.item="{{ $task->id }}"
                                                 class="bg-gray-100 border-b rounded-lg p-2 mb-2 cursor-pointer">
                                                <span>{{ $task->name }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            function initializeColoris() {
                if (document.querySelector('.clr-picker')) {
                    Coloris.close();
                }

                Coloris({
                    el: '#color-input',
                    theme: 'classic',
                    swatches: [
                        '#264653',
                        '#2a9d8f',
                        '#e9c46a',
                        '#f4a261',
                        '#e76f51'
                    ],
                    onChange: (color) => {
                        @this.
                        set('colorHash', color);

                    }
                });
            }

            initializeColoris();
        });
    </script>

</div>
