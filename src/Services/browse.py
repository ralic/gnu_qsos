"""
Browse module.

This module handles the repository browse page.
"""
##
#    @defgroup browse Browse
#    @ingroup Services
#    @author Hery Randriamanamihaga
#
#    @todo
#        - Use core module of Engine for interactions with it
#        - Handle properly templates and families pages
#        - Cleanup/split this module's code?

from twisted.application import service, internet

from nevow  import rend
from nevow  import loaders
from nevow  import tags as T
from nevow  import inevow
from nevow  import static

from os     import listdir

from Engine import core


##
#    @ingroup browse
#   
class SubPage ( rend.Page ):
    """
    Handles repository subpages
    
    This class renders a page for any location on the repository that is not
    explicitly binded to a handler
    """
    def __init__ (self, repository):
        rend.Page.__init__ (self)
        self.repository = repository
        self.docFactory = self.makeDocFactory()
        
        
    def render_title ( self, ctx, data ):
        "Renders page's title"
        request = inevow.IRequest ( ctx )
        return "Currently browsing  %s" % ( "/".join(request.prepath[1:]), )
    
    def render_body ( self, ctx, data ):
        "Renders page's content"
        path = "/".join(inevow.IRequest(ctx).prepath[1:])
        return [ T.h1 [path] ] + \
               [ T.p[
                     T.a ( href = path+'/'+item ) [ item.split(".")[:-1] ]
                     ] for item in listdir(self.repository + "/sheets/"+path) ]
    
    def makeDocFactory(self):
        page = T.html [
                      T.head[ T.title [ self.render_title ] ],
                      T.body[ self.render_body ]
                      ]
        return loaders.stan (page)
    
  
##
#    @ingroup browse
#  
class EvaluationPage ( rend.Page ):
    """
    Renders Evaluation pages content
    
    This class renders the evaluation home page dynamically from repository's
    content.
    """
    def __init__ (self, repository):
        rend.Page.__init__ (self)
        self.repository = repository
        self.docFactory = self.makeDocFactory()
        
    def render_title ( self, ctx, data ):
        "Renders page's title"
        request = inevow.IRequest ( ctx )
        return "Currently browsing  %s" % ( "/".join(request.prepath[1:]), )
    
    def render_body ( self, ctx, data ):
        "Renders page's body"
        path = "/".join([self.repository, "sheets"]+
                        inevow.IRequest(ctx).prepath[1:])
        evals = [ T.p [
                       T.a (href = "/".join(["evaluations", dir, version]))
                       [ dir + "-" + version ]
                    ] for dir in listdir(path)
                      for version in listdir(path + '/' + dir) ]
        
        return [ T.h1 ["/".join(inevow.IRequest(ctx).prepath[1:])] ] + evals 
    
    def makeDocFactory(self):
        page = T.html [
                      T.head[ T.title [ self.render_title ] ],
                      T.body[ self.render_body]
                      ]
        return loaders.stan (page)
    
    def locateChild ( self, ctx, segments ):
#        "Locate and generate the evaluation page"
        id= "-".join(segments)
        tmp =  "/tmp/" + id + "." + "xml"
        file = open(tmp,'w')
        file.write(core.request(id))
        file.close()
        return (static.File(tmp), ())

##
#    @ingroup browse
#
class MainPage ( rend.Page ):
    """
    Renders repository home page
    
    This class renders the main page when browse request is handled
    """
    
    def __init__ (self, repository):
        rend.Page.__init__ (self)
        self.repository = repository
        self.docFactory = self.makeDocFactory()
        
    
    def makeDocFactory (self):
        dirs = [T.p[
                    T.a(href='repository/' + dir)[dir]
                    ] for dir in listdir(self.repository + "/sheets") ]
                    
        page = T.html[
                      T.head(title='Main Page'),
                      T.body[[T.h1["/"]], dirs]
                      ]
        return loaders.stan(page)
    
    def childFactory (self, ctx, name):
        "Handles children with no explicit renderer"
        return SubPage(self.repository)
    
    def child_evaluations (self, ctx):
        return EvaluationPage(self.repository)

