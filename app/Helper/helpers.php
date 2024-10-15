<?php
function hitungjamterlambat($jadwal_jam_masuk, $jam_presensi)
{
    $j1 = strtotime($jadwal_jam_masuk);
    $j2 = strtotime($jam_presensi);

    $diffterlambat = $j2 - $j1;

    $jamterlambat = floor($diffterlambat / (60 * 60));
    $menitterlambat = floor(($diffterlambat - ($jamterlambat * (60 * 60)))/60);

    $jterlambat = $jamterlambat <= 9 ? "0" . $jamterlambat : $jamterlambat;
    $mterlambat=$menitterlambat <= 9 ? "0" . $menitterlambat : $menitterlambat;

    $terlambat=$jterlambat . ":" . $mterlambat;
    return $terlambat;
}

function hitungjamterlambatdesimal($jam_masuk, $jam_presensi)
{
    $j1 = strtotime($jam_masuk);
    $j2 = strtotime($jam_presensi);

    // Selisih waktu dalam detik
    $diffterlambat = $j2 - $j1;

    // Konversi selisih waktu menjadi jam dan menit
    $jamterlambat = floor($diffterlambat / (60 * 60)); // Hitung jam
    $menitterlambat = floor(($diffterlambat - ($jamterlambat * (60 * 60))) / 60); // Hitung menit

    // Gabungkan jam dengan desimal menit
    $desimalterlambat = $jamterlambat + ROUND(($menitterlambat / 60), 2);

    return $desimalterlambat;
}
