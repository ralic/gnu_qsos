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
from os import path

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
    def renderList ( self, ctx, data ):
        "Renders page's content"
        location = "/".join([self.repository, 'sheets']+inevow.IRequest(ctx).prepath[1:])
        return [ self.renderItem(item, location)  for item in listdir(location) ]
        
    def renderItem (self, item, location):
        if path.isdir(location+item) :
            return T.li ( class_ = 'folder' ) [ T.a (href=item) [item] ]
        elif path.isfile(location+item) :
            return T.li ( class_ = item.split(".")[-1] ) [ T.a (href=item) [item.split(".")[:-1]] ]
        
    def childFactory (self, ctx, name):
        "Locate and generate the evaluation page"
        location = "/".join([self.repository, 'sheets'] + inevow.IRequest(ctx).prepath[1:]+[name])
        if path.isfile(location): 
            return static.File(location, defaultType='xml')
        elif path.isdir(location):
            return SubPage(self.repository)
        else :
            return None
    
  
##
#    @ingroup browse
#  
class EvaluationPage ( QSOSPage ):
    """
    Renders Evaluation pages content
    
    This class renders the evaluation home page dynamically from repository's
    content.
    """
    
    def renderList (self, ctx, data):
        "Renders page's body"
        path = "/".join([self.repository, "sheets"]+
                        inevow.IRequest(ctx).prepath[1:])
        return [ T.li (class_='sheet') [
                       T.a (href = "/".join([dir, version]))
                       [ dir + "-" + version ]
                    ] for dir in listdir(path)
                      for version in listdir(path + '/' + dir) ]
        
    

    
    def locateChild ( self, ctx, segments ):
        "Locate and generate the evaluation page"
#        id= "-".join(segments)
        if not "-".join(segments) :
            return (self, ())
        tmp =  "/tmp/" + "-".join(segments) + "." + "qsos"
        file = open(tmp,'w')
        file.write(core.request(segments))
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
    
    def renderList (self, ctx, data):
        return [T.li (class_='folder')[
                    T.a(href= dir)[dir]
                    ] for dir in listdir(self.repository + "/sheets") ]
                    
    
    def childFactory (self, ctx, name):
        "Handles children with no explicit renderer"
        if name :
            return SubPage(self.repository)
        else :
            return None
    
    def child_evaluations (self, ctx):
        return EvaluationPage(self.repository)

