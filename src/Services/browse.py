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

from nevow               import rend
from nevow               import loaders
from nevow               import tags as T
from nevow               import inevow
from nevow               import static

from Engine import builder
from Engine import core
import os

##
#    @ingroup browse
#
class Page404 ( rend.Page ):
    """
    Handles Error 404
    
    Renders Page not found Error
    """
    
    def render_title ( self, ctx, data ):
        "Renders page's title"
        request = inevow.IRequest ( ctx )
        return "Currently browsing  %s" % ( "/".join(request.prepath[1:]), )

    def render_body ( self, ctx, data ):
        "Renders page's body"
        return "Not Found"

    docFactory = loaders.stan (
        T.html [ T.head [ T.title [ render_title ] ],
                 T.body [ render_body ]
        ]
    ) 

##
#    @ingroup browse
#
class RenderEvaluation ( rend.Page ):
    """
    Handles Evaluations' subpages
    
    This class renders QSOS evaluation sheets from files located on local 
    copy of repository
    """
    
    def getName ( self, ctx ):
        """
        Extract evaluation's name and version from request.
        
        These informations are extracted from the request's context and are 
        returned in a list [name, version]
        
        @param self
                Pointer to the object
        @param ctx
                Invocation Context from which informations are extracted
        @return
            The list [name, version]
        """ 
        return inevow.IRequest ( ctx ).path.split("/")[-2:]
    
    ##
    #    @todo Extract evaluation name from request
    #
    def render_title ( self, ctx, data ):
        "Renders page's title"
        [name,version]= self.getName ( ctx )
        return "Evaluation of "+name+"-"+version
    
    ##
    #    @todo
    #        A true page's structure!
    def render_body ( self, ctx, data ):
        "Renders page's body"
        [name,version]= self.getName ( ctx )
        document = builder.build(name, version,"..")
        return builder.assembleSheet(document,"..")
    
    docFactory = loaders.stan (
        T.html [ T.head [ T.title [ render_title ] ],
                 T.body [ render_body ]
        ]
    )  

##
#    @ingroup browse
#
class ReportsPage ( rend.Page ):
    """Handles unbinded pages
    
    This class provides a handler for any page's requested not handled"""

    def locateChild ( self, ctx, segments ):
        "Generate a 404 Page"
        return ( Page404(), () )
 
##
#    @ingroup browse
#    
class ReportEvaluation ( rend.Page ):
    """
    Report Evaluation page
    
    This class locates evaluation page of requested qsos sheet
    """
    def locateChild ( self, ctx, segments ):
#        "Locate and generate the evaluation page"
        id= "-".join(inevow.IRequest ( ctx ).path.split("/")[-2:])
        tmp =  "/tmp/" + id + "." + "xml"
        file = open(tmp,'w')
        file.write(core.request(id))
        file.close()
        return (static.File(tmp), ())
#    def locateChild ( self, ctx, segments ):
#        "Locate and generate the evaluation page"
#        return ( RenderEvaluation(), () )
 
##
#    @ingroup browse
#   
class SubPage ( rend.Page ):
    """
    Handles repository subpages
    
    This class renders a page for any location on the repository that is not
    explicitly binded to a handler
    """
    
    def render_title ( self, ctx, data ):
        "Renders page's title"
        request = inevow.IRequest ( ctx )
        return "Currently browsing  %s" % ( "/".join(request.prepath[1:]), )
    
    def body ( self, ctx, data ):
        "Renders page's content"
        request = inevow.IRequest ( ctx )
        path = "/".join(request.prepath[1:])
        body = [ T.h1 [path] ]
        body.extend([T.p [ T.a ( href = 'repository/'+path+'/'+dir ) [ dir.split(".")[:-1] ] ] for dir in os.listdir("../sheets/"+path)])
        return body
    
    docFactory = loaders.stan (
        T.html [  T.head [ T.title [ render_title ] ], 
                  body
                 ]
        )
    
  
##
#    @ingroup browse
#  
class EvaluationPage ( rend.Page ):
    """
    Renders Evaluation pages content
    
    This class renders the evaluation home page dynamically from repository's
    content.
    """
    
    def render_title ( self, ctx, data ):
        "Renders page's title"
        request = inevow.IRequest ( ctx )
        return "Currently browsing  %s" % ( "/".join(request.prepath[1:]), )
    
    def body ( self, ctx, data ):
        "Renders page's body"
        request = inevow.IRequest ( ctx )
        path = "/".join(request.prepath[1:])
        body = [ T.h1 [path] ]
        body.extend([ [ T.p [
                             T.a ( href = "/".join(["evaluations",dir,version]) )
                             [ dir + "-" + version ]
                             ] for version in os.listdir("../sheets/"+path+'/'+dir)]
                      for dir in os.listdir("../sheets/"+path)])
        return body
    
    docFactory = loaders.stan (
        T.html [  T.head [ T.title [ render_title ] ], 
                  body
                 ]
        )
    
    def childFactory ( self, ctx, name ):
        "Locate and redirect to the evalution sheet requested"
        return ReportEvaluation()

##
#    @ingroup browse
#
class MainPage ( rend.Page ):
    """
    Renders repository home page
    
    This class renders the main page when browse request is handled
    """
    
    body = [ T.h1 ["/"] ]
    body.extend([T.p [ T.a ( href = 'repository/'+dir ) [ dir ] ]for dir in os.listdir("../sheets")])
    docFactory = loaders.stan (
        T.html [ T.head ( title = 'Main Page' ),
                 T.body [ body ]
                 ]
        )
    
    
    def childFactory ( self, ctx, name ):
        "Handles children with no explicit renderer"
        return SubPage()

    children = { 'evaluations' : EvaluationPage() }