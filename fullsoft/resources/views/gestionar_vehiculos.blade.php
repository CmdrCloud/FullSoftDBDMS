<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="vehicleManager()" xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>FullSoft</title>
  @vite(['resources/css/app.css'])
  <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>
<body class="bg-gray-50">

  {{-- Navbar --}}
  <nav class="absolute top-0 left-0 w-full bg-white p-4 shadow">
    <div class="container mx-auto flex justify-between">
      <a href="{{ route('home') }}" class="font-bold">FullSoft</a>
      <div class="space-x-4">
        <a href="{{ route('dashboard') }}">Regresar</a>
        <a href="{{ route('logout') }}">Cerrar Sesion</a>
      </div>
    </div>
  </nav>

  <main class="container mx-auto mt-24">

    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold">Gestionar Vehículos</h1>
      <button @click="openCreate()" class="bg-red-600 text-white px-4 py-2 rounded cursor-pointer hover:bg-[#f6a3ab]">Agregar Nuevo Vehículo</button>
    </div>

    {{-- Vehicles table --}}
    <table class="w-full border-collapse text-left">
      <thead>
        <tr class="bg-gray-100">
          <th class="border px-3 py-2">Marca</th>
          <th class="border px-3 py-2">Modelo</th>
          <th class="border px-3 py-2">Año</th>
          <th class="border px-3 py-2">Cilindrada</th>
          <th class="border px-3 py-2">A/C</th>
          <th class="border px-3 py-2">Metalizada</th>
          <th class="border px-3 py-2">Precio (USD)</th>
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
          <td class="border px-3 py-2">
            @if($v->imgPath)
              <img src="{{ asset($v->imgPath) }}" class="w-16 h-16 object-cover rounded">
            @else
              ❌
            @endif
          </td>
          <td class="border px-3 py-2 space-x-2">
            <button @click="openEdit({
              id: {{ $v->id }},
              brand: '{{ $v->brand }}',
              model: '{{ $v->model }}',
              year: '{{ $v->year }}',
              cylinders: '{{ $v->cylinders }}',
              price: '{{ $v->price }}',
              numberPlate: '{{ $v->numberPlate }}',
              airConditioning: {{ $v->airConditioning ? 'true':'false' }},
              metallicPaint: {{ $v->metallicPaint ? 'true':'false' }},
              partOfPayment: {{ $v->partOfPayment ? 'true':'false' }}
            })" class="bg-yellow-500 text-white px-2 rounded cursor-pointer hover:bg-yellow-400">Editar</button>

            <form action="{{ route('vehicles.destroy',$v->id) }}" method="POST" class="inline">
              @csrf @method('DELETE')
              <button type="submit" class="bg-[#F61500] text-white px-2 rounded cursor-pointer hover:bg-[#f6a3ab]" onclick="return confirm('Eliminar?')">Eliminar</button>
            </form>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>

    {{-- Create Modal --}}
    <div x-show="createOpen" class="fixed inset-0 bg-black/50 flex items-center justify-center" @click.away="closeCreate()">
      <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <h2 class="text-lg font-semibold mb-4">Nuevo Vehículo</h2>
        <form action="{{ route('vehicles.store') }}" method="POST" enctype="multipart/form-data" class="space-y-3">
          @csrf
          <input x-model="newVehicle.brand"      name="brand"         placeholder="Marca"        class="w-full border p-2 rounded">
          <input x-model="newVehicle.model"      name="model"         placeholder="Modelo"       class="w-full border p-2 rounded">
          <input x-model="newVehicle.year"       name="year"  type="number" placeholder="Año"           class="w-full border p-2 rounded">
          <input x-model="newVehicle.cylinders"  name="cylinders"     placeholder="Cilindrada"   class="w-full border p-2 rounded">
          <input x-model="newVehicle.price"      name="price" type="number" step="0.01" placeholder="Precio"  class="w-full border p-2 rounded">
          <input x-model="newVehicle.numberPlate" name="numberPlate"  placeholder="Placa"        class="w-full border p-2 rounded">
          <div class="flex flex-col space-y-2">
            <label><input x-model="newVehicle.airConditioning"  name="airConditioning" type="checkbox" class="cursor-pointer"> Aire Acondicionado</label>
            <label><input x-model="newVehicle.metallicPaint"    name="metallicPaint"   type="checkbox" class="cursor-pointer"> Pintura Metalizada</label>
            <label><input x-model="newVehicle.partOfPayment"    name="partOfPayment"   type="checkbox" class="cursor-pointer"> Parte de Pago</label>
          </div>
          <input name="imgPath" type="file" class="cursor-pointer block w-full text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-[#F61500] file:text-white hover:file:bg-[#ffa39b]"">
          <div class="flex justify-end space-x-2 mt-4">
            <button type="button" @click="closeCreate()" class="cursor-pointer px-4 py-2 border rounded hover:bg-zinc-100">Cancelar</button>
            <button type="submit" class="cursor-pointer px-4 py-2 bg-[#F61500] text-white rounded hover:bg-[#f6a3ab]">Guardar</button>
          </div>
        </form>
      </div>
    </div>

    {{-- Edit Modal --}}
    <div x-show="editOpen" class="fixed inset-0 bg-black/50 flex items-center justify-center" @click.away="closeEdit()">
      <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <h2 class="text-lg font-semibold mb-4">Editar Vehículo</h2>
        <form x-bind:action="`/vehicles/${selected.id}`" method="POST" enctype="multipart/form-data" class="space-y-3">
          @csrf @method('PUT')
          <input x-model="selected.brand"      name="brand"         placeholder="Marca"        class="w-full border p-2 rounded">
          <input x-model="selected.model"      name="model"         placeholder="Modelo"       class="w-full border p-2 rounded">
          <input x-model="selected.year"       name="year"  type="number" placeholder="Año"           class="w-full border p-2 rounded">
          <input x-model="selected.cylinders"  name="cylinders"     placeholder="Cilindrada"   class="w-full border p-2 rounded">
          <input x-model="selected.price"      name="price" type="number" step="0.01" placeholder="Precio"  class="w-full border p-2 rounded">
          <input x-model="selected.numberPlate" name="numberPlate"  placeholder="Placa"        class="w-full border p-2 rounded">
          <div class="flex flex-col space-y-2">
            <label><input x-model="selected.airConditioning"  name="airConditioning" type="checkbox" class="mr-2"> Aire Acondicionado</label>
            <label><input x-model="selected.metallicPaint"    name="metallicPaint"   type="checkbox" class="mr-2"> Pintura Metalizada</label>
            <label><input x-model="selected.partOfPayment"    name="partOfPayment"   type="checkbox" class="mr-2"> Parte de Pago</label>
          </div>
          <input name="imgPath" type="file" class="cursor-pointer block w-full text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-[#F61500] file:text-white hover:file:bg-[#ffa39b]">
          <div class="flex justify-end space-x-2 mt-4">
            <button type="button" @click="closeEdit()" class="cursor-pointer px-4 py-2 border rounded hover:bg-zinc-100">Cancelar</button>
            <button type="submit"               class="cursor-pointer px-4 py-2 bg-[#F61500] text-white rounded hover:bg-[#f6a3ab]">Actualizar</button>
          </div>
        </form>
      </div>
    </div>

  </main>

  <script>
    function vehicleManager() {
      return {
        createOpen: false,
        editOpen: false,
        selected: {},
        newVehicle: {
          brand: '', model: '', year: '', cylinders: '',
          price: '', numberPlate: '',
          airConditioning: false,
          metallicPaint: false,
          partOfPayment: false
        },
        openCreate() { this.createOpen = true },
        closeCreate(){ this.createOpen = false },
        openEdit(v){ this.selected = v; this.editOpen = true },
        closeEdit(){ this.editOpen = false },
      }
    }
  </script>

</body>
</html>
