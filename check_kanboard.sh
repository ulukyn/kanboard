#!/bin/sh

while read line
do
	echo "Killing client"
	PID=`ps -aux | grep "php \./cli worker" |  awk '{ print $2 }'`
	kill -9 $PID
done

