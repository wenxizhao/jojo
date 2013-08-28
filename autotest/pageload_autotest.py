#define return
#1:page totallyloaded test fail!
#2:page totallyloaded test success! but analyse log fail!
#3:page totallyloaded test success! and analyse log success!
import subprocess,os,signal,sys
import atexit
import logging
from datetime import date,datetime
logging.basicConfig(
	filename='adblock_cn.log',
	level=logging.NOTSET,
	format='%(name)-12s %(asctime)s %(levelname)-8s %(message)s',
	datefmt='%a, %d %b %Y %H:%M:%S',
	filemiode='a')

def main(version):
	#if len(argv)<2:
	#	print "please input version"
	#	sys.exit(4)
	#version=argv[1]
	_DATETIME=datetime.now().strftime("%Y-%m-%d_%H-%M-%S")
	#*******************DOLPHIN*********************
	#start adb logcat
	cmd="adb logcat -v threadtime > dolphin/"+version+"/totallyloaded/"+_DATETIME+".log &"
	child_adb=subprocess.Popen(cmd,stdout=subprocess.PIPE,shell=True,preexec_fn=os.setsid)
	#atexit.register kill the subprocess when parent crash
	@atexit.register
	def kill_adb():
		ret=subprocess.Popen.poll(child_adb)
		#if the subprocess is running,poll() return None
		if ret is None:
			os.killpg(child_adb.pid,signal.SIGTERM)
		else:
			pass
	#start adblock_cn.py
	monkey_return=subprocess.check_call('monkeyrunner adblock_dolphin.py',shell=True)
	if monkey_return==0:
		#shutdown adb logcat
		os.killpg(child_adb.pid,signal.SIGTERM)
		#start readlog
		cmd="python readlog.py dolphin "+_DATETIME
		dolphin_readlog_return=subprocess.check_call(cmd,shell=True)
 
	#**********************UC*************************
	#start adb logcat
	cmd="adb logcat -v threadtime > uc/"+version+"/totallyloaded/"+_DATETIME+".log &"
	child_adb=subprocess.Popen(cmd,stdout=subprocess.PIPE,shell=True,preexec_fn=os.setsid)
	#atexit.register kill the subprocess when parent crash
	@atexit.register
	def kill_adb():
		ret=subprocess.Popen.poll(child_adb)
		#if the subprocess is running,poll() return None
		if ret is None:
			os.killpg(child_adb.pid,signal.SIGTERM)
		else:
			pass
	#start adblock_cn.py
	monkey_return=subprocess.check_call('monkeyrunner adblock_uc.py',shell=True)
	if monkey_return==0:
		#shutdown adb logcat
		os.killpg(child_adb.pid,signal.SIGTERM)
		#start readlog
		cmd='python readlog.py uc '+_DATETIME
		uc_readlog_return=subprocess.check_call(cmd,shell=True)


	#*********************insert into mysql****************
	if dolphin_readlog_return==0:
		print 'readlog finished!\n'
		cmd='python insert2mysql.py dolphin '+version+' '+_DATETIME
		dolphin_insert2db_return=subprocess.check_call(cmd,shell=True)
		if dolphin_insert2db_return==0:
			print 'insert to mysql finished!\n'
		else:
			print 
 	if uc_readlog_return==0:
		print 'readlog finished!\n'
		cmd='python insert2mysql.py uc '+version+' '+_DATETIME
		uc_insert2db_return=subprocess.check_call(cmd,shell=True)
		if uc_insert2db_return==0:
			print 'insert to mysql finished!\n'

	if monkey_return !=0:
		return 1
	elif dolphin_insert2db_return!=0 or uc_insert2db_return!=0:
		return 2
	else:
		return 3
