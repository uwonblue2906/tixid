@extends('templates.app')

@section('content')
    <div class="container my-5 card">
        <div class="card-body">
            {{-- krn data schedules diambil dgn get() dan data ebih dari 1, maka utk mengambil data cinemanya ambil dari 1 data ajaa index 0 --}}
            <i class="fa-solid fa-location-dot me-3"></i>{{ $schedules[0]['cinema']['location'] }}
            <hr>
            @foreach ($schedules as $schedule)
                <div class="my-2">
                    <div class="d-flex">
                        <div style="width: 150px height: 200px">
                            <img src="{{ asset('storage/' . $schedule['movie']['poster']) }}" alt="poster"
                                class="img-fluid mx-auto d-block" style="max-width:250px; border-radius:12px;">
                        </div>
                        <div class="ms-5 mt-4">
                            <h5>{{ $schedule['movie']['title'] }}</h5>
                            <table>
                                <tr>
                                    <td><b class="text-seondary">Genre</b></td>
                                    <td class="px-3"></td>
                                    <td>{{ $schedule['movie']['genre'] }}</td>
                                </tr>
                                <tr>
                                    <td><b class="text-seondary">Durasi</b></td>
                                    <td class="px-3"></td>
                                    <td>{{ $schedule['movie']['duration'] }}</td>
                                </tr>
                                <tr>
                                    <td><b class="text-seondary">Sutradara</b></td>
                                    <td class="px-3"></td>
                                    <td>{{ $schedule['movie']['director'] }}</td>
                                </tr>
                                <tr>
                                    <td><b class="text-seondary">Rating Usia</b></td>
                                    <td class="px-3"></td>
                                    <td><span class="badge badge-danger">{{ $schedule['movie']['age_rating'] }}</span></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="w-100 my-3">
                        <div class="d-flex justify-content-between">
                            <div>
                                <b>Rp. {{ number_format($schedule['price'], 0, ',', '.') }}</b>
                            </div>
                        </div>
                        <div class="d-flex gap-3 ps-3 my-2">
                            @foreach ($schedule['hours'] as $index => $hours)
                                {{-- this : mengirim element html ini ke js utk di manipulasi --}}
                                <div class="btn btn-outline-secondary" style="cursor: pointer"
                                    onclick="selectedHour('{{ $schedule->id }}', '{{ $index }}', this)">
                                    {{ $hours }}</div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <hr>
            @endforeach
        </div>
    </div>
    <div class="w-100 p-2 bg-light text-center fixed-bottom" id="wrapBtn">
        {{-- dissable button = klo di href (javascript:void(0)) --}}
        <a href="javascript:void(0)" id="btnTicket"><i class="fa-solid fa-ticket"> Beli Tiket</i></a>
    </div>
@endsection
@push('script')
    <script>
        // menyimpan element sebelumnya yg pernah di klik
        let elementBefore = null;

        function selectedHour(scheduleId, hourId, el) {
            // jika elemen sblmnya ada dan skrg pindah ke elemen lain kliknya. ubah elemen sblmnya jadi putih lg
            if (elementBefore) {
                // ubah styling css : style property
                elementBefore.style.background = "";
                elementBefore.style.color = "";
                // ubah properti css kebab di js jadi camel
                elementBefore.style.borderColor = "";
            }
            // kasih wwarna elemen baru
            el.style.background = "#112646";
            el.style.color = "white";
            el.style.borderColor = "#112646";
            // update elemen sblmnya pake elemen baru
            elementBefore = el;

            let wrapBtn = document.querySelector("#wrapBtn");
            let btnTicket = document.querySelector("#btnTicket");
            // kasih warna biru ke div wrap dan hilangkan abu
            // warna abu dari bg-light class bootstrap
            wrapBtn.classList.remove('bg-light');
            wrapBtn.style.background = "#112646";
            // siapkan route
            let url = "{{ route('schedules.show_seats', ['scheduleId' => ':scheduleId', 'hourId' => ':hourId']) }}"
                .replace(':scheduleId', scheduleId) //ubah parameter : dgn value dari js
                .replace(':hourId', hourId);
            // isi url ke href btnTicket
            btnTicket.href = url;
            btnTicket.style.color = 'white';
        }
    </script>
@endpush
