<?php

/**
 * Pesan kesalahan formulir, dalam bahasa Indonesia.
 *
 * MENGAPA BERBAHASA INDONESIA DI DALAM FOLDER 'en'
 * Seluruh antarmuka aplikasi ini berbahasa Indonesia dan penggunanya pengurus
 * masjid, tetapi Config\App::$defaultLocale bernilai 'en' dan $supportedLocales
 * hanya ['en'] — tidak ada pemilihan bahasa. Berkas ini menimpa
 * system/Language/en/Validation.php, sehingga cukup satu berkas untuk membuat
 * SELURUH pesan kesalahan formulir berbahasa Indonesia, tanpa mengubah
 * konfigurasi apa pun. Memindah locale ke 'id' menuntut folder id/ yang lengkap
 * dan mengubah perilaku global demi hasil yang sama.
 *
 * MENGAPA PERLU
 * Enam belas model mendeklarasikan $validationMessages = [] dan dua model zakat
 * tidak mendeklarasikannya sama sekali, jadi setiap kegagalan formulir dijawab
 * kalimat Inggris bawaan CI4 — "The name field must be at least 3 characters in
 * length." Mengisi pesan pada tiap model berarti menyalin kalimat yang sama
 * berulang-ulang; di sini cukup sekali, dan berlaku untuk model baru juga.
 *
 * GAYA BAHASA
 * Ditujukan kepada pengurus masjid, bukan pemrogram: hindari istilah teknis.
 * {field} diganti nama kolomnya oleh CI4, dan sebagian nama kolom di basis data
 * masih berbahasa Inggris — karena itu kalimatnya disusun agar tetap terbaca
 * wajar apa pun nama yang disisipkan.
 */

return [
    // Kesalahan penyiapan aturan — hanya muncul ke pemrogram, dibiarkan apa adanya.
    'noRuleSets'      => 'No rulesets specified in Validation configuration.',
    'ruleNotFound'    => '{0} is not a valid rule.',
    'groupNotFound'   => '{0} is not a validation rules group.',
    'groupNotArray'   => '{0} rule group must be an array.',
    'invalidTemplate' => '{0} is not a valid Validation template.',

    // Wajib diisi
    'required'         => 'Kolom {field} wajib diisi.',
    'required_with'    => 'Kolom {field} wajib diisi bila {param} diisi.',
    'required_without' => 'Kolom {field} wajib diisi bila {param} dikosongkan.',

    // Panjang isian
    'min_length'   => 'Kolom {field} harus diisi minimal {param} huruf.',
    'max_length'   => 'Kolom {field} paling panjang {param} huruf.',
    'exact_length' => 'Kolom {field} harus tepat {param} huruf.',

    // Angka
    'numeric'               => 'Kolom {field} harus berupa angka.',
    'integer'               => 'Kolom {field} harus berupa angka bulat, tanpa koma.',
    'decimal'               => 'Kolom {field} harus berupa angka.',
    'is_natural'            => 'Kolom {field} harus angka 0 atau lebih besar.',
    'is_natural_no_zero'    => 'Kolom {field} harus angka lebih besar dari 0.',
    'greater_than'          => 'Kolom {field} harus lebih besar dari {param}.',
    'greater_than_equal_to' => 'Kolom {field} minimal {param}.',
    'less_than'             => 'Kolom {field} harus lebih kecil dari {param}.',
    'less_than_equal_to'    => 'Kolom {field} maksimal {param}.',
    'hex'                   => 'Kolom {field} hanya boleh berisi angka heksadesimal.',

    // Pilihan
    'in_list'     => 'Kolom {field} harus dipilih dari daftar yang tersedia.',
    'not_in_list' => 'Pilihan pada kolom {field} tidak diperbolehkan.',
    'equals'      => 'Kolom {field} harus bernilai {param}.',
    'not_equals'  => 'Kolom {field} tidak boleh bernilai {param}.',
    'matches'     => 'Kolom {field} harus sama dengan kolom {param}.',
    'differs'     => 'Kolom {field} tidak boleh sama dengan kolom {param}.',
    'regex_match' => 'Format kolom {field} tidak sesuai.',

    // Data ganda
    'is_unique'     => '{field} ini sudah terdaftar. Silakan gunakan yang lain.',
    'is_not_unique' => '{field} ini belum terdaftar.',
    'field_exists'  => 'Kolom {field} tidak dikenali.',

    // Huruf
    'alpha'               => 'Kolom {field} hanya boleh berisi huruf.',
    'alpha_space'         => 'Kolom {field} hanya boleh berisi huruf dan spasi.',
    'alpha_dash'          => 'Kolom {field} hanya boleh berisi huruf, angka, garis bawah, dan tanda hubung.',
    'alpha_numeric'       => 'Kolom {field} hanya boleh berisi huruf dan angka.',
    'alpha_numeric_space' => 'Kolom {field} hanya boleh berisi huruf, angka, dan spasi.',
    'alpha_numeric_punct' => 'Kolom {field} hanya boleh berisi huruf, angka, spasi, dan tanda baca umum.',
    'string'              => 'Kolom {field} harus berupa teks.',

    // Bentuk khusus
    'valid_email'      => 'Alamat email pada kolom {field} tidak sah.',
    'valid_emails'     => 'Ada alamat email tidak sah pada kolom {field}.',
    'valid_date'       => 'Tanggal pada kolom {field} tidak sah.',
    'valid_url'        => 'Alamat tautan pada kolom {field} tidak sah.',
    'valid_url_strict' => 'Alamat tautan pada kolom {field} tidak sah.',
    'valid_ip'         => 'Alamat IP pada kolom {field} tidak sah.',
    'valid_json'       => 'Kolom {field} harus berisi JSON yang sah.',
    'valid_cc_num'     => 'Nomor kartu pada kolom {field} tidak sah.',
    'timezone'         => 'Kolom {field} harus berisi zona waktu yang sah.',

    // Unggahan berkas
    'uploaded' => 'Berkas {field} belum dipilih atau gagal diunggah.',
    'max_size' => 'Ukuran berkas {field} melebihi batas yang diizinkan.',
    'is_image' => 'Berkas {field} harus berupa gambar.',
    'mime_in'  => 'Jenis berkas {field} tidak diperbolehkan.',
    'ext_in'   => 'Jenis berkas {field} tidak diperbolehkan.',
    'max_dims' => 'Ukuran gambar {field} terlalu besar.',
    'min_dims' => 'Ukuran gambar {field} terlalu kecil.',
];
