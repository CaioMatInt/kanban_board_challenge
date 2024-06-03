<div class="py-12">


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
                    <button wire:click="create()"
                            class="bg-green-600 hover:bg-green-600 text-white py-1 mb-6 px-3 rounded my-3 mt-1">
                        + New Board
                    </button>
                </div>
            </div>


            @if($isOpen)
                @include('livewire.create')
            @endif

            <hr>

            <div>
                <div class="container mx-auto py-8">
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4" wire:sortable-group="updateBoardOrder">

                    @foreach($data as $board)
                            <div class="col-span-1" wire:key="group-{{ $board }}">
                                <a href="{{ route('boards.show', $board->id) }}" class="block h-full w-full">
                                    <div class="bg-white rounded-lg shadow-md p-2 h-32 cursor-pointer"
                                         wire:sortable-group.item-group="{{ $board->id }}" style="background-color: {{ $board->color_hash }};">

                                        <div wire:key="board-{{ $board->id }}"
                                             wire:sortable-group.handle
                                             wire:sortable-group.item="{{ $board->id }}"
                                             class="rounded-lg text-white text-lg">
                                            <span>{{ $board->name }}</span>
                                        </div>

                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>


            {{--            <table class="table-fixed w-full">
                            <thead>
                            <tr class="bg-gray-100">
                                <th class="px-4 py-2">Name</th>
                                <th class="px-4 py-2">Color</th>
                                <th class="px-4 py-2 w-60">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($boards as $board)

                                <tr>
                                    <td class="border px-4 py-2">{{ $board->name }}</td>
                                    <td class="border px-4 py-2">{{ $board->color_hash }}</td>
                                    <td class="border px-4 py-2 text-center">
                                        <button wire:click="edit({{ $board->id }})"
                                                class="bg-blue-500 hover:bg-blue-700 text-white py-1 px-3 rounded">Edit
                                        </button>
                                        <button wire:click="delete({{ $board->id }})"
                                                class="bg-red-500 hover:bg-red-700 text-white py-1 px-3 rounded">Delete
                                        </button>

                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>--}}
        </div>
    </div>
</div>
