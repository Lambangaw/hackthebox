# oopsie


IP : http://10.10.10.28/

scan nmap

```s
nmap -A -T4 -p- 10.10.10.28 -oN nmap
```

mencoba menggunakan dirb untuk mendapatkan listing direktori pada website tersebut.

Setelah berfikir keras ternyata nama perusahaan yang sama dengan soal Archetype sebelumnya. Menggunakan credential yang didapatkan pada soal sebelumnya dan ternyata bisa login

```
username : admin
password : MEGACORP_4dm1n!!
```

bruteforce pada page account dan didapatkan sesuatu pada page `http://10.10.10.28/cdn-cgi/login/admin.php?content=accounts&id=30` 

pada /uploads terdapat perintah bahwa hanya super admin yang dapat mengaksesnya. Dimana kita sudah mendapatkan id untuk role super admin. Setelah dicek dari cookie yang tersimpan dalam website kita mengganti cookienya menjadi `role : super admin` dan `user : 86575`. 

Ketika sudah masuk sebagai superadmin kita bisa mengupload file. Coba kita upload file reverse shell php dari pentestmonkey dan kita buka pada path `http://10.10.10.28/uploads/re.php` . oke dari sini kita sudah mendapatkan shell nya. 


Setelah mendapat shell seperti biasa kita cek pada home directory terdapat file user.txt dimana file tersebut merupakan flag user. Selanjutnya kita coba connect ke service dengan ssh dengan user robert. Saya mencoba mencari beberapa clue menuju user robert dan didapat pada /var/www/html/cdn-cgi/login/db.php

```php
<?php
$conn = mysqli_connect('localhost','robert','M3g4C0rpUs3r!','garage');
?>
```

Dari sini saya coba koneksi dengan ssh dengan username robert dan password M3g4C0rpUs3r!.

Dengan menggunakan pwncat dan linpeas ada group yang aneh yaitu `bugtracker` yang juga merupakan group dari robert. Disini saya coba menjari file dengan group bugtracker dan didapatkan file executable pada `/usr/bin/bugtracker` setelah file tersebut di download pada local ternyata file tersebut akan melakukan cat pada `/root/reports/$id` . $id merupakan nilai yang kita masukan. Disini saya coba mendapatkan root.txt pada /root/. Ketika menjalankan bugtracker saya memasukan `../root.txt` dan muncul flag rootnya.

