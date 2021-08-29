<!DOCTYPE html>
<html>

<head>
    <style>
        @page {
            margin: 0px;
            font-size: 20px;
            /* font-family: Calibri; */
        }

        body {
            margin: 0px;
            font-size: 20px;
            font-family: Arial, Helvetica, sans-serif;
        }

        table,
        td,
        th {
            border: 2px solid black;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            padding: 0px;
            margin: 0px;
        }

        th {
            height: 10px;
        }

        label.title {
            font-weight: bold;
            font-size: 20px
        }

        label.titlePetName {
            font-weight: bold;
            font-size: 20px
        }

        label.address {
            font-size: 15px
        }

        td.date {
            text-align: right;
            font-size: 15px
        }

        .img-style {
            border-radius: 50%;
        }

        .center-content {
            text-align: center;
            vertical-align: middle;
        }

    </style>
    {{-- <title>{{ $registration_number }}</title> --}}
</head>


<body>
    <table>
        <tr>
            <td>
                <label class="titlePetName">Bintang Vet Clinic</label>
            </td>
            <td>

            </td>
            <td>

            </td>
        </tr>
        <tr>
            <td>
                <label class="address">Alamat</label>
            </td>
            <td style="text-align: center">
                <label class="title">SLIP GAJI</label>
            </td>
            <td class="date">
                <label>Tanggal: </label>
            </td>

        </tr>

        <tr>
            <td>

            </td>
            <td>

            </td>
            <td class="date">
                <label>Kode Karyawan: </label>
            </td>

        </tr>
    </table>

    <table>

        <tr>
            <td>
                <label>Nama: </label>
            </td>
            <td>
                <label>Alamat: </label>
            </td>

        </tr>

        <tr>
            <td>
                <label>Jabatan: </label>
            </td>
            <td>
                <label>Telepon: </label>
            </td>

        </tr>

    </table>

    <table>
        <tr>
            <td>
                <label>No</label>
            </td>
            <td>
                <label>Keterangan</label>
            </td>
            <td>
                <label>Jumlah</label>
            </td>
        </tr>

        <tr>
            <td>
                <label>1</label>
            </td>
            <td>
                <label>Gaji Pokok</label>
            </td>
            <td>
                <label>Jumlah: </label>
            </td>
        </tr>

        <tr>
            <td>
                <label>2</label>
            </td>
            <td>
                <label>Akomodasi</label>
            </td>
            <td>
                <label>Jumlah: </label>
            </td>
        </tr>

        <tr>
            <td>
                <label>3</label>
            </td>
            <td>
                <label>Bonus Omzet</label>
            </td>
            <td>
                <label>Jumlah: </label>
            </td>
        </tr>

        <tr>
            <td>
                <label>4</label>
            </td>
            <td>
                <label>Bonus Rawat Inap</label>
            </td>
            <td>
                <label>Jumlah: </label>
            </td>
        </tr>

        <tr>
            <td>
                <label>5</label>
            </td>
            <td>
                <label>Bonus Operasi</label>
            </td>
            <td>
                <label>Jumlah: </label>
            </td>
        </tr>

        <tr>
          <td>
              <label></label>
          </td>
          <td>
              <label>Total Diterima</label>
          </td>
          <td>
              <label>Jumlah: </label>
          </td>
      </tr>
    </table>
</body>

</html>
