from twisted.application import service, internet

from nevow  import rend
from nevow  import loaders
from nevow  import tags as T
from nevow  import inevow
from nevow  import static

from os     import listdir

class QSOSPage (rend.Page):
    def __init__ (self, repository):
        rend.Page.__init__ (self)
        self.title = ""
        self.repository = repository
        self.docFactory = self.makeDocFactory()
        self.child_css = static.File(self.repository + "/style/")
        
    def renderHead (self, ctx, data):
        css =  T.link (rel="stylesheet", type="text/css", href='/css/qsos-listing.css')
        return T.head [ T.title [ self.renderTitle ], css ]

    def renderTitle(self, ctx, data):
        return "QSOS Repository/%s" % ("/".join(inevow.IRequest ( ctx ).prepath[1:]), )
    
    def makeDocFactory(self):
        page = T.html [
                      self.renderHead,
                      T.body [T.div(id = "corp")[
                                                  T.h1 [self.renderTitle],
                                                  T.ul (class_ = "downloads")[self.renderBody]
                                                  ] ] ]
        return loaders.stan (page)
    
    def child_css (self, ctx):
        return self.child_css
    