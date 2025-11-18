@extends('templates.app')

@section('content')
    <div class="container card my-5 p-4" style="margin-bottom: 10% !important">
        <div class="card-body">
            <b>{{ $schedule['cinema']['name'] }}</b>
            <br>
            {{-- mengambil tanggal hari ini carbon::now() --}}

            <b>{{ \Carbon\Carbon::now()->format('d M Y') }} || {{ $hour }}</b>

            <div class="d-flex justify-content-center mt-3">
                <div class="row w-50">
                    <div class="col-4 d-flex">
                        <div style="width: 20px; height: 20px; background: #112646; border-radius: 5px;"></div>
                        <p class="ms-2">Kursi Kosong</p>
                    </div>
                    <div class="col-4 d-flex">
                        <div style="width: 20px; height: 20px; background: #eaeaea; border-radius: 5px;"></div>
                        <p class="ms-2">Kursi Terjual</p>
                    </div>
                    <div class="col-4 d-flex">
                        <div style="width: 20px; height: 20px; background: #a0cbf3; border-radius: 5px;"></div>
                        <p class="ms-2">Kursi Dipilih</p>
                    </div>
                </div>
            </div>

            @php
                $row = range('A', 'J');
                $col = range(1, 16);
            @endphp
            {{-- looping untuk membuat baris A-H --}}
            @foreach ($row as $baris)
                <div class="d-flex justify-content-center my-1">
                    {{-- looping untuk membuat kursi di satu baris --}}
                    @foreach ($col as $kursi)
                        {{-- jika kursi nomor 7, tambahkan space kosong untuk jalan --}}
                        @if ($kursi == 7)
                            <div style="width: 30px"></div>
                        @endif

                        @php
                            $seat = $baris . '-' . $kursi;
                        @endphp
                        {{-- in_array : mencari item di array php --}}
                        @if (in_array($seat, $seatsFormat))
                            <div style="background:#eaeaea; border-radius: 10px; width: 45px; height: 45px; margin: 5px;  cursor: pointer;"
                                class="p-2 mx-1 text-dark text-center">
                                <span style="font-size: 12px;">{{ $baris }}-{{ $kursi }}</span>
                            </div>
                        @else
                            {{-- munculkan A-1 A-2 dst --}}
                            <div style="background:#112646; border-radius: 10px; width: 45px; height: 45px; margin: 5px;  cursor: pointer;"
                                class="p-2 mx-1 text-white text-center"
                                onclick="selectedSeat('{{ $schedule->price }}',
                            '{{ $baris }}', '{{ $kursi }}', this)">
                                <span style="font-size: 12px;">{{ $baris }}-{{ $kursi }}</span>
                            </div>
                        @endif
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>

    <div class="w-100 p-2 bg-light text-center fixed-bottom" id="wrapBtn">
        <b class="text-center p-3 ">LAYAR BIOSKOP</b>
        <div class="row" style="border: 1px solid #ffb0d0;">
            <div class="col-6 text-center" style="border: 1px solid #d1d1d1">
                <p>Total Harga</p>
                <h5 id="totalPrice">Rp. -</h5>
            </div>
            <div class="col-6 text-center" style="border: 1px solid #d1d1d1">
                <p>Kursi Dipilih</p>
                <h5 id="selectedSeats">-</h5>
            </div>
        </div>
        {{-- menyimpan value yang diperlukan untuk aksi ringkasan pemesanan --}}
        <input type="hidden" name="user_id" value="{{ Auth::user()->id }}" id="user_id">
        <input type="hidden" name="schedule_id" value="{{ $schedule->id }}" id="schedule_id">
        <input type="hidden" name="hour" value="{{ $hour }}" id="hour">

        <div style="color: black; font-weight: bold; cursor: not-allowed;" class="w-100 text-center" id="btnOrder">
            RINGKASAN PEMESANAN</div>
    </div>
@endsection

@push('script')
    <script>
        // array data kursi yang sudah dipilih
        let seats = [];
        let totalPriceData = null;

        function selectedSeat(price, row, col, el) {
            // membuat A-1 sesuai row dan col yang dipilih
            let seatItem = row + "-" + col;
            // cek apakah kursi ini sudah ada di arrat seats
            let indexSeat = seats.indexOf(seatItem);
            // jika ada akan muncul index nya jika engga ada -1\
            if (indexSeat == -1) {

                // kalau engga ada simpen kursi yang dipilih ke array
                seats.push(seatItem);
                // kasi warna kuning ke element yang dipilih
                el.style.background = "#a0cbf3";
            } else {
                // kalau ada di array artinya klik kali ini untuk membatalakan pilihan (klikan ke 2 pada kursi)
                seats.splice(indexSeat, 1); //hapus item dari array
                //kembalikan warna ke biru tua
                el.style.background = "#112646";
            }

            //menghitung total harga sesuai kursi yang dipilih
            let totalPrice = price * (seats.length); // seats.lenght : jumlah item array
            totalPriceData = totalPrice;
            let totalPriceEl = document.querySelector("#totalPrice");
            totalPriceEl.innerText = "Rp" + totalPrice;

            //munculkan daftar kursi yang dipilih
            let selectedSeatsEl = document.querySelector("#selectedSeats");
            //seats.join(",") mengubah array jadi string, dipisahkan dengan tanda tertentu
            selectedSeatsEl.innerText = seats.join(",");


            //jika seats nya lebih dari/sama dengan 1 aktifkan order dan tambahkan fungsi onclick untuk proses data tiket
            if (seats.length > 0) {
                let btnOrder = document.querySelector('#btnOrder');
                btnOrder.style.background = '#112646';
                btnOrder.style.color = 'white';
                btnOrder.style.cursor = 'pointer';
                btnOrder.onclick = createTicketData;
            } else {
                btnOrder.style.background = '';
                btnOrder.style.color = '';
                btnOrder.style.cursor = '';
                btnOrder.onclick = null;
            }
        }

        function createTicketData() {
            $.ajax({
                url: "{{ route('tickets.store') }}", //routing untuk akses data
                method: "POST", //http method
                data: { //data yang akan dikirm (diambil pake Request $request)
                    _token: "{{ csrf_token() }}", //csrf token
                    user_id: $('#user_id').val(),
                    schedule_id: $('#schedule_id').val(),
                    rows_of_seats: seats,
                    quantity: seats.length,
                    total_price: totalPriceData,
                    hour: $("#hour").val()
                },
                success: function(response) {
                    // console.log(response)
                    //jika berhasil menambahkan data arahkan ke ticket order (ringkasan order)
                    //response : message & data
                    let ticketId = response.data.id;
                    window.location.href = `/tickets/${ticketId}/order`;
                },
                error: function(message) {
                    console.log(message);
                    alert("Terjadi kesalahan ketika mambuat data tiket!");
                }
            })
        }
    </script>
@endpush
