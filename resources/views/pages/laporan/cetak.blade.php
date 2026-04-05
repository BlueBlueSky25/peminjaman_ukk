<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Peminjaman Alat - {{ date('d/m/Y', strtotime($tanggal)) }}</title>
    <style>
        @media print {
            @page {
                margin: 1.5cm;
            }
            body {
                margin: 0;
                padding: 0;
            }
            .no-print {
                display: none;
            }
        }
        
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            max-width: 210mm;
            margin: 0 auto;
            color: #000;
        }
        
        .header {
            text-align: center;
            border-bottom: 3px solid #000;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        
        .header h1 {
            margin: 0 0 5px 0;
            font-size: 22px;
        }
        
        .header h2 {
            margin: 0;
            font-size: 18px;
            font-weight: normal;
        }
        
        .header p {
            margin: 5px 0;
            color: #555;
            font-size: 14px;
        }
        
        .info-box {
            background: #f5f5f5;
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        
        .info-box table {
            width: 100%;
        }
        
        .info-box td {
            padding: 5px 0;
            font-size: 14px;
        }
        
        .summary {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 25px;
        }
        
        .summary-box {
            border: 2px solid #333;
            padding: 15px;
            text-align: center;
            border-radius: 5px;
            background: #fff;
        }
        
        .summary-box .label {
            font-size: 11px;
            color: #555;
            margin-bottom: 8px;
            text-transform: uppercase;
            font-weight: bold;
        }
        
        .summary-box .value {
            font-size: 28px;
            font-weight: bold;
            color: #000;
        }
        
        .section {
            margin-bottom: 30px;
            page-break-inside: avoid;
        }
        
        .section h3 {
            background: #333;
            color: white;
            padding: 10px 15px;
            margin-bottom: 15px;
            font-size: 16px;
            border-radius: 3px;
        }
        
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        
        table.data-table th,
        table.data-table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
            font-size: 11px;
        }
        
        table.data-table th {
            background-color: #e5e5e5;
            font-weight: bold;
        }
        
        table.data-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        .badge {
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
            border: 1px solid #000;
            background: #fff;
        }
        
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        
        .footer {
            margin-top: 50px;
            page-break-inside: avoid;
        }
        
        .footer-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 80px;
            margin-top: 30px;
        }
        
        .signature {
            text-align: center;
        }
        
        .signature .role {
            margin-bottom: 70px;
            font-weight: bold;
        }
        
        .signature .name {
            border-top: 1px solid #000;
            padding-top: 5px;
            font-weight: bold;
            display: inline-block;
            min-width: 200px;
        }
        
        .print-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 12px 24px;
            background: #333;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            z-index: 1000;
        }
        
        .print-btn:hover {
            background: #000;
        }
        
        .empty-state {
            text-align: center;
            padding: 40px;
            color: #999;
            font-style: italic;
        }
        
        .highlight {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <button onclick="window.print()" class="print-btn no-print">
        Cetak Laporan
    </button>

    <div class="header">
        <h1>SISTEM PEMINJAMAN ALAT</h1>
        <h2>LAPORAN PEMINJAMAN HARIAN</h2>
        <p>Tanggal: {{ \Carbon\Carbon::parse($tanggal)->locale('id')->isoFormat('D MMMM Y') }}</p>
    </div>

    <div class="info-box">
        <table>
            <tr>
                <td width="150"><strong>Tanggal Laporan</strong></td>
                <td>: {{ \Carbon\Carbon::parse($tanggal)->locale('id')->isoFormat('D MMMM Y') }}</td>
            </tr>
            <tr>
                <td><strong>Waktu Cetak</strong></td>
                <td>: {{ \Carbon\Carbon::now()->locale('id')->isoFormat('D MMMM Y, HH:mm') }} WIB</td>
            </tr>
            <tr>
                <td><strong>Dicetak oleh</strong></td>
                <td>: {{ auth()->user()->username }}</td>
            </tr>
        </table>
    </div>

    <!-- Summary KPI -->
    <div class="summary">
        <div class="summary-box">
            <div class="label">Total Peminjaman</div>
            <div class="value">{{ $totalPeminjamanHariIni }}</div>
        </div>
        <div class="summary-box">
            <div class="label">Total Pengembalian</div>
            <div class="value">{{ $totalPengembalianHariIni }}</div>
        </div>
        <div class="summary-box">
            <div class="label">Alat Dipinjam</div>
            <div class="value">{{ $peminjamanHariIni->where('status', 'disetujui')->count() }}</div>
        </div>
        <div class="summary-box">
            <div class="label">Total Denda</div>
            <div class="value" style="font-size: 18px;">Rp {{ number_format($totalDendaHariIni, 0, ',', '.') }}</div>
        </div>
    </div>

    <!-- Peminjaman Hari Ini -->
    <div class="section">
        <h3>Daftar Peminjaman ({{ \Carbon\Carbon::parse($tanggal)->locale('id')->isoFormat('D/M/Y') }})</h3>
        @if($peminjamanHariIni->count() > 0)
            <table class="data-table">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th width="15%">Kode</th>
                        <th width="15%">Peminjam</th>
                        <th width="20%">Alat</th>
                        <th width="8%">Jumlah</th>
                        <th width="12%">Tgl Pinjam</th>
                        <th width="12%">Jatuh Tempo</th>
                        <th width="13%">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($peminjamanHariIni as $index => $item)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td class="highlight">{{ $item->kode_peminjaman }}</td>
                            <td>{{ $item->user->username }}</td>
                            <td>{{ $item->alat->nama_alat }}</td>
                            <td class="text-center">{{ $item->jumlah }}</td>
                            <td>{{ $item->tanggal_peminjaman->locale('id')->isoFormat('D/M/Y') }}</td>
                            <td>{{ $item->tanggal_kembali_rencana->locale('id')->isoFormat('D/M/Y') }}</td>
                            <td class="text-center">
                                <span class="badge">{{ strtoupper($item->status) }}</span>
                            </td>
                        </tr>
                    @endforeach
                    <tr style="background: #e5e5e5; font-weight: bold;">
                        <td colspan="4" class="text-right">TOTAL:</td>
                        <td class="text-center">{{ $peminjamanHariIni->sum('jumlah') }}</td>
                        <td colspan="3"></td>
                    </tr>
                </tbody>
            </table>
        @else
            <div class="empty-state">
                <p>Tidak ada peminjaman pada tanggal ini</p>
            </div>
        @endif
    </div>

    <!-- Pengembalian Hari Ini -->
    <div class="section">
        <h3>Daftar Pengembalian ({{ \Carbon\Carbon::parse($tanggal)->locale('id')->isoFormat('D/M/Y') }})</h3>
        @if($pengembalianHariIni->count() > 0)
            <table class="data-table">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th width="15%">Kode</th>
                        <th width="15%">Peminjam</th>
                        <th width="18%">Alat</th>
                        <th width="12%">Tgl Kembali</th>
                        <th width="10%">Kondisi</th>
                        <th width="10%">Terlambat</th>
                        <th width="15%">Denda</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pengembalianHariIni as $index => $item)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td class="highlight">{{ $item->peminjaman->kode_peminjaman }}</td>
                            <td>{{ $item->peminjaman->user->username }}</td>
                            <td>{{ $item->peminjaman->alat->nama_alat }}</td>
                            <td>{{ $item->tanggal_kembali_aktual->locale('id')->isoFormat('D/M/Y') }}</td>
                            <td class="text-center">
                                <span class="badge">{{ strtoupper($item->kondisi_alat) }}</span>
                            </td>
                            <td class="text-center">
                                @if($item->keterlambatan_hari > 0)
                                    <strong>{{ $item->keterlambatan_hari }} hari</strong>
                                @else
                                    Tepat waktu
                                @endif
                            </td>
                            <td class="text-right">
                                @if($item->total_denda > 0)
                                    <strong>Rp {{ number_format($item->total_denda, 0, ',', '.') }}</strong>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    <tr style="background: #e5e5e5; font-weight: bold;">
                        <td colspan="7" class="text-right">TOTAL DENDA:</td>
                        <td class="text-right">Rp {{ number_format($pengembalianHariIni->sum('total_denda'), 0, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>
        @else
            <div class="empty-state">
                <p>Tidak ada pengembalian pada tanggal ini</p>
            </div>
        @endif
    </div>

    <!-- Alat yang Sedang Dipinjam (Belum Dikembalikan) -->
    <div class="section">
        <h3>Alat yang Sedang Dipinjam</h3>
        @php
            $alatDipinjam = \App\Models\Peminjaman::with(['user', 'alat'])
                ->where('status', 'disetujui')
                ->whereDoesntHave('pengembalian')
                ->get();
        @endphp
        
        @if($alatDipinjam->count() > 0)
            <table class="data-table">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th width="15%">Kode</th>
                        <th width="15%">Peminjam</th>
                        <th width="25%">Alat</th>
                        <th width="8%">Jumlah</th>
                        <th width="12%">Tgl Pinjam</th>
                        <th width="12%">Jatuh Tempo</th>
                        <th width="8%">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($alatDipinjam as $index => $item)
                        @php
                            $hariTersisa = \Carbon\Carbon::now()->diffInDays($item->tanggal_kembali_rencana, false);
                        @endphp
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td class="highlight">{{ $item->kode_peminjaman }}</td>
                            <td>{{ $item->user->username }}</td>
                            <td>{{ $item->alat->nama_alat }}</td>
                            <td class="text-center">{{ $item->jumlah }}</td>
                            <td>{{ $item->tanggal_peminjaman->locale('id')->isoFormat('D/M/Y') }}</td>
                            <td>{{ $item->tanggal_kembali_rencana->locale('id')->isoFormat('D/M/Y') }}</td>
                            <td class="text-center">
                                @if($hariTersisa < 0)
                                    <strong>LEWAT {{ abs($hariTersisa) }} hari</strong>
                                @elseif($hariTersisa == 0)
                                    <strong>HARI INI</strong>
                                @else
                                    {{ $hariTersisa }} hari lagi
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    <tr style="background: #e5e5e5; font-weight: bold;">
                        <td colspan="4" class="text-right">TOTAL ALAT DIPINJAM:</td>
                        <td class="text-center">{{ $alatDipinjam->sum('jumlah') }}</td>
                        <td colspan="3"></td>
                    </tr>
                </tbody>
            </table>
        @else
            <div class="empty-state">
                <p>Tidak ada alat yang sedang dipinjam</p>
            </div>
        @endif
    </div>

    <!-- Footer TTD -->
    <div class="footer">
        <div class="footer-grid">
            <div class="signature">
                <div class="role">Mengetahui,<br>Kepala Bagian</div>
                <div class="name">(...........................)</div>
            </div>
            <div class="signature">
                <div class="role">Petugas,</div>
                <div class="name">{{ auth()->user()->username }}</div>
            </div>
        </div>
    </div>
</body>
</html>