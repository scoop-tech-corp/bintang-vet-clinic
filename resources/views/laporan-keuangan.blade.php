<table>
    <thead>
        <tr>
            <th style="text-align: center; border: 1px solid black;" width="10"><b>TANGGAL</b></th>
            <th style="text-align: center; border: 1px solid black;" width="13"><b>NAMA OWNER</b></th>
            <th style="text-align: center; border: 1px solid black;" width="13"><b>NAMA HEWAN</b></th>
            <th style="text-align: center; border: 1px solid black;" width="27"><b>TINDAKAN</b></th>
            <th style="text-align: center; border: 1px solid black;" width="18"><b>TOTAL PENDAPATAN</b></th>
            <th style="text-align: center; border: 1px solid black;" width="8"><b>PETSHOP</b></th>
            <th style="text-align: center; border: 1px solid black;" width="10"><b>MODAL</b></th>
            <th style="text-align: center; border: 1px solid black;" width="11"><b>DOKTER</b></th>
            <th style="text-align: center; border: 1px solid black;" width="18"><b>PERSENTASE DISKON</b></th>
            <th style="text-align: center; border: 1px solid black;" width="16"><b>NOMINAL DISKON</b></th>
            <th style="text-align: center; border: 1px solid black;" width="26"><b>FEE DOKTER SETELAH DISKON</b></th>
            <th style="text-align: center; border: 1px solid black;" width="13"><b>TOTAL DOKTER</b></th>
            <th style="text-align: center; border: 1px solid black;" width="22"><b>METODE PEMBAYARAN</b></th>
        </tr>
    </thead>
    <tbody>

        @foreach ($data as $datas)
            @php
                $total_income = 0;
                $total_petshop = 0;
                $total_capital = 0;
                $total_doctor = 0;
                $total_overall_doctor = 0;
                $total_amount_discount = 0;
                $total_doctor_after_discount = 0;

                $temp_date = '';
                $temp_owner = '';
                $temp_pet = '';
            @endphp

            @foreach ($datas as $res_data)
                <tr>

                    @if ($temp_date != '')
                        @if ($temp_date == $res_data->created_at)
                            <td style="text-align: center; border: 1px solid black;"></td>
                        @else
                            @php
                                $temp_date = $res_data->created_at;
                            @endphp
                            <td style="text-align: left; border: 1px solid black;">{{ $temp_date }}</td>
                        @endif

                    @else
                        @php
                            $temp_date = $res_data->created_at;
                        @endphp

                        <td style="text-align: left; border: 1px solid black;">{{ $res_data->created_at }}</td>
                    @endif

                    {{-- ======================================================= --}}

                    @if ($temp_owner != '')
                        @if ($temp_owner == $res_data->owner_name)
                            <td style="text-align: center; border: 1px solid black;"></td>
                        @else
                            @php
                                $temp_owner = $res_data->owner_name;
                            @endphp
                            <td style="text-align: left; border: 1px solid black;">{{ $temp_owner }}</td>
                        @endif

                    @else
                        @php
                            $temp_owner = $res_data->owner_name;
                        @endphp

                        <td style="text-align: left; border: 1px solid black;">{{ $res_data->owner_name }}</td>
                    @endif

                    {{-- ======================================================= --}}

                    @if ($temp_pet != '')
                        @if ($temp_pet == $res_data->pet_name)
                            <td style="text-align: left; border: 1px solid black;"></td>
                        @else
                            @php
                                $temp_pet = $res_data->pet_name;
                            @endphp
                            <td style="text-align: left; border: 1px solid black;">{{ $temp_pet }}</td>
                        @endif

                    @else
                        @php
                            $temp_pet = $res_data->pet_name;
                        @endphp

                        <td style="text-align: left; border: 1px solid black;">{{ $res_data->pet_name }}</td>
                    @endif

                    <td style="text-align: left; border: 1px solid black;">{{ $res_data->action }}</td>
                    <td style="text-align: right; border: 1px solid black;">

                        @php
                            $float = (float) $res_data->selling_price;
                        @endphp


                        {{ $float }}
                        {{-- {{ number_format($res_data->selling_price, 2, ',', '.') }} --}}
                    </td>
                    <td style="text-align: right; border: 1px solid black;">
                        @php
                            $float = (float) $res_data->petshop_fee;
                        @endphp
                        {{ $float }}
                        {{-- {{ number_format($res_data->petshop_fee, 2, ',', '.') }} --}}
                    </td>
                    <td style="text-align: right; border: 1px solid black;">
                        @php
                            $float = (float) $res_data->capital_price;
                        @endphp
                        {{ $float }}
                        {{-- {{ number_format($res_data->capital_price, 2, ',', '.') }} --}}
                    </td>
                    <td style="text-align: right; border: 1px solid black;">
                        @php
                            $float = (float) $res_data->doctor_fee;
                        @endphp
                        {{ $float }}
                        {{-- {{ number_format($res_data->doctor_fee, 2, ',', '.') }} --}}
                    </td>
                    <td style="text-align: right; border: 1px solid black;">
                        @php
                            $float = (float) $res_data->discount;
                        @endphp
                        {{ $float }} %
                        {{-- {{ number_format($res_data->doctor_fee, 2, ',', '.') }} --}}
                    </td>
                    <td style="text-align: right; border: 1px solid black;">
                        @php
                            $float = (float) $res_data->amount_discount;
                        @endphp
                        {{ $float }}
                        {{-- {{ number_format($res_data->doctor_fee, 2, ',', '.') }} --}}
                    </td>
                    <td style="text-align: right; border: 1px solid black;">
                        @php
                            $float = (float) $res_data->after_discount;
                        @endphp
                        {{ $float }}
                        {{-- {{ number_format($res_data->doctor_fee, 2, ',', '.') }} --}}
                    </td>
                    <td style="text-align: right; border: 1px solid black;"></td>

                    <td style="text-align: right; border: 1px solid black;">{{ $res_data->payment_name }}</td>
                </tr>

                @php
                    $total_income += $res_data->selling_price;
                    $total_petshop += $res_data->petshop_fee;
                    $total_capital += $res_data->capital_price;
                    $total_doctor += $res_data->doctor_fee;
                    $total_amount_discount += $res_data->amount_discount;
                    $total_doctor_after_discount += $res_data->after_discount;
                @endphp
            @endforeach
            <tr>
                <td colspan="4" style="text-align: center; border: 1px thick black;"><b>TOTAL</b></td>
                <td style="text-align: center; border: 1px thick black;">
                    <b>
                        @php
                            $float = (float) $total_income;
                        @endphp
                        {{ $float }}
                        {{-- {{ number_format($total_income, 2, ',', '.') }} --}}
                    </b>
                </td>
                <td style="text-align: center; border: 1px thick black;">
                    <b>
                        @php
                            $float = (float) $total_petshop;
                        @endphp
                        {{ $float }}
                        {{-- {{ number_format($total_petshop, 2, ',', '.') }} --}}
                    </b>
                </td>
                <td style="text-align: center; border: 1px thick black;">
                    <b>
                        @php
                            $float = (float) $total_capital;
                        @endphp
                        {{ $float }}
                        {{-- {{ number_format($total_capital, 2, ',', '.') }} --}}
                    </b>
                </td>
                <td style="text-align: center; border: 1px thick black;">
                    <b>
                        @php
                            $float = (float) $total_doctor;
                        @endphp
                        {{ $float }}
                        {{-- {{ number_format($total_doctor, 2, ',', '.') }} --}}
                    </b>
                </td>

                <td style="text-align: center; border: 1px thick black;"> {{-- persentase diskon --}}

                </td>

                <td style="text-align: center; border: 1px thick black;"> {{-- nominal diskon --}}
                    <b>
                        @php
                            $float = (float) $total_amount_discount;
                        @endphp
                        {{ $float }}
                        {{-- {{ number_format($total_petshop, 2, ',', '.') }} --}}
                    </b>
                </td>

                <td style="text-align: center; border: 1px thick black;"> {{-- fee dokter setelah diskon --}}
                    <b>
                        @php
                            $float = (float) $total_doctor_after_discount;
                        @endphp
                        {{ $float }}
                        {{-- {{ number_format($total_petshop, 2, ',', '.') }} --}}
                    </b>
                </td>

                <td style="text-align: right; border: 1px thick black;">
                    <b>
                        @php
                            $float = (float) $total_capital + $total_doctor;
                        @endphp
                        {{ $float }}
                        {{-- {{ number_format($total_capital + $total_doctor, 2, ',', '.') }} --}}
                    </b>
                </td>
            </tr>
        @endforeach

    </tbody>
</table>
