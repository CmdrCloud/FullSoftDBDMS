<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>FullSoft - Ventas</title>

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
                <div class="flex items-center">
                    <a href="{{ route('home')}}" class="inline-block py-1.5 dark:text-[#EDEDEC] text-[#1b1b18] rounded-sm text-lg leading-normal cursor-pointer hover:text-zinc-200 dark:hover:text-zinc-50">FullSoft</a>
                </div>
                <div>
                    <a href="{{ route('dashboard')}}" class="inline-block px-2 py-1.5 dark:text-[#EDEDEC] text-[#1b1b18] rounded-sm text-sm leading-normal cursor-pointer hover:text-zinc-200 dark:hover:text-zinc-50 no-underline transition-colors hover:underline">Regresar</a>
                    <a href="{{ route('logout') }}" class="inline-block px-2 py-1.5 dark:text-[#EDEDEC] text-[#1b1b18] rounded-sm text-sm leading-normal cursor-pointer hover:text-zinc-200 dark:hover:text-zinc-50 no-underline transition-colors hover:underline">Cerrar Sesion</a>
                </div>
            </div>
        </nav>

        <!-- Main content -->
        <div class="container mx-auto mt-16">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <!-- Left column for car display -->
                <div class="bg-white dark:bg-[#252525] p-4 rounded-lg shadow-md">
                    <h2 class="text-xl font-semibold mb-4">Lista de vehículos</h2>
                    <ul class="space-y-4" id="vehicles-list">
                        @if(isset($vehicles) && count($vehicles) > 0)
                            @foreach ($vehicles as $vehicle)
                                <li class="flex items-center bg-gray-100 dark:bg-[#1b1b18] p-4 rounded-lg shadow-sm">
                                    <img src="{{ $vehicle->imgPath ? asset($vehicle->imgPath) : asset('images/car-placeholder.png') }}"
                                         alt="{{ $vehicle->brand }} {{ $vehicle->model }}"
                                         class="w-24 h-16 object-cover rounded mr-4">
                                    <div class="flex-grow">
                                        <h3 class="text-lg font-semibold">{{ $vehicle->model }}</h3>
                                        <p class="text-gray-600 dark:text-gray-400">Marca: {{ $vehicle->brand }}</p>
                                        <p class="text-gray-600 dark:text-gray-400">Precio: ${{ number_format($vehicle->price, 2) }}</p>
                                        <p class="text-gray-600 dark:text-gray-400">Año: {{ $vehicle->year }}</p>
                                        <p class="text-gray-600 dark:text-gray-400">Cilindrada: {{ $vehicle->cylinders }}</p>
                                    </div>
                                    <button class="ml-auto bg-[#F61500] text-white px-4 py-2 rounded hover:bg-red-700"
                                            onclick="selectVehicle({{ $vehicle->id }})">Seleccionar</button>
                                </li>
                            @endforeach
                        @else
                            <li class="text-center py-4">No se encontraron vehículos.</li>
                        @endif
                    </ul>
                </div>

                <!-- Right column for search filters -->
                <div class="bg-white dark:bg-[#252525] p-4 rounded-lg shadow-md">
                    <h2 class="text-xl font-semibold mb-4">Buscar vehículo</h2>
                    <form action="{{ route('ventas') }}" method="GET" class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <input type="radio" name="search_type" id="searchByModel" value="model" class="mr-2">
                                <label for="searchByModel">Buscar por modelo</label>
                            </div>
                            <div>
                                <input type="radio" name="search_type" id="searchByBrand" value="brand" class="mr-2">
                                <label for="searchByBrand">Buscar por marca</label>
                            </div>
                            <div>
                                <input type="radio" name="search_type" id="searchByYear" value="year" class="mr-2">
                                <label for="searchByYear">Buscar por año</label>
                            </div>
                            <div>
                                <input type="radio" name="search_type" id="searchByCylinders" value="cylinders" class="mr-2">
                                <label for="searchByCylinders">Buscar por cilindrada</label>
                            </div>
                        </div>
                        <div class="flex">
                            <input type="text" name="query" placeholder="Buscar..."
                                   class="border border-zinc-300 dark:border-gray-600 rounded-l px-4 py-2 flex-grow focus:outline-none focus:ring-2 focus:ring-red-500">
                            <button type="submit" class="bg-[#F61500] text-white rounded-r px-4 py-2 hover:bg-red-700">Buscar</button>
                        </div>
                    </form>

                    <!-- Recent Sales -->
                    <div class="mt-8">
                        <h3 class="text-lg font-semibold mb-2">Ventas recientes</h3>
                        <div class="bg-gray-100 dark:bg-[#1b1b18] p-3 rounded-lg">
                            <div id="recent-sales-list" class="text-sm">
                                <!-- This will be populated via JavaScript -->
                                <p class="text-center text-gray-500 dark:text-gray-400">No hay ventas recientes para mostrar</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Vehicle Selection Modal -->
        <div id="vehicleModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
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
                    <div>
                        <img id="modalVehicleImage" src="" alt="Vehículo" class="w-full h-48 object-cover rounded">
                    </div>
                    <div>
                        <p><strong>Marca:</strong> <span id="modalVehicleBrand"></span></p>
                        <p><strong>Modelo:</strong> <span id="modalVehicleModel"></span></p>
                        <p><strong>Año:</strong> <span id="modalVehicleYear"></span></p>
                        <p><strong>Cilindrada:</strong> <span id="modalVehicleCylinders"></span></p>
                        <p><strong>Matrícula:</strong> <span id="modalVehicleNumberPlate"></span></p>
                        <p><strong>Precio Base:</strong> $<span id="modalVehicleBasePrice"></span></p>
                    </div>
                </div>

                <form id="saleForm" class="space-y-4">
                    <input type="hidden" id="vehicle_id" name="vehicle_id">

                    <!-- Client Information -->
                    <div class="border-t pt-4">
                        <h3 class="text-xl font-semibold mb-2">Información del Cliente</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="client_name" class="block text-sm font-medium">Nombre del Cliente *</label>
                                <input type="text" id="client_name" name="client_name" required class="w-full mt-1 border border-gray-300 dark:border-gray-600 rounded p-2">
                            </div>
                            <div>
    <label for="client_last_name" class="block text-sm font-medium">Apellido del Cliente *</label>
    <input type="text" id="client_last_name" name="client_last_name" required class="w-full mt-1 border border-gray-300 dark:border-gray-600 rounded p-2">
</div>

                            <div>
                                <label for="client_email" class="block text-sm font-medium">Email</label>
                                <input type="email" id="client_email" name="client_email" class="w-full mt-1 border border-gray-300 dark:border-gray-600 rounded p-2">
                            </div>
                            <div>
                                <label for="client_phone" class="block text-sm font-medium">Teléfono</label>
                                <input type="text" id="client_phone" name="client_phone" class="w-full mt-1 border border-gray-300 dark:border-gray-600 rounded p-2">
                            </div>
                            <div>
                                <label for="client_address" class="block text-sm font-medium">Dirección</label>
                                <input type="text" id="client_address" name="client_address" class="w-full mt-1 border border-gray-300 dark:border-gray-600 rounded p-2">
                            </div>
                            <div>
                                <label for="client_dni" class="block text-sm font-medium">DNI</label>
                                <input type="text" id="client_dni" name="client_dni" class="w-full mt-1 border border-gray-300 dark:border-gray-600 rounded p-2">
                            </div>
                            <div>
                                <label for="client_rfc" class="block text-sm font-medium">RFC</label>
                                <input type="text" id="client_rfc" name="client_rfc" class="w-full mt-1 border border-gray-300 dark:border-gray-600 rounded p-2">
                            </div>
                        </div>
                    </div>

                    <!-- Additional Options -->
                    <div class="border-t pt-4">
                        <h3 class="text-xl font-semibold mb-2">Opciones adicionales</h3>

                        <div class="flex items-center mb-2">
                            <input type="checkbox" id="air_conditioning" name="air_conditioning" class="mr-2" onchange="updateTotalPrice()">
                            <label for="air_conditioning">Aire acondicionado (+$500)</label>
                        </div>

                        <div class="flex items-center mb-4">
                            <input type="checkbox" id="metallic_paint" name="metallic_paint" class="mr-2" onchange="updateTotalPrice()">
                            <label for="metallic_paint">Pintura metálica (+$300)</label>
                        </div>
                    </div>

                    <!-- Part Payment Vehicle -->
                    <div class="border-t pt-4">
                        <h3 class="text-xl font-semibold mb-2">Parte de pago</h3>

                        <div class="flex items-center mb-2">
                            <input type="checkbox" id="part_of_payment" name="part_of_payment" class="mr-2" onchange="togglePartPayment()">
                            <label for="part_of_payment">Cliente deja vehículo como parte de pago</label>
                        </div>

                        <div id="part_payment_section" class="ml-6 mt-2 hidden">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label for="part_payment_brand" class="block text-sm font-medium">Marca:</label>
                                    <input type="text" id="part_payment_brand" name="part_payment_brand" class="w-full mt-1 border border-gray-300 dark:border-gray-600 rounded p-2">
                                </div>
                                <div>
                                    <label for="part_payment_model" class="block text-sm font-medium">Modelo:</label>
                                    <input type="text" id="part_payment_model" name="part_payment_model" class="w-full mt-1 border border-gray-300 dark:border-gray-600 rounded p-2">
                                </div>
                                <div>
                                    <label for="part_payment_year" class="block text-sm font-medium">Año:</label>
                                    <input type="number" id="part_payment_year" name="part_payment_year" min="1900" max="2025" class="w-full mt-1 border border-gray-300 dark:border-gray-600 rounded p-2">
                                </div>
                                <div>
                                    <label for="part_payment_plate" class="block text-sm font-medium">Matrícula:</label>
                                    <input type="text" id="part_payment_plate" name="part_payment_plate" class="w-full mt-1 border border-gray-300 dark:border-gray-600 rounded p-2">
                                </div>
                            </div>

                            <div class="mb-2">
                                <label for="part_payment_details" class="block text-sm font-medium">Detalles adicionales:</label>
                                <textarea id="part_payment_details" name="part_payment_details" rows="3" class="w-full mt-1 border border-gray-300 dark:border-gray-600 rounded p-2"></textarea>
                            </div>

                            <div class="mb-2">
                                <label for="part_payment_value" class="block text-sm font-medium">Valor estimado ($):</label>
                                <input type="number" id="part_payment_value" name="part_payment_value" min="0" step="0.01" class="w-full mt-1 border border-gray-300 dark:border-gray-600 rounded p-2" onchange="updateTotalPrice()">
                            </div>
                        </div>
                    </div>

                    <!-- Payment Summary -->
                    <div class="border-t pt-4">
                        <h3 class="text-xl font-semibold mb-2">Resumen de pago</h3>

                        <div class="grid grid-cols-2 gap-2 bg-gray-50 dark:bg-[#1d1d1d] p-3 rounded">
                            <div>Precio base:</div>
                            <div>$<span id="summary_base_price">0.00</span></div>

                            <div>Opciones adicionales:</div>
                            <div>$<span id="summary_options">0.00</span></div>

                            <div>Valor vehículo de parte de pago:</div>
                            <div>-$<span id="summary_part_payment">0.00</span></div>

                            <div class="font-bold text-lg">Precio final:</div>
                            <div class="font-bold text-lg">$<span id="summary_final_price">0.00</span></div>
                        </div>

                        <div class="mt-4">
                            <label for="upfront_payment" class="block text-sm font-medium">Pago inicial ($):</label>
                            <input type="number" id="upfront_payment" name="upfront_payment" min="0" step="0.01" required class="w-full mt-1 border border-gray-300 dark:border-gray-600 rounded p-2" onchange="validateUpfrontPayment()">
                            <p id="upfront_payment_error" class="text-red-500 text-sm hidden">El pago inicial no debe ser mayor que el precio final</p>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-2 pt-4">
                        <button type="button" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-700" onclick="closeModal()">Cancelar</button>
                        <button type="button" id="complete_sale_btn" class="bg-[#F61500] text-white px-4 py-2 rounded hover:bg-red-700" onclick="processSale()">Completar venta</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Receipt Modal -->
        <div id="receiptModal" class="fixed inset-0 bg-black bg-opacity-50  flex hidden items-center justify-center z-50">
            <div class="bg-white dark:bg-[#252525] p-6 rounded-lg shadow-xl w-full max-w-2xl">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-2xl font-bold">Comprobante de Venta</h2>
                    <button class="text-gray-500 hover:text-gray-700" onclick="closeReceiptModal()">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div id="receipt-content" class="mb-4 bg-gray-50 dark:bg-[#1d1d1d] p-4 rounded">
                    <div class="text-center mb-4">
                        <h3 class="text-xl font-bold">FullSoft Auto Sales</h3>
                        <p>Factura #<span id="receipt-invoice-number"></span></p>
                        <p>Fecha: <span id="receipt-date"></span></p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <h4 class="font-semibold mb-1">Información del Cliente</h4>
                            <p>Nombre: <span id="receipt-client-name"></span></p>
                            <p>Email: <span id="receipt-client-email"></span></p>
                            <p>Teléfono: <span id="receipt-client-phone"></span></p>
                        </div>
                        <div>
                            <h4 class="font-semibold mb-1">Información del Vendedor</h4>
                            <p>Nombre: <span id="receipt-seller-name"></span></p>
                            <p>Comisión (3%): $<span id="receipt-commission"></span></p>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h4 class="font-semibold mb-1">Detalles del Vehículo</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                            <p>Marca: <span id="receipt-vehicle-brand"></span></p>
                            <p>Modelo: <span id="receipt-vehicle-model"></span></p>
                            <p>Año: <span id="receipt-vehicle-year"></span></p>
                            <p>Matrícula: <span id="receipt-vehicle-plate"></span></p>
                        </div>
                    </div>

                    <div id="receipt-part-payment-section" class="mb-4 hidden">
                        <h4 class="font-semibold mb-1">Vehículo de Parte de Pago</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                            <p>Marca: <span id="receipt-part-payment-brand"></span></p>
                            <p>Modelo: <span id="receipt-part-payment-model"></span></p>
                            <p>Año: <span id="receipt-part-payment-year"></span></p>
                            <p>Matrícula: <span id="receipt-part-payment-plate"></span></p>
                            <p>Valor: $<span id="receipt-part-payment-value"></span></p>
                        </div>
                    </div>

                    <div class="border-t border-gray-300 dark:border-gray-600 pt-3 mt-3">
                        <h4 class="font-semibold mb-1">Resumen de la Venta</h4>
                        <div class="grid grid-cols-2 gap-1">
                            <p>Precio base:</p>
                            <p class="text-right">$<span id="receipt-base-price"></span></p>

                            <div id="receipt-options-section" class="col-span-2"></div>

                            <div id="receipt-part-payment-amount-row" class="hidden col-span-2">
                                <div class="grid grid-cols-2 gap-1">
                                    <p>Vehículo de parte de pago:</p>
                                    <p class="text-right">-$<span id="receipt-part-payment-amount"></span></p>
                                </div>
                            </div>

                            <p class="font-bold">Precio final:</p>
                            <p class="text-right font-bold">$<span id="receipt-final-price"></span></p>

                            <p>Pago inicial:</p>
                            <p class="text-right">$<span id="receipt-upfront-payment"></span></p>

                            <p>Saldo restante:</p>
                            <p class="text-right">$<span id="receipt-remaining-balance"></span></p>
                        </div>
                    </div>

                    <div class="text-center mt-6 text-sm text-gray-500 dark:text-gray-400">
                        <p>Gracias por su compra</p>
                        <p>Este documento sirve como comprobante de pago</p>
                    </div>
                </div>

                <div class="flex justify-between">
                    <button class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700" onclick="printReceipt()">
                        Imprimir
                    </button>
                    <button class="bg-[#F61500] text-white px-4 py-2 rounded hover:bg-red-700" onclick="closeReceiptModal()">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="w-full h-10 bg-white dark:bg-black dark:text-white text-black mt-auto py-8 text-center">
            <p>© {{ date('Y') }} FullSoft. Todos los derechos reservados.</p>
        </footer>

        <script>
            // Variables to store vehicle data
let currentVehicle = null;
const AIR_CONDITIONING_PRICE = 500;
const METALLIC_PAINT_PRICE = 300;
let recentSales = [];

// Load recent sales on page load
document.addEventListener('DOMContentLoaded', function() {
    loadRecentSales();
});

// Function to load recent sales
function loadRecentSales() {
    // In a real application, you would fetch this from the backend
    // For now, we'll use localStorage to simulate persistence
    const savedSales = localStorage.getItem('recentSales');
    if (savedSales) {
        recentSales = JSON.parse(savedSales);
        updateRecentSalesList();
    }
}

// Update the recent sales list in the UI
function updateRecentSalesList() {
    const recentSalesList = document.getElementById('recent-sales-list');

    if (recentSales.length === 0) {
        recentSalesList.innerHTML = '<p class="text-center text-gray-500 dark:text-gray-400">No hay ventas recientes para mostrar</p>';
        return;
    }

    let html = '<ul class="divide-y divide-gray-200 dark:divide-gray-700">';

    // Show at most 5 recent sales
    const salesToShow = recentSales.slice(0, 5);

    salesToShow.forEach(sale => {
        html += `
            <li class="py-2">
                <div class="flex justify-between">
                    <div>
                        <p class="font-medium">${sale.vehicleBrand} ${sale.vehicleModel}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Cliente: ${sale.clientName}</p>
                    </div>
                    <div class="text-right">
                        <p class="font-medium">$${parseFloat(sale.finalPrice).toFixed(2)}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">${formatDate(sale.date)}</p>
                    </div>
                </div>
            </li>
        `;
    });

    html += '</ul>';
    recentSalesList.innerHTML = html;
}

// Format date for display
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('es-ES', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
}

// Select a vehicle and open the modal
function selectVehicle(vehicleId) {
    fetch(`/api/vehicles/${vehicleId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Error al cargar los detalles del vehículo');
            }
            return response.json();
        })
        .then(vehicle => {
            currentVehicle = vehicle;

            // Fill the modal with vehicle information
            document.getElementById('modalVehicleTitle').textContent = `${vehicle.brand} ${vehicle.model}`;
            document.getElementById('modalVehicleBrand').textContent = vehicle.brand;
            document.getElementById('modalVehicleModel').textContent = vehicle.model;
            document.getElementById('modalVehicleYear').textContent = vehicle.year;
            document.getElementById('modalVehicleCylinders').textContent = vehicle.cylinders;
            document.getElementById('modalVehicleNumberPlate').textContent = vehicle.numberPlate || 'N/A';
            document.getElementById('modalVehicleBasePrice').textContent = parseFloat(vehicle.price).toFixed(2);

            // Set image
            const imgElement = document.getElementById('modalVehicleImage');
            if (vehicle.imgPath) {
                imgElement.src = vehicle.imgPath.startsWith('/') ? vehicle.imgPath : '/' + vehicle.imgPath;
            } else {
                imgElement.src = '/images/car-placeholder.png';
            }

            // Reset form fields
            document.getElementById('saleForm').reset();

            // Set form fields
            document.getElementById('vehicle_id').value = vehicle.id;
            document.getElementById('air_conditioning').checked = vehicle.airConditioning;
            document.getElementById('metallic_paint').checked = vehicle.metallicPaint;

            // Reset part payment fields
            document.getElementById('part_of_payment').checked = false;
            togglePartPayment();

            // Update price summary
            updateTotalPrice();

            // Show the modal
            document.getElementById('vehicleModal').classList.remove('hidden');
        })
        .catch(error => {
            console.error('Error fetching vehicle details:', error);
            alert('Error al cargar los detalles del vehículo');
        });
}

// Toggle part payment section visibility
function togglePartPayment() {
    const isChecked = document.getElementById('part_of_payment').checked;
    const partPaymentSection = document.getElementById('part_payment_section');

    if (isChecked) {
        partPaymentSection.classList.remove('hidden');
    } else {
        partPaymentSection.classList.add('hidden');
        // Clear part payment fields
        document.getElementById('part_payment_brand').value = '';
        document.getElementById('part_payment_model').value = '';
        document.getElementById('part_payment_year').value = '';
        document.getElementById('part_payment_plate').value = '';
        document.getElementById('part_payment_details').value = '';
        document.getElementById('part_payment_value').value = '';
        updateTotalPrice();
    }
}

// Update total price based on selected options and part payment
function updateTotalPrice() {
    if (!currentVehicle) return;

    const basePrice = parseFloat(currentVehicle.price);
    let optionsPrice = 0;
    let partPaymentValue = 0;

    // Calculate additional options
    if (document.getElementById('air_conditioning').checked) {
        optionsPrice += AIR_CONDITIONING_PRICE;
    }

    if (document.getElementById('metallic_paint').checked) {
        optionsPrice += METALLIC_PAINT_PRICE;
    }

    // Calculate part payment value
    if (document.getElementById('part_of_payment').checked) {
        const partPaymentInput = document.getElementById('part_payment_value').value;
        partPaymentValue = partPaymentInput ? parseFloat(partPaymentInput) : 0;
    }

    // Calculate final price
    const finalPrice = basePrice + optionsPrice - partPaymentValue;

    // Update the summary display
    document.getElementById('summary_base_price').textContent = basePrice.toFixed(2);
    document.getElementById('summary_options').textContent = optionsPrice.toFixed(2);
    document.getElementById('summary_part_payment').textContent = partPaymentValue.toFixed(2);
    document.getElementById('summary_final_price').textContent = finalPrice.toFixed(2);

    // Reset upfront payment validation
    validateUpfrontPayment();
}

// Validate upfront payment amount
function validateUpfrontPayment() {
    const upfrontPaymentInput = document.getElementById('upfront_payment');
    const upfrontPayment = parseFloat(upfrontPaymentInput.value) || 0;
    const finalPrice = parseFloat(document.getElementById('summary_final_price').textContent);
    const errorElement = document.getElementById('upfront_payment_error');
    const completeButton = document.getElementById('complete_sale_btn');

    if (upfrontPayment > finalPrice) {
        errorElement.classList.remove('hidden');
        completeButton.disabled = true;
        completeButton.classList.add('opacity-50');
    } else {
        errorElement.classList.add('hidden');
        completeButton.disabled = false;
        completeButton.classList.remove('opacity-50');
    }
}

// Close the vehicle selection modal
function closeModal() {
    document.getElementById('vehicleModal').classList.add('hidden');
    currentVehicle = null;
}

// Generate a random invoice number
function generateInvoiceNumber() {
    const prefix = 'INV';
    const timestamp = new Date().getTime().toString().slice(-6);
    const random = Math.floor(Math.random() * 900 + 100);
    return `${prefix}-${timestamp}-${random}`;
}

// Process the sale
function processSale() {
    const form = document.getElementById('saleForm');
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }

    // 1) Gather client & sale options
    const clientName    = document.getElementById('client_name').value;
    const clientLastName = document.getElementById('client_last_name').value;
    const clientEmail   = document.getElementById('client_email').value;
    const clientPhone   = document.getElementById('client_phone').value;
    const clientAddress = document.getElementById('client_address').value;
    const clientDni     = document.getElementById('client_dni').value;
    const clientRfc     = document.getElementById('client_rfc').value;

    const hasAirConditioning = document.getElementById('air_conditioning').checked;
    const hasMetallicPaint   = document.getElementById('metallic_paint').checked;
    const hasPartPayment     = document.getElementById('part_of_payment').checked;

    // 2) Build optional part-payment object
    let partPaymentData = null;
    if (hasPartPayment) {
        partPaymentData = {
            brand:   document.getElementById('part_payment_brand').value,
            model:   document.getElementById('part_payment_model').value,
            year:    document.getElementById('part_payment_year').value,
            plate:   document.getElementById('part_payment_plate').value,
            details: document.getElementById('part_payment_details').value,
            value:   parseFloat(document.getElementById('part_payment_value').value) || 0
        };
    }

    // 3) Price calculations
    const basePrice    = parseFloat(currentVehicle.price);
    const optionsPrice = (hasAirConditioning ? AIR_CONDITIONING_PRICE : 0) +
                         (hasMetallicPaint ? METALLIC_PAINT_PRICE : 0);
    const partPaymentValue = hasPartPayment ? parseFloat(document.getElementById('part_payment_value').value) || 0 : 0;
    const finalPrice   = basePrice + optionsPrice - partPaymentValue;
    const upfrontPayment = parseFloat(document.getElementById('upfront_payment').value) || 0;
    const remainingBalance = finalPrice - upfrontPayment;

    // Get seller ID safely - may be missing in the form
    const sellerIdInput = document.querySelector('input[name="seller_id"]');
    const sellerId = sellerIdInput ? sellerIdInput.value : '';

    // Get seller name safely - may be missing in the form
    const sellerNameInput = document.querySelector('input[name="seller_name"]');
    const sellerName = sellerNameInput ? sellerNameInput.value : '';

    // 4) Build payload
    const saleData = {
        vehicle_id:        currentVehicle.id,
        client_name:       clientName,
        client_email:      clientEmail,
        client_phone:      clientPhone,
        client_address:    clientAddress,
        client_dni:        clientDni,
        client_rfc:        clientRfc,
        air_conditioning:  hasAirConditioning,
        metallic_paint:    hasMetallicPaint,
        has_part_payment:  hasPartPayment,
        part_payment_data: partPaymentData,
        base_price:        basePrice,
        options_price:     optionsPrice,
        part_payment_value: partPaymentValue,
        final_price:       finalPrice,
        upfront_payment:   upfrontPayment,
        remaining_balance: remainingBalance,
        seller_id:         sellerId,
        seller_name:       sellerName,
        date:              new Date().toISOString()
    };

    // 5) AJAX call
    fetch('/ventas', {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify(saleData)
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(errorData => {
                throw new Error(errorData.error || 'Error al procesar la venta');
            });
        }
        return response.json();
    })
    .then(data => {
        // 6) Show receipt and update UI
        // Convert backend data format to our frontend format
        const receiptData = {
            date: saleData.date,
            clientName: saleData.client_name,
            clientEmail: saleData.client_email,
            clientPhone: saleData.client_phone,
            sellerId: {{ Auth::id() }},
            sellerName: '{{ Auth::user()->name }}',
            vehicleBrand: currentVehicle.brand,
            vehicleModel: currentVehicle.model,
            vehicleYear: currentVehicle.year,
            vehiclePlate: currentVehicle.numberPlate || 'N/A',
            hasAirConditioning: saleData.air_conditioning,
            hasMetallicPaint: saleData.metallic_paint,
            hasPartPayment: saleData.has_part_payment,
            partPaymentData: saleData.part_payment_data,
            partPaymentValue: saleData.part_payment_value,
            basePrice: saleData.base_price,
            optionsPrice: saleData.options_price,
            finalPrice: saleData.final_price,
            upfrontPayment: saleData.upfront_payment,
            remainingBalance: saleData.remaining_balance
        };

        // Save to recent sales
        const saleForList = {
            vehicleBrand: currentVehicle.brand,
            vehicleModel: currentVehicle.model,
            clientName: clientName,
            finalPrice: finalPrice,
            date: new Date().toISOString()
        };
        recentSales.unshift(saleForList);
        if (recentSales.length > 10) {
            recentSales.pop();
        }
        localStorage.setItem('recentSales', JSON.stringify(recentSales));
        updateRecentSalesList();

        // Show receipt modal
        showReceipt(receiptData);

        // Close the sales modal
        closeModal();
    })
    .catch(error => {
        console.error('Error in sale processing:', error);
        alert('Error al procesar la venta: ' + error.message);
    });
}


// Show the receipt modal
function showReceipt(saleData) {
    // Fill receipt with sale data
    document.getElementById('receipt-date').textContent = new Date(saleData.date).toLocaleDateString('es-ES');

    document.getElementById('receipt-client-name').textContent = saleData.clientName;
    document.getElementById('receipt-client-email').textContent = saleData.clientEmail || 'N/A';
    document.getElementById('receipt-client-phone').textContent = saleData.clientPhone || 'N/A';

    document.getElementById('receipt-seller-name').textContent = saleData.sellerName;
    const commission = saleData.finalPrice * 0.03;
    document.getElementById('receipt-commission').textContent = commission.toFixed(2);

    document.getElementById('receipt-vehicle-brand').textContent = saleData.vehicleBrand;
    document.getElementById('receipt-vehicle-model').textContent = saleData.vehicleModel;
    document.getElementById('receipt-vehicle-year').textContent = saleData.vehicleYear;
    document.getElementById('receipt-vehicle-plate').textContent = saleData.vehiclePlate;

    // Part payment section
    const partPaymentSection = document.getElementById('receipt-part-payment-section');
    const partPaymentAmountRow = document.getElementById('receipt-part-payment-amount-row');

    if (saleData.hasPartPayment) {
        partPaymentSection.classList.remove('hidden');
        partPaymentAmountRow.classList.remove('hidden');

        document.getElementById('receipt-part-payment-brand').textContent = saleData.partPaymentData.brand;
        document.getElementById('receipt-part-payment-model').textContent = saleData.partPaymentData.model;
        document.getElementById('receipt-part-payment-year').textContent = saleData.partPaymentData.year;
        document.getElementById('receipt-part-payment-plate').textContent = saleData.partPaymentData.plate;
        document.getElementById('receipt-part-payment-value').textContent = saleData.partPaymentData.value.toFixed(2);
        document.getElementById('receipt-part-payment-amount').textContent = saleData.partPaymentValue.toFixed(2);
    } else {
        partPaymentSection.classList.add('hidden');
        partPaymentAmountRow.classList.add('hidden');
    }

    // Price summary
    document.getElementById('receipt-base-price').textContent = saleData.basePrice.toFixed(2);

    // Options
    let optionsHtml = '';
    if (saleData.hasAirConditioning) {
        optionsHtml += `
            <div class="grid grid-cols-2 gap-1">
                <p>Aire acondicionado:</p>
                <p class="text-right">+$${AIR_CONDITIONING_PRICE.toFixed(2)}</p>
            </div>
        `;
    }

    if (saleData.hasMetallicPaint) {
        optionsHtml += `
            <div class="grid grid-cols-2 gap-1">
                <p>Pintura metálica:</p>
                <p class="text-right">+$${METALLIC_PAINT_PRICE.toFixed(2)}</p>
            </div>
        `;
    }

    document.getElementById('receipt-options-section').innerHTML = optionsHtml;

    // Final summary
    document.getElementById('receipt-final-price').textContent = saleData.finalPrice.toFixed(2);
    document.getElementById('receipt-upfront-payment').textContent = saleData.upfrontPayment.toFixed(2);
    document.getElementById('receipt-remaining-balance').textContent = saleData.remainingBalance.toFixed(2);

    // Show the receipt modal
    document.getElementById('receiptModal').classList.remove('hidden');
}

// Close the receipt modal
function closeReceiptModal() {
    document.getElementById('receiptModal').classList.add('hidden');
}

// Print the receipt
function printReceipt() {
    const receiptContent = document.getElementById('receipt-content');
    const windowContent = `
        <!DOCTYPE html>
        <html>
        <head>
            <title>Comprobante de Venta</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    margin: 0;
                    padding: 20px;
                }
                .receipt {
                    max-width: 800px;
                    margin: 0 auto;
                    padding: 20px;
                    border: 1px solid #ccc;
                }
                .text-center {
                    text-align: center;
                }
                .grid-2 {
                    display: grid;
                    grid-template-columns: 1fr 1fr;
                    gap: 10px;
                }
                .grid-col-2 {
                    grid-column: span 2;
                }
                .font-bold {
                    font-weight: bold;
                }
                .text-right {
                    text-align: right;
                }
                .border-top {
                    border-top: 1px solid #ccc;
                    padding-top: 10px;
                    margin-top: 10px;
                }
                .mt-4 {
                    margin-top: 16px;
                }
                .mb-4 {
                    margin-bottom: 16px;
                }
                .text-sm {
                    font-size: 0.875rem;
                }
                .text-gray {
                    color: #666;
                }
            </style>
        </head>
        <body>
            <div class="receipt">
                ${receiptContent.innerHTML}
            </div>
        </body>
        </html>
    `;

    const printWindow = window.open('', '_blank', 'height=600,width=800');
    printWindow.document.write(windowContent);
    printWindow.document.close();
    printWindow.focus();

    setTimeout(() => {
        printWindow.print();
        printWindow.close();
    }, 500);
}
        </script>
    </body>
</html>
