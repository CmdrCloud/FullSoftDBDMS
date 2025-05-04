{{-- resources/views/backup.blade.php --}}
<x-layouts.app :title="__('Backups')">
  <div class="container mx-auto p-6">
    {{-- Status Message --}}
    @if(session('status'))
      <div class="mb-4 px-4 py-2 bg-green-100 text-green-800 rounded">
        {{ session('status') }}
      </div>
    @endif

    {{-- Error Message --}}
    @if(session('error'))
      <div class="mb-4 px-4 py-2 bg-red-100 text-red-800 rounded">
        {{ session('error') }}
      </div>
    @endif

    {{-- Run Backup Button --}}
    <form method="POST" action="{{ route('backup.run') }}" class="mb-6">
      @csrf
      <button
        type="submit"
        class="px-6 py-2 text-white rounded bg-[#F61500] hover:bg-[#F61500]/80 focus:outline-none focus:ring-2 focus:ring-[#F61500]/50 transition duration-200">
        <svg class="inline w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Run Backup
      </button>
    </form>

    {{-- Backups Table --}}
    <div class="overflow-x-auto text-zinc-800">
      <table class="min-w-full bg-white rounded shadow">
        <thead>
          <tr class="bg-gray-100 text-left">
            <th class="px-4 py-2">Filename</th>
            <th class="px-4 py-2">Date</th>
            <th class="px-4 py-2">Size</th>
            <th class="px-4 py-2">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($backups as $backup)
            <tr class="border-t">
              <td class="px-4 py-2">{{ $backup['name'] }}</td>
              <td class="px-4 py-2">{{ $backup['modified_at']->format('Y-m-d H:i:s') }}</td>
              <td class="px-4 py-2">{{ number_format($backup['size'] / 1024, 2) }} KB</td>
              <td class="px-4 py-2">
                <a
                    href="{{ route('backup.download', ['filename' => $backup['path']]) }}"
                    class="text-blue-600 hover:underline">
                    Download
                </a>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="4" class="px-4 py-2 text-center text-gray-500">
                No backups found.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</x-layouts.app>
