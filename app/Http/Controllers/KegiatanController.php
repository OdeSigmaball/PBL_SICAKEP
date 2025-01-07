<?php

namespace App\Http\Controllers;

use App\Models\Kegiatan;
use App\Services\GoogleDriveService;
use Illuminate\Http\Request;

class KegiatanController extends Controller
{
    private $googleDriveService;

    public function __construct(GoogleDriveService $googleDriveService)
    {
        $this->googleDriveService = $googleDriveService;
    }

    public function index()
    {
        // Ambil semua data kegiatan
        $kegiatans = Kegiatan::all();

        // Kirim data ke view
        return view('bidang.datalaporangtk', compact('kegiatans'), ['judul' => "data laporan gtk"]);
    }


    public function store(Request $request)
{
    $request->validate([
        'nama_kegiatan' => 'required|string|max:255',
        'tanggal_kegiatan' => 'required|date',
        'lokasi_kegiatan' => 'required|string|max:255',
        'bidang' => 'required|string|max:255',
    ]);

    // Tentukan ID folder induk berdasarkan bidang
    $folderBidangMapping = [
        'GTK' => env('GOOGLE_DRIVE_FOLDER_GTK'),
        'PAUD' => env('GOOGLE_DRIVE_FOLDER_PAUD'),
        'PUBLIKASI' => env('GOOGLE_DRIVE_FOLDER_PUBLIKASI'),
        'SD_SMK' => env('GOOGLE_DRIVE_FOLDER_SD_SMK'),
    ];

    $parentFolderId = $folderBidangMapping[$request->bidang] ?? null;

    if (!$parentFolderId) {
        return redirect()->back()->withErrors(['error' => 'Bidang tidak valid atau folder induk tidak ditemukan.']);
    }

    // Buat folder kegiatan di Google Drive
    try {
        $folder = $this->googleDriveService->createFolder($request->nama_kegiatan, $parentFolderId);
    } catch (\Exception $e) {
        return redirect()->back()->withErrors(['error' => 'Gagal membuat folder di Google Drive: ' . $e->getMessage()]);
    }

    // Simpan ke database
    Kegiatan::create([
        'nama_kegiatan' => $request->nama_kegiatan,
        'tanggal_kegiatan' => $request->tanggal_kegiatan,
        'lokasi_kegiatan' => $request->lokasi_kegiatan,
        'bidang' => $request->bidang,
        'linkdrive' => $folder->id,
    ]);

    return redirect()->route('datalaporangtk.index')->with('success', 'Kegiatan berhasil ditambahkan.');
}



    public function uploadFile(Request $request, Kegiatan $kegiatan)
    {
        $request->validate([
            'uploadFile' => 'required|file',
            'deskripsiFile' => 'nullable|string',
            'id_kategori' => 'required|exists:kategoris,id_kategori',
        ]);

        $file = $request->file('uploadFile');
        $filePath = $file->getPathname();
        $fileName = $file->getClientOriginalName();

        // Upload file ke Google Drive
        $uploadedFile = $this->googleDriveService->uploadFile($filePath, $fileName, $kegiatan->linkdrive);

        // Simpan laporan ke database
        // $kegiatan->laporans()->create([
        //     'nama_laporan' => $fileName,
        //     'dokumen' => $uploadedFile->id,
        // ]);

        $kegiatan->laporans()->create([
            'nama_laporan' => $fileName,
            'dokumen' => $uploadedFile->id,
            'id_kegiatan' => $kegiatan->id_kegiatan,
            'id_user' => auth()->id(), // Pastikan user ID ditambahkan
            'id_kategori' => $request->id_kategori,
        ]);

        return redirect()->route('datalaporangtk.index')->with('success', 'File berhasil diupload.');
    }

    public function destroy(Kegiatan $kegiatan)
    {
        // Hapus folder Google Drive
        $this->googleDriveService->deleteFile($kegiatan->nama_kegiatan);

        // Hapus dari database
        $kegiatan->delete();

        return redirect()->route('datalaporangtk.index')->with('success', 'Kegiatan berhasil dihapus.');
    }

    public function deleteKegiatan(string $id){
        $kegiatan = Kegiatan::where('id_kegiatan',$id)->delete();

        if (!$kegiatan) {
            return redirect()->back();
        }
        return redirect('bidang/datalaporangtk')->with('success','data berhasil di hapus!');
    }
}
