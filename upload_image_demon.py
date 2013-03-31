# -*- coding: utf-8 -*-
import sys
import redis
from poster.encode import multipart_encode
from poster.streaminghttp import register_openers
import urllib2
import logging
import time

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
redis = redis.StrictRedis( host='172.17.0.46', port=6379, db=0 )

#some test data
i = 100
while i>0:
    redis.rpush( 'image_to_upload' , "544:test.jpg" )
    i = i - 1

logger = initlog()
image_info = redis.lpop( "image_to_upload" )
while image_info:
    [user_id, image_name] = image_info.split( ":" )
    image_info = redis.lpop( "image_to_upload" )
    try:
        img_fp = open( "./temp/" + image_name , "rb" )
    except Exception , data:
        logger.error( "image file is not exist : " + user_id + ":" + image_name )

    register_openers()
    datagen, headers = multipart_encode(
            {
                "user_id": user_id,
                "image": img_fp
                }
            )
    request = urllib2.Request("http://localhost:1979/Action/WeixinMpApi.aspx?action=uploadImg", datagen, headers)
    print urllib2.urlopen(request).read()




