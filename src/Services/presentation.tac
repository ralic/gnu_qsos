from twisted.application import service, internet
from nevow import appserver
from Services import homepage, browse


application = service.Application ( "nevowdemo" )
port        = 8080
res         = homepage.MainPage()
site        = appserver.NevowSite ( res )
webService  = internet.TCPServer ( port, site )
webService.setServiceParent ( application )
