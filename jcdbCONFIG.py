import pymysql
import random
import os

def randPass(length = 32):
    return ''.join(random.choices("ABCDEFGHIJKLMNOP"+\
                                 "QRSTUVWQXYZabcde"+\
                                 "fghijklmnopqrstu"+\
                                 "vwxyz123456789",k=length))
def getPath():
    return os.path.dirname(__file__).replace('\\','/')

modifyPass = randPass()
tempPass = randPass(4)

try:
    dbConn = pymysql.connect(host=input("mySQL server host: "),
                             user=input("admin username: "),
                             password=input("admin password: "))
    
    with dbConn.cursor() as cursor:
        print("Adding database...")
        cursor.execute("CREATE DATABASE jcdb")
        cursor.execute("USE jcdb")

        print("Adding tables...")
        cursor.execute("CREATE TABLE casehistory(prefix INTEGER, caseNumber INTEGER AUTO_INCREMENT PRIMARY KEY, formScan TEXT, plaintiff TEXT, defendant TEXT, witness TEXT, dateOfIncident TEXT, timeOfIncident TEXT, location TEXT, charge TEXT, whatHappened TEXT, hearingDate TEXT, hearingNotes TEXT)")
        cursor.execute("CREATE TABLE casestate(prefix INTEGER, caseNumber INTEGER, plaintiff TEXT, defendant TEXT, witness TEXT, charge TEXT, status TEXT, hearingDate TEXT, verdict TEXT, sentence TEXT, sentenceStatus TEXT, rowID INTEGER AUTO_INCREMENT PRIMARY KEY)")
        cursor.execute("CREATE TABLE users(username VARCHAR(255) UNIQUE, password TEXT, superuser TINYINT(1), rowID INTEGER AUTO_INCREMENT PRIMARY KEY)")

        print("Adding temporary user...")
        cursor.execute("INSERT INTO users(username,password,superuser) VALUES ('temp','"+tempPass+"',1)")
        
        print("Adding internal database users for table manipulation...")
        cursor.execute("CREATE USER viewer@'localhost'")
        cursor.execute("GRANT SELECT ON jcdb.casestate TO viewer@'localhost'")
        cursor.execute("GRANT SELECT ON jcdb.casehistory TO viewer@'localhost'")
        
        cursor.execute("CREATE USER modifier@'localhost' IDENTIFIED BY '"+modifyPass+"'")
        cursor.execute("GRANT SELECT, INSERT, UPDATE, DELETE ON jcdb.casestate TO modifier@'localhost'")
        cursor.execute("GRANT SELECT, INSERT, UPDATE, DELETE ON jcdb.casehistory TO modifier@'localhost'")
        cursor.execute("GRANT SELECT, INSERT, UPDATE, DELETE ON jcdb.users TO modifier@'localhost'")
        
finally:
    dbConn.close()

print("Writing config file...")
f = open("./config.ini","w")
f.write("SQL_HOST = 'localhost'\n")
f.write("SQL_DB = 'jcdb'\n")
f.write("SQL_VIEW_USER = 'viewer'\n")
f.write("SQL_MODIFY_USER = 'modifier'\n")
f.write("SQL_MODIFY_PASS = '"+modifyPass+"'\n")
f.write("TEMP_SETUP_PASS = '"+tempPass+"'")
f.close()

print("Setting Apache environment variable...")
f = open(input("location of Apache server httpd.conf: ")+"/httpd.conf",'a')
f.write("\nSetEnv CONFIG_PATH \""+getPath()+"/config.ini\"")
f.close()

print("Setup complete. Please use username 'temp' with password: '"+tempPass+"' to add a new superuser right away.")
input("Press Enter to quit...")
