<div class="py-12">








    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg px-4 py-4">
            @if (session()->has('message'))
                <div class="bg-teal-100 border-t-4 border-teal-500 rounded-b text-teal-900 px-4 py-3 shadow-md my-3" role="alert">
                    <div class="flex">
                        <div>
                            <p class="text-sm">{{ session('message') }}</p>
                        </div>
                    </div>
                </div>
            @endif
            <button wire:click="create()" class="bg-blue-500 hover:bg-blue-700 text-white py-1 mb-6 px-3 rounded my-3 mt-1">Create New board</button>
            @if($isOpen)
                @include('livewire.create')
            @endif

            <hr>

            <div>
                <div class="container mx-auto py-8">
                    <div class="grid grid-cols-3 gap-4" wire:sortable="updateLeadOrder" wire:sortable-group="updateLeadStatus">
                        @foreach($order as $status)
                            <div class="col-span-1" wire:key="group-{{ $status }}">
                                <div class="bg-white rounded-lg shadow-md p-4" wire:sortable-group.item-group="{{ $status }}">
                                    @if(!is_null($data[$status]))

                                        <div wire:sortable-group.handle wire:key="lead-{{ $data[$status]->id }}"
                                             wire:sortable-group.item="{{ $data[$status]->id }}"
                                             class="bg-yellow-200 rounded-lg p-2 mb-2 cursor-pointer">
                                            <span>{{ $data[$status]->name }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-1">

                @foreach($boards as $board)
                    <div class="flex items-center justify-center border-8 border-indigo-600" style="background-color: #2563eb">
                        <div class="flex flex-col w-full" style="background-color: white">
                            <div class="w-full text-center">{{ $board->name }}</div>
                            <div class="w-full text-center" style="background-color: #2563eb">{{ $board->color_hash }}</div>
                        </div>
                    </div>
                @endforeach
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
                            <button wire:click="edit({{ $board->id }})" class="bg-blue-500 hover:bg-blue-700 text-white py-1 px-3 rounded">Edit</button>
                            <button wire:click="delete({{ $board->id }})" class="bg-red-500 hover:bg-red-700 text-white py-1 px-3 rounded">Delete</button>
                            <!--button to redirect to boards/{id}-->
                            <a href="{{ route('boards.show', $board->id) }}" class="bg-green-500 hover:bg-green-700 text-white py-1 px-3 rounded">Show</a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>



