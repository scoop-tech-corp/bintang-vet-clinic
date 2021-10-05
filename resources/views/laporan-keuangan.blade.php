<table>
    <thead>
        <tr>
            <th style="text-align: center; border: 1px solid black; width:17px;"><b>TANGGAL</b></th>
            <th style="text-align: center; border: 1px solid black; width:17px;"><b>NAMA OWNER</b></th>
            <th style="text-align: center; border: 1px solid black; width:17px;"><b>NAMA HEWAN</b></th>
            <th style="text-align: center; border: 1px solid black; width:17px;"><b>TINDAKAN</b></th>
            <th style="text-align: center; border: 1px solid black; width:20px;"><b>TOTAL PENDAPATAN</b></th>
            <th style="text-align: center; border: 1px solid black; width:17px;"><b>PETSHOP</b></th>
            <th style="text-align: center; border: 1px solid black; width:17px;"><b>MODAL</b></th>
            <th style="text-align: center; border: 1px solid black; width:17px;"><b>DOKTER</b></th>
            <th style="text-align: center; border: 1px solid black; width:17px;"><b>TOTAL DOKTER</b></th>
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
                        {{ number_format($res_data->selling_price, 2, ',', '.') }}
                    </td>
                    <td style="text-align: right; border: 1px solid black;">
                        {{ number_format($res_data->petshop_fee, 2, ',', '.') }}
                    </td>
                    <td style="text-align: right; border: 1px solid black;">
                        {{ number_format($res_data->capital_price, 2, ',', '.') }}
                    </td>
                    <td style="text-align: right; border: 1px solid black;">
                        {{ number_format($res_data->doctor_fee, 2, ',', '.') }}
                    </td>
                    <td style="text-align: right; border: 1px solid black;"></td>
                </tr>

                @php
                    $total_income += $res_data->selling_price;
                    $total_petshop += $res_data->petshop_fee;
                    $total_capital += $res_data->capital_price;
                    $total_doctor += $res_data->doctor_fee;
                @endphp
            @endforeach
            <tr>
                <td colspan="4" style="text-align: center; border: 1px thick black;"><b>TOTAL</b></td>
                <td style="text-align: center; border: 1px thick black;">
                    <b>{{ number_format($total_income, 2, ',', '.') }}</b>
                </td>
                <td style="text-align: center; border: 1px thick black;">
                    <b>{{ number_format($total_petshop, 2, ',', '.') }}</b>
                </td>
                <td style="text-align: center; border: 1px thick black;">
                    <b>{{ number_format($total_capital, 2, ',', '.') }}</b>
                </td>
                <td style="text-align: center; border: 1px thick black;">
                    <b>{{ number_format($total_doctor, 2, ',', '.') }}</b>
                </td>
                <td style="text-align: right; border: 1px thick black;">
                    <b>{{ number_format($total_capital + $total_doctor, 2, ',', '.') }}</b>
                </td>
            </tr>
        @endforeach

    </tbody>
</table>
