from twisted.application import service, internet

from nevow               import rend
from nevow               import loaders
from nevow               import tags as T
from nevow               import inevow

import os

class RepositoryBrowser ( rend.Page ):

    def render_title ( self, ctx, data ):
        request = inevow.IRequest ( ctx )
        return "Currently browsing  %s" % ( "/".join(request.prepath[1:]), )

    def render_body ( self, ctx, data ):
        request = inevow.IRequest ( ctx )
        path = "/".join(request.prepath[1:])
        return path

    docFactory = loaders.stan (
        T.html [ T.head [ T.title [ render_title ] ],
                 T.body [ render_body ]
        ]
    )

    
class ReportsPage ( rend.Page ):

    def locateChild ( self, ctx, segments ):
        return ( RepositoryBrowser(), () )

class MainPage ( rend.Page ):
    body = [ T.h1 ["/"] ]
    body.extend([T.p [ T.a ( href = "repository/reports/"+dir ) [ dir ] ]for dir in os.listdir("../../sheets")])
    docFactory = loaders.stan (
        T.html [ T.head ( title = 'Main Page' ),
                 T.body [ body ]
                 ]
        )
         
    child_reports = ReportsPage()

