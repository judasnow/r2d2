# -*- coding: utf-8 -*-
import sys
import redis
from poster.encode import multipart_encode
from poster.streaminghttp import register_openers
import urllib2
import logging
import time
import json

log_file_name = "upload_image_demon.log"

def initlog():
    logger = logging.getLogger()
    hdlr = logging.FileHandler(log_file_name)
    formatter = logging.Formatter("%(asctime)s %(levelname)s %(message)s")
    hdlr.setFormatter(formatter)
    logger.addHandler(hdlr)
    logger.setLevel(logging.NOTSET)
    return logger

# try to connect redis server
redis = redis.StrictRedis( host='106.187.102.131', port=6379, password='erlang/otp' )

# some test data
#i = 5
#while i>0:
#    redis.rpush( 'image_to_upload' , "544:test.jpg" )
#    i = i - 1

logger = initlog()

# read image infomation that to upload
while True:
    image_info = redis.lpop( "image_to_upload" )
    if not image_info:
        break
    [user_id, image_name] = image_info.split( ":" )
    try:
        img_fp = open( "./temp/" + image_name , "rb" )
    except Exception , data:
        logger.error( "image file is not exist : " + user_id + ":" + image_name )
        break;

    # do upload
    # max try 5 times, if all fail I will push image_info back to the stack of image_to_upload
    while True:
        try_count = 0
        backoff_time = 1
        register_openers()
        datagen, headers = multipart_encode(
                {
                    "user_id": user_id,
                    "image": img_fp
                }
        )

        try:
            request = urllib2.Request( "http://www.huaban123.com/Action/WeixinMpApi.aspx?action=uploadImg", datagen, headers )
            res_json = urllib2.urlopen(request).read()
        except Exception , data:
            print "post exception: " , data
            if try_count <= 5:
                continue
            else:
                redis.rpush( "image_to_upload", image_info )
            
        res = json.loads( res_json )
        if res["type"] == "success":
            print image_info + " upload ok!"
            time.sleep(1)
            break
        else:
            # upload fail
            if try_count <= 5:
                print image_info + " upload fail but still trying, count" + try_count
                try_count = try_count + 1
                time.sleep( backoff_time )
                backoff_time = 2 * backoff_time
            else:
                # push the image_info back
                print image_info + " push it back"
                redis.rpush( "image_to_upload", image_info )
                break
