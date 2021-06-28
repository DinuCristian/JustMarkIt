#!/bin/sh

rm -rf marking_server.out
rm -rf marking_server.err

echo '' > marking_server.out

nohup python3.9 marking_server.py > marking_server.out 2> marking_server.err &

tail -f marking_server.out
