import MySQLdb
import fileinput
from urlparse import urlparse
import subprocess,os,signal,sys
import time
from pageload_autotest import main
import logging
logging.basicConfig(
	filename='adblock_cn.log',
	level=logging.NOTSET,
	format='%(name)-12s %(asctime)s %(levelname)-8s %(message)s',
	datefmt='%a, %d %b %Y %H:%M:%S',
	filemiode='a')
Apk={}
def dbget_apk():
	global Apk
	try:
		conn=MySQLdb.connect(host='localhost',user='root',passwd='123456',db='performance',port=3306)
		cur=conn.cursor()
		sql="select id,src,pid,version from t_apk where status='0' and priority='1' order by id;"
		count=cur.execute(sql)
		print 'there are %s rows where priority=1'%count
		if(count>0):
			result=cur.fetchone()
			Apk['id']=result[0]
			Apk['src']=result[1]
			Apk['pid']=result[2]
			Apk['version']=result[3]
		else:
			sql="select id,src,pid,version from t_apk where status='0' and priority='0' order by id;"
			count=cur.execute(sql)
			print 'there are %s rows where priority=0'%count
			if(count>0):
				result=cur.fetchone()
				Apk['id']=result[0]
				Apk['src']=result[1]
				Apk['pid']=result[2]
				Apk['version']=result[3]
			else:
				return False
				sys.exit(0)
			cur.close()
			conn.close()
		return True	
	except MySQLdb.Error,e:
		print 'Mysql Error %d:%s'%(e.args[0],e.args[1])
def download():
	apkname=urlparse(Apk['src']).path.split('/')[-1]
	apkpath="apk/"+apkname
	if(os.path.exists(apkpath) is False):
		cmd="wget -P apk "+Apk['src']
		os.system(cmd)
	else:
		print '%s already exists'%apkname
		pass
def modifyconf():
	apkname=urlparse(Apk['src']).path.split('/')[-1]
	try:
		with open('pageload.conf') as f:lines=f.read().splitlines()
		with open('pageload.conf','w') as f:
			for line in lines:
				if line.startswith('dolphin install apk path:'):
#					line = line.rstrip('\n')
					items=line.split(':')
					oldname=items[1]
					#print(line.replace(oldname,apkname),file=f)
					f.write(line.replace(oldname,apkname)+'\n')
				else:
					#print(line,file=f)
					f.write(line+'\n')
	except IOError:
		print "can not open pageload.conf\n"
		logging.error('can not open pageload.conf\n')
		sys.exit(1)
def update_status(status):
	try:
		conn=MySQLdb.connect(host='localhost',user='root',passwd='123456',db='performance',port=3306)
		cur=conn.cursor()
		sql="update t_apk set status='"+status+"' where id='"+Apk['id']+"';"
		cur.execute(sql)
		conn.commit()
		cur.close()
		conn.close()
	except MySQLdb.Error,e:
		print 'Mysql Error %d:%s'%(e.args[0],e.args[1])
if __name__=='__main__':
	while(1):
		hasapk=dbget_apk()
		if(hasapk):
	#		download()
			modifyconf()
			version=Apk['version']
			#cmd=["python","pageload_autotest.py",version]
			#test_return=subprocess.check_call(cmd,0,None,None,None,None)
			test_return=main(version)
			update_status(test_return)
			if test_return==1:
				print "page totallyloaded test fail!"
			elif test_return ==2:
				print "page totallyloaded test success! but analyse log fail!"
			elif test_return ==3:
				print "page totallyloaded test success! and analyse log success!"
			else:
				pass
		else:
			time.sleep(3600)
