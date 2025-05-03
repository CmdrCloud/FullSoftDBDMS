<!DOCTYPE html>
<html lang="{{ str_replace('_','-',app()->getLocale()) }}" x-data="userManager()">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>FullSoft — Gestionar Usuarios</title>
  @vite(['resources/css/app.css'])
  <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
  <style>[x-cloak]{display:none!important;}</style>
</head>
<body class="bg-gray-50">

  {{-- Navbar --}}
  <nav class="absolute top-0 left-0 w-full bg-white dark:bg-[#0a0a0a] p-4 shadow-md">
    <div class="container mx-auto flex justify-between items-center">
      <div class="flex items-center">
        <a href="{{ route('home') }}" class="inline-block py-1.5 dark:text-[#EDEDEC] text-[#1b1b18] rounded-sm text-lg leading-normal cursor-pointer hover:text-zinc-200 dark:hover:text-zinc-50">FullSoft</a>
      </div>
      <div>
        <a href="{{ route('dashboard') }}" class="inline-block px-2 py-1.5 dark:text-[#EDEDEC] text-[#1b1b18] rounded-sm text-sm leading-normal cursor-pointer hover:text-zinc-200 dark:hover:text-zinc-50 no-underline transition-colors hover:underline">Regresar</a>
        <a href="{{ route('logout') }}" class="inline-block px-2 py-1.5 dark:text-[#EDEDEC] text-[#1b1b18] rounded-sm text-sm leading-normal cursor-pointer hover:text-zinc-200 dark:hover:text-zinc-50 no-underline transition-colors hover:underline">Cerrar Sesión</a>
      </div>
    </div>
  </nav>

  <main class="container mx-auto mt-24">

    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold">Gestionar Usuarios</h1>
      <button @click="openCreate()" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-500">
        Agregar Nuevo Usuario
      </button>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
      <div class="mb-4 p-2 bg-green-100 text-green-800 rounded">
        {{ session('success') }}
      </div>
    @endif

    {{-- Users table --}}
    <table class="w-full border-collapse text-left">
      <thead>
        <tr class="bg-gray-100">
          <th class="border px-3 py-2">#</th>
          <th class="border px-3 py-2">Nombre</th>
          <th class="border px-3 py-2">Email</th>
          <th class="border px-3 py-2">Título</th>
          <th class="border px-3 py-2">Comisión</th>
          <th class="border px-3 py-2">Admin</th>
          <th class="border px-3 py-2">Acciones</th>
        </tr>
      </thead>
      <tbody>
        @foreach($users as $u)
        <tr class="hover:bg-white">
          <td class="border px-3 py-2">{{ $u->id }}</td>
          <td class="border px-3 py-2">{{ $u->name }}</td>
          <td class="border px-3 py-2">{{ $u->email }}</td>
          <td class="border px-3 py-2">{{ $u->title }}</td>
          <td class="border px-3 py-2">{{ number_format($u->commission,2) }}</td>
          <td class="border px-3 py-2 text-center">{{ $u->isAdmin ? '✔️':'❌' }}</td>
          <td class="border px-3 py-2 space-x-2">
            <button
              @click="openEdit({
                id: {{ $u->id }},
                name: '{{ $u->name }}',
                email: '{{ $u->email }}',
                title: '{{ $u->title ?? '' }}',
                commission: '{{ $u->commission ?? 0 }}',
                isAdmin: {{ $u->isAdmin ? 'true':'false' }}
              })"
              class="bg-yellow-500 text-white px-2 rounded hover:bg-yellow-400 cursor-pointer"
            >Editar</button>

            <form action="{{ route('users.destroy', $u->id) }}" method="POST" class="inline">
              @csrf @method('DELETE')
              <button
                type="submit"
                class="bg-red-600 text-white px-2 rounded hover:bg-red-500 cursor-pointer"
                onclick="return confirm('¿Eliminar usuario?')"
              >Eliminar</button>
            </form>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>

    {{-- Create Modal --}}
    <div
      x-cloak
      x-show="createOpen"
      class="fixed inset-0 bg-black/50 flex items-center justify-center"
      @click.away="closeCreate()"
    >
      <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <h2 class="text-lg font-semibold mb-4">Nuevo Usuario</h2>
        <form action="{{ route('users.store') }}" method="POST" class="space-y-3">
          @csrf

          {{-- Validation Errors --}}
          @php $errors = session('errors') ?? new \Illuminate\Support\MessageBag(); @endphp
          @if($errors->any())
            <div class="p-2 bg-red-100 text-red-700 rounded">
              <ul class="list-disc list-inside text-sm">
                @foreach($errors->all() as $e)
                  <li>{{ $e }}</li>
                @endforeach
              </ul>
            </div>
          @endif

          <input x-model="newUser.name" name="name" placeholder="Nombre" class="w-full border p-2 rounded">
          <input x-model="newUser.email" name="email" type="email" placeholder="Email" class="w-full border p-2 rounded">
          <input name="password" type="password" placeholder="Contraseña" class="w-full border p-2 rounded">
          <input x-model="newUser.title" name="title" placeholder="Título" class="w-full border p-2 rounded">
          <input x-model="newUser.commission" name="commission" type="number" step="0.01" placeholder="Comisión" class="w-full border p-2 rounded">

          <input type="hidden" name="isAdmin" :value="newUser.isAdmin ? '1' : '0'">
          <label class="inline-flex items-center">
            <input type="checkbox" x-model="newUser.isAdmin" class="mr-2"> Es Admin
          </label>

          <div class="flex justify-end space-x-2 mt-4">
            <button type="button" @click="closeCreate()" class="px-4 py-2 border rounded hover:bg-gray-100 cursor-pointer">Cancelar</button>
            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-500 cursor-pointer">Guardar</button>
          </div>
        </form>
      </div>
    </div>

    {{-- Edit Modal --}}
    <div
      x-cloak
      x-show="editOpen"
      class="fixed inset-0 bg-black/50 flex items-center justify-center"
      @click.away="closeEdit()"
    >
      <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <h2 class="text-lg font-semibold mb-4">Editar Usuario</h2>
        <form :action="`/users/${selected.id}`" method="POST" class="space-y-3">
          @csrf
          @method('PUT')
          <input x-model="selected.name" name="name" placeholder="Nombre" class="w-full border p-2 rounded">
          <input x-model="selected.email" name="email" type="email" placeholder="Email" class="w-full border p-2 rounded">
          <input name="password" type="password" placeholder="Nueva contraseña (opcional)" class="w-full border p-2 rounded">
          <input x-model="selected.title" name="title" placeholder="Título" class="w-full border p-2 rounded">
          <input x-model="selected.commission" name="commission" type="number" step="0.01" placeholder="Comisión" class="w-full border p-2 rounded">

          <input type="hidden" name="isAdmin" :value="selected.isAdmin ? '1' : '0'">
          <label class="inline-flex items-center">
            <input type="checkbox" x-model="selected.isAdmin" class="mr-2"> Es Admin
          </label>

          <div class="flex justify-end space-x-2 mt-4">
            <button type="button" @click="closeEdit()" class="px-4 py-2 border rounded hover:bg-gray-100 cursor-pointer">Cancelar</button>
            <button type="submit" class="px-4 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-400 cursor-pointer">Actualizar</button>
          </div>
        </form>
      </div>
    </div>

  </main>

  <script>
  function userManager() {
    return {
      createOpen: false,
      editOpen:   false,
      selected:   {},
      newUser: {
        name: '', email: '', title: '', commission: '', isAdmin: false
      },
      openCreate() {
        this.newUser = { name: '', email: '', title: '', commission: '', isAdmin: false };
        this.createOpen = true;
      },
      closeCreate() { this.createOpen = false },
      openEdit(u) {
        this.selected = { ...u };
        this.editOpen = true;
      },
      closeEdit() { this.editOpen = false },
    }
  }
  </script>

</body>
</html>
