import requests

url = "http://10.10.10.239/login.php"

# print(x.text)
for i in range(10000):
    print(i)
    file = open("payload", "a")
    file.write(str(i) + "\n")
    file.close()