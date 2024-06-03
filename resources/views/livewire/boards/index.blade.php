<div class="py-12">

    @if($createModalIsOpen)
        @include('livewire.boards.create')
    @endif

    @if($editModalIsOpen)
        @include('livewire.boards.edit')
    @endif

    @if($deleteModalIsOpen)
        @include('livewire.boards.delete')
    @endif

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg px-4 py-4">

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

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="text-center sm:text-left text-2xl">
                    Boards
                </div>
                <div class="text-center sm:text-right">
                    <button wire:click="openCreateModal()"
                            class="bg-green-600 hover:bg-green-600 text-white py-1 mb-6 px-3 rounded my-3 mt-1">
                        + New Board
                    </button>
                </div>
            </div>

            <hr>

            <div>
                <div class="container mx-auto py-8">
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4"
                         wire:sortable-group="updateBoardOrder">

                        @foreach($boards as $board)

                            <div class="col-span-1" wire:key="group-{{ $board }}">
                                <a href="{{ route('boards.show', $board->id) }}" class="block h-full w-full">
                                    <div class="bg-white rounded-lg shadow-md p-2 h-32 cursor-pointer"
                                         wire:sortable-group.item-group="{{ $board->id }}"
                                         style="background-color: {{ $board->color_hash }};">

                                        <div wire:key="board-{{ $board->id }}"
                                             wire:sortable-group.handle
                                             wire:sortable-group.item="{{ $board->id }}"
                                             class="flex justify-between rounded-lg mb-2">

                                            <div class="rounded-lg text-white text-lg h-32">
                                                <span>{{ $board->name }}</span>
                                            </div>

                                            <div class="grid grid-cols-2 w-auto mt-1">
                                                <div class="mr-1">
                                                    <button class="text-white"
                                                            wire:click.prevent="openEditModal('{{ $board->id }}')">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                </div>
                                                <div class="mr-1">
                                                    <button class="text-white"
                                                            wire:click.prevent="openDeleteModal('{{ $board->id }}')">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>

                        @endforeach

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
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
            });
        }

        initializeColoris();
    </script>
</div>
