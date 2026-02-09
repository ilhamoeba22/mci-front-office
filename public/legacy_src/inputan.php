  
  <script>
        function terbilang(Field1, Field2) {
            var bilangan = document.getElementById(Field1).value;
            var kalimat = '';
            var angka = new Array('0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0');
            var kata = new Array('', 'Satu ', 'Dua ', 'Tiga ', 'Empat ', 'Lima ', 'Enam ', 'Tujuh ', 'Delapan ', 'Sembilan ');
            var tingkat = new Array('', 'Ribu ', 'Juta ', 'Milyar ', 'Triliun ');
            var panjang_bilangan = bilangan.length;
            var rupiah;
            if (panjang_bilangan == 0 || bilangan == 0) {
                rupiah = '';
            }
            else if (panjang_bilangan > 15) {
                kalimat = 'Diluar Batas';
                rupiah = '';
            }
            else {
                rupiah = 'Rupiah';
                for (i = 1; i <= panjang_bilangan; i++) {
                    angka[i] = bilangan.substr(-(i), 1);
                }
                var i = 1;
                var j = 0;
                while (i <= panjang_bilangan) {
                    subkalimat = '';
                    kata1 = '';
                    kata2 = '';
                    kata3 = '';
                    if (angka[i + 2] != '0') {
                        if (angka[i + 2] == '1') {
                            kata1 = 'Seratus ';
                        }
                        else {
                            kata1 = kata[angka[i + 2]] + 'Ratus ';
                        }
                    }
                    if (angka[i + 1] != '0') {
                        if (angka[i + 1] == '1') {
                            if (angka[i] == '0') {
                                kata2 = 'Sepuluh ';
                            }
                            else if (angka[i] == '1') {
                                kata2 = 'Sebelas ';
                            }
                            else {
                                kata2 = kata[angka[i]] + 'Belas ';
                            }
                        }
                        else {
                            kata2 = kata[angka[i + 1]] + 'Puluh ';
                        }
                    }
                    if (angka[i] != '0') {
                        if (angka[i + 1] != '1') {
                            kata3 = kata[angka[i]];
                        }
                    }
                    if ((angka[i] != '0') || (angka[i + 1] != '0') || (angka[i + 2] != '0')) {
                        subkalimat = kata1 + '' + kata2 + '' + kata3 + '' + tingkat[j] + '';
                    }
                    kalimat = subkalimat + kalimat;
                    i = i + 3;
                    j = j + 1;
                }
                if ((angka[5] == '0') && (angka[6] == '0')) {
                    kalimat = kalimat.replace('Satu Ribu', 'Seribu ');
                }
            }
            document.getElementById(Field2).value = kalimat + rupiah;
        }
        $(".email").bind("keyup paste", function(){setTimeout(jQuery.proxy(function() {this.val(this.val().replace(/[^0-9A-Za-z .@_-]/g, '')); }, $(this)), 0); }); 
        $(".numeric").bind("keyup paste", function(){setTimeout(jQuery.proxy(function() {this.val(this.val().replace(/[^0-9./]/g, '')); }, $(this)), 0); }); 
        $(".kalimat").bind("keyup paste", function(){setTimeout(jQuery.proxy(function() {this.val(this.val().replace(/[^0-9A-Za-z ,./]/g, '')); }, $(this)), 0); }); 
        $(".huruf").bind("keyup paste", function(){setTimeout(jQuery.proxy(function() {this.val(this.val().replace(/[^A-Za-z ]/g, '')); }, $(this)), 0); });
        $(".angkatok").bind("keyup paste", function(){setTimeout(jQuery.proxy(function() {this.val(this.val().replace(/[^0-9]/g, '')); }, $(this)), 0); }); 
    </script>
  
    <script>
        $(function(){$('.select2').select2({width:'100%',allowClear: true}); });
    </script>

    <script>
        function cek_pinbuk(cek_norek, cek_norek2) {
            var cek_norek = document.getElementById(cek_norek).value;
            var cek_norek2 = document.getElementById(cek_norek2).value;
            if (cek_norek ==  cek_norek2) {
                var yakin = alert("Harap Mengganti NoRek Tujuan, Terdapat Kesamaan Dgn NoRek Pengirim...");
                $("#cek_norek2").val('');
            }
        }

        function cek_saldo(saldo1, nominal1, minimum) {
            var saldo1 = document.getElementById(saldo1).value;
            var nominal1 = document.getElementById(nominal1).value;
            var minimum = document.getElementById(minimum).value;
            if ( (saldo1 - nominal1 - minimum) < 0) {
                var bilangan = minimum;
                var	number_string = bilangan.toString(),
                    sisa 	= number_string.length % 3,
                    rupiah 	= number_string.substr(0, sisa),
                    ribuan 	= number_string.substr(sisa).match(/\d{3}/g);
                        
                if (ribuan) {
                    separator = sisa ? '.' : '';
                    rupiah += separator + ribuan.join('.');
                }
                var yakin = alert("Saldo Minimal Kurang Dari Rp. "+rupiah+" ...");
                $("#nominal1").val('');
            }
        }

        function cek_saldo_trf(saldo1, nominal1, minimum) {
            var saldo1 = document.getElementById(saldo1).value;
            var nominal1 = document.getElementById(nominal1).value;
            var minimum = document.getElementById(minimum).value;
            if ( (saldo1 - nominal1 - minimum - 6500) < 0) {
                var bilangan = minimum;
                var	number_string = bilangan.toString(),
                    sisa 	= number_string.length % 3,
                    rupiah 	= number_string.substr(0, sisa),
                    ribuan 	= number_string.substr(sisa).match(/\d{3}/g);
                        
                if (ribuan) {
                    separator = sisa ? '.' : '';
                    rupiah += separator + ribuan.join('.');
                }
                var yakin = alert("Saldo Minimal Kurang Dari Rp. "+rupiah+" ...");
                $("#nominal1").val('');
            }
        }
    </script>