#!/usr/bin/python
import smbus
import time
import sqlite3

i2cbus = smbus.SMBus(0)

#Ports=input mode
i2cbus.write_byte(0x48, 0x0)

#Read all input lines
temp = i2cbus.read_byte(0x48)
print temp


#Log in database
connect = sqlite3.connect('/home/pi/project2/temp.db')
curs=connect.cursor()

curs.execute("INSERT INTO templog values(datetime('now'), (?))", (temp,))

#commit changes
connect.commit()
connect.close()