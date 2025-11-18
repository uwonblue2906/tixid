<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Bukti Pembelian Tiket</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            padding: 20px;
        }

        .wrapper {
            width: 300px;
            display: block;
            margin: 20px auto;
            border: 1px solid #eaeaea;
            background: #fff;
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .ticket-item {
            padding: 12px;
            border-bottom: 1px solid #eee;
        }

        .ticket-item:last-child {
            border-bottom: none;
        }

        .ticket-item b {
            font-size: 14px;
            display: block;
            margin-bottom: 6px;
            /* jarak antara judul */
        }

        hr {
            border: none;
            border-top: 1px solid #ddd;
            margin: 10px 0;
            /* tambah jarak horizontal */
        }

        p {
            margin: 6px 0;
            /* jarak antar teks */
            font-size: 13px;
            color: #333;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        @foreach ($ticket['rows_of_seats'] as $kursi)
            <div class="ticket-item">
                <b>{{ $ticket['schedule']['cinema']['name'] }}</b>
                <hr>
                <b>{{ $ticket['schedule']['movie']['title'] }}</b>

                <p>Tanggal : {{ \Carbon\Carbon::parse($ticket['ticket_payment']['booked_date'])->format('d F Y') }}</p>
                <p>Waktu : {{ \Carbon\Carbon::parse($ticket['hour'])->format('H:i') }}</p>
                <p>Kursi : {{ $kursi }}</p>
                <p>Harga Tiket : Rp. {{ number_format($ticket['schedule']['price'], 0, ',', '.') }}</p>
            </div>
        @endforeach
    </div>
</body>

</html>
