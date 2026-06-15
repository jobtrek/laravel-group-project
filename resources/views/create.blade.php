<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Propose a project') }}
        </h2>
    </x-slot>

    <div class="flex justify-center py-12">
        <div class="w-full max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    @if ($errors->any())
                        <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded text-red-700 text-sm">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <h3 class="text-2xl font-medium text-gray-900 mb-6">New Project Proposal</h3>

                    <form method="POST" action="/propositions">
                        @csrf
                        <x-proposition.wizard />
                    </form>

                </div>
            </div>
        </div>
    </div>

</x-app-layout>
