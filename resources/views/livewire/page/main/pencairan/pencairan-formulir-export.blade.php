<style>
  body {
    font-family: Arial, sans-serif;
    font-size: 12px;
  }
  .box {
    border: 2px solid #000;
    padding: 15px;
  }
  .title {
    background-color: #8fd4f5;
    font-weight: bold;
    padding: 8px;
    border: 1px solid #000;
    text-align: center;
    position: relative;
  }
  /* .title img {
    position: absolute;
    left: 10px;
    top: 50%;
    transform: translateY(-50%);
    height: 30px;
  } */
  .section-title {
    background-color: #d1e6f9;
    font-weight: bold;
    padding: 4px 8px;
    margin-top: 10px;
  }
  table.info-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
  }
  table.info-table td {
    padding: 4px 8px;
    vertical-align: top;
    line-height: 1.5;
  }
  .label-col {
    width: 30%;
    white-space: nowrap;
  }

  table.title-table {
    width: 100%;
    border-collapse: collapse;
  }
  table.title-table td {
    vertical-align: top;
    line-height: 1.5;
  }
  .underline {
    border-bottom: 1px dotted #000;
    display: inline-block;
    width: 100%;
  }
  .signature-section {
    margin-top: 40px;
  }
  .signature-line {
    border-top: 1px solid #000;
    margin-top: 5px;
    width: 150px;
    text-align: center;
    font-size: 11px;
    margin-left: auto;
    margin-right: auto;
  }
</style>

<div class="box">
    <div class="title">
        <table class="title-table">
            <tr>
                <td class="label-col"><img src="{{ public_path('assets/media/logo/logokkba-only.svg') }}" width="20"></td>
                <td>TABUNGAN SUKARELA ANGGOTA</td>
        </table>
    </div>

  <table class="info-table">
    <tr><td class="label-col">NAMA</td><td>: {{$nama_anggota ? $nama_anggota : '.............................................................................................'}}</td></tr>
    <tr><td class="label-col">NO. ANGGOTA</td><td>: {{$nomor_anggota ? $nomor_anggota : '.............................................................................................'}}</td></tr>
    <tr><td class="label-col">NO. INDUK PEGAWAI</td><td>: {{$nik ? $nik : '.............................................................................................'}}</td></tr>
    <tr><td class="label-col">UNIT KERJA</td><td>: .............................................................................................</td></tr>
    <tr><td class="label-col">NO. HANDPHONE</td><td>: {{$mobile ? $mobile : '.............................................................................................'}}</td></tr>
  </table>

  <div class="section-title">PENYERTAAN TABUNGAN</div>
  <table class="info-table">
    <tr><td class="label-col">JUMLAH</td><td>: .............................................................................................</td></tr>
    <tr><td class="label-col">TERBILANG</td><td>: .............................................................................................</td></tr>
    <tr><td class="label-col">TANGGAL PENYERTAAN</td><td>: .............................................................................................</td></tr>
  </table>

  <div class="section-title">PERUBAHAN PENYERTAAN TABUNGAN</div>
  <table class="info-table">
    <tr><td class="label-col">SEBELUM</td><td>: .............................................................................................</td></tr>
    <tr><td class="label-col">BERUBAH MENJADI</td><td>: .............................................................................................</td></tr>
    <tr><td class="label-col">TERHITUNG MULAI</td><td>: .............................................................................................</td></tr>
  </table>

  <div class="section-title">PENGAMBILAN TABUNGAN</div>
  <table class="info-table">
    <tr><td class="label-col">SALDO SAAT INI</td><td>: {{ $saldo_saat_ini ? 'Rp ' . number_format($saldo_saat_ini) : '.............................................................................................' }}</td></tr>
    <tr><td class="label-col">JUMLAH PENGAMBILAN</td><td>: <span style="color: blue; font-weight: bold;">{{ $jumlah_pengambilan ? 'Rp ' . number_format($jumlah_pengambilan) : '.............................................................................................' }}</span></td></tr>
    <tr><td class="label-col">TERBILANG</td><td>: <i>{{ $terbilang ? $terbilang . ' Rupiah' : '.............................................................................................' }}</i></td></tr>
    <tr><td class="label-col">NO. REKENING</td><td>: <u>{{ $no_rek }} Bank {{ $nama_bank }}</u></td></tr>
  </table>

  <p style="font-size: 11px; text-align: justify; margin-top: 20px;">
    Dengan ini menyatakan semua informasi yang diberikan adalah benar, informasi ini diberikan untuk tujuan Penyertaan/Perubahan/Pengambilan Tabungan Sukarela. Formulir ini adalah milik KKBA. KKBA berhak menyetujui atau menolak permohonan ini dengan ketentuan yang berlaku.
  </p>

  <div class="signature-section">
    <table style="width: 100%; margin-top: 30px; text-align: center;">
      <tr>
        <td style="width: 33%;">Menyetujui,</td>
        <td style="width: 33%;">Diperiksa oleh,</td>
        <td style="width: 33%;">
          Jakarta, {{ $tgl_pengajuan ? $tgl_pengajuan : '' }}<br>
          Pembuat Pernyataan
        </td>
      </tr>
      <tr>
        <td style="padding-top: 100px;">
          <div class="signature-line">Manager</div>
        </td>
        <td style="padding-top: 100px;">
          <div class="signature-line">Bag. Simpin</div>
        </td>
        <td style="padding-top: 85px;">
          <div style="font-weight: bold;">{{ $nama_anggota ? $nama_anggota : '' }}</div>
          <div class="signature-line"></div>
          <div style="font-size: 11px;">Anggota</div>
        </td>
      </tr>
    </table>
  </div>
</div>
