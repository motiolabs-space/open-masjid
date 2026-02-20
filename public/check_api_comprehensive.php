// 7. Try Monthly Endpoint
$jakartaID = '58a2fc6ed39fd083f55d4182bf88826d';
testUrl("https://api.myquran.com/v3/sholat/jadwal/$jakartaID/2025/02");

// 8. Try Today Endpoint (No date)
testUrl("https://api.myquran.com/v3/sholat/jadwal/$jakartaID");

// 9. Try Legacy ID (1301) Monthly
testUrl("https://api.myquran.com/v3/sholat/jadwal/1301/2025/02");

