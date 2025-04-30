<x-layouts.app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="grid auto-rows-min gap-4 md:grid-cols-3">
            <div class="flex items-center justify-center relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 bg-zinc-500">
                <a href="{{ route('ventas') }}" class="cursor-pointer p-2 hover:text-gray-300">Registrar una Venta</a>
            </div>
            <div class="flex items-center justify-center relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 bg-zinc-500">
                <a href="{{ route('backup') }}" class="cursor-pointer p-2 hover:text-gray-300">Realizar Backup</a>
            </div>
            <div class="flex items-center justify-center relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 bg-zinc-500">
                <a href="{{ route('reporte') }}" class="cursor-pointer p-2 hover:text-gray-300">Emitir Reportes</a>
            </div>
        </div>
        <div class="relative h-full flex-1 overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
            <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
        </div>
    </div>
</x-layouts.app>
