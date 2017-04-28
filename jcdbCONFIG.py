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
viewPass = randPass()

tempPass = randPass(4)

suffix = randPass(6)

try:
    dbConn = pymysql.connect(host=input("mySQL server host: "),
                             user=input("admin username: "),
                             password=input("admin password: "))
    
    with dbConn.cursor() as cursor:
        print("Adding database...")
        cursor.execute("CREATE DATABASE JCDB_"+suffix)
        cursor.execute("USE JCDB_"+suffix)

        print("Adding tables...")
        cursor.execute("CREATE TABLE casehistory(prefix INTEGER, caseNumber INTEGER AUTO_INCREMENT PRIMARY KEY, formScan TEXT, plaintiff TEXT, defendant TEXT, witness TEXT, dateOfIncident TEXT, timeOfIncident TEXT, location TEXT, charge TEXT, whatHappened TEXT, hearingDate TEXT, hearingNotes TEXT)")
        cursor.execute("CREATE TABLE casestate(prefix INTEGER, caseNumber INTEGER, plaintiff TEXT, defendant TEXT, witness TEXT, charge TEXT, status TEXT, hearingDate TEXT, verdict TEXT, sentence TEXT, sentenceStatus TEXT, rowID INTEGER AUTO_INCREMENT PRIMARY KEY)")
        cursor.execute("CREATE TABLE users(username VARCHAR(255) UNIQUE, password TEXT, superuser TINYINT(1), rowID INTEGER AUTO_INCREMENT PRIMARY KEY)")

        print("Adding temporary user...")
        cursor.execute("INSERT INTO users(username,password,superuser) VALUES ('temp','"+tempPass+"',1)")
        
        print("Adding internal database users for table manipulation...")
        cursor.execute("CREATE USER VIEWER_"+suffix+"@'localhost' IDENTIFIED BY '"+viewPass+"'")
        cursor.execute("GRANT SELECT ON JCDB_"+suffix+".casestate TO VIEWER_"+suffix+"@'localhost'")
        cursor.execute("GRANT SELECT ON JCDB_"+suffix+".casehistory TO VIEWER_"+suffix+"@'localhost'")
        
        cursor.execute("CREATE USER MODIFIER_"+suffix+"@'localhost' IDENTIFIED BY '"+modifyPass+"'")
        cursor.execute("GRANT SELECT, INSERT, UPDATE, DELETE ON JCDB_"+suffix+".casestate TO MODIFIER_"+suffix+"@'localhost'")
        cursor.execute("GRANT SELECT, INSERT, UPDATE, DELETE ON JCDB_"+suffix+".casehistory TO MODIFIER_"+suffix+"@'localhost'")
        cursor.execute("GRANT SELECT, INSERT, UPDATE, DELETE ON JCDB_"+suffix+".users TO MODIFIER_"+suffix+"@'localhost'")
        
finally:
    dbConn.close()

print("Writing config file...")
f = open("JCDBconfig.ini","w")
f.write("SQL_HOST = 'localhost'\n")
f.write("SQL_DB = 'JCDB_"+suffix+"'\n")
f.write("SQL_VIEW_USER = 'VIEWER_"+suffix+"'\n")
f.write("SQL_VIEW_PASS = '"+viewPass+"'\n")
f.write("SQL_MODIFY_USER = 'MODIFIER_"+suffix+"'\n")
f.write("SQL_MODIFY_PASS = '"+modifyPass+"'\n")
f.write("TEMP_SETUP_PASS = '"+tempPass+"'")
f.close()

print("Setup complete. Please use username 'temp' with password: '"+tempPass+"' to add a new superuser right away.")
input("Press Enter to quit...")
