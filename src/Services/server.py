from twisted.web import server, resource
from twisted.internet import reactor

class Simple(resource.Resource):
    isLeaf = True
    def render_GET(self, request):
        print dir(request)
        print "".join(["<html>",str(request),"</html>"])
        return "<html>Hello World!</html>"
site = server.Site(Simple())
reactor.listenTCP(8080, site)
reactor.run()
