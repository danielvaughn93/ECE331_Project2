#!/usr/bin/python
import smbus
import time
import sqlite3

I2C_ADDRESS = 0x48

bus = smbus.SMBus(1)

#Set all ports in input mode
bus.write_byte(I2C_ADDRESS, 0x0)

#Read all input lines
value = bus.read_byte(I2C_ADDRESS)
print value
#print "%02X" % value


####################################
# Store temperature in a database  #
####################################

connect = sqlite3.connect('/home/pi/ece331/project2/templog.db')
curs=connect.cursor()

curs.execute("INSERT INTO temps values(datetime('now'), (?))", (value,))

#commit changes
connect.commit()
connect.close()