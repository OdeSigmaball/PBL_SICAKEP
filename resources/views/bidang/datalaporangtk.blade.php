@extends('layouts.main')

@section('container')
<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Breadcrumbs -->
    <div aria-label="breadcrumb">
        <ol class="p-3 breadcrumb bg-light">
            <li class="breadcrumb-item">
                <a href="/" class="text-dark text-decoration-none">
                    <i class="fas fa-home text-dark"></i> Dashboard
                </a>
            </li>
            <li class="breadcrumb-item active text-dark" aria-current="page">
                <i class="fas fa-database"></i>
                <a href="{{ route('datalaporangtk.index') }}" class="text-dark text-decoration-none">Data Laporan</a>
            </li>
        </ol>
    </div>
    <!-- Breadcrumbs END -->

    <!-- Main Content -->
    <div class="mb-4 shadow card">
        <div class="py-3 card-header d-flex justify-content-between align-items-center">
            <div>
                <h4 class="m-0 font-weight-bold text-primary">Data Laporan Kegiatan</h4>
                <h4 class="font-weight-normal text-dark">Bidang GTK</h4>
            </div>
            <button class="btn btn-primary" data-toggle="modal" data-target="#tambahDataModal">
                <i class="fas fa-plus"></i> Tambah Data
            </button>
        </div>
        <div class="card-body">
            <div class="list-group">
                @foreach($kegiatans as $kegiatan)
                <!-- Item -->
                <div class="mb-3 list-group-item d-flex justify-content-between align-items-center border-left-primary">
                    <div>
                        <h5>{{ $kegiatan->nama_kegiatan }}</h5>
                        <p class="mb-0">Lokasi: <span class="text-muted">{{ $kegiatan->lokasi_kegiatan }}</span></p>
                        <p class="mb-0">Tanggal: <span class="text-muted">{{ $kegiatan->tanggal_kegiatan }}</span></p>
                    </div>
                    <div>
                        <button class="btn btn-primary" data-toggle="modal" data-target="#uploadModal-{{ $kegiatan->id_kegiatan }}">
                            <i class="fas fa-upload"></i>
                        </button>
                        <form action="{{ route('datalaporangtk.destroy', $kegiatan->id_kegiatan) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger"><i class="fas fa-trash"></i></button>
                        </form>
                    </div>
                </div>

                <!-- Modal Upload -->
                <div class="modal fade" id="uploadModal-{{ $kegiatan->id_kegiatan }}" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="uploadModalLabel">Upload File</h5>
                                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form action="{{ route('datalaporangtk.upload', $kegiatan->id_kegiatan) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="uploadFile" class="form-label">Pilih File</label>
                                        <input type="file" class="form-control" id="uploadFile" name="uploadFile" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="deskripsiFile" class="form-label">Deskripsi</label>
                                        <textarea class="form-control" id="deskripsiFile" name="deskripsiFile" rows="3" placeholder="Masukkan deskripsi file"></textarea>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-primary">Upload</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    <!-- Main Content END -->

    <!-- Modal Tambah Data -->
    <div class="modal fade" id="tambahDataModal" tabindex="-1" aria-labelledby="tambahDataLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tambahDataLabel">Form Input Kegiatan</h5>
                    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('datalaporangtk.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="namaKegiatan" class="form-label">Nama Kegiatan</label>
                            <input type="text" class="form-control" id="namaKegiatan" name="nama_kegiatan" placeholder="Masukkan nama kegiatan" required>
                        </div>
                        <div class="mb-3">
                            <label for="tanggalKegiatan" class="form-label">Tanggal Kegiatan</label>
                            <input type="date" class="form-control" id="tanggalKegiatan" name="tanggal_kegiatan" required>
                        </div>
                        <div class="mb-3">
                            <label for="lokasiKegiatan" class="form-label">Lokasi Kegiatan</label>
                            <input type="text" class="form-control" id="lokasiKegiatan" name="lokasi_kegiatan" placeholder="Masukkan lokasi kegiatan" required>
                        </div>
                        <div class="mb-3">
                            <input type="hidden" class="form-control" id="bidang" name="bidang" value="GTK" placeholder="Masukkan bidang kegiatan" required>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
<!-- /.container-fluid -->

</div>
<!-- End of Main Content -->
@endsection
