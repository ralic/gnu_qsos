from twisted.application import service, internet
from nevow import appserver
from Services import homepage
from Engine import core
import os


application = service.Application ( "QSOS Engine" )
core.setup('/tmp/localrepository')
port        = 8080
res         = homepage.MainPage('/tmp/localrepository')
site        = appserver.NevowSite ( res )
webService  = internet.TCPServer ( port, site )
webService.setServiceParent ( application )
