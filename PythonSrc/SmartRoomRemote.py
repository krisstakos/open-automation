#библиотеки нужни за програмата
import socket
import ctypes
from ctypes import windll, Structure, c_ulong, byref
import time
import os
import sys
import json
import re
from win32api import * #api за ос
SendInput = ctypes.windll.user32.SendInput # Начало на инициализация на ctypes библиотеката
PUL = ctypes.POINTER(ctypes.c_ulong)
class KeyBdInput(ctypes.Structure):
    _fields_ = [("wVk", ctypes.c_ushort),
                ("wScan", ctypes.c_ushort),
                ("dwFlags", ctypes.c_ulong),
                ("time", ctypes.c_ulong),
                ("dwExtraInfo", PUL)]

class HardwareInput(ctypes.Structure):
    _fields_ = [("uMsg", ctypes.c_ulong),
                ("wParamL", ctypes.c_short),
                ("wParamH", ctypes.c_ushort)]

class MouseInput(ctypes.Structure):
    _fields_ = [("dx", ctypes.c_long),
                ("dy", ctypes.c_long),
                ("mouseData", ctypes.c_ulong),
                ("dwFlags", ctypes.c_ulong),
                ("time",ctypes.c_ulong),
                ("dwExtraInfo", PUL)]

class Input_I(ctypes.Union):
    _fields_ = [("ki", KeyBdInput),
                 ("mi", MouseInput),
                 ("hi", HardwareInput)]

class Input(ctypes.Structure):
    _fields_ = [("type", ctypes.c_ulong),
                ("ii", Input_I)]
def PressKey(hexKeyCode):

    extra = ctypes.c_ulong(0)
    ii_ = Input_I()
    ii_.ki = KeyBdInput( hexKeyCode, 0x48, 0, 0, ctypes.pointer(extra) )
    x = Input( ctypes.c_ulong(1), ii_ )
    SendInput(1, ctypes.pointer(x), ctypes.sizeof(x))

def ReleaseKey(hexKeyCode):

    extra = ctypes.c_ulong(0)
    ii_ = Input_I()
    ii_.ki = KeyBdInput( hexKeyCode, 0x48, 0x0002, 0, ctypes.pointer(extra) )
    x = Input( ctypes.c_ulong(1), ii_ )
    SendInput(1, ctypes.pointer(x), ctypes.sizeof(x)) #<Край
class POINT(Structure):
    _fields_ = [("x", c_ulong), ("y", c_ulong)]
try:
    with open('config.txt') as data_file:    
        data = json.load(data_file)
except:
    ctypes.windll.user32.MessageBoxW(0,"Липсва конфигурационен файл!", "Данни за свързване", 0)
    sys.exit()
x_old=0
y_old=0
conf_port=data["settings"]["port"]
conf_mouse_x=data["settings"]["mouse_sens_x"]
conf_mouse_y=data["settings"]["mouse_sens_y"]
conf_mouse_x=re.sub(r'[^\d]+','',conf_mouse_x)
conf_mouse_y=re.sub(r'[^\d]+','',conf_mouse_y)
conf_port=re.sub(r'[^\d]+','',conf_port)
conf_port=int(conf_port)
conf_mouse_x=int(conf_mouse_x)
conf_mouse_y=int(conf_mouse_y)
UDP_IP = socket.gethostbyname(socket.getfqdn()) #адрес на хост-а
if conf_port !=0 and conf_port <= 65535 and conf_port>0:
    UDP_PORT = conf_port #Порт
    ctypes.windll.user32.MessageBoxW(0,"Адрес:"+UDP_IP+'\n'
                                     +"Порт:"+str(UDP_PORT)+'\n'
                                     +"X офсет:"+str(conf_mouse_x)+'\n'
                                     +"У офсет:"+str(conf_mouse_y), "Данни за свързване", 0)
elif conf_port<0:
    conf_port=abs(conf_port)
    if conf_port > 65535:
        UDP_PORT = 65535 #Порт
    else:
        UDP_PORT=conf_port
    ctypes.windll.user32.MessageBoxW(0,"Адрес:"+UDP_IP+'\n'+"Порт:"+str(UDP_PORT), "Данни за свързване", 0)
elif conf_port>65535:
    UDP_PORT = 65535 #Порт
    ctypes.windll.user32.MessageBoxW(0,"Адрес:"+UDP_IP+'\n'+"Порт:"+str(UDP_PORT), "Данни за свързване", 0)
else:
    ctypes.windll.user32.MessageBoxW(0,"Проблем с порта!", "Данни за свързване", 0)
    sys.exit()
sock = socket.socket(socket.AF_INET,socket.SOCK_DGRAM) #Създаване на сокет и bind-ване
sock.bind((UDP_IP, UDP_PORT))
#x=int(GetSystemMetrics(0)/2) # половината от широчината на екрана в пиксели
#y=int(GetSystemMetrics(1)/2) # половината от височината на екрана в пиксели
while True:
    try:
        data, addr = sock.recvfrom(1024) #Задаване размер на буфер за получените данни
        line=data.decode("ascii")#декодиранена данните
        loaded = json.loads(line)  
        dv_type=loaded["type"]
        dv_command=loaded["command"]
        line="@"+dv_type+":"+dv_command
        print(line)
        if line=='@key:up':
            PressKey(0x26)    #стрелка нагоре
            ReleaseKey(0x26)
        elif line=='@key:down':
            PressKey(0x28)#стрелка надолу
            ReleaseKey(0x28)
        elif line=='@key:left':
            PressKey(0x25)# лява стрелка
            ReleaseKey(0x25)   
        elif line=='@key:right':
            PressKey(0x27)# дясна стрелка
            ReleaseKey(0x27)
        elif line=='@key:esc':
            PressKey(0x1B)# esc
            ReleaseKey(0x1B)
        elif line=='@key:space':          
            PressKey(0x20)
            ReleaseKey(0x20)
        elif line=='@key:mute':          
            PressKey(0xAD)
            ReleaseKey(0xAD)
        elif line=='@key:exit':
            PressKey(0x12)
            PressKey(0x73)
            ReleaseKey(0x12)
            ReleaseKey(0x73)
        elif line=='@key:menu':
            PressKey(0x5C)# старт бутон
            ReleaseKey(0x5C)      
        elif line=='@key:enter':
            PressKey(0x0D)
            ReleaseKey(0x0D)#ентър
        elif line=='@key:vup':
            PressKey(0xAF)
            ReleaseKey(0xAF)# увеличаване на звука
        elif line=='@key:vdown':
            PressKey(0xAE)
            ReleaseKey(0xAE)# намаляне на звука
        elif line=='@key:f':
            PressKey(0x46)
            ReleaseKey(0x46)#  f бутон   
        elif line=='@key:pc_restart':
            os.system('shutdown /r /t 0')# рестартиране на машината
        elif line=='@key:pc_shutdown':
            os.system('shutdown /s /t 0')# рестартиране на машината
        elif line=='@key:pc_sleep':
            os.system('rundll32.exe powrprof.dll,SetSuspendState 0,1,0')# вкарване на машината в спящ режим
        elif line=='@mouse_click:left':
            ctypes.windll.user32.mouse_event(2, 0, 0, 0,0)# ляв бутон на мишката
            ctypes.windll.user32.mouse_event(4, 0, 0, 0,0)
        elif line=='@mouse_click:right':
            ctypes.windll.user32.mouse_event(8, 0, 0, 0,0)# десен бутон на мишката
            ctypes.windll.user32.mouse_event(16, 0, 0, 0,0)
        elif '@mouse:' in line:
            aft_line=line.replace("@mouse:", "")
            x2,y2=aft_line.split("&")
            x2=int(x2)
            y2=int(y2)
            x2=x2/conf_mouse_x #offset x
            y2=y2/conf_mouse_y# offset y
            pt = POINT()
            windll.user32.GetCursorPos(byref(pt))
            x=pt.x+((pt.x+int(x2))-x_old)#-conf_mouse_x
            y=pt.y+(pt.y+int(y2)-y_old)#-conf_mouse_y
            x_old=x
            y_old=y
            ctypes.windll.user32.SetCursorPos(x,y)
    except:
        pass
    
