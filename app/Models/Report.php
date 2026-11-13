<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Report extends Model
{
    use HasFactory;

    public const STATUS_DIAJUKAN = 'diajukan';
    public const STATUS_DALAM_VERIFIKASI = 'dalam_verifikasi';
    public const STATUS_PERLU_INFORMASI = 'perlu_informasi';
    public const STATUS_DITOLAK = 'ditolak';
    public const STATUS_DALAM_PENANGANAN = 'dalam_penanganan';
    public const STATUS_SELESAI = 'selesai';
    public const STATUS_DIBATALKAN = 'dibatalkan';

    /** Label dan warna badge setiap status (Tabel 6). */
    public const STATUSES = [
        self::STATUS_DIAJUKAN => ['label' => 'Diajukan', 'color' => 'gray'],
        self::STATUS_DALAM_VERIFIKASI => ['label' => 'Dalam Verifikasi', 'color' => 'amber'],
        self::STATUS_PERLU_INFORMASI => ['label' => 'Perlu Informasi', 'color' => 'purple'],
        self::STATUS_DITOLAK => ['label' => 'Ditolak', 'color' => 'red'],
        self::STATUS_DALAM_PENANGANAN => ['label' => 'Dalam Penanganan', 'color' => 'blue'],
        self::STATUS_SELESAI => ['label' => 'Selesai', 'color' => 'green'],
        self::STATUS_DIBATALKAN => ['label' => 'Dibatalkan', 'color' => 'slate'],
    ];

    public const MODE_TERIDENTIFIKASI = 'teridentifikasi';
    public const MODE_ANONIM = 'anonim';

    public const MODES = [
        self::MODE_TERIDENTIFIKASI => 'Teridentifikasi',
        self::MODE_ANONIM => 'Anonim ke Petugas',
    ];

    public const TERLAPOR_PERAN = [
        'mahasiswa' => 'Mahasiswa',
        'dosen' => 'Dosen',
        'tenaga_kependidikan' => 'Tenaga Kependidikan',
        'petugas_satgas' => 'Anggota Satgas PPKPT',
        'lainnya' => 'Lainnya',
        'tidak_diketahui' => 'Tidak diketahui',
    ];

    protected $fillable = [
        'user_id',
        'petugas_id',
        'category_id',
        'unit_id',
        'kode_tiket',
        'kronologi',
        'mode_identitas',
        'terlapor_peran',
        'terlapor_keterangan',
        'status',
    ];

    /**
     * Kronologi dan keterangan terlapor disimpan terenkripsi di basis data (NFR-01).
     * Isi kolom di MySQL tidak dapat dibaca tanpa APP_KEY.
     */
    protected function casts(): array
    {
        return [
            'kronologi' => 'encrypted',
            'terlapor_keterangan' => 'encrypted',
        ];
    }

    public function pelapor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function petugas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function statuses(): HasMany
    {
        return $this->hasMany(ReportStatus::class)->orderBy('created_at')->orderBy('id');
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status]['label'] ?? $this->status;
    }

    public function statusColor(): string
    {
        return self::STATUSES[$this->status]['color'] ?? 'gray';
    }

    public function modeLabel(): string
    {
        return self::MODES[$this->mode_identitas] ?? $this->mode_identitas;
    }

    public function terlaporPeranLabel(): string
    {
        return self::TERLAPOR_PERAN[$this->terlapor_peran] ?? $this->terlapor_peran;
    }

    /**
     * Pelapor boleh mengubah atau membatalkan laporan selama belum diverifikasi petugas.
     */
    public function isEditableByReporter(): bool
    {
        return $this->status === self::STATUS_DIAJUKAN;
    }

    public function isAnonymous(): bool
    {
        return $this->mode_identitas === self::MODE_ANONIM;
    }

    /**
     * Identitas pelapor sesuai siapa yang melihat (penyamaran identitas).
     *
     * - Pelapor sendiri selalu melihat identitasnya.
     * - Petugas melihat identitas hanya jika mode Teridentifikasi.
     * - Admin dan pihak lain tidak pernah melihat identitas pelapor.
     *
     * @return array{anonim: bool, nama: string, nim: ?string, email: ?string, unit: ?string}
     */
    public function pelaporUntuk(?User $viewer): array
    {
        $isOwner = $viewer && $viewer->id === $this->user_id;
        $canSeeIdentity = $isOwner || ($viewer?->isPetugas() && ! $this->isAnonymous());

        if (! $canSeeIdentity) {
            return [
                'anonim' => true,
                'nama' => $this->isAnonymous() ? 'Pelapor Anonim' : 'Pelapor',
                'nim' => null,
                'email' => null,
                // Unit tetap ditampilkan karena tidak menunjuk ke individu tertentu.
                'unit' => $this->unit?->nama_unit,
            ];
        }

        $pelapor = $this->pelapor;

        return [
            'anonim' => false,
            'nama' => $pelapor->nama,
            'nim' => $pelapor->nim,
            'email' => $pelapor->email,
            'unit' => $this->unit?->nama_unit,
        ];
    }
}
