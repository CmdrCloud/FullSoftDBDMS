{{-- resources/views/catalogo.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>FullSoft - Catálogo de Vehículos</title>

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.bunny.net">
  <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

  <!-- Styles -->
  @vite(['resources/css/app.css'])
</head>
<body class="bg-[#FDFDFC] dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-white flex p-6 lg:p-8 items-center lg:justify-center min-h-screen flex-col">

  <!-- Navbar -->
  <nav class="absolute top-0 left-0 w-full bg-white dark:bg-[#0a0a0a] p-4 shadow-md">
    <div class="container mx-auto flex justify-between items-center">
      <a href="{{ route('home') }}" class="inline-block py-1.5 dark:text-[#EDEDEC] text-[#1b1b18] rounded-sm text-lg hover:text-zinc-200">FullSoft</a>
      <a href="{{ route('home') }}" class="inline-block px-2 py-1.5 dark:text-[#EDEDEC] text-[#1b1b18] rounded-sm text-sm hover:text-zinc-200">Inicio</a>
    </div>
  </nav>

  <!-- Main content -->
  <div class="container mx-auto mt-16">
    <div class="mb-8 text-center">
      <h1 class="text-3xl font-bold">Catálogo de Vehículos</h1>
      <p class="text-gray-600 dark:text-gray-400">Explore nuestra selección de vehículos disponibles</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Available Vehicles -->
      <div class="lg:col-span-2 bg-white dark:bg-[#252525] p-4 rounded-lg shadow-md">
        <h2 class="text-xl font-semibold mb-4">Vehículos disponibles</h2>

        @if($vehicles->count())
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4" id="vehicles-list">
            @foreach ($vehicles as $vehicle)
              <div class="bg-gray-100 dark:bg-[#1b1b18] p-4 rounded-lg shadow-sm">
                <img
                  src="{{ $vehicle->imgPath ? asset($vehicle->imgPath) : asset('images/car-placeholder.png') }}"
                  alt="{{ $vehicle->brand }} {{ $vehicle->model }}"
                  class="w-full h-48 object-cover rounded-lg mb-3"
                >
                <h3 class="text-lg font-semibold">{{ $vehicle->model }}</h3>
                <div class="grid grid-cols-2 gap-1 mt-2">
                  <span class="text-gray-600 dark:text-gray-400">Marca:</span><span>{{ $vehicle->brand }}</span>
                  <span class="text-gray-600 dark:text-gray-400">Precio:</span><span>${{ number_format($vehicle->price,2) }}</span>
                  <span class="text-gray-600 dark:text-gray-400">Año:</span><span>{{ $vehicle->year }}</span>
                  <span class="text-gray-600 dark:text-gray-400">Cilindrada:</span><span>{{ $vehicle->cylinders }}</span>
                </div>
                <button
                  class="mt-4 bg-[#F61500] text-white px-4 py-2 rounded w-full hover:bg-red-700"
                  onclick="showVehicleDetails({{ $vehicle->id }})"
                >
                  Ver detalles
                </button>
              </div>
            @endforeach
          </div>
        @else
          <div class="text-center py-8 bg-gray-100 dark:bg-[#1b1b18] rounded-lg">
            <p class="text-xl text-gray-500 dark:text-gray-400">No se encontraron vehículos disponibles.</p>
            <p class="text-gray-500 dark:text-gray-400 mt-2">Intente cambiar los criterios de búsqueda.</p>
          </div>
        @endif
      </div>

      <!-- Search & Featured -->
      <div class="bg-white dark:bg-[#252525] p-4 rounded-lg shadow-md">
        <h2 class="text-xl font-semibold mb-4">Buscar vehículo</h2>
        <form action="{{ route('catalogo') }}" method="GET" class="space-y-4">
          <div class="grid grid-cols-2 gap-4">
            @foreach(['model'=>'modelo','brand'=>'marca','year'=>'año','cylinders'=>'cilindrada'] as $value => $label)
              <label class="flex items-center">
                <input type="radio" name="search_type" value="{{ $value }}" class="mr-2">
                Buscar por {{ $label }}
              </label>
            @endforeach
          </div>
          <div class="flex">
            <input
              type="text"
              name="query"
              placeholder="Buscar..."
              class="flex-grow border border-zinc-300 dark:border-gray-600 rounded-l px-4 py-2 focus:ring-2 focus:ring-red-500"
            >
            <button type="submit" class="bg-[#F61500] text-white rounded-r px-4 py-2 hover:bg-red-700">Buscar</button>
          </div>

          <h3 class="text-lg font-semibold mt-8 mb-2">Vehículos destacados</h3>
          <div class="space-y-3">
            @if($vehicles->count())
              @foreach($vehicles->take(3) as $fv)
                <div class="bg-gray-100 dark:bg-[#1b1b18] p-3 rounded-lg flex items-center">
                  <img
                    src="{{ $fv->imgPath ? asset($fv->imgPath) : asset('images/car-placeholder.png') }}"
                    alt="{{ $fv->brand }} {{ $fv->model }}"
                    class="w-16 h-12 object-cover rounded mr-3"
                  >
                  <div>
                    <p class="font-medium">{{ $fv->brand }} {{ $fv->model }}</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">${{ number_format($fv->price,2) }} – {{ $fv->year }}</p>
                  </div>
                </div>
              @endforeach
            @else
              <p class="text-center text-gray-500 dark:text-gray-400">No hay vehículos destacados.</p>
            @endif
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Vehicle Details Modal -->
  <div id="vehicleModal" class="fixed inset-0 hidden flex items-center justify-center bg-black bg-opacity-50 p-4 z-50">
    <div class="bg-white dark:bg-[#252525] p-6 rounded-lg shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
      <div class="flex justify-between items-center mb-4">
        <h2 class="text-2xl font-bold" id="modalVehicleTitle">Detalles del Vehículo</h2>
        <button class="text-gray-500 hover:text-gray-700" onclick="closeModal()">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
        <img id="modalVehicleImage" src="" alt="Vehículo" class="w-full h-48 object-cover rounded">
        <div>
          <p><strong>Marca:</strong> <span id="modalVehicleBrand"></span></p>
          <p><strong>Modelo:</strong> <span id="modalVehicleModel"></span></p>
          <p><strong>Año:</strong> <span id="modalVehicleYear"></span></p>
          <p><strong>Cilindrada:</strong> <span id="modalVehicleCylinders"></span></p>
          <p><strong>Matrícula:</strong> <span id="modalVehicleNumberPlate"></span></p>
          <p><strong>Precio:</strong> $<span id="modalVehicleBasePrice"></span></p>
        </div>
      </div>

      <div class="border-t pt-4">
        <h3 class="text-xl font-semibold mb-2">Características</h3>
        <div class="grid grid-cols-2 gap-2">
          <div class="flex items-center">
            <span id="airConditioningIcon" class="mr-2"></span>Aire acondicionado
          </div>
          <div class="flex items-center">
            <span id="metallicPaintIcon" class="mr-2"></span>Pintura metálica
          </div>
        </div>
      </div>

      <div class="mt-6 border-t pt-4 text-center text-gray-600 dark:text-gray-400">
        Para más información o comprar, visite nuestra concesionaria.
      </div>

      <div class="flex justify-end space-x-2 mt-4">
        <button class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-700" onclick="closeModal()">Cerrar</button>
      </div>
    </div>
  </div>

  <!-- Footer -->
  <footer class="w-full bg-white dark:bg-black dark:text-white text-black py-4 text-center">
    © {{ date('Y') }} FullSoft. Todos los derechos reservados.
  </footer>

  <script>
    function showVehicleDetails(id) {
        fetch(`/api/vehicles/${id}`)
        .then(res => {
            if (!res.ok) throw new Error(`Status ${res.status}`);
            return res.json();
        })
        .then(data => {
            // Populate modal
            document.getElementById('modalVehicleTitle').textContent = `${data.brand} ${data.model}`;
            document.getElementById('modalVehicleBrand').textContent = data.brand;
            document.getElementById('modalVehicleModel').textContent = data.model;
            document.getElementById('modalVehicleYear').textContent = data.year;
            document.getElementById('modalVehicleCylinders').textContent = data.cylinders;
            document.getElementById('modalVehicleNumberPlate').textContent = data.numberPlate || 'N/A';

            // ← Fix: convert to Number before toFixed()
            document.getElementById('modalVehicleBasePrice').textContent = parseFloat(data.price).toFixed(2);

            // Image
            const img = document.getElementById('modalVehicleImage');
            img.src = data.imgPath ? `/${data.imgPath}` : '/images/car-placeholder.png';
            img.alt = `${data.brand} ${data.model}`;

            // Features
            document.getElementById('airConditioningIcon').innerHTML = data.airConditioning
            ? '<span class="text-green-500">✔️</span>'
            : '<span class="text-red-500">❌</span>';
            document.getElementById('metallicPaintIcon').innerHTML = data.metallicPaint
            ? '<span class="text-green-500">✔️</span>'
            : '<span class="text-red-500">❌</span>';

            document.getElementById('vehicleModal').classList.remove('hidden');
        })
        .catch(err => {
            console.error('Error fetching vehicle details:', err);
            alert('No se pudieron cargar los detalles. Intente de nuevo.');
        });
    }

        function closeModal() {
            document.getElementById('vehicleModal').classList.add('hidden');
        }
    </script>
</body>
</html>
