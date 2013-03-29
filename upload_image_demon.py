# -*- coding: utf-8 -*-

from poster.encode import multipart_encode
from poster.streaminghttp import register_openers
import urllib2
 
register_openers()
datagen, headers = multipart_encode({"image1": open("DSC0001.jpg", "rb")})


