@php
  $keperluanList = [
    1 => 'Pendidikan',
    2 => 'Renovasi',
    3 => 'Pembangunan',
    4 => 'Kesehatan',
    5 => 'Investasi',
    6 => 'Kendaraan',
    7 => 'Pernikahan/Khitanan',
    8 => 'Modal Usaha',
    9 => 'Lainnya',
  ];

  $selectedKeperluan = collect($p_pinjaman_keperluan_ids ?? [])->map(fn($v) => (int) $v)->toArray();
@endphp
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <style>
    body {
      font-family: Arial, sans-serif;
      font-size: 12px;
    }
    .form-section {
      border: 1px solid #ccc;
      margin-bottom: 15px;
    }
    .section-title {
      background-color: #1E90FF;
      color: white;
      padding: 5px 10px;
      font-weight: bold;
    }
    table {
      width: 100%;
      border-collapse: collapse;
    }
    td {
      padding: 4px;
      vertical-align: top;
    }
    .input-line {
      border-bottom: 1px solid #000;
      display: inline-block;
      width: 100%;
      height: 14px;
    }
    .checkbox-group {
      display: flex;
      flex-direction: column;
      gap: 10px;
    }
    .checkbox-inline-group {
        flex-direction: row !important;
        flex-wrap: wrap;
        gap: 10px;
    }
    .checkbox {
        border: 1px solid #000;
        width: 12px;
        height: 12px;
        display: inline-block;
        font-size: 15px;
        font-weight: bold;
        text-align: center;
        line-height: 12px;
        vertical-align: middle;
        padding-left: 1px;
    }
    .checkbox.checked::before {
        content: "X";
    }
    .check-label {
      margin-right: 15px;
      display: flex;
      align-items: center;
    }
    .footer {
      margin-top: 30px;
    }
    .signature-section {
      /* margin-top: 50px; */
      display: flex;
      justify-content: space-between;
      page-break-inside: avoid;
    }
    .signature-box {
      text-align: center;
      width: 200px;
    }
  </style>
</head>
<body>

{{-- <div style="text-align: center; font-weight: bold; margin-bottom: 20px;">
  FORMULIR APLIKASI PINJAMAN / KREDIT ANGGOTA<br>
  KOPERASI KARYAWAN BRANTAS ABIPRAYA (KKBA)
</div> --}}
<table style="width: 100%; margin-bottom: 10px;">
  <tr>
    <td style="width: 80px; vertical-align: top;">
      <img src="{{ public_path('assets/media/logo/logokkba-only.svg') }}" width="30">
    </td>
    <td style="text-align: center; font-weight: bold; font-size: 14px;">
      FORMULIR APLIKASI PINJAMAN / KREDIT ANGGOTA<br>
      KOPERASI KARYAWAN BRANTAS ABIPRAYA (KKBA)
    </td>
  </tr>
</table>

<hr style="border: 1px solid black; margin: 5px 0;">

<div style="background-color: #e5e5e5; padding: 4px 8px; font-size: 11px;">
  Mohon diisi dengan huruf cetak dan beri tanda <span style="font-weight:bold">X</span> pada kotak pilihan
</div>
<!-- DATA ANGGOTA / PEMOHON -->
<div class="form-section">
  <div class="section-title">DATA ANGGOTA / PEMOHON</div>
  <table>
    <tr>
      <td>Nama Lengkap (sesuai KTP)</td>
      <td colspan="3"><div class="input-line">{{ $master_anggota['nama'] ?? '' }}</div></td>
    </tr>
    <tr>
      <td>Nomor Anggota</td>
      <td><div class="input-line">{{ $master_anggota['nomor_anggota'] ?? '' }}</div></td>
      <td>No. Induk Pegawai</td>
      <td><div class="input-line">{{ $master_anggota['nik'] ?? '' }}</div></td>
    </tr>
    <tr>
      <td>Unit Kerja / Bisnis</td>
      <td><div class="input-line">{{ $master_anggota['unit']['unit_name'] ?? '' }}</div></td>
      <td>Lama Bekerja</td>
      <td><div class="input-line">{{ $master_anggota['lama_bekerja'] ?? '' }}</div></td>
    </tr>
    <tr>
      <td>Alamat (sesuai KTP)</td>
      <td colspan="3"><div class="input-line">{{ $master_anggota['alamat'] ?? '' }}</div></td>
    </tr>
    <tr>
      <td>Desa / Kelurahan</td>
      <td><div class="input-line">{{ $master_anggota['desa'] ?? '' }}</div></td>
      <td>RT / RW</td>
      <td>
        <div style="display: flex; gap: 5px;">
          <div class="input-line" style="width: 40px;">{{ $master_anggota['rt'] ?? '' }}</div> /
          <div class="input-line" style="width: 40px;">{{ $master_anggota['rw'] ?? '' }}</div>
        </div>
      </td>
    </tr>
    <tr>
      <td>Kecamatan</td>
      <td><div class="input-line">{{ $master_anggota['kecamatan'] ?? '' }}</div></td>
      <td>Kota</td>
      <td><div class="input-line">{{ $master_anggota['kota'] ?? '' }}</div></td>
    </tr>
    <tr>
      <td>No. Telepon</td>
      <td><div class="input-line">{{ $master_anggota['mobile'] ?? '' }}</div></td>
      <td>Kode Pos</td>
      <td><div class="input-line">{{ $master_anggota['kode_pos'] ?? '' }}</div></td>
    </tr>
    <tr>
      <td>No. HP</td>
      <td><div class="input-line">{{ $master_anggota['mobile'] ?? '' }}</div></td>
      <td>Email</td>
      <td><div class="input-line">{{ $master_anggota['email'] ?? '' }}</div></td>
    </tr>
    <tr>
      <td>No. KTP</td>
      <td colspan="3"><div class="input-line">{{ $master_anggota['ktp'] ?? '' }}</div></td>
    </tr>
    <tr>
      <td>Tempat Lahir</td>
      <td><div class="input-line">{{ $master_anggota['tempat_lahir'] ?? '' }}</div></td>
      <td>Tanggal Lahir</td>
      <td><div class="input-line">{{ $master_anggota['tgl_lahir'] ?? '' }}</div></td>
    </tr>
    <tr>
      <td>Jenis Kelamin</td>
      <td colspan="3" class="checkbox-group">
        <div class="check-label">
          <div class="checkbox">
            @if(($master_anggota['p_jenis_kelamin_id'] ?? '') === 1) X @endif
          </div> Laki-laki
        </div>
        <div class="check-label">
          <div class="checkbox">
            @if(($master_anggota['p_jenis_kelamin_id'] ?? '') === 2) X @endif
          </div> Perempuan
        </div>
      </td>
    </tr>
    <tr>
      <td>Status Pernikahan</td>
      <td class="checkbox-group">
        <div class="check-label">
          <div class="checkbox">
            @if(($master_anggota['status_pernikahan'] ?? '') === 'Lajang') X @endif
          </div> Lajang
        </div>
        <div class="check-label">
          <div class="checkbox">
            @if(($master_anggota['status_pernikahan'] ?? '') === 'Menikah') X @endif
          </div> Menikah
        </div>
      </td>
      <td>Jumlah Tanggungan</td>
      <td><div class="input-line">{{ $master_anggota['jumlah_tanggungan'] ?? '' }}</div></td>
    </tr>
    <tr>
        <td>Pendidikan Terakhir</td>
        <td>
            <div class="checkbox-group" style="flex-wrap: wrap;">
            @foreach (['SD','SMP','SMA','Akademi'] as $pendidikan)
                <div class="check-label">
                <div class="checkbox">
                    @if(($master_anggota['pendidikan_terakhir'] ?? '') === $pendidikan) X @endif
                </div> {{ $pendidikan }}
                </div>
            @endforeach
            </div>
        </td>
        <td>
            <div class="checkbox-group" style="flex-wrap: wrap;">
            @foreach (['S1','S2','S3'] as $pendidikan)
                <div class="check-label">
                <div class="checkbox">
                    @if(($master_anggota['pendidikan_terakhir'] ?? '') === $pendidikan) X @endif
                </div> {{ $pendidikan }}
                </div>
            @endforeach
            </div>
        </td>
        <td></td>
    </tr>
    <tr>
      <td>Agama</td>
      <td>
        <div class="checkbox-group">
            @foreach (['Islam','Kristen'] as $agama)
            <div class="check-label">
                <div class="checkbox">
                @if(($master_anggota['agama'] ?? '') === $agama) X @endif
                </div> {{ $agama }}
            </div>
            @endforeach
        </div>
      </td>
      <td>
        <div class="checkbox-group">
        @foreach (['Katholik', 'Hindu'] as $agama)
          <div class="check-label">
            <div class="checkbox">
              @if(($master_anggota['agama'] ?? '') === $agama) X @endif
            </div> {{ $agama }}
          </div>
        @endforeach
        </div>
      </td>
      <td>
        <div class="checkbox-group">
        @foreach (['Budha','Kong Hu Chu'] as $agama)
          <div class="check-label">
            <div class="checkbox">
              @if(($master_anggota['agama'] ?? '') === $agama) X @endif
            </div> {{ $agama }}
          </div>
        @endforeach
        </div>
      </td>
    </tr>
    <tr>
        <td>Kendaraan yang dimiliki</td>
        <td class="checkbox-group">
            <div class="checkbox-group">
                @foreach (['Motor', 'Mobil'] as $kendaraan)
                <div class="check-label">
                    <div class="checkbox">
                    @if(($master_anggota['kendaraan'] ?? '') === $kendaraan) X @endif
                    </div> {{ $kendaraan }}
                </div>
                @endforeach
            </div>
        </td>
        <td>
            <div class="checkbox-group">
                @foreach (['Tidak Ada'] as $kendaraan)
                <div class="check-label">
                    <div class="checkbox">
                    @if(($master_anggota['kendaraan'] ?? '') === $kendaraan) X @endif
                    </div> {{ $kendaraan }}
                </div>
                @endforeach
            </div>
        </td>
        <td></td>
    </tr>
    <tr>
      <td>Status Rumah Tinggal</td>
      <td class="checkbox-group">
        @foreach (['Milik Sendiri', 'Milik Keluarga', 'Sewa/Kontrak'] as $rumah)
          <div class="check-label">
            <div class="checkbox">
              @if(($master_anggota['status_rumah'] ?? '') === $rumah) X @endif
            </div> {{ $rumah }}
          </div>
        @endforeach
      </td>
      <td>Lama Tinggal di Rumah tsb</td>
      <td colspan="3">
        <div style="display: inline-flex; gap: 10px;">
          <div class="input-line" style="width: 50px;">{{ $master_anggota['lama_tinggal_tahun'] ?? '' }}</div> Tahun
          <div class="input-line" style="width: 50px;">{{ $master_anggota['lama_tinggal_bulan'] ?? '' }}</div> Bulan
        </div>
      </td>
    </tr>
  </table>
</div>


<!-- DATA SUAMI / ISTRI -->
<div class="form-section">
  <div class="section-title">DATA SUAMI / ISTRI ANGGOTA</div>
  <table>
    <tr>
      <td>Nama Lengkap (sesuai KTP)</td>
      <td colspan="3"><div class="input-line" style="color: transparent">hahahahahahahaah</div></td>
    </tr>
    <tr>
      <td>Alamat (sesuai KTP)</td>
      <td colspan="3"><div class="input-line" style="color: transparent">hahahahahahahaah</div></td>
    </tr>
    <tr>
      <td>Desa/Kelurahan</td>
      <td><div class="input-line" style="color: transparent">hahahahahahahaah</div></td>
      <td>Kecamatan</td>
      <td><div class="input-line" style="color: transparent">hahahahahahahaah</div></td>
    </tr>
    <tr>
      <td>Kota</td>
      <td><div class="input-line" style="color: transparent">hahahahahahahaah</div></td>
      <td>No. Telepon</td>
      <td><div class="input-line" style="color: transparent">hahahahahahahaah</div></td>
    </tr>
    <tr>
      <td>No. HP</td>
      <td><div class="input-line" style="color: transparent">hahahahahahahaah</div></td>
      <td>Email</td>
      <td><div class="input-line" style="color: transparent">hahahahahahahaah</div></td>
    </tr>
    <tr>
      <td>No. KTP</td>
      <td><div class="input-line" style="color: transparent">hahahahahahahaah</div></td>
      <td>Tempat Lahir</td>
      <td><div class="input-line" style="color: transparent">hahahahahahahaah</div></td>
    </tr>
    <tr>
      <td>Tanggal Lahir</td>
      <td><div class="input-line" style="color: transparent">hahahahahahahaah</div></td>
      <td>Pendidikan Terakhir</td>
      <td><div class="input-line" style="color: transparent">hahahahahahahaah</div></td>
    </tr>
    <tr>
      <td>Pekerjaan</td>
      <td colspan="3"><div class="input-line" style="color: transparent">hahahahahahahaah</div></td>
    </tr>
  </table>
</div>
<!-- KONTAK DARURAT -->
<div class="form-section">
  <div class="section-title">DATA KELUARGA TIDAK SERUMAH / PIHAK LAIN YANG DAPAT DIHUBUNGI</div>
  <table>
    <tr>
      <td>Nama Lengkap (sesuai KTP)</td>
      <td colspan="3"><div class="input-line" style="color: transparent">hahahahahahahaah</div></td>
    </tr>
    <tr>
      <td>Hubungan</td>
      <td colspan="3"><div class="input-line" style="color: transparent">hahahahahahahaah</div></td>
    </tr>
    <tr>
      <td>Alamat Tempat Tinggal Terkini</td>
      <td colspan="3"><div class="input-line" style="color: transparent">hahahahahahahaah</div></td>
    </tr>
    <tr>
      <td>Desa/Kelurahan</td>
      <td><div class="input-line" style="color: transparent">hahahahahahahaah</div></td>
      <td>Kecamatan</td>
      <td><div class="input-line" style="color: transparent">hahahahahahahaah</div></td>
    </tr>
    <tr>
      <td>Kota</td>
      <td><div class="input-line" style="color: transparent">hahahahahahahaah</div></td>
      <td>No. Telepon</td>
      <td><div class="input-line" style="color: transparent">hahahahahahahaah</div></td>
    </tr>
    <tr>
      <td>No. HP</td>
      <td colspan="3"><div class="input-line" style="color: transparent">hahahahahahahaah</div></td>
    </tr>
  </table>
</div>
<!-- JENIS PINJAMAN -->
<div class="form-section">
  <div class="section-title">JENIS PINJAMAN YANG DIINGINKAN</div>
  <table>
    <tr>
      <td>Jenis Pinjaman</td>
      <td>
        <div style="display: flex; gap: 10px;">
          <div class="check-label"><div class="checkbox">@if(($master_jenis_pinjaman['p_jenis_pinjaman_id'] ?? '') === 1) X @endif</div> Pinjaman Umum</div>
          <div class="check-label"><div class="checkbox">@if(($master_jenis_pinjaman['p_jenis_pinjaman_id'] ?? '') === 3) X @endif</div> Kredit Barang</div>
          <div class="check-label"><div class="checkbox">@if(($master_jenis_pinjaman['p_jenis_pinjaman_id'] ?? '') === 2) X @endif</div> Pinjaman Khusus</div>
        </div>
      </td>
      <td>Keperluan Pinjaman</td>
      <td>
        <div style="display: flex; gap: 10px;">
          @foreach($keperluanList as $id => $label)
            <div class="check-label">
              <div class="checkbox @if(in_array($id, $selectedKeperluan)) checked @endif"></div> {{ $label }}
            </div>
          @endforeach
        </div>
      </td>
    </tr>
    <tr>

    </tr>
    <tr>
      <td>Jumlah yang Diajukan</td>
      <td><div class="input-line">{{ number_format($ra_jumlah_pinjaman, 0, ',', '.') }}</div></td>
      <td>Jangka Waktu (bulan)</td>
      <td><div class="input-line">{{ $tenor }}</div></td>
    </tr>
    <tr>
      <td colspan="4"><strong>Jika Kredit Barang:</strong></td>
    </tr>
    <tr>
      <td>Jenis Barang</td>
      <td><div class="input-line">{{ $jenis_barang }}</div></td>
      <td>Merk/Type</td>
      <td><div class="input-line">{{ $merk_type }}</div></td>
    </tr>
    <tr>
      <td>Harga Barang</td>
      <td colspan="3"><div class="input-line" style="color: transparent">hahahahahahahaah</div></td>
    </tr>
    <tr>
      <td colspan="4"><strong>Jika Pinjaman Khusus:</strong></td>
    </tr>
    <tr>
      <td>Jenis Jaminan</td>
      <td><div class="input-line">{{ $jaminan }}</div></td>
      <td>Nilai Jaminan</td>
      <td><div class="input-line">{{ number_format($jaminan_perkiraan_nilai, 0, ',', '.') }}</div></td>
    </tr>
  </table>
</div>
<!-- DATA JAMINAN -->
<div class="form-section">
  <div class="section-title">DATA JAMINAN YANG DISERAHKAN</div>
  <table>
    <tr>
      <td>Jenis Jaminan</td>
      <td><div class="input-line">{{ $jaminan }}</div></td>
      <td>Perkiraan Nilai Pasar</td>
      <td><div class="input-line">{{ number_format($jaminan_perkiraan_nilai, 0, ',', '.') }}</div></td>
    </tr>
    <tr>
      <td>Alamat Jaminan</td>
      <td colspan="3"><div class="input-line">{{ $jaminan_keterangan }}</div></td>
    </tr>
  </table>
</div>

<!-- INFORMASI REKENING -->
<div class="form-section">
  <div class="section-title">INFORMASI REKENING PEMOHON</div>
  <table>
    <tr>
      <td>Nomor Rekening</td>
      <td><div class="input-line">{{ $no_rekening }}</div></td>
      <td>Bank</td>
      <td><div class="input-line">{{ $bank }}</div></td>
    </tr>
  </table>
</div>

<!-- DOKUMEN PENDUKUNG -->
<div class="form-section">
  <div class="section-title">DOKUMEN KELENGKAPAN YANG DILAMPIRKAN</div>
  <table>
    <tr><td colspan="4">Checklist dokumen yang dilampirkan</td></tr>
    <tr>
      <td colspan="2">
        <div class="checkbox-group">
          <div class="check-label"><div class="checkbox"></div> FC KTP</div>
          <div class="check-label"><div class="checkbox"></div> FC Kartu Keluarga</div>
          <div class="check-label"><div class="checkbox"></div> FC Slip Gaji Terakhir</div>
        </div>
      </td>
      <td colspan="2">
        <div class="checkbox-group">
          <div class="check-label"><div class="checkbox"></div> FC Buku Tabungan</div>
          <div class="check-label"><div class="checkbox"></div> FC Surat Nikah (jika menikah)</div>
          <div class="check-label"><div class="checkbox"></div> Dokumen Jaminan</div>
        </div>
      </td>
    </tr>
    <tr>
        <td colspan="4">
            <div style="font-size:12px;"><i style="background-color:teal;color:white">*) ceklis kelengkapan dokumen oleh petugas.</i></div>
        </td>
    </tr>
  </table>
</div>
<!-- PERNYATAAN & TANDA TANGAN -->
<div class="form-section">
  <div class="section-title">PERNYATAAN PEMOHON</div>
  <p>
    Saya menyatakan bahwa data yang saya berikan dalam formulir ini adalah benar dan lengkap.
    Saya bersedia menerima sanksi apabila informasi ini tidak sesuai dengan keadaan sebenarnya.
    Saya juga menyatakan bahwa saya telah membaca dan menyetujui syarat dan ketentuan yang berlaku di Koperasi Karyawan Brantas Abipraya (KKBA).
  </p>
</div>

<table style="width: 100%; margin-top: 10px; text-align: center;">
    <tr>
        <td colspan="3">
        ........,.......................
        </td>
    </tr>
    <tr>
        <td colspan="3" style="padding-bottom: 60px;">
        Pemohon
        <div style="margin-top: 60px;">( {{ $master_anggota['nama'] ?? '.......................' }} )</div>
        </td>
    </tr>
    <tr>
        <td style="width: 33%;">
        Menyetujui
        <div style="margin-top: 60px;">( ......................................... )</div>
        </td>
        <td style="width: 34%;">&nbsp;</td> <!-- Spacer -->
        <td style="width: 33%;">
        Diperiksa oleh
        <div style="margin-top: 60px;">( ......................................... )</div>
        </td>
    </tr>
</table>

</body>
</html>
