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
            <button wire:click="create()"
                    class="bg-blue-500 hover:bg-blue-700 text-white py-1 mb-6 px-3 rounded my-3 mt-1">Create New board
            </button>
            @if($isOpen)
            @include('livewire.create')
            @endif

            <hr>

            <div>
                <div class="container mx-auto py-8">
                    <div class="grid grid-cols-3 gap-4" wire:sortable="updateLeadOrder"
                         wire:sortable-group="updateLeadStatus">
                        @foreach($data as $board)

                        <div class="col-span-1" wire:key="group-{{ $board }}">
                            <div class="bg-white rounded-lg shadow-md p-4"
                                 wire:sortable-group.item-group="{{ $board }}" style="background-color: #2563eb">

                                <div wire:sortable-group.handle wire:key="lead-{{ $board->id }}"

                                     wire:sortable-group.item="{{ $board->id }}"
                                     class="rounded-lg p-2 mb-2 cursor-pointer text-white text-lg">
                                    <span>{{ $board->name }}</span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <table class="table-fixed w-full">
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
                        <!--button to redirect to boards/{id}-->
                        <a href="{{ route('boards.show', $board->id) }}"
                           class="bg-green-500 hover:bg-green-700 text-white py-1 px-3 rounded">Show</a>
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>



