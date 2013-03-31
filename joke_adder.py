# -*- coding: utf-8 -*-

import redis
import os

redis = redis.StrictRedis(host='106.187.34.51', port=6379, password='erlang/otp', db=0)

joke_fp = open("./j.txt", "r")
joke_items = joke_fp.readlines()
joke_fp.close();
for item in joke_items:
    if redis.sadd( "joke:text" , item ):
        print "ok"

