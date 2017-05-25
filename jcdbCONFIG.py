import pymysql
import random
import os

def randPass(length = 32):
    newPass = ''
    for i in range(length):
        newPass = ''.join((newPass,random.choice("ABCDEFGHIJKLMNOP"+\
                                                 "QRSTUVWQXYZabcde"+\
                                                 "fghijklmnopqrstu"+\
                                                 "vwxyz123456789")))
    return newPass

def getPath():
    return os.path.dirname(os.path.abspath(__file__)).replace('\\','/')

modifyPass = randPass()
viewPass = randPass()

tempPass = 'temp'

try:
    dbConn = pymysql.connect(host=input("mySQL server host: "),
                             user=input("admin username: "),
                             password=input("admin password: "))

    with dbConn.cursor() as cursor:
        print("Adding database...")
        cursor.execute("CREATE DATABASE jcdb")
        cursor.execute("USE jcdb")

        print("Adding tables...")
        cursor.execute("CREATE TABLE casehistory(prefix INTEGER, caseNumber INTEGER, formScan TEXT, plaintiff TEXT, defendant TEXT, witness TEXT, dateOfIncident TEXT, timeOfIncident TEXT, location TEXT, charge TEXT, whatHappened TEXT, rowID INTEGER AUTO_INCREMENT PRIMARY KEY)")
        cursor.execute("CREATE TABLE casestate(prefix INTEGER, caseNumber INTEGER, plaintiff TEXT, defendant TEXT, witness TEXT, charge TEXT, status TEXT, hearingDate TEXT, verdict TEXT, sentence TEXT, sentenceStatus TEXT, rowID INTEGER AUTO_INCREMENT PRIMARY KEY)")
        cursor.execute("CREATE TABLE users(username VARCHAR(255) UNIQUE, password TEXT, superuser TINYINT(1), rowID INTEGER AUTO_INCREMENT PRIMARY KEY)")

        print("Adding temporary user...")
        cursor.execute("INSERT INTO users(username,password,superuser) VALUES ('temp','"+tempPass+"',1)")

        print("Adding internal database users for table manipulation...")
        cursor.execute("CREATE USER JCDB_viewer@'localhost' IDENTIFIED BY '"+viewPass+"'")
        cursor.execute("GRANT SELECT ON jcdb.casestate TO JCDB_viewer@'localhost'")
        cursor.execute("GRANT SELECT ON jcdb.casehistory TO JCDB_viewer@'localhost'")

        cursor.execute("CREATE USER JCDB_modifier@'localhost' IDENTIFIED BY '"+modifyPass+"'")
        cursor.execute("GRANT SELECT, INSERT, UPDATE, DELETE ON jcdb.casestate TO JCDB_modifier@'localhost'")
        cursor.execute("GRANT SELECT, INSERT, UPDATE, DELETE ON jcdb.casehistory TO JCDB_modifier@'localhost'")
        cursor.execute("GRANT SELECT, INSERT, UPDATE, DELETE ON jcdb.users TO JCDB_modifier@'localhost'")

finally:
    dbConn.close()

print("Writing config file...")
os.makedirs("JCDB_configs",exist_ok=True)
with open("JCDB_configs/JCDBconfig.ini","w") as f:
	f.write("SQL_HOST = 'localhost'\n")
	f.write("SQL_DB = 'jcdb'\n")
	f.write("SQL_VIEW_USER = 'JCDB_viewer'\n")
	f.write("SQL_VIEW_PASS = '"+viewPass+"'\n")
	f.write("SQL_MODIFY_USER = 'JCDB_modifier'\n")
	f.write("SQL_MODIFY_PASS = '"+modifyPass+"'\n")
	f.write("TEMP_SETUP_PASS = '"+tempPass+"'")
	f.close()

print("Setting config path environment variable...")
with open(input("location of Apache server httpd.conf: ")+"/httpd.conf",'a') as f:
	f.write("\nSetEnv JCDB_CONFIG_PATH \""+getPath()+"/JCDB_configs/\"")
	f.close()

print("Setup complete. Please use username 'temp' with password: '"+tempPass+"' to add a new superuser right away.")
input("Press Enter to quit...")
