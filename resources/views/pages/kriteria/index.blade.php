@extends('layouts.app')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="py-3 mb-4"><span class="text-muted fw-light"></span> {{ $title }}</h4>

        <!-- Card Border Shadow -->
        <div class="row">
            <div class="col-sm-12 col-md-12 col-lg-12 mb-4">
                <div class="card card-border-shadow-primary">
                    <div class="card-header header-elements">
                        <h4 class="me-2">Data {{ $title }}</h4>

                        <div class="card-header-elements ms-auto">
                            <a href="javascript:void(0)" id="tambah_kriteria"
                                class="btn btn-primary waves-effect waves-light">
                                <span class="tf-icon mdi mdi-plus me-1"></span> Tambah Kriteria
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped" id="dataTables">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Kode</th>
                                    <th>Kriteria</th>
                                    <th>Bobot</th>
                                    <th>Tipe</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            {{-- Isi Tabel --}}
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Canvas --}}
    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvas-kriteria" aria-labelledby="offcanvasEndLabel">
        <div class="offcanvas-header">
            <h5 id="offcanvas-kriteria-heading" class="offcanvas-title"></h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <form action="" method="post" id="kriteriaForm" name="kriteriaForm" autocomplete="off">
                <input type="hidden" name="id" id="id" value="">
                <div class="col mb-4 mt-2">
                    <div class="form-floating form-floating-outline">
                        <input type="text" id="kode" name="kode" class="form-control" placeholder="Ketikkan kode"
                            value="" required />
                        <label for="kode">Kode</label>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="col-md-12 mb-4 mt-2">
                    <div class="form-floating form-floating-outline">
                        <input type="text" id="kriteria_name" name="kriteria_name" class="form-control"
                            placeholder="Ketikkan nama kriteria" value="" required />
                        <label for="kriteria_name">Nama Kriteria</label>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="col-md-12 mb-4 mt-2">
                    <div class="form-floating form-floating-outline">
                        <input type="number" id="bobot" name="bobot" class="form-control"
                            placeholder="Ketikkan bobot" value="" required />
                        <label for="bobot">Bobot</label>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="col-md-12 mb-4 mt-2">
                    <label class="d-block form-label">Tipe</label>
                    <div class="form-check mb-2">
                        <input type="radio" id="tipe" name="tipe" class="form-check-input" required=""
                            value="benefit">
                        <label class="form-check-label" for="tipe">Benefit</label>
                    </div>
                    <div class="form-check">
                        <input type="radio" id="tipe" name="tipe" class="form-check-input" required=""
                            value="cost">
                        <label class="form-check-label" for="tipe">Cost</label>
                    </div>
                </div>

                <button type="Submit" class="btn btn-primary mb-2 d-grid w-100" id="saveBtn" value="">Save
                    Changes</button>
                <button type="button" class="btn btn-outline-secondary d-grid w-100" data-bs-dismiss="offcanvas">
                    Cancel
                </button>
            </form>
        </div>
    </div>

    {{-- Modal --}}
@endsection

@push('custom-scripts')
    <script type="text/javascript">
        $(document).ready(function() {

            // Data tables
            var table = $('#dataTables').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('kriteria.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex'
                    },
                    {
                        data: 'kode',
                        name: 'kode'
                    },
                    {
                        data: 'kriteria_name',
                        name: 'kriteria_name'
                    },
                    {
                        data: 'bobot',
                        name: 'bobot'
                    },
                    {
                        data: 'tipe',
                        name: 'tipe',
                        render: function(data, type, row) {
                            var badgeClass = data === 'benefit' ? 'bg-label-success' :
                                'bg-label-danger';
                            return '<span class="badge ' + badgeClass + '">' + data + '</span>';
                        }
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ],
                responsive: true
            });

            // Ajax Setup Token
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Deklarasi OffCanvas
            var offcanvas = new bootstrap.Offcanvas(document.getElementById('offcanvas-kriteria'));

            // Tambah
            $('#tambah_kriteria').click(function() {
                $('#saveBtn').val("tambah-kriteria");
                $('#id').val('');
                $('#kriteriaForm').trigger("reset");
                $('#offcanvas-kriteria-heading').html("Tambah Kriteria");
                offcanvas.show(); // Show the offcanvas
            });

            // Edit
            $('body').on('click', '.edit_kriteria', function() {
                var url = $(this).data('url');
                $.get(url, function(data) {
                    $('#offcanvas-kriteria-heading').html("Edit Kriteria");
                    $('#saveBtn').val("edit-kriteria");
                    offcanvas.show();
                    $('#id').val(data.id);
                    $('#kode').val(data.kode);
                    $('#kriteria_name').val(data.kriteria_name);
                    $('#bobot').val(data.bobot);

                    // Set nilai radio button 'tipe' berdasarkan data yang diterima
                    if (data.tipe === 'benefit') {
                        $('input[name="tipe"][value="benefit"]').prop('checked', true);
                    } else if (data.tipe === 'cost') {
                        $('input[name="tipe"][value="cost"]').prop('checked', true);
                    }
                })
            });

            // Create and update proses
            $('#saveBtn').click(function(e) {
                e.preventDefault();

                $(this).html('Sending..');
                var formData = $('#kriteriaForm').serialize();

                $.ajax({
                    data: formData,
                    url: "{{ route('kriteria.store') }}",
                    type: "POST",
                    dataType: 'json',
                    success: function(response) {
                        $('#kriteriaForm').trigger("reset");
                        $('#saveBtn').html('Save Changes');
                        offcanvas.hide();

                        table.draw()
                        toastr.success(response.message, 'Success', {
                            progressBar: true,
                            positionClass: 'toast-top-center'
                        });

                    },
                    error: function(err) {
                        // Reset
                        $('#saveBtn').html('Save Changes');
                        toastr.error('Periksa inputan anda.', 'Error', {
                            progressBar: true,
                            positionClass: 'toast-top-center'
                        });

                        if (err.responseJSON && err.responseJSON.errors) {
                            displayValidationErrors(err.responseJSON.errors);
                        }
                    }
                });
            });

            // Deleted
            $('body').on('click', '.hapus_kriteria', function() {
                var id = $(this).data('id');

                Swal.fire({
                    title: 'Yakin ?',
                    text: "Anda akan menghapus data ini!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Hapus!',
                    customClass: {
                        confirmButton: 'btn btn-primary me-3 waves-effect waves-light',
                        cancelButton: 'btn btn-outline-secondary waves-effect'
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            type: "DELETE",
                            url: "{{ route('kriteria.store') }}" + '/' + id,
                            success: function(response) {
                                table.draw();
                                toastr.success(response.message, 'Success', {
                                    progressBar: true,
                                    positionClass: 'toast-top-center'
                                });
                            },
                            error: function(data) {
                                console.log('Error:', data);
                            }
                        });
                    }
                });
            });


            function displayValidationErrors(errors) {
                let errorMessage = 'Periksa inputan anda:<br>';
                $.each(errors, function(key, value) {
                    errorMessage += `<li>${value}</li>`;
                });
                toastr.error(errorMessage, 'Validasi Error', {
                    progressBar: true,
                    positionClass: 'toast-top-center'
                });
            }

        });
    </script>
@endpush
