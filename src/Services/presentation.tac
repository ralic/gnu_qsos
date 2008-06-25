from twisted.application import service, internet
from nevow import appserver
from Services import homepage, browse
from Engine import core


application = service.Application ( "nevowdemo" )
port        = 8080
scheduler   = core.Core()
res         = homepage.MainPage()
site        = appserver.NevowSite ( res )
webService  = internet.TCPServer ( port, site )
webService.setServiceParent ( application )
