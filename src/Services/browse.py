from twisted.application import service, internet

from nevow               import rend
from nevow               import loaders
from nevow               import tags as T
from nevow               import inevow

import os

class Page404 ( rend.Page ):

    def render_title ( self, ctx, data ):
        request = inevow.IRequest ( ctx )
        return "Currently browsing  %s" % ( "/".join(request.prepath[1:]), )

    def render_body ( self, ctx, data ):
        request = inevow.IRequest ( ctx )
        return "Not Found"

    docFactory = loaders.stan (
        T.html [ T.head [ T.title [ render_title ] ],
                 T.body [ render_body ]
        ]
    )  

class ReportsPage ( rend.Page ):

    def locateChild ( self, ctx, segments ):
        request = inevow.IRequest ( ctx )
        print "/".join(request.prepath[1:])
        return ( Page404(), () )
     

class SubPage ( rend.Page ):
    
    def render_title ( self, ctx, data ):
        request = inevow.IRequest ( ctx )
        return "Currently browsing  %s" % ( "/".join(request.prepath[1:]), )
    
    def body ( self, ctx, data ):
        request = inevow.IRequest ( ctx )
        path = "/".join(request.prepath[1:])
        body = [ T.h1 [path] ]
        body.extend([T.p [ T.a ( href = 'repository/'+path+'/'+dir ) [ dir ] ] for dir in os.listdir("../../sheets/"+path)])
        return body
    
    docFactory = loaders.stan (
        T.html [  T.head [ T.title [ render_title ] ], 
                  body
                 ]
        )
    
    child_evaluations = ReportsPage();
    
class EvaluationPage ( rend.Page ):
    
    def render_title ( self, ctx, data ):
        request = inevow.IRequest ( ctx )
        return "Currently browsing  %s" % ( "/".join(request.prepath[1:]), )
    
    def body ( self, ctx, data ):
        request = inevow.IRequest ( ctx )
        path = "/".join(request.prepath[1:])
        body = [ T.h1 [path] ]
        body.extend([ [ T.p [
                             T.a ( href = "/".join(["repository",dir,version]) ) [
                                                               dir + "-" + version
                                                               ]
                             ] for version in os.listdir("../../sheets/"+path+'/'+dir)]
                      for dir in os.listdir("../../sheets/"+path)])
        return body
    
    docFactory = loaders.stan (
        T.html [  T.head [ T.title [ render_title ] ], 
                  body
                 ]
        )
    
    child_evaluations = ReportsPage();

class MainPage ( rend.Page ):
    body = [ T.h1 ["/"] ]
    body.extend([T.p [ T.a ( href = 'repository/'+dir ) [ dir ] ]for dir in os.listdir("../../sheets")])
    docFactory = loaders.stan (
        T.html [ T.head ( title = 'Main Page' ),
                 T.body [ body ]
                 ]
        )
    
    def childFactory ( self, ctx, name ):
        return SubPage()

    children = { 'evaluations' : EvaluationPage() }