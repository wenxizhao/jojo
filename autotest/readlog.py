# -*- coding: utf-8 -*-
#!/usr/bin/python
import re
import sys,string
#global
DATETIME=None
s=0
ms=0
def lastline(company):
	global DATETIME
	logfile=company+'/cn/totallyloaded/'+DATETIME+'.log'
	try:
		log= open(logfile,'r')
	except IOError:
		print("%s.log not exist!"%DATETIME)
		sys.exit(1)
	for line in log:
		pass
	last=line
	log.close()
	return last

def count(h1,m1,s1,ms1,h2,m2,s2,ms2):
	h1=int(h1)
	m1=int(m1)
	s1=int(s1)
	ms1=int(ms1)
	h2=int(h2)
	m2=int(m2)
	s2=int(s2)
	ms2=int(ms2)
	global s
	global ms

	flag0=0
	flag1=0
	flag2=0
	#ms
	if ms2>=ms1:
		 ms=ms2-ms1
	else:
		ms=ms2+1000-ms1
		flag0=1
	#秒
	if flag0==0:

		if s2>=s1:
			s=s2-s1
		else:
			s=s2+60-s1
			flag1=1
	elif flag0==1:
		s2-=1
		if s2>=s1:
			s=s2-s1
		else:
			s=s2+60-s1
		flag1=1
	#分
	if flag1==0:

		if m2>=m1:
			m=m2-m1
		else:
			m=m2+60-m1
			flag2=1
	elif flag1==1:
		m2-=1
		if m2>=m1:
			m=m2-m1
		else:
			m=m2+60-m1
			flag2=1
	#小时
	if flag2==0:
		if h2>=h1:
			h=h2-h1
		else:
			h=h2+24-h1
	elif flag2==1:
		h2-=1
		if h2>=h1:
			h=h2-h1
		else:
			h=h2+60-h1

def analyse(company):
	global s,ms
	global DATETIME
	if company=='dolphin':
		#dolphin log start 
		begin="onPageStarted:"
		#dolphin log finished
		end="onPageFinished:"
		slice_mark='MonkeyStub: translateCommand: type'
	elif company=='uc':
		#uc log start
		begin="Started\]\[D\]: "
		#uc log finished
		end="Finished\]\[\D\]: "
		slice_mark='MonkeyStub: translateCommand: type'
	else:
		print 'unknown input'
		sys.exit()
	resultpath=company+'/cn/totallyloaded/'
	resultfile=DATETIME+'.txt'
	try:
		result =open(resultpath+resultfile,'w')
	except IOError:
		print("open %s.txt error!"%DATETIME)
		sys.exit(1)
	#urllist search txt
	try:
		url_list=open("testcase/totallyloaded/urllist.txt","r")
	except IOError:
		print("open urllist error!")
		sys.exit(1)
	log_lastline=lastline(company)
	for url in url_list:
		#open running log
		logfile=company+'/cn/totallyloaded/'+DATETIME+'.log'
		try:
			log= open(logfile,'r')
		except IOError:
			print("%s.log not exist!"%DATETIME)
			sys.exit(1)
		fix_start=time1_valid=time2_valid=False
		i=0
		for line in log:
			i=i+1
			url=url.rstrip('\n').rstrip('\r')
			if re.search('type '+url,line)!=None:
				fix_start=True
				print('%d-type %s'%(i,url))
				continue
			if fix_start is True and re.search(begin+url,line)!=None:
				time1=line[6:19]
				h1=time1[0:2]
				m1=time1[3:5]
				s1=time1[6:8]
				ms1=time1[9:12]
				time1_valid=True
				fix_start=False
				print('%d-start time:%s'%(i,time1[0:12]))
				continue
			if time1_valid is True and re.search(end+url,line)!=None:
				time2=line[6:19]
				h2=time2[0:2]
				m2=time2[3:5]
				s2=time2[6:8]
				ms2=time2[9:12]
				time2_valid=True
				print('%d-finished time:%s'%(i,time2[0:12]))
				continue
			if time1_valid and time2_valid and (re.search(slice_mark,line)!=None or re.search(log_lastline,line)!=None):
				count(h1,m1,s1,ms1,h2,m2,s2,ms2)
				print url
				print("%d.%d" % (s,ms))
				result.writelines('%s finished: %d.%d\n'%(url,s,ms))
				fix_start=time1_valid=time2_valid=False


	url_list.close()
	log.close()
	result.close()



if __name__=='__main__':
#	global DATETIME
	if len(sys.argv)<3:
		print "please input company and DATETIME"
		sys.exit(1)
	company=sys.argv[1]
	DATETIME=sys.argv[2]
	analyse(company)

