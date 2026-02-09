<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Reports') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800">Implementation Reports</h3>
                    <p class="mt-2 text-sm text-gray-600">Select the audience to view the report and export formats.</p>

                    <div class="mt-6 grid gap-4 sm:grid-cols-3">
                        <div class="rounded-lg border border-gray-200 p-4">
                            <h4 class="text-sm font-semibold uppercase tracking-widest text-gray-500">Internal</h4>
                            <p class="mt-2 text-sm text-gray-600">Internal review and operations.</p>
                            <div class="mt-4 flex flex-wrap gap-2">
                                <a href="{{ route('reports.internal') }}" class="rounded-md bg-gray-900 px-3 py-1.5 text-xs font-semibold text-white">View</a>
                                <a href="{{ route('reports.internal.csv') }}" class="rounded-md border border-gray-300 px-3 py-1.5 text-xs font-semibold text-gray-700">CSV</a>
                                <a href="{{ route('reports.internal.pdf') }}" class="rounded-md border border-gray-300 px-3 py-1.5 text-xs font-semibold text-gray-700">PDF</a>
                            </div>
                        </div>
                        <div class="rounded-lg border border-gray-200 p-4">
                            <h4 class="text-sm font-semibold uppercase tracking-widest text-gray-500">BDUK</h4>
                            <p class="mt-2 text-sm text-gray-600">Project report for Boeing Defence UK.</p>
                            <div class="mt-4 flex flex-wrap gap-2">
                                <a href="{{ route('reports.bduk') }}" class="rounded-md bg-gray-900 px-3 py-1.5 text-xs font-semibold text-white">View</a>
                                <a href="{{ route('reports.bduk.csv') }}" class="rounded-md border border-gray-300 px-3 py-1.5 text-xs font-semibold text-gray-700">CSV</a>
                                <a href="{{ route('reports.bduk.pdf') }}" class="rounded-md border border-gray-300 px-3 py-1.5 text-xs font-semibold text-gray-700">PDF</a>
                            </div>
                        </div>
                        <div class="rounded-lg border border-gray-200 p-4">
                            <h4 class="text-sm font-semibold uppercase tracking-widest text-gray-500">MOD</h4>
                            <p class="mt-2 text-sm text-gray-600">Project report for Ministry of Defence.</p>
                            <div class="mt-4 flex flex-wrap gap-2">
                                <a href="{{ route('reports.mod') }}" class="rounded-md bg-gray-900 px-3 py-1.5 text-xs font-semibold text-white">View</a>
                                <a href="{{ route('reports.mod.csv') }}" class="rounded-md border border-gray-300 px-3 py-1.5 text-xs font-semibold text-gray-700">CSV</a>
                                <a href="{{ route('reports.mod.pdf') }}" class="rounded-md border border-gray-300 px-3 py-1.5 text-xs font-semibold text-gray-700">PDF</a>
                            </div>
                        </div>
                    </div>
                    <p class="mt-6 text-xs text-gray-500">PDF links open a print-friendly view. Use your browser to print/save as PDF.</p>
                </div>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800">DSFA Tank Report</h3>
                    <p class="mt-2 text-sm text-gray-600">Latest tank list per plant for Defence Strategic Fuel Authority.</p>

                    <div class="mt-6 flex flex-wrap gap-2">
                        <a href="{{ route('reports.dsfa-tanks') }}" class="rounded-md bg-gray-900 px-3 py-1.5 text-xs font-semibold text-white">View</a>
                        <a href="{{ route('reports.dsfa-tanks.pdf') }}" class="rounded-md border border-gray-300 px-3 py-1.5 text-xs font-semibold text-gray-700">PDF</a>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800">Routine Status (Annual)</h3>
                    <p class="mt-2 text-sm text-gray-600">Routine completion, asset capture, and SRF status by year.</p>

                    <div class="mt-6 flex flex-wrap gap-2">
                        <a href="{{ route('reports.routine-status') }}" class="rounded-md bg-gray-900 px-3 py-1.5 text-xs font-semibold text-white">View</a>
                        <a href="{{ route('reports.routine-status.pdf') }}" class="rounded-md border border-gray-300 px-3 py-1.5 text-xs font-semibold text-gray-700">PDF</a>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800">Outstanding SRFs</h3>
                    <p class="mt-2 text-sm text-gray-600">Outstanding service report tasks with aging.</p>

                    <div class="mt-6 flex flex-wrap gap-2">
                        <a href="{{ route('reports.outstanding-srfs') }}" class="rounded-md bg-gray-900 px-3 py-1.5 text-xs font-semibold text-white">View</a>
                        <a href="{{ route('reports.outstanding-srfs.pdf') }}" class="rounded-md border border-gray-300 px-3 py-1.5 text-xs font-semibold text-gray-700">PDF</a>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800">Asset Capture Compliance</h3>
                    <p class="mt-2 text-sm text-gray-600">Routines completed but asset capture still pending.</p>

                    <div class="mt-6 flex flex-wrap gap-2">
                        <a href="{{ route('reports.asset-capture-compliance') }}" class="rounded-md bg-gray-900 px-3 py-1.5 text-xs font-semibold text-white">View</a>
                        <a href="{{ route('reports.asset-capture-compliance.pdf') }}" class="rounded-md border border-gray-300 px-3 py-1.5 text-xs font-semibold text-gray-700">PDF</a>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800">SRF Volume by Engineer</h3>
                    <p class="mt-2 text-sm text-gray-600">Monthly SRF volume totals by engineer.</p>

                    <div class="mt-6 flex flex-wrap gap-2">
                        <a href="{{ route('reports.srf-volume') }}" class="rounded-md bg-gray-900 px-3 py-1.5 text-xs font-semibold text-white">View</a>
                        <a href="{{ route('reports.srf-volume.pdf') }}" class="rounded-md border border-gray-300 px-3 py-1.5 text-xs font-semibold text-gray-700">PDF</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
