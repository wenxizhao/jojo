#-*- coding: utf-8 -*-
#!/usr/bin/env monkeyrunner
from com.android.monkeyrunner import MonkeyRunner,MonkeyDevice
from com.android.monkeyrunner.easy import EasyMonkeyDevice
from com.android.monkeyrunner.easy import By
from com.android.chimpchat.hierarchyviewer import HierarchyViewer
from com.android.monkeyrunner import MonkeyView
import sys, os
from sys import argv
import logging
logging.basicConfig(
	filename='adblock_cn.log',
	level=logging.NOTSET,
	format='%(name)-12s %(asctime)s %(levelname)-8s %(message)s',
	datefmt='%a, %d %b %Y %H:%M:%S',
	filemode='a')
#global variables
dolphinpackage='com.dolphin.browser.xf'
dolphinapk=None	
dolphinuninstall=None
ucpackage='com.UCMobile'
ucapk=None
ucuninstall=None
device=None
pos_title={'x':0,'y':0}
pos_go={'x':0,'y':0}
pos_home={'x':0,'y':0}

def get_arg():
	global dolphinpackage,ucpackage,dolphinapk,dolphinuninstall,ucapk,ucuninstall
	try:
		f=open('pageload.conf','r')
	except IOError:
		print "pageload.conf does not exist\n"
		logging.error('pageload.conf does not exist\n')
		sys.exit(1)
	for line in f.readlines():
		line = line.rstrip('\n')
		items=line.split(':')
		if(cmp(items[0],"dolphin uninstall package name")==0):
			dolphinuninstall=items[1]
		elif(cmp(items[0],"dolphin install apk path")==0):
			dolphinapk='apk/'+items[1]
		elif(cmp(items[0],"dolphin install package name")==0):			
			dolphinpackage=items[1]
		elif(cmp(items[0],"uc uninstall package name")==0):
			ucuninstall=items[1]
		elif(cmp(items[0],"uc install apk path")==0):
			ucapk='apk/'+items[1]
		elif(cmp(items[0],"uc install package name")==0):
			ucpackage=items[1]


def install(company):
	global dolphinpackage,ucpackage,device,dolphinapk,dolphinuninstall,ucapk,ucuninstall
	#uninstall package already on device
	if(company=='dolhpin'):	
		if dolphinuninstall:
			result=device.removePackage(dolphinuninstall)
			if result:
				print 'uninstall '+dolphinuninstall
			else:
				print "uninstall error "+dolphinuninstall
				logging.error("uninstall error "+dolphinuninstall)
		else:
			pass
		#install app
		if dolphinapk:
			result = device.installPackage(dolphinapk)
			if result:
				print 'install '+dolphinapk
			else:
				print "install error "+dolphinapk
				logging.error("install error "+dolphinapk)
				sys.exit(1)
		else:
			print 'no dolphinapk to install'
			logging.warn('no dolphinapk to install')
	
	elif(company=='uc'):
		if ucuninstall:
			result=device.removePackage(ucuninstall)
			if result:
				print 'uninstall '+ucuninstall
			else:
				print "uninstall error "+ucuninstall
				logging.error("uninstall error "+ucuninstall)
		else:
			pass
		if ucapk:
			result = device.installPackage(ucapk)
			if result:
				print 'install'+ucapk
			else:
				print "install error "+ucapk
				logging.error("install error "+ucapk)
				sys.exit(1)
		else:
			print 'no ucapk to install'
			logging.warn('no ucapk to install')
	else:
		print 'please input dolhpin or uc\n'

		
def setpos(runComponent):
	global device
	global pos_title
	global pos_go
	global pos_home
	
#	getpos(pos_home,'id/0x8f90000',runComponent)
	getpos(pos_title,'id/0x2012',runComponent)
	#id/go is on anathor view
	device.touch(pos_title['x'],pos_title['y'],"DOWN_AND_UP")
	#wait for keyboard ready
	MonkeyRunner.sleep(1.0)
	#input anything to make the 'go' visiable
	device.type('a')
	getpos(pos_go,'id/cancel',runComponent)
	#close keyboard
	device.press('KEYCODE_BACK', MonkeyDevice.DOWN_AND_UP)
	#back to home
	device.press('KEYCODE_BACK', MonkeyDevice.DOWN_AND_UP)

	#because there is something bug of id for uc,the bottom buttons have same id/0x8f90000
	#get screen width and height
	w=int(device.getProperty('display.width'))
	h=int(device.getProperty('display.height'))
	pos_home['x']=int(0.9*w)
	pos_home['y']=h-5


def getpos(pos,tag,runComponent):
	global device
	viewer = device.getHierarchyViewer()
	time=2.5
	while(viewer==None):
		#wait for browser ready
		time=time*2
		MonkeyRunner.sleep(time)
		viewer = device.getHierarchyViewer()
		if(viewer==None and time>20):
			#restart browser
			device.startActivity(component = runComponent)
		elif(viewer==None and time>50):
			print "get Hierarchy Viewer error\n"
			logging.error('get Hierarchy Viewer error\n')
			sys.exit(1)			
	view = viewer.findViewById(tag)
	time=2.5
	while(view==None):
		time=time*2
		MonkeyRunner.sleep(time)
		view = viewer.findViewById(tag)
		if(view==None and time>20):
			print "get Hierarchy Viewer error\n"
			logging.error('get Hierarchy Viewer error\n')
			sys.exit(1)	
	position=viewer.getAbsoluteCenterOfView(view)
	pos['x']=position.x
	pos['y']=position.y

def pageload(company):
	global dolphinpackage
	global ucpackage
	global device
	if(company=='dolphin'):
		package=dolphinpackage
		activity='mobi.mgeek.TunnyBrowser.BrowserActivity'
	elif(company=='uc'):
		package=ucpackage
		activity='com.UCMobile.main.UCMobile'
	#kill browser before start
	device.shell('am force-stop '+package)
	runComponent = package + '/' + activity
	#start browser
	device.startActivity(component = runComponent)
	#wait for browser ready
	MonkeyRunner.sleep(10.0)
	#set position for touching
	setpos(runComponent)
	try:
		URL_LIST = open("testcase/totallyloaded/urllist.txt",'r')
	except IOError:
		print "urllist.txt does not exist\n"
		logging.error('urllist.txt does not exist\n')
		sys.exit(1)
	#wait for file ready
	MonkeyRunner.sleep(5.0)
	#读取指定行
	for key in URL_LIST.readlines():
		key = key.rstrip('\n')
		device.touch(pos_title['x'],pos_title['y'],MonkeyDevice.DOWN_AND_UP)
		#wait for keyboard ready
		MonkeyRunner.sleep(2.0)
		#type to 'id/serch_src_text'
		device.type(key)
		#wait for input finished
		MonkeyRunner.sleep(2.0)
		device.touch(pos_go['x'],pos_go['y'],MonkeyDevice.DOWN_AND_UP)
		MonkeyRunner.sleep(60.0)
		#take snapshot
		key = key.replace("\n","").replace("/","").replace('?','')
		print key
		filename=company+'/cn/snapshot/'+key+'.png'
		result = device.takeSnapshot()
		MonkeyRunner.sleep(2.0)
		if result:
			result.writeToFile(filename,'png')
			#wait for writeToFile finished
			MonkeyRunner.sleep(1.0)
		else:
			print 'takeSnapshot error with '+key
			logging.info('takeSnapshot error with '+key)
		
		#in case of dialog
		device.press('KEYCODE_BACK', MonkeyDevice.DOWN_AND_UP)
		#wait for dialog invisiable
		MonkeyRunner.sleep(2.0)
		#back to home
		device.touch(pos_home['x'],pos_home['y'],MonkeyDevice.DOWN_AND_UP)
		MonkeyRunner.sleep(2.0)
	URL_LIST.close()

	print 'test is finish!'
	device.shell('am force-stop com.UCMobile')

if __name__=='__main__':
	# 获取设备
	try:
		device = MonkeyRunner.waitForConnection()
	except:
		print "Please check the device or USB"
	else:
		MonkeyRunner.sleep(10.0)
		get_arg()
	#	install('uc')
		pageload('uc')
	#	setpos('com.UCMobile/com.UCMobile.main.UCMobile')
	#	print pos_home
	#	print pos_title
	#	print pos_go
