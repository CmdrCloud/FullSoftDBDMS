<!-- resources/views/commissions.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Gestión de Comisiones</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if(Auth::user()->isAdmin)
                    <h3>Comisiones de todos los usuarios</h3>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Email</th>
                                <th>Comisión</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="commission-table-body">
                            <!-- Will be populated via JavaScript -->
                        </tbody>
                    </table>
                    @else
                    <h3>Tu Comisión Actual</h3>
                    <div class="card">
                        <div class="card-body">
                            <h4 id="user-name">{{ Auth::user()->name }}</h4>
                            <h2 id="commission-amount">$<span>{{ number_format(Auth::user()->commission, 2) }}</span></h2>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@if(Auth::user()->isAdmin)
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Fetch all user commissions
    fetch('/user-commissions')
        .then(response => response.json())
        .then(data => {
            const tableBody = document.getElementById('commission-table-body');
            tableBody.innerHTML = '';

            data.forEach(user => {
                const row = document.createElement('tr');

                row.innerHTML = `
                    <td>${user.name}</td>
                    <td>${user.email}</td>
                    <td>$${user.commission.toFixed(2)}</td>
                    <td>
                        <button class="btn btn-sm btn-warning reset-commission" data-id="${user.id}">
                            Reiniciar Comisión
                        </button>
                    </td>
                `;

                tableBody.appendChild(row);
            });

            // Add event listeners to reset buttons
            document.querySelectorAll('.reset-commission').forEach(button => {
                button.addEventListener('click', function() {
                    const userId = this.dataset.id;
                    resetCommission(userId);
                });
            });
        });
});

function resetCommission(userId) {
    if (confirm('¿Está seguro de reiniciar esta comisión? Esta acción no se puede deshacer.')) {
        fetch(`/reset-commission/${userId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert(data.error);
            } else {
                alert(data.message);
                location.reload();
            }
        });
    }
}
</script>
@else
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Fetch current user's commission
    fetch('/my-commission')
        .then(response => response.json())
        .then(data => {
            document.getElementById('user-name').textContent = data.name;
            document.getElementById('commission-amount').querySelector('span').textContent =
                parseFloat(data.commission).toFixed(2);
        });
});
</script>
@endif
@endsection
