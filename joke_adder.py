# -*- coding: utf-8 -*-

import redis
import os

redis = redis.StrictRedis(host='172.17.0.46', port=6379, db=0)

joke_fp = open("./j.txt", "r")
joke_items = joke_fp.readlines()
joke_fp.close();
for item in joke_items:
    if redis.sadd( "joke:text" , item ):
        print "ok"

