# pathfinder

`nmap -A -T4 -p- 10.10.10.30 -oN nmap`

Dari nmap tidak terdapat port http/https yang terbuka namun ada beberapa seperti active directory dan LDAP. Dari nmap juga terlihat bahwa ini adalah mesin windows

seperti chall sebelumnya. login pada domain chall ini juga terdapat dari credential chall sebelumnya 

```
sandra
Paswword1234!
```

untuk melakukan enumerasi pada active dir kita menggunakan bloodhound. Dengan perintah `bloodhound-python -u sandra -p Password1234! -d MEGACORP.LOCAL -c all -ns`

hasil dari bloodhound

```log
INFO: Found AD domain: megacorp.local
INFO: Connecting to LDAP server: Pathfinder.MEGACORP.LOCAL
INFO: Found 1 domains
INFO: Found 1 domains in the forest
INFO: Found 1 computers
INFO: Connecting to LDAP server: Pathfinder.MEGACORP.LOCAL
INFO: Found 5 users
INFO: Connecting to GC LDAP server: Pathfinder.MEGACORP.LOCAL
INFO: Found 51 groups
INFO: Found 0 trusts
INFO: Starting computer enumeration with 10 workers
INFO: Querying computer: Pathfinder.MEGACORP.LOCAL
INFO: Done in 00M 48S
```



