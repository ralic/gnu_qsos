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
from QSOSpage import QSOSPage


##
#    @ingroup browse
#   


class SubPage ( QSOSPage ):
    """
    Handles repository subpages
    
    This class renders a page for any location on the repository that is not
    explicitly binded to a handler
    """
    def renderBody ( self, ctx, data ):
        "Renders page's content"
        path = "/".join(inevow.IRequest(ctx).prepath[1:])
        return [ T.li (class_='sheet') [T.a ( href = path + '/' + item ) [ item.split(".")[:-1] ]
                     ] for item in listdir(self.repository + "/sheets/"+path) ]
        
    def childFactory ( self, ctx, segments ):
        "Locate and generate the evaluation page"
        path = "/".join([self.repository, "sheets"]+
                        inevow.IRequest(ctx).prepath[1:]+list(segments))
        return (static.File(path, defaultType='xml'),())
    
  
##
#    @ingroup browse
#  
class EvaluationPage ( QSOSPage ):
    """
    Renders Evaluation pages content
    
    This class renders the evaluation home page dynamically from repository's
    content.
    """
        
    
    def renderBody (self, ctx, data):
        "Renders page's body"
        path = "/".join([self.repository, "sheets"]+
                        inevow.IRequest(ctx).prepath[1:])
        return [ T.li (class_='sheet') [
                       T.a (href = "/".join(["evaluations", dir, version]))
                       [ dir + "-" + version ]
                    ] for dir in listdir(path)
                      for version in listdir(path + '/' + dir) ]
        
    

    
    def locateChild ( self, ctx, segments ):
        "Locate and generate the evaluation page"
        id= "-".join(segments)
        tmp =  "/tmp/" + id + "." + "qsos"
        file = open(tmp,'w')
        file.write(core.request(id))
        file.close()
        return (static.File(tmp, defaultType='xml'), ())

##
#    @ingroup browse
#
class MainPage ( QSOSPage ):
    """
    Renders repository home page
    
    This class renders the main page when browse request is handled
    """
    
    def renderBody (self, ctx, data):
        return [T.li (class_='folder')[
                    T.a(href='repository/' + dir)[dir]
                    ] for dir in listdir(self.repository + "/sheets") ]
                    
    
    def childFactory (self, ctx, name):
        "Handles children with no explicit renderer"
        return SubPage(self.repository)
    
    def child_evaluations (self, ctx):
        return EvaluationPage(self.repository)

