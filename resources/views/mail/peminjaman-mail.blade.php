<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 560px;
            margin: 30px auto;
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        .header {
            background-color: #2563eb;
            color: white;
            padding: 28px 32px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 22px;
            letter-spacing: 0.5px;
        }
        .header p {
            margin: 6px 0 0;
            font-size: 13px;
            opacity: 0.85;
        }
        .kode-box {
            background: #eff6ff;
            border: 2px dashed #2563eb;
            border-radius: 8px;
            margin: 24px 32px 0;
            padding: 16px;
            text-align: center;
        }
        .kode-box p {
            margin: 0 0 4px;
            font-size: 12px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .kode-box span {
            font-size: 26px;
            font-weight: bold;
            color: #1d4ed8;
            letter-spacing: 3px;
        }
        .body {
            padding: 24px 32px 32px;
        }
        .body p.greeting {
            font-size: 15px;
            color: #374151;
            margin-top: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 16px;
            font-size: 14px;
        }
        table tr td {
            padding: 10px 12px;
            border-bottom: 1px solid #f0f0f0;
            color: #374151;
        }
        table tr td:first-child {
            color: #6b7280;
            width: 45%;
        }
        table tr td:last-child {
            font-weight: 600;
        }
        .status-badge {
            display: inline-block;
            background: #fef9c3;
            color: #92400e;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        .note {
            background: #fff7ed;
            border-left: 4px solid #f97316;
            border-radius: 4px;
            padding: 12px 16px;
            margin-top: 24px;
            font-size: 13px;
            color: #7c2d12;
            line-height: 1.6;
        }
        .footer {
            text-align: center;
            padding: 20px 32px;
            background: #f9fafb;
            font-size: 12px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔧 Struk Peminjaman Alat</h1>
            <p>Simpan email ini sebagai bukti pengajuan peminjaman kamu</p>
        </div>

        <div class="kode-box">
            <p>Kode Peminjaman</p>
            <span>{{ $peminjaman->kode_peminjaman }}</span>
        </div>

        <div class="body">
            <p class="greeting">Halo, <strong>{{ $peminjaman->nama_peminjam }}</strong>!</p>
            <p style="font-size:14px; color:#6b7280; margin-top:0;">
                Peminjaman alat kamu berhasil diajukan. Berikut detail peminjaman kamu:
            </p>

            <table>
                <tr>
                    <td>Nama Peminjam</td>
                    <td>{{ $peminjaman->nama_peminjam }}</td>
                </tr>
                <tr>
                    <td>Email</td>
                    <td>{{ $peminjaman->email }}</td>
                </tr>
                <tr>
                    <td>Nama Alat</td>
                    <td>{{ $peminjaman->alat->nama_alat }}</td>
                </tr>
                <tr>
                    <td>Kode Alat</td>
                    <td>{{ $peminjaman->alat->kode_alat }}</td>
                </tr>
                <tr>
                    <td>Jumlah</td>
                    <td>{{ $peminjaman->jumlah }} unit</td>
                </tr>
                <tr>
                    <td>Tanggal Pinjam</td>
                    <td>{{ $peminjaman->tanggal_peminjaman->format('d/m/Y') }}</td>
                </tr>
                <tr>
                    <td>Rencana Kembali</td>
                    <td>{{ $peminjaman->tanggal_kembali_rencana->format('d/m/Y') }}</td>
                </tr>
                @if($peminjaman->tujuan_peminjaman)
                <tr>
                    <td>Tujuan</td>
                    <td>{{ $peminjaman->tujuan_peminjaman }}</td>
                </tr>
                @endif
                <tr>
                    <td>Status</td>
                    <td><span class="status-badge">Menunggu Persetujuan</span></td>
                </tr>
                <tr>
                    <td>Waktu Pengajuan</td>
                    <td>{{ $peminjaman->created_at->format('d/m/Y H:i') }}</td>
                </tr>
            </table>

            <div class="note">
                <strong>⚠️ Penting:</strong> Tunjukkan kode peminjaman
                <strong>{{ $peminjaman->kode_peminjaman }}</strong> kepada petugas
                saat mengambil alat. Petugas akan menggunakan kode ini untuk memproses
                persetujuan peminjaman kamu.
            </div>
        </div>

        <div class="footer">
            Email ini dikirim otomatis, mohon tidak membalas email ini.<br>
            &copy; {{ date('Y') }} Sistem Peminjaman Alat
        </div>
    </div>
</body>
</html>
