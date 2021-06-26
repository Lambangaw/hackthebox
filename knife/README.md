# KNIFE 

10.10.10.242

Dalam mesin ini terdapat port http dan ssh yang terbuka. Setelah pencarian ternyata vuln terdapat dari versi php yang digunakan yaitu versi php 8.1.0-dev dimana baru saja terdapat celah rce untuk versi tersebut. Eksploitasi referensi dari https://packetstormsecurity.com/files/162749/PHP-8.1.0-dev-Backdoor-Remote-Command-Injection.html . Kita jalankan code tersebut dan mendapatkan shell.

dapat user flag : 72d29131e7c39b5e5c14a29ea55c0861

Setelah itu saya menggunakan id_rsa untuk bisa terhubung secara remote ke server, copy file ~/.ssh/id_rsa ke lokal, jika menjalankan command ssh -i id_rsa james@10.10.10.242 menyatakan key invalid coba jalanakan program ini puttygen id_rsa -O private-openssh -o id_rsa.conv. lalu jalankan ssh lagi dengan command ssh -i id_rsa.conv james@10.10.10.242. boom dapet ssh. Disini saya menggunakan pwncat untuk terhubung instead of ssh.

## privesc

kita jalankan perintah sudo -l dan mendapati user james bisa privesc menggunakan command knife.  sedikit membaca dari dokumentasi knife https://docs.chef.io/workstation/knife/ . Lalu buat file conf.rb dengan isi

```rb
exec "chmod +s /bin/bash"
```

Dari sini bash sudah mempunyai suid dimana kita tinggal menajalankan command bash -p dan sudah menjadi root group. tinggal cat /root/root.txt didapatkan flag : f8ab3d940404d7312f4b082da588954b