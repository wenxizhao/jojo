import MySQLdb
import time,sys
import logging
logging.basicConfig(
	filename='adblock_cn.log',
	level=logging.NOTSET,
	format='%(name)-12s %(asctime)s %(levelname)-8s %(message)s',
	datefmt='%a, %d %b %Y %H:%M:%S',
	filemode='a')
ip='localhost'
username='root'
psw='123456'
database='performance'
time=0
_DATETIME=None
def set_time():
	global time,_DATETIME
	time=_DATETIME.replace("_"," ")

def getapkname(company):
	try:
		f=open('pageload.conf','r')
	except IOError:
		print "can not open pageload.conf\n"
		logging.error('can not open pageload.conf\n')
		sys.exit(1)
	for line in f.readlines():
		line = line.rstrip('\n')
		items=line.split(':')
		if(cmp(items[0],"dolphin install apk path")==0 and company=='dolphin'):
			name=items[1]
		elif(cmp(items[0],"uc install apk path")==0 and company=='uc'):
			name=items[1]
		else:
			pass
	return name

def getpid():
	#totallyloaded
	return 1

def t_build(company):
	global time
	apkname=getapkname(company)
	value=[time,apkname,company]
	try:
		conn=MySQLdb.connect(host=ip,user=username,passwd=psw,db=database,port=3306,charset='utf8')
		cur=conn.cursor()
		cur.execute("insert into t_build(date,name,company) value(%s,%s,%s)",value)
		conn.commit()
		temp=[apkname,time]
		cur.execute("select id from t_build where name=%s and date=%s",temp)
		bid=cur.fetchone()
		bid=bid[0]
		cur.close()
		conn.close()
	except MySQLdb.Error,e:
		print 'Mysql Error %d:%s'%(e.args[0],e.args[1])
	return bid

def t_totallyloaded(company,version):
	global ip,username,psw,database
	global time,_DATETIME
	#finename
	if version=='cn' or version=='en':
		filepath=company+'/'+version+'/'+'totallyloaded/'
		filename=_DATETIME+'.txt'
	else:
		print 'version is wrong\n'
	#open file
	try:
		f=open(filepath+filename,'r')
	except IOError:
		print filepath+filename+' not exist\n'
		logging.error(filepath+filename+' not exist\n')
		sys.exit(1)
	#deal with db
	try:
		conn=MySQLdb.connect(host=ip,user=username,passwd=psw,db=database,port=3306,charset='utf8')
		cur=conn.cursor()
		apkname=getapkname(company)
		count=0
		summary=0
		for line in f:
			line=line.rstrip('\n')
			items=line.split(' finished: ')
			count+=1
			summary+=float(items[1])
			value=[apkname,time,items[0],items[1]]
			cur.execute("insert into t_totallyloaded(name,date,url,seconds) value(%s,%s,%s,%s)",value)
		conn.commit()
		if(count==0):
			sys.exit(1)
		avg=summary/count
		avg=float('%.3f'%avg)
		pid=getpid()
		bid=t_build(company)
		value=[bid,pid,avg,'seconds']
		cur.execute("insert into t_results(bid,pid,value,denomination) value(%s,%s,%s,%s)",value)
		conn.commit()
		print 'insert into t_results finished'
		cur.close()
		conn.close()
		print 'url counts:'+str(count)
		print 'summary time of all url totally loaded:'+str(summary)
		print 'average time of totally loaded:'+str(avg)
	except MySQLdb.Error,e:
		print 'Mysql Error %d:%s'%(e.args[0],e.args[1])

if __name__=='__main__':
#	global _DATETIME
	if len(sys.argv)<4:
		print "please input company, version and _DATETIME"
		sys.exit(1)
	company=sys.argv[1]
	version=sys.argv[2]
	_DATETIME=sys.argv[3]
	set_time()
	if company=='dolphin' or company=='uc':
		t_totallyloaded(company,version)
	else:
		print 'unknown input'
		sys.exit(1)

