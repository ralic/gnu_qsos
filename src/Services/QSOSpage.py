from twisted.application import service, internet

from nevow  import rend
from nevow  import loaders
from nevow  import tags as T
from nevow  import inevow
from nevow  import static

from os     import listdir

class DefaultPage (rend.Page):
    def __init__ (self, repository):
        rend.Page.__init__ (self)
        self.title = ""
        self.repository = repository
        self.docFactory = self.makeDocFactory()
        self.child_css = static.File(self.repository + "/style/")
        self.addSlash = True
        
    def render_Head (self, ctx, data):
        css =  T.link (rel="stylesheet", type="text/css", href='/css/qsos-listing.css')
        favicon = T.link (rel="icon", type="image/png", href='/css/favicon.ico')
        return T.head [ T.title [ self.renderTitle ], css , favicon]

    def makeDocFactory(self):
        page = T.html [
                      self.render_Head,
                      self.renderBody
                      ]
        return loaders.stan (page)

    def child_css (self, ctx):
        return self.child_css


class QSOSPage (DefaultPage):

    def renderTitle(self, ctx, data):
        return "QSOS Repository/%s" % ("/".join(inevow.IRequest ( ctx ).prepath[1:]), )
    
    def renderBody(self, ctx, data):
        return T.div(id = "corp")[
                                  T.h1 [self.renderTitle],
                                  T.ul [self.renderList]
                                                  ]

        

class ErrorPage(QSOSPage):
    """
    Renders confirmation page.
    
    Handles the confirmation page of a successful evaluation upload.
    Return to root page or submit another evaluation are suggested.
    """
    def __init__ (self, repository, errorList):
        QSOSPage.__init__(self, repository)
        self.errorList = errorList
    
    def renderList(self, ctx, data):
        return [ self.renderItem(error, level)  for (error,level) in self.errorList ]
    
    
    def renderItem (self, error, level):
            return T.li ( class_ = level ) [ error ]
        

    def renderTitle(self, ctx, data):
        return "QSOS Error Page"