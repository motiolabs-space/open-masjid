<?php
/**
 * Penghapusan lewat POST + token CSRF, dipakai bersama seluruh halaman dashboard.
 *
 * Sebelumnya semua tombol hapus adalah tautan GET. Tautan GET yang merusak data
 * bisa dipicu tanpa sepengetahuan pengurus — cukup sebuah <img src="…/delete/7">
 * di halaman atau email mana pun selagi ia masih login — dan token CSRF tak
 * pernah ikut terkirim. Satu formulir tersembunyi di sini melayani semua tombol
 * hapus di halaman, jadi tiap view cukup memanggil hapusLewatPost().
 */
?>
<form id="formHapusPost" method="post" hidden>
    <?= csrf_field() ?>
</form>
<script>
    /**
     * @param {string} url    Alamat rute hapus (POST).
     * @param {string} pesan  Bila diisi, ditanyakan lebih dulu lewat confirm().
     * @returns {boolean}     Selalu false, agar aman dipakai di onclick tautan.
     */
    function hapusLewatPost(url, pesan) {
        if (pesan && !window.confirm(pesan)) {
            return false;
        }
        const form = document.getElementById('formHapusPost');
        form.action = url;
        form.submit();

        return false;
    }
</script>
