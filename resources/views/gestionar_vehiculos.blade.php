{{-- resources/views/gestionar_vehiculos.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_','-',app()->getLocale()) }}" x-data="vehicleManager()">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>FullSoft</title>
  @vite(['resources/css/app.css'])
  <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
  <style>
    /* hide until Alpine is ready */
    [x-cloak] { display: none !important; }
  </style>
</head>
<body class="bg-gray-50">

  {{-- Navbar --}}
    <nav class="absolute top-0 left-0 w-full bg-white dark:bg-[#0a0a0a] p-4 shadow-md">
        <div class="container mx-auto flex justify-between items-center">
            <div class="flex items-center">
                <a href="{{ route('home')}} " class="inline-block py-1.5 dark:text-[#EDEDEC] text-[#1b1b18] rounded-sm text-lg leading-normal cursor-pointer hover:text-zinc-200 dark:hover:text-zinc-50">FullSoft</a>
            </div>
            <div>
                <a href="{{ route('dashboard')}}" class="inline-block px-2 py-1.5 dark:text-[#EDEDEC] text-[#1b1b18] rounded-sm text-sm leading-normal cursor-pointer hover:text-zinc-200 dark:hover:text-zinc-50 no-underline transition-colors hover:underline">Regresar</a>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button
                    type="submit"
                    class="inline-block px-2 py-1.5 dark:text-[#EDEDEC] text-[#1b1b18]rounded-sm text-sm leading-normal cursor-pointer hover:text-zinc-200 dark:hover:text-zinc-50 no-underline transition-colors hover:underline">Cerrar Sesión</button>
                </form>
            </div>
        </div>
    </nav>

  <main class="container mx-auto mt-24">

    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold">Gestionar Vehículos</h1>
      <button @click="openCreate()" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-400 cursor-pointer">
        Agregar Nuevo Vehículo
      </button>
    </div>

    {{-- Vehicles Table --}}
    <table class="w-full border-collapse text-left">
      <thead class="bg-gray-100">
        <tr>
          <th class="border px-3 py-2">Marca</th>
          <th class="border px-3 py-2">Modelo</th>
          <th class="border px-3 py-2">Año</th>
          <th class="border px-3 py-2">Cilindrada</th>
          <th class="border px-3 py-2">A/C</th>
          <th class="border px-3 py-2">Metalizada</th>
          <th class="border px-3 py-2">Precio</th>
          <th class="border px-3 py-2">Parte Pago</th>
          <th class="border px-3 py-2">Placa</th>
          <th class="border px-3 py-2">Imagen</th>
          <th class="border px-3 py-2">Acciones</th>
        </tr>
      </thead>
      <tbody>
        @foreach($vehicles as $v)
        <tr class="hover:bg-white">
          <td class="border px-3 py-2">{{ $v->brand }}</td>
          <td class="border px-3 py-2">{{ $v->model }}</td>
          <td class="border px-3 py-2">{{ $v->year }}</td>
          <td class="border px-3 py-2">{{ $v->cylinders }}</td>
          <td class="border px-3 py-2 text-center">{{ $v->airConditioning ? '✔️':'❌' }}</td>
          <td class="border px-3 py-2 text-center">{{ $v->metallicPaint ? '✔️':'❌' }}</td>
          <td class="border px-3 py-2">${{ number_format($v->price,2) }}</td>
          <td class="border px-3 py-2 text-center">{{ $v->partOfPayment ? '✔️':'❌' }}</td>
          <td class="border px-3 py-2">{{ $v->numberPlate ?: '❌' }}</td>
          <td class="border px-3 py-2 text-center">
            @if($v->imgPath)
              <img src="{{ asset($v->imgPath) }}" class="w-16 h-16 object-cover rounded">
            @else
              ❌
            @endif
          </td>
          <td class="border px-3 py-2 space-x-2">
            <button
              @click="openEdit({
                id: {{ $v->id }},
                brand: '{{ $v->brand }}',
                model: '{{ $v->model }}',
                year: '{{ $v->year }}',
                cylinders: '{{ $v->cylinders }}',
                price: '{{ $v->price }}',
                numberPlate: '{{ $v->numberPlate }}',
                airConditioning: {{ $v->airConditioning ? 'true':'false' }},
                metallicPaint:   {{ $v->metallicPaint   ? 'true':'false' }},
                partOfPayment:   {{ $v->partOfPayment   ? 'true':'false' }},
                imgPath: '{{ $v->imgPath }}'
              })"
              class="bg-yellow-500 text-white px-2 rounded hover:bg-yellow-400 cursor-pointer"
            >Editar</button>

            <form action="{{ route('vehicles.destroy',$v->id) }}" method="POST" class="inline">
              @csrf @method('DELETE')
              <button
                type="submit"
                class="bg-red-600 text-white px-2 rounded hover:bg-red-400 cursor-pointer"
                onclick="return confirm('¿Eliminar?')"
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
        <h2 class="text-lg font-semibold mb-4">Nuevo Vehículo</h2>
        <form action="{{ route('vehicles.store') }}" method="POST" enctype="multipart/form-data" class="space-y-3">
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

          <input x-model="newVehicle.brand"       name="brand"         placeholder="Marca"        class="w-full border p-2 rounded">
          <input x-model="newVehicle.model"       name="model"         placeholder="Modelo"       class="w-full border p-2 rounded">
          <input x-model="newVehicle.year"        name="year"  type="number" placeholder="Año"           class="w-full border p-2 rounded">
          <input x-model="newVehicle.cylinders"   name="cylinders"     placeholder="Cilindrada"   class="w-full border p-2 rounded">
          <input x-model="newVehicle.price"       name="price" type="number" step="0.01" placeholder="Precio" class="w-full border p-2 rounded">
          <input x-model="newVehicle.numberPlate" name="numberPlate" placeholder="Placa"        class="w-full border p-2 rounded">

          {{-- Checkboxes with hidden fallbacks --}}
          <input type="hidden" name="airConditioning" :value="newVehicle.airConditioning ? 1 : 0">
          <label class="inline-flex items-center space-x-1">
            <input type="checkbox" x-model="newVehicle.airConditioning">
            <span>Aire Acondicionado</span>
          </label>

          <input type="hidden" name="metallicPaint" :value="newVehicle.metallicPaint ? 1 : 0">
          <label class="inline-flex items-center space-x-1">
            <input type="checkbox" x-model="newVehicle.metallicPaint">
            <span>Pintura Metalizada</span>
          </label>

          <input type="hidden" name="partOfPayment" :value="newVehicle.partOfPayment ? 1 : 0">
          <label class="inline-flex items-center space-x-1">
            <input type="checkbox" x-model="newVehicle.partOfPayment">
            <span>Parte de Pago</span>
          </label>

          <input
            name="imgPath"
            type="file"
            class="cursor-pointer block w-full text-gray-500
                   file:mr-4 file:py-4 file:px-3
                   file:rounded-full file:border-0
                   file:text-sm file:font-semibold
                   file:bg-red-600 file:text-white
                   hover:file:bg-red-400"
          >

          <div class="flex justify-end space-x-2 mt-4">
            <button type="button" @click="closeCreate()" class="px-4 py-2 border rounded hover:bg-gray-100 cursor-pointer">Cancelar</button>
            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-400 cursor-pointer">Guardar</button>
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
        <h2 class="text-lg font-semibold mb-4">Editar Vehículo</h2>
        <form x-bind:action="`/vehicles/${selected.id}`" method="POST" enctype="multipart/form-data" class="space-y-3">
          @csrf @method('PUT')

          <input x-model="selected.brand"       name="brand"         placeholder="Marca"        class="w-full border p-2 rounded">
          <input x-model="selected.model"       name="model"         placeholder="Modelo"       class="w-full border p-2 rounded">
          <input x-model="selected.year"        name="year"  type="number" placeholder="Año"           class="w-full border p-2 rounded">
          <input x-model="selected.cylinders"   name="cylinders"     placeholder="Cilindrada"   class="w-full border p-2 rounded">
          <input x-model="selected.price"       name="price" type="number" step="0.01" placeholder="Precio" class="w-full border p-2 rounded">
          <input x-model="selected.numberPlate" name="numberPlate" placeholder="Placa"        class="w-full border p-2 rounded">

          {{-- Preview existing --}}
          <template x-if="selected.imgPath">
            <img :src="`/${selected.imgPath}`" class="w-24 h-24 object-cover rounded mb-2">
          </template>

          {{-- Hidden fallbacks + real checkboxes --}}
          <input type="hidden" name="airConditioning" :value="selected.airConditioning ? 1 : 0">
          <label class="inline-flex items-center space-x-1">
            <input type="checkbox" x-model="selected.airConditioning">
            <span>Aire Acondicionado</span>
          </label>

          <input type="hidden" name="metallicPaint" :value="selected.metallicPaint ? 1 : 0">
          <label class="inline-flex items-center space-x-1">
            <input type="checkbox" x-model="selected.metallicPaint">
            <span>Pintura Metalizada</span>
          </label>

          <input type="hidden" name="partOfPayment" :value="selected.partOfPayment ? 1 : 0">
          <label class="inline-flex items-center space-x-1">
            <input type="checkbox" x-model="selected.partOfPayment">
            <span>Parte de Pago</span>
          </label>

          {{-- New image optional --}}
          <input
            name="imgPath"
            type="file"
            class="block w-full text-gray-500
                   file:mr-4 file:py-2 file:px-4
                   file:rounded-full file:border-0
                   file:text-sm file:font-semibold
                   file:bg-red-600 file:text-white
                   hover:file:bg-red-400"
          >

          <div class="flex justify-end space-x-2 mt-4">
            <button type="button" @click="closeEdit()" class="px-4 py-2 border rounded hover:bg-gray-100 cursor-pointer">Cancelar</button>
            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-400 cursor-pointer">Actualizar</button>
          </div>
        </form>
      </div>
    </div>
  </main>

  <script>
    function vehicleManager() {
      return {
        createOpen: false,
        editOpen:   false,
        selected:   {},
        newVehicle: {
          brand: '', model: '', year: '', cylinders: '',
          price: '', numberPlate: '',
          airConditioning: false,
          metallicPaint:   false,
          partOfPayment:   false,
          imgPath: ''
        },
        openCreate() {
          this.newVehicle = {
            brand: '', model: '', year: '', cylinders: '',
            price: '', numberPlate: '',
            airConditioning: false,
            metallicPaint:   false,
            partOfPayment:   false,
            imgPath: ''
          };
          this.createOpen = true;
        },
        closeCreate() { this.createOpen = false },
        openEdit(v) {
          this.selected = { ...v };
          this.editOpen = true;
        },
        closeEdit() { this.editOpen = false },
      }
    }
  </script>

</body>
</html>
