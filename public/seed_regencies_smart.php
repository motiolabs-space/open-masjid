<?php
// Script to seed Regencies from MyQuran ID with Smart Mapping

// 1. Setup DB Connection
$envFile = __DIR__ . '/../app-core/.env';
$lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$env = [];
foreach ($lines as $line) {
    if (strpos($line, '#') === 0) continue;
    $parts = explode('=', $line, 2);
    if (count($parts) === 2) {
        $env[trim($parts[0])] = trim($parts[1]);
    }
}
$host = $env['database.default.hostname'] ?? 'localhost';
$user = $env['database.default.username'] ?? 'root';
$pass = $env['database.default.password'] ?? '';
$dbname = $env['database.default.database'] ?? 'masjid_db';

try {
    $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Connected to DB.\n";

    // 2. Alter Table to support String IDs
    echo "Altering regencies table ID to VARCHAR(50)...\n";
    try {
        // Drop auto_increment if exists
        $pdo->exec("ALTER TABLE regencies MODIFY id VARCHAR(50) NOT NULL");
        // Check if primary key needs updating? Usually okay if we just modify type.
    } catch (Exception $e) {
        echo "Alter warning (might already be varchar): " . $e->getMessage() . "\n";
    }

    // 3. Truncate Table
    echo "Truncating regencies table...\n";
    $pdo->exec("TRUNCATE TABLE regencies");

    // 4. Load Provinces Map [Name => ID]
    $stmt = $pdo->query("SELECT id, name FROM provinces");
    $provinces = $stmt->fetchAll(PDO::FETCH_KEY_PAIR); // [1 => Aceh, 2 => Sumut]
    // Flip to [Aceh => 1] for lookup, but need to normalize
    $provMap = [];
    foreach ($provinces as $id => $name) {
        $key = strtoupper($name);
        $provMap[$key] = $id;
        // Also map standard variations if needed
        if ($key == 'DI YOGYAKARTA') $provMap['DAERAH ISTIMEWA YOGYAKARTA'] = $id;
        if ($key == 'DKI JAKARTA') $provMap['JAKARTA'] = $id;
    }

    // 5. Fetch API
    echo "Fetching API Data...\n";
    $json = file_get_contents('https://api.myquran.com/v3/sholat/kota/semua');
    $data = json_decode($json, true);
    $cities = $data['data'] ?? [];

    echo "Found " . count($cities) . " cities.\n";

    // 6. Manual Keyword Map (City Keyword -> Province Name in DB)
    // Keys must be UPPERCASE. Values must match DB Province Names (UPPERCASE) OR the alias mapped above.
    $keywordMap = [
        'BANDA ACEH' => 'ACEH', 'LANGSA' => 'ACEH', 'LHOKSEUMAWE' => 'ACEH', 'SABANG' => 'ACEH', 'SUBULUSSALAM' => 'ACEH', 'SIMEULUE' => 'ACEH', 
        'BENER MERIAH' => 'ACEH', 'BIREUEN' => 'ACEH', 'GAYO LUES' => 'ACEH', 'NAGAN RAYA' => 'ACEH', 'PIDIE' => 'ACEH', 'ACEH' => 'ACEH',
        'MEDAN' => 'SUMATERA UTARA', 'BINJAI' => 'SUMATERA UTARA', 'TEBING TINGGI' => 'SUMATERA UTARA', 'TANJUNGBALAI' => 'SUMATERA UTARA', 'PEMATANGSIANTAR' => 'SUMATERA UTARA', 'SIBOLGA' => 'SUMATERA UTARA', 'PADANGSIDEMPUAN' => 'SUMATERA UTARA', 'GUNUNGSITOLI' => 'SUMATERA UTARA', 'NIAS' => 'SUMATERA UTARA', 'DELI SERDANG' => 'SUMATERA UTARA', 'KARO' => 'SUMATERA UTARA', 'LANGKAT' => 'SUMATERA UTARA', 'ASAHAN' => 'SUMATERA UTARA', 'DAIRI' => 'SUMATERA UTARA', 'LABUHANBATU' => 'SUMATERA UTARA', 'MANDAILING NATAL' => 'SUMATERA UTARA', 'SAMOSIR' => 'SUMATERA UTARA', 'SERDANG BEDAGAI' => 'SUMATERA UTARA', 'TAPANULI' => 'SUMATERA UTARA', 'TOBA' => 'SUMATERA UTARA', 'PAKPAK' => 'SUMATERA UTARA', 'BATUBARA' => 'SUMATERA UTARA', 'BATU BARA' => 'SUMATERA UTARA', 'HUMBANG HASUNDUTAN' => 'SUMATERA UTARA', 'PADANG LAWAS' => 'SUMATERA UTARA',
        'PADANG' => 'SUMATERA BARAT', 'BUKITTINGGI' => 'SUMATERA BARAT', 'PARIAMAN' => 'SUMATERA BARAT', 'PAYAKUMBUH' => 'SUMATERA BARAT', 'SAWAHLUNTO' => 'SUMATERA BARAT', 'SOLOK' => 'SUMATERA BARAT', 'AGAM' => 'SUMATERA BARAT', 'DHARMASRAYA' => 'SUMATERA BARAT', 'MENTAWAI' => 'SUMATERA BARAT', 'TANAH DATAR' => 'SUMATERA BARAT', 'PASAMAN' => 'SUMATERA BARAT', 'SIJUNJUNG' => 'SUMATERA BARAT', 'LIMA PULUH KOTA' => 'SUMATERA BARAT', 'PESISIR SELATAN' => 'SUMATERA BARAT',
        'PEKANBARU' => 'RIAU', 'DUMAI' => 'RIAU', 'BENGKALIS' => 'RIAU', 'INDRAGIRI' => 'RIAU', 'KAMPAR' => 'RIAU', 'MERANTI' => 'RIAU', 'PELALAWAN' => 'RIAU', 'ROKAN' => 'RIAU', 'SIAK' => 'RIAU', 'KUANTAN' => 'RIAU',
        'JAMBI' => 'JAMBI', 'SUNGAI PENUH' => 'JAMBI', 'BATANGHARI' => 'JAMBI', 'BUNGO' => 'JAMBI', 'KERINCI' => 'JAMBI', 'MERANGIN' => 'JAMBI', 'MUARO JAMBI' => 'JAMBI', 'SAROLANGUN' => 'JAMBI', 'TANJUNG JABUNG' => 'JAMBI', 'TEBO' => 'JAMBI',
        'PALEMBANG' => 'SUMATERA SELATAN', 'LUBUKLINGGAU' => 'SUMATERA SELATAN', 'PAGAR ALAM' => 'SUMATERA SELATAN', 'PRABUMULIH' => 'SUMATERA SELATAN', 'BANYUASIN' => 'SUMATERA SELATAN', 'EMPAT LAWANG' => 'SUMATERA SELATAN', 'LAHAT' => 'SUMATERA SELATAN', 'MUARA ENIM' => 'SUMATERA SELATAN', 'MUSI' => 'SUMATERA SELATAN', 'OGAN' => 'SUMATERA SELATAN', 'PENUKAL' => 'SUMATERA SELATAN',
        'BENGKULU' => 'BENGKULU', 'KAUR' => 'BENGKULU', 'KEPAHIANG' => 'BENGKULU', 'LEBONG' => 'BENGKULU', 'MUKOMUKO' => 'BENGKULU', 'REJANG LEBONG' => 'BENGKULU', 'SELUMA' => 'BENGKULU',
        'BANDAR LAMPUNG' => 'LAMPUNG', 'METRO' => 'LAMPUNG', 'LAMBAR' => 'LAMPUNG', 'LAMSEL' => 'LAMPUNG', 'LAMTENG' => 'LAMPUNG', 'LAMTIM' => 'LAMPUNG', 'LAMUT' => 'LAMPUNG', 'MESUJI' => 'LAMPUNG', 'PESAWARAN' => 'LAMPUNG', 'PESISIR BARAT' => 'LAMPUNG', 'PRINGSEWU' => 'LAMPUNG', 'TANGGAMUS' => 'LAMPUNG', 'TULANG BAWANG' => 'LAMPUNG', 'WAY KANAN' => 'LAMPUNG',
        'PANGKAL PINANG' => 'KEPULAUAN BANGKA BELITUNG', 'BANGKA' => 'KEPULAUAN BANGKA BELITUNG', 'BELITUNG' => 'KEPULAUAN BANGKA BELITUNG',
        'TANJUNGPINANG' => 'KEPULAUAN RIAU', 'BATAM' => 'KEPULAUAN RIAU', 'BINTAN' => 'KEPULAUAN RIAU', 'KARIMUN' => 'KEPULAUAN RIAU', 'ANAMBAS' => 'KEPULAUAN RIAU', 'LINGGA' => 'KEPULAUAN RIAU', 'NATUNA' => 'KEPULAUAN RIAU',
        'JAKARTA' => 'DKI JAKARTA', 'KEPULAUAN SERIBU' => 'DKI JAKARTA',
        'BANDUNG' => 'JAWA BARAT', 'BEKASI' => 'JAWA BARAT', 'BOGOR' => 'JAWA BARAT', 'CIMAHI' => 'JAWA BARAT', 'CIREBON' => 'JAWA BARAT', 'DEPOK' => 'JAWA BARAT', 'SUKABUMI' => 'JAWA BARAT', 'TASIKMALAYA' => 'JAWA BARAT', 'BANJAR' => 'JAWA BARAT', 'CIAMIS' => 'JAWA BARAT', 'CIANJUR' => 'JAWA BARAT', 'GARUT' => 'JAWA BARAT', 'INDRAMAYU' => 'JAWA BARAT', 'KARAWANG' => 'JAWA BARAT', 'KUNINGAN' => 'JAWA BARAT', 'MAJALENGKA' => 'JAWA BARAT', 'PANGANDARAN' => 'JAWA BARAT', 'PURWAKARTA' => 'JAWA BARAT', 'SUBANG' => 'JAWA BARAT', 'SUMEDANG' => 'JAWA BARAT',
        'SEMARANG' => 'JAWA TENGAH', 'MAGELANG' => 'JAWA TENGAH', 'PEKALONGAN' => 'JAWA TENGAH', 'SALATIGA' => 'JAWA TENGAH', 'SURAKARTA' => 'JAWA TENGAH', 'TEGAL' => 'JAWA TENGAH', 'BANJARNEGARA' => 'JAWA TENGAH', 'BANYUMAS' => 'JAWA TENGAH', 'BATANG' => 'JAWA TENGAH', 'BLORA' => 'JAWA TENGAH', 'BOYOLALI' => 'JAWA TENGAH', 'BREBES' => 'JAWA TENGAH', 'CILACAP' => 'JAWA TENGAH', 'DEMAK' => 'JAWA TENGAH', 'GROBOGAN' => 'JAWA TENGAH', 'JEPARA' => 'JAWA TENGAH', 'KARANGANYAR' => 'JAWA TENGAH', 'KEBUMEN' => 'JAWA TENGAH', 'KENDAL' => 'JAWA TENGAH', 'KLATEN' => 'JAWA TENGAH', 'KUDUS' => 'JAWA TENGAH', 'PATI' => 'JAWA TENGAH', 'PEMALANG' => 'JAWA TENGAH', 'PURBALINGGA' => 'JAWA TENGAH', 'PURWOREJO' => 'JAWA TENGAH', 'REMBANG' => 'JAWA TENGAH', 'SRAGEN' => 'JAWA TENGAH', 'SUKOHARJO' => 'JAWA TENGAH', 'TEMANGGUNG' => 'JAWA TENGAH', 'WONOGIRI' => 'JAWA TENGAH', 'WONOSOBO' => 'JAWA TENGAH',
        'YOGYAKARTA' => 'DI YOGYAKARTA', 'BANTUL' => 'DI YOGYAKARTA', 'GUNUNGKIDUL' => 'DI YOGYAKARTA', 'KULON PROGO' => 'DI YOGYAKARTA', 'SLEMAN' => 'DI YOGYAKARTA',
        'SURABAYA' => 'JAWA TIMUR', 'BATU' => 'JAWA TIMUR', 'BLITAR' => 'JAWA TIMUR', 'KEDIRI' => 'JAWA TIMUR', 'MADIUN' => 'JAWA TIMUR', 'MALANG' => 'JAWA TIMUR', 'MOJOKERTO' => 'JAWA TIMUR', 'PASURUAN' => 'JAWA TIMUR', 'PROBOLINGGO' => 'JAWA TIMUR', 'BANGKALAN' => 'JAWA TIMUR', 'BANYUWANGI' => 'JAWA TIMUR', 'BOJONEGORO' => 'JAWA TIMUR', 'BONDOWOSO' => 'JAWA TIMUR', 'GRESIK' => 'JAWA TIMUR', 'JEMBER' => 'JAWA TIMUR', 'JOMBANG' => 'JAWA TIMUR', 'LAMONGAN' => 'JAWA TIMUR', 'LUMAJANG' => 'JAWA TIMUR', 'MAGETAN' => 'JAWA TIMUR', 'NGANJUK' => 'JAWA TIMUR', 'NGAWI' => 'JAWA TIMUR', 'PACITAN' => 'JAWA TIMUR', 'PAMEKASAN' => 'JAWA TIMUR', 'PONOROGO' => 'JAWA TIMUR', 'SAMPANG' => 'JAWA TIMUR', 'SIDOARJO' => 'JAWA TIMUR', 'SITUBONDO' => 'JAWA TIMUR', 'SUMENEP' => 'JAWA TIMUR', 'TRENGGALEK' => 'JAWA TIMUR', 'TUBAN' => 'JAWA TIMUR', 'TULUNGAGUNG' => 'JAWA TIMUR',
        'SERANG' => 'BANTEN', 'CILEGON' => 'BANTEN', 'TANGERANG' => 'BANTEN', 'LEBAK' => 'BANTEN', 'PANDEGLANG' => 'BANTEN',
        'DENPASAR' => 'BALI', 'BADUNG' => 'BALI', 'BANGLI' => 'BALI', 'BULELENG' => 'BALI', 'GIANYAR' => 'BALI', 'JEMBRANA' => 'BALI', 'KARANGASEM' => 'BALI', 'KLUNGKUNG' => 'BALI', 'TABANAN' => 'BALI',
        'MATARAM' => 'NUSA TENGGARA BARAT', 'BIMA' => 'NUSA TENGGARA BARAT', 'DOMPU' => 'NUSA TENGGARA BARAT', 'LOMBOK' => 'NUSA TENGGARA BARAT', 'SUMBAWA' => 'NUSA TENGGARA BARAT',
        'KUPANG' => 'NUSA TENGGARA TIMUR', 'ALOR' => 'NUSA TENGGARA TIMUR', 'BELU' => 'NUSA TENGGARA TIMUR', 'ENDE' => 'NUSA TENGGARA TIMUR', 'FLORES' => 'NUSA TENGGARA TIMUR', 'LEM BATA' => 'NUSA TENGGARA TIMUR', 'MALAKA' => 'NUSA TENGGARA TIMUR', 'MANGGARAI' => 'NUSA TENGGARA TIMUR', 'NAGEKEO' => 'NUSA TENGGARA TIMUR', 'NGADA' => 'NUSA TENGGARA TIMUR', 'ROTE' => 'NUSA TENGGARA TIMUR', 'SABU' => 'NUSA TENGGARA TIMUR', 'SIKKA' => 'NUSA TENGGARA TIMUR', 'SUMBA' => 'NUSA TENGGARA TIMUR', 'TIMOR' => 'NUSA TENGGARA TIMUR',
        'PONTIANAK' => 'KALIMANTAN BARAT', 'SINGKAWANG' => 'KALIMANTAN BARAT', 'BENGKAYANG' => 'KALIMANTAN BARAT', 'KAPUAS HULU' => 'KALIMANTAN BARAT', 'KAYONG' => 'KALIMANTAN BARAT', 'KETAPANG' => 'KALIMANTAN BARAT', 'KUBU RAYA' => 'KALIMANTAN BARAT', 'LANDAK' => 'KALIMANTAN BARAT', 'MELAWI' => 'KALIMANTAN BARAT', 'MEMPAWAH' => 'KALIMANTAN BARAT', 'SAMBAS' => 'KALIMANTAN BARAT', 'SANGGAU' => 'KALIMANTAN BARAT', 'SEKADAU' => 'KALIMANTAN BARAT', 'SINTANG' => 'KALIMANTAN BARAT',
        'PALANGKARAYA' => 'KALIMANTAN TENGAH', 'BARITO' => 'KALIMANTAN TENGAH', 'GUNUNG MAS' => 'KALIMANTAN TENGAH', 'KAPUAS' => 'KALIMANTAN TENGAH', 'KATINGAN' => 'KALIMANTAN TENGAH', 'KOTAWARINGIN' => 'KALIMANTAN TENGAH', 'LAMANDAU' => 'KALIMANTAN TENGAH', 'MURUNG RAYA' => 'KALIMANTAN TENGAH', 'PULANG PISAU' => 'KALIMANTAN TENGAH', 'SERUYAN' => 'KALIMANTAN TENGAH', 'SUKAMARA' => 'KALIMANTAN TENGAH',
        'BANJARMASIN' => 'KALIMANTAN SELATAN', 'BANJARBARU' => 'KALIMANTAN SELATAN', 'BALANGAN' => 'KALIMANTAN SELATAN', 'BANJAR' => 'KALIMANTAN SELATAN', 'BARITO KUALA' => 'KALIMANTAN SELATAN', 'HULU SUNGAI' => 'KALIMANTAN SELATAN', 'KOTABARU' => 'KALIMANTAN SELATAN', 'TABALONG' => 'KALIMANTAN SELATAN', 'TANAH BUMBU' => 'KALIMANTAN SELATAN', 'TANAH LAUT' => 'KALIMANTAN SELATAN', 'TAPIN' => 'KALIMANTAN SELATAN',
        'SAMARINDA' => 'KALIMANTAN TIMUR', 'BALIKPAPAN' => 'KALIMANTAN TIMUR', 'BONTANG' => 'KALIMANTAN TIMUR', 'BERAU' => 'KALIMANTAN TIMUR', 'KUTAI' => 'KALIMANTAN TIMUR', 'MAHAKAM' => 'KALIMANTAN TIMUR', 'PASER' => 'KALIMANTAN TIMUR',
        'TARAKAN' => 'KALIMANTAN UTARA', 'BULUNGAN' => 'KALIMANTAN UTARA', 'MALINAU' => 'KALIMANTAN UTARA', 'NUNUKAN' => 'KALIMANTAN UTARA', 'TANA TIDUNG' => 'KALIMANTAN UTARA',
        'MANADO' => 'SULAWESI UTARA', 'BITUNG' => 'SULAWESI UTARA', 'TOMOHON' => 'SULAWESI UTARA', 'KOTAMOBAGU' => 'SULAWESI UTARA', 'BOLAANG' => 'SULAWESI UTARA', 'KEPULAUAN SANGIHE' => 'SULAWESI UTARA', 'KEPULAUAN SITARO' => 'SULAWESI UTARA', 'KEPULAUAN TALAUD' => 'SULAWESI UTARA', 'MINAHASA' => 'SULAWESI UTARA',
        'GORONTALO' => 'GORONTALO', 'BOALEMO' => 'GORONTALO', 'BONE BOLANGO' => 'GORONTALO', 'PAHUWATO' => 'GORONTALO',
        'PALU' => 'SULAWESI TENGAH', 'BANGGAI' => 'SULAWESI TENGAH', 'BUOL' => 'SULAWESI TENGAH', 'DONGGALA' => 'SULAWESI TENGAH', 'MOROWALI' => 'SULAWESI TENGAH', 'PARIGI' => 'SULAWESI TENGAH', 'POSO' => 'SULAWESI TENGAH', 'SIGI' => 'SULAWESI TENGAH', 'TOJO' => 'SULAWESI TENGAH', 'TOLITOLI' => 'SULAWESI TENGAH',
        'MAMUJU' => 'SULAWESI BARAT', 'MAJENE' => 'SULAWESI BARAT', 'MAMASA' => 'SULAWESI BARAT', 'PASANGKAYU' => 'SULAWESI BARAT', 'POLEWALI' => 'SULAWESI BARAT',
        'MAKASSAR' => 'SULAWESI SELATAN', 'PAREPARE' => 'SULAWESI SELATAN', 'PALOPO' => 'SULAWESI SELATAN', 'BANTAENG' => 'SULAWESI SELATAN', 'BARRU' => 'SULAWESI SELATAN', 'BONE' => 'SULAWESI SELATAN', 'BULUKUMBA' => 'SULAWESI SELATAN', 'ENREKANG' => 'SULAWESI SELATAN', 'GOWA' => 'SULAWESI SELATAN', 'JENEPONTO' => 'SULAWESI SELATAN', 'KEPULAUAN SELAYAR' => 'SULAWESI SELATAN', 'LUWU' => 'SULAWESI SELATAN', 'MAROS' => 'SULAWESI SELATAN', 'PANGKAJENE' => 'SULAWESI SELATAN', 'PINRANG' => 'SULAWESI SELATAN', 'SIDENRENG' => 'SULAWESI SELATAN', 'SINJAI' => 'SULAWESI SELATAN', 'SOPPENG' => 'SULAWESI SELATAN', 'TAKALAR' => 'SULAWESI SELATAN', 'TANA TORAJA' => 'SULAWESI SELATAN', 'TORAJA' => 'SULAWESI SELATAN', 'WAJO' => 'SULAWESI SELATAN',
        'KENDARI' => 'SULAWESI TENGGARA', 'BAUBAU' => 'SULAWESI TENGGARA', 'BOMBANA' => 'SULAWESI TENGGARA', 'BUTON' => 'SULAWESI TENGGARA', 'KOLAKA' => 'SULAWESI TENGGARA', 'KONAWE' => 'SULAWESI TENGGARA', 'MUNA' => 'SULAWESI TENGGARA', 'WAKATOBI' => 'SULAWESI TENGGARA',
        'AMBON' => 'MALUKU', 'TUAL' => 'MALUKU', 'BURU' => 'MALUKU', 'KEPULAUAN ARU' => 'MALUKU', 'MALUKU BARAT' => 'MALUKU', 'MALUKU TENGAH' => 'MALUKU', 'MALUKU TENGGARA' => 'MALUKU', 'SERAM' => 'MALUKU',
        'TERNATE' => 'MALUKU UTARA', 'TIDORE' => 'MALUKU UTARA', 'HALMAHERA' => 'MALUKU UTARA', 'KEPULAUAN SULA' => 'MALUKU UTARA', 'PULAU MOROTAI' => 'MALUKU UTARA', 'PULAU TALIABU' => 'MALUKU UTARA',
        'JAYAPURA' => 'PAPUA', 'KEEROM' => 'PAPUA', 'MAMBERAMO RAYA' => 'PAPUA', 'SARMI' => 'PAPUA', 'SUPIORI' => 'PAPUA', 'WAROPEN' => 'PAPUA', 'YAPEN' => 'PAPUA', 'BIAK' => 'PAPUA',
        'SORONG' => 'PAPUA BARAT DAYA', 'RAJA AMPAT' => 'PAPUA BARAT DAYA', 'TAMBRAUW' => 'PAPUA BARAT DAYA', 'MAYBRAT' => 'PAPUA BARAT DAYA',
        'MANOKWARI' => 'PAPUA BARAT', 'FAKFAK' => 'PAPUA BARAT', 'KAIMANA' => 'PAPUA BARAT', 'PEGUNUNGAN ARFAK' => 'PAPUA BARAT', 'TELUK BINTUNI' => 'PAPUA BARAT', 'TELUK WONDAMA' => 'PAPUA BARAT',
        'NABIRE' => 'PAPUA TENGAH', 'DEIYAI' => 'PAPUA TENGAH', 'DOGIYAI' => 'PAPUA TENGAH', 'INTAN JAYA' => 'PAPUA TENGAH', 'MIMIKA' => 'PAPUA TENGAH', 'PANIAI' => 'PAPUA TENGAH', 'PUNCAK' => 'PAPUA TENGAH',
        'WAMENA' => 'PAPUA PEGUNUNGAN', 'JAYAWIJAYA' => 'PAPUA PEGUNUNGAN', 'LANNY JAYA' => 'PAPUA PEGUNUNGAN', 'MAMBERAMO TENGAH' => 'PAPUA PEGUNUNGAN', 'NDUGA' => 'PAPUA PEGUNUNGAN', 'PEGUNUNGAN BINTANG' => 'PAPUA PEGUNUNGAN', 'TOLIKARA' => 'PAPUA PEGUNUNGAN', 'YAHUKIMO' => 'PAPUA PEGUNUNGAN', 'YALIMO' => 'PAPUA PEGUNUNGAN',
        'MERAUKE' => 'PAPUA SELATAN', 'ASMAT' => 'PAPUA SELATAN', 'BOVEN DIGOEL' => 'PAPUA SELATAN', 'MAPPI' => 'PAPUA SELATAN',
    ];

    $insertCount = 0;
    $unmapped = [];

    $stmtInsert = $pdo->prepare("INSERT INTO regencies (id, province_id, name) VALUES (:id, :pid, :name)");

    foreach ($cities as $city) {
        $id = $city['id'];
        $name = strtoupper(trim($city['lokasi']));

        // Matching Logic
        $provId = null;
        
        // 1. Check if name contains any Province Name
        foreach ($provMap as $pName => $pId) {
            if (strpos($name, $pName) !== false) {
                // Special case: "Sulawesi" matches "Sulawesi Utara" etc. but we want specific match.
                // Usually longer match is better?
                // Actually my provinces list has full names "SULAWESI UTARA".
                $provId = $pId;
                break; 
            }
        }

        // 2. Keyword Map Check
        if (!$provId) {
            foreach ($keywordMap as $keyword => $pName) {
                if (strpos($name, $keyword) !== false) {
                    if (isset($provMap[$pName])) {
                        $provId = $provMap[$pName];
                        break;
                    }
                }
            }
        }

        if ($provId) {
            try {
                $stmtInsert->execute([':id' => $id, ':pid' => $provId, ':name' => $city['lokasi']]);
                $insertCount++;
            } catch (Exception $e) {
                echo "Error inserting $name: " . $e->getMessage() . "\n";
            }
        } else {
            $unmapped[] = $name;
        }
    }

    echo "Inserted: $insertCount cities.\n";
    echo "Unmapped: " . count($unmapped) . "\n";
    if (count($unmapped) > 0) {
        echo "Example unmapped: " . implode(", ", array_slice($unmapped, 0, 10)) . "\n";
    }

} catch (Exception $e) {
    echo "Global Error: " . $e->getMessage() . "\n";
}
