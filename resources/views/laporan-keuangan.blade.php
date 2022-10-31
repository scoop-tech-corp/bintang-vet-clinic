<table>
    <thead>
        <tr>
            <th style="text-align: center; border: 1px solid black;" width="10"><b>TANGGAL</b></th>
            <th style="text-align: center; border: 1px solid black;" width="14"><b>NAMA OWNER</b></th>
            <th style="text-align: center; border: 1px solid black;" width="13"><b>NAMA HEWAN</b></th>
            <th style="text-align: center; border: 1px solid black;" width="27"><b>ITEM</b></th>
            <th style="text-align: center; border: 1px solid black;" width="13"><b>JUMLAH ITEM</b></th>
            <th style="text-align: center; border: 1px solid black;" width="15"><b>HARGA SATUAN</b></th>
            <th style="text-align: center; border: 1px solid black;" width="27"><b>HARGA KESELURUHAN</b></th>
            <th style="text-align: center; border: 1px solid black;" width="20"><b>TOTAL PER NOTA</b></th>
            <th style="text-align: center; border: 1px solid black;" width="15"><b>PETSHOP</b></th>
            <th style="text-align: center; border: 1px solid black;" width="10"><b>MODAL</b></th>
            <th style="text-align: center; border: 1px solid black;" width="11"><b>DOKTER</b></th>
            <th style="text-align: center; border: 1px solid black;" width="18"><b>PERSENTASE DISKON</b></th>
            <th style="text-align: center; border: 1px solid black;" width="16"><b>NOMINAL DISKON</b></th>
            <th style="text-align: center; border: 1px solid black;" width="26"><b>FEE DOKTER SETELAH DISKON</b></th>
            <th style="text-align: center; border: 1px solid black;" width="13"><b>TOTAL DOKTER</b></th>
            <th style="text-align: center; border: 1px solid black;" width="13"><b>PENGELUARAN</b></th>
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
                $price_each_note = 0;
                $total_note = 0;

                $total_item = 0;

                $temp_date = '';
                $temp_owner = '';
                $temp_pet = '';
                $flag_color = 0;
            @endphp

            @for ($i = 0; $i < $datas->count(); $i++)
                @if($datas[$i]->data_category == "clinic")
                    <tr>
                        @if ($temp_date != '')
                            @if ($temp_date == $datas[$i]->created_at)
                                <td style="text-align: center; border: 1px solid black;"></td>
                            @else
                                @php
                                    $temp_date = $datas[$i]->created_at;
                                @endphp
                                <td style="text-align: left; border: 1px solid black;">{{ $temp_date }}</td>
                            @endif

                        @else
                            @php
                                $temp_date = $datas[$i]->created_at;
                            @endphp

                            <td style="text-align: left; border: 1px solid black;">{{ $datas[$i]->created_at }}</td>
                        @endif

                        {{-- ======================================================= --}}

                        @if ($temp_owner != '')
                            @if ($temp_owner == $datas[$i]->owner_name)
                                <td style="text-align: center; border: 1px solid black;"></td>
                            @else
                                @php
                                    $temp_owner = $datas[$i]->owner_name;
                                @endphp
                                <td style="text-align: left; border: 1px solid black;">{{ $temp_owner }}</td>
                            @endif

                        @else
                            @php
                                $temp_owner = $datas[$i]->owner_name;
                            @endphp

                            <td style="text-align: left; border: 1px solid black;">{{ $datas[$i]->owner_name }}</td>
                        @endif

                        {{-- ======================================================= --}}

                        @if ($temp_pet != '')
                            @if ($temp_pet == $datas[$i]->pet_name)
                                <td style="text-align: left; border: 1px solid black;"></td>
                            @else
                                @php
                                    $temp_pet = $datas[$i]->pet_name;
                                @endphp
                                <td style="text-align: left; border: 1px solid black;">{{ $temp_pet }}</td>
                            @endif

                        @else
                            @php
                                $temp_pet = $datas[$i]->pet_name;
                            @endphp

                            <td style="text-align: left; border: 1px solid black;">{{ $datas[$i]->pet_name }}</td>
                        @endif

                        <td style="text-align: left; border: 1px solid black;">{{ $datas[$i]->item }}</td>
                        <td style="text-align: left; border: 1px solid black;">{{ $datas[$i]->total_item }}</td>
                        @php
                            $total_item += $datas[$i]->total_item;
                        @endphp

                        <td style="text-align: right; border: 1px solid black;">

                            @php
                                $float = (float) $datas[$i]->each_price;
                            @endphp


                            {{ $float }}
                            {{-- {{ number_format($datas[$i]->each_price, 2, ',', '.') }} --}}
                        </td>

                        <!-- <td style="text-align: right; border: 1px solid black;">

                            @php
                                $float = (float) $datas[$i]->price_overall;
                            @endphp


                            {{ $float }}
                            {{-- {{ number_format($datas[$i]->price_overall, 2, ',', '.') }} --}}
                        </td> -->

                        <td style="text-align: right; border: 1px solid black;">

                            @php
                                $float = (float) $datas[$i]->selling_price;
                            @endphp


                            {{ $float }}
                            {{-- {{ number_format($datas[$i]->selling_price, 2, ',', '.') }} --}}
                        </td>
                        
                        @if($i + 1 < $datas->count())

                            @if ($datas[$i]->pet_name == $datas[$i + 1]->pet_name)
                                <td style="text-align: right; border: 1px solid black;"></td>

                                @php
                                    $price_each_note += $datas[$i]->selling_price;
                                @endphp
                            @else
                                @php
                                    $price_each_note += $datas[$i]->selling_price;
                                @endphp
                            <td style="text-align: right; border: 1px solid black;"><b>{{ $price_each_note }}</b></td>
                                @php
                                    $total_note += $price_each_note;
                                    $price_each_note = 0;
                                @endphp
                            @endif

                        @else

                            @php
                                $price_each_note += $datas[$i]->selling_price;
                                $total_note += $price_each_note;
                            @endphp
                            <td style="text-align: right; border: 1px solid black;"><b>{{ $price_each_note }}</b></td>

                        @endif

                            <td style="text-align: right; border: 1px solid black;">{{ $datas[$i]->petshop_fee }}</td>
                            <!-- @php
                                $float = (float) $datas[$i]->petshop_fee;
                            @endphp
                            {{ $float }}
                            {{-- {{ number_format($datas[$i]->petshop_fee, 2, ',', '.') }} --}} -->
                        
                        <td style="text-align: right; border: 1px solid black;">
                            {{$datas[$i]->capital_price}}
                            <!-- @php
                                $float = (float) $datas[$i]->capital_price;
                            @endphp
                            {{ $float }}
                            {{-- {{ number_format($datas[$i]->capital_price, 2, ',', '.') }} --}} -->
                        </td>
                        <td style="text-align: right; border: 1px solid black;">
                            @php
                                $float = (float) $datas[$i]->doctor_fee;
                            @endphp
                            {{ $float }}
                            {{-- {{ number_format($datas[$i]->doctor_fee, 2, ',', '.') }} --}}
                        </td>
                        <td style="text-align: right; border: 1px solid black;">
                            @php
                                $float = (float) $datas[$i]->discount;
                            @endphp
                            {{ $float }} %
                            {{-- {{ number_format($datas[$i]->doctor_fee, 2, ',', '.') }} --}}
                        </td>
                        <td style="text-align: right; border: 1px solid black;">
                            @php
                                $float = (float) $datas[$i]->amount_discount;
                            @endphp
                            {{ $float }}
                            {{-- {{ number_format($datas[$i]->doctor_fee, 2, ',', '.') }} --}}
                        </td>
                        <td style="text-align: right; border: 1px solid black;">
                            @php
                                $float = (float) $datas[$i]->after_discount;
                            @endphp
                            {{ $float }}
                            {{-- {{ number_format($datas[$i]->doctor_fee, 2, ',', '.') }} --}}
                        </td>
                        <td style="text-align: right; border: 1px solid black;"></td>

                        <td style="text-align: right; border: 1px solid black;"></td>

                        <td style="text-align: right; border: 1px solid black;">{{ $datas[$i]->payment_name }}</td>
                    </tr>

                @else
                    @if($flag_color == 0)
                        <tr>
                            <td style="text-align: left; border: 1px solid black;"></td>
                            <td style="text-align: left; border: 1px solid black;"></td>
                            <td style="text-align: left; border: 1px solid black;"></td>
                            <td style="text-align: left; border: 1px solid black;"></td>
                            <td style="text-align: left; border: 1px solid black;"></td>
                            <td style="text-align: left; border: 1px solid black;"></td>
                            <td style="text-align: left; border: 1px solid black;"></td>
                            <td style="text-align: left; border: 1px solid black;"></td>
                            <td style="text-align: left; border: 1px solid black;"></td>
                            <td style="text-align: left; border: 1px solid black;"></td>
                            <td style="text-align: left; border: 1px solid black;"></td>
                            <td style="text-align: left; border: 1px solid black;"></td>
                            <td style="text-align: left; border: 1px solid black;"></td>
                            <td style="text-align: left; border: 1px solid black;"></td>
                            <td style="text-align: left; border: 1px solid black;"></td>
                            <td style="text-align: left; border: 1px solid black;"></td>
                            <td style="text-align: left; border: 1px solid black;"></td>
                        </tr>
                        <tr>
                            <td style="text-align: left; border: 1px solid black; background-color: #F6D7B8;"></td>
                            <td style="text-align: left; border: 1px solid black; background-color: #F6D7B8;"></td>
                            <td style="text-align: left; border: 1px solid black; background-color: #F6D7B8;"></td>
                            <td style="text-align: left; border: 1px solid black; background-color: #F6D7B8;"></td>
                            <td style="text-align: left; border: 1px solid black; background-color: #F6D7B8;"></td>
                            <td style="text-align: left; border: 1px solid black; background-color: #F6D7B8;"></td>
                            <td style="text-align: left; border: 1px solid black; background-color: #F6D7B8;"></td>
                            <td style="text-align: left; border: 1px solid black; background-color: #F6D7B8;"></td>
                            <td style="text-align: left; border: 1px solid black; background-color: #F6D7B8;"></td>
                            <td style="text-align: left; border: 1px solid black; background-color: #F6D7B8;"></td>
                            <td style="text-align: left; border: 1px solid black; background-color: #F6D7B8;"></td>
                            <td style="text-align: left; border: 1px solid black; background-color: #F6D7B8;"></td>
                            <td style="text-align: left; border: 1px solid black; background-color: #F6D7B8;"></td>
                            <td style="text-align: left; border: 1px solid black; background-color: #F6D7B8;"></td>
                            <td style="text-align: left; border: 1px solid black; background-color: #F6D7B8;"></td>
                            <td style="text-align: left; border: 1px solid black; background-color: #F6D7B8;"></td>
                            <td style="text-align: left; border: 1px solid black; background-color: #F6D7B8;"></td>
                        </tr>
                        <tr>
                            <td style="text-align: left; border: 1px solid black;">{{ $datas[$i]->created_at }}</td>
                            <td style="text-align: left; border: 1px solid black;">{{ $datas[$i]->owner_name }}</td>
                            <td style="text-align: left; border: 1px solid black;"></td>
                            <td style="text-align: left; border: 1px solid black;">{{ $datas[$i]->pet_name }}</td>
                            <td style="text-align: left; border: 1px solid black;">{{ $datas[$i]->total_item }}</td>
                            @php
                                $total_item += $datas[$i]->total_item;
                            @endphp
                            <td style="text-align: right; border: 1px solid black;">{{ $datas[$i]->each_price }}</td>
                            <td style="text-align: right; border: 1px solid black;">{{ $datas[$i]->price_overall }}</td>

                            @if($i + 1 < $datas->count())
                                @if($datas[$i]->owner_name == $datas[$i + 1]->owner_name)
                                    <td style="text-align: right; border: 1px solid black;"></td>

                                    @php
                                        $price_each_note += $datas[$i]->price_overall;
                                    @endphp
                                @else
                                    @php
                                        $price_each_note += $datas[$i]->price_overall;
                                    @endphp
                                    <td style="text-align: right; border: 1px solid black;"><b>{{ $price_each_note }}</b></td>
                                    @php
                                        $total_note += $price_each_note;
                                        $price_each_note = 0;
                                    @endphp
                                @endif
                            @else
                                @php
                                    $price_each_note += $datas[$i]->price_overall;
                                    $total_note += $price_each_note;
                                @endphp
                                <td style="text-align: right; border: 1px solid black;"><b>{{ $price_each_note }}</b></td>
                            @endif

                            <td style="text-align: right; border: 1px solid black;">{{0}}</td>
                            <td style="text-align: right; border: 1px solid black;">{{$datas[$i]->capital_price}}</td>
                            <td style="text-align: right; border: 1px solid black;">{{$datas[$i]->doctor_fee}}</td>
                            <td style="text-align: right; border: 1px solid black;">{{$datas[$i]->discount}}</td> <!--persentase dokter -->
                            <td style="text-align: right; border: 1px solid black;">{{$datas[$i]->amount_discount}}</td> <!--nominal diskon -->
                            <td style="text-align: right; border: 1px solid black;">{{$datas[$i]->after_discount}}</td> <!--fee dokter setelah diskon -->
                            <td style="text-align: right; border: 1px solid black;"></td>
                            <td style="text-align: right; border: 1px solid black;"></td>
                            <td style="text-align: right; border: 1px solid black;"></td>
                        </tr>
                        @php
                        $flag_color = 1;
                        @endphp
                    @else
                        <tr>
                            <td style="text-align: left; border: 1px solid black;">{{ $datas[$i]->created_at }}</td>
                            <td style="text-align: left; border: 1px solid black;">{{ $datas[$i]->owner_name }}</td>
                            <td style="text-align: left; border: 1px solid black;"></td>
                            <td style="text-align: left; border: 1px solid black;">{{ $datas[$i]->pet_name }}</td>
                            <td style="text-align: left; border: 1px solid black;">{{ $datas[$i]->total_item }}</td>
                            @php
                                $total_item += $datas[$i]->total_item;
                            @endphp
                            <td style="text-align: right; border: 1px solid black;">{{ $datas[$i]->each_price }}</td>
                            <td style="text-align: right; border: 1px solid black;">{{ $datas[$i]->price_overall }}</td>

                            @if($i + 1 < $datas->count())
                                @if($datas[$i]->owner_name == $datas[$i + 1]->owner_name)
                                    <td style="text-align: right; border: 1px solid black;"></td>

                                    @php
                                        $price_each_note += $datas[$i]->price_overall;
                                    @endphp
                                @else
                                    @php
                                        $price_each_note += $datas[$i]->price_overall;
                                    @endphp
                                    <td style="text-align: right; border: 1px solid black;"><b>{{ $price_each_note }}</b></td>
                                    @php
                                        $total_note += $price_each_note;
                                        $price_each_note = 0;
                                    @endphp
                                @endif
                            @else
                                @php
                                    $price_each_note += $datas[$i]->price_overall;
                                    $total_note += $price_each_note;
                                @endphp
                                <td style="text-align: right; border: 1px solid black;"><b>{{ $price_each_note }}</b></td>
                            @endif

                            <td style="text-align: right; border: 1px solid black;">{{0}}</td>
                            <td style="text-align: right; border: 1px solid black;">{{$datas[$i]->capital_price}}</td>
                            <td style="text-align: right; border: 1px solid black;">{{$datas[$i]->doctor_fee}}</td>
                            <td style="text-align: right; border: 1px solid black;">{{$datas[$i]->discount}}</td> <!--persentase dokter -->
                            <td style="text-align: right; border: 1px solid black;">{{$datas[$i]->amount_discount}}</td> <!--nominal diskon -->
                            <td style="text-align: right; border: 1px solid black;">{{$datas[$i]->after_discount}}</td> <!--fee dokter setelah diskon -->
                            <td style="text-align: right; border: 1px solid black;"></td>
                            <td style="text-align: right; border: 1px solid black;"></td>
                            <td style="text-align: right; border: 1px solid black;"></td>
                        </tr>  
                    @endif
                    
                @endif

                @php
                    $total_income += $datas[$i]->selling_price;
                    $total_petshop += $datas[$i]->petshop_fee;
                    $total_capital += $datas[$i]->capital_price;
                    $total_doctor += $datas[$i]->doctor_fee;
                    $total_amount_discount += $datas[$i]->amount_discount;
                    $total_doctor_after_discount += $datas[$i]->after_discount;
                @endphp
                
            @endfor
            <tr>
                <td colspan="4" style="text-align: center; border: 1px thick black;"><b>TOTAL</b></td>
                <td style="text-align: center; border: 1px thick black;">
                    <b>
                        @php
                            $float = (float) $total_item;
                        @endphp
                        {{ $float }}
                        {{-- {{ number_format($total_income, 2, ',', '.') }} --}}
                    </b>
                </td>
                <td style="text-align: center; border: 1px thick black;">
                    <b>
                        <!-- @php
                            $float = (float) $total_petshop;
                        @endphp
                        {{ $float }} -->
                        <!-- {{-- {{ number_format($total_petshop, 2, ',', '.') }} --}} -->
                    </b>
                </td>
                <td style="text-align: center; border: 1px thick black;">
                    <b>
                        <!-- @php
                            $float = (float) $total_capital;
                        @endphp
                        {{ $float }} -->
                        <!-- {{-- {{ number_format($total_capital, 2, ',', '.') }} --}} -->
                    </b>
                </td>
                <td style="text-align: center; border: 1px thick black;">
                    <b>
                        @php
                            $float = (float) $total_note;
                        @endphp
                        {{ $float }}
                        <!-- {{-- {{ number_format($total_doctor, 2, ',', '.') }} --}} -->
                    </b>
                </td>

                <td style="text-align: center; border: 1px thick black;"> {{-- persentase diskon --}}

                </td>

                <td style="text-align: center; border: 1px thick black;"></td>

                <td style="text-align: center; border: 1px thick black;"></td>

                <td style="text-align: center; border: 1px thick black;"></td>

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

                <td style="text-align: right; border: 1px thick black;">
                    <b>
                        {{ $expenses }}
                    </b>
                </td>
                <td style="text-align: right; border: 1px thick black;">
                    <b>
                        {{ $total_doctor_after_discount - $expenses }}
                    </b>
                </td>
            </tr>
        @endforeach

    </tbody>
</table>
