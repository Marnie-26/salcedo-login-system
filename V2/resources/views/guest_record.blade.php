<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Guest Login System</title>
        <link rel="stylesheet" href="{{ asset('css/guest_login_style.css') }}">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    </head>
    <body>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark border-bottom border-body">
            <div class="container-fluid">
                <!-- Current Admin Logged In -->
                <span class="navbar-text">
                    @if(auth()->check())
                        Admin: {{ auth()->user()->first_name }} {{ auth()->user()->last_name }}
                    @endif
                </span>
                <!-- Logout button -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-outline-light">Logout</button>
                </form>
            </div>
        </nav>
        <div class="container mt-5">
            <div class="row">
                <div class="col">
                    <h2>Visitor's Record Book</h2>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col">
                    <a href="{{ route('guest.login') }}" class="btn btn-dark">Back</a>
                    <button type="submit" class="btn btn-dark">Export as Excel</button>
                </div>
            </div>

            @if (session('success'))
                <div class="alert alert-success" role="alert" style="margin-top: 15px">
                    {{ session('success') }}
                </div>
            @endif
            
            @if (session('error'))
                <div class="alert alert-danger" role="alert" style="margin-top: 15px">
                    {{ session('error') }}
                </div>
            @endif

            <div class="row mt-3">
                <div class="col">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-dark">
                                <tr>
                                    <th>First Name</th>
                                    <th>Middle Name</th>
                                    <th>Last Name</th>
                                    <th>Purpose</th>
                                    <th>Date</th>
                                    <th>Time In</th>
                                    <th>Time Out</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($guests as $guest)
                                <tr>
                                    <td>{{ $guest->first_name }}</td>
                                    <td>{{ $guest->middle_name }}</td>
                                    <td>{{ $guest->last_name }}</td>
                                    <td>{{ $guest->visit_purpose }}</td>
                                    <td>{{ $guest->visit_date }}</td>
                                    <td>{{ $guest->time_in }}</td>
                                    <td>
                                        @if ($guest->time_out)
                                            {{ $guest->time_out }}
                                        @else
                                            <form id="logTimeOutForm" action="{{ route('log.time.out') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="guest_id" value="{{ $guest->id }}">
                                                <input type="time" class="form-control time-out-input" name="time_out">
                                            </form>
                                        @endif
                                        <span class="time-out-cell" style="display: none;">{{ $guest->time_out }}</span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            @if (!$guest->time_out)
                                                <button type="button" class="btn btn-outline-success me-2 save-time-out-btn">Log Time Out</button></form>
                                            @endif
                                            <button type="button" class="btn btn-outline-primary me-2 edit-btn">Edit</button>
                                            <button type="button" class="btn btn-outline-danger me-2 delete-btn" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $guest->id }}">Delete</button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
  
                        <!-- Delete Modal -->
                        @foreach($guests as $guest)
                        <div class="modal fade" id="deleteModal{{ $guest->id }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="deleteModalLabel{{ $guest->id }}" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h1 class="modal-title fs-5" id="deleteModalLabel{{ $guest->id }}">Confirm Delete</h1>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        Are you sure you want to delete visitor's record?
                                    </div>
                                    <div class="modal-footer">
                                        <form action="{{ route('delete.guest.record', $guest->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">Delete Record</button>
                                        </form>
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <script>
            // Logging time out
            document.addEventListener('DOMContentLoaded', function() {
                const saveTimeOutButtons = document.querySelectorAll('.save-time-out-btn');
                saveTimeOutButtons.forEach(function(button) {
                    button.addEventListener('click', function() {
                        const row = button.closest('tr');
                        const guestId = row.querySelector('input[name="guest_id"]').value;
                        const timeOutInput = row.querySelector('.time-out-input');
                        const timeOutValue = timeOutInput.value;
                        
                        const formData = new FormData();
                        formData.append('guest_id', guestId);
                        formData.append('time_out', timeOutValue);

                        fetch('{{ route('log.time.out') }}', {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if(data.success) {
                                const successAlert = document.createElement('div');
                                successAlert.classList.add('alert', 'alert-success');
                                successAlert.setAttribute('role', 'alert');
                                successAlert.style.marginTop = '10px';
                                successAlert.textContent = data.message;

                                const table = document.querySelector('.table');
                                table.parentNode.insertBefore(successAlert, table);
                                
                                const timeOutCell = row.querySelector('.time-out-cell');
                                timeOutCell.textContent = timeOutValue;

                                timeOutInput.style.display = 'none';
                                timeOutCell.style.display = 'inline';
                                button.style.display = 'none'; 
                            } else {
                                const errorAlert = document.createElement('div');
                                errorAlert.classList.add('alert', 'alert-danger');
                                errorAlert.setAttribute('role', 'alert');
                                errorAlert.style.marginTop = '10px';
                                errorAlert.textContent = data.message;

                                const table = document.querySelector('.table');
                                table.parentNode.insertBefore(errorAlert, table);
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                        });
                    });
                });
            });

            // Show 'Log Time Out' button if time is selected'
            document.addEventListener('DOMContentLoaded', function() {
                const timeOutInputs = document.querySelectorAll('.time-out-input');
                timeOutInputs.forEach(function(input) {
                    input.addEventListener('change', function() {
                        const row = input.closest('tr');
                        const saveButton = row.querySelector('.save-time-out-btn');
                        saveButton.style.display = input.value ? 'block' : 'none';
                    });
        
                    if (!input.value) {
                        const row = input.closest('tr');
                        const saveButton = row.querySelector('.save-time-out-btn');
                        saveButton.style.display = 'none';
                    }
                });
            });
        </script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    </body>
</html>