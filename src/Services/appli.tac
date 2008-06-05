######################################################################
# Run using 'twistd -noy file.tac', then point your browser to
# http://localhost:8080
# A very simple Nevow site.
######################################################################

from twisted.application import service, internet

from nevow               import appserver
from nevow               import rend
from nevow               import loaders
from nevow               import tags as T
from nevow               import inevow

class AccountPage ( rend.Page ):

    def render_title ( self, ctx, data ):
        request = inevow.IRequest ( ctx )
        account_number = request.prepath [ 1 ]
        return "Account Summary for Account %s" % ( account_number, )

    def render_body ( self, ctx, data ):
        request = inevow.IRequest ( ctx )
        account_number = request.prepath [ 1 ]
        year           = request.prepath [ 2 ]
        month          = request.prepath [ 3 ]
        return "Account Balance at month end %s %s was %d" % \
          ( year, month,
            int ( account_number ) + ( int ( year ) * int ( month ) ) )

    docFactory = loaders.stan (
        T.html [ T.head [ T.title [ render_title ] ],
                 T.body [ render_body ]
        ]
    )

class ReportsPage ( rend.Page ):

    def locateChild ( self, ctx, segments ):
        return ( AccountPage(), () )


class MainPage ( rend.Page ):

    docFactory = loaders.stan (
        T.html [ T.head ( title = 'Main Page' ),
                 T.body [ T.h1 [ "This is the Main Page" ],
                          T.p [ "Account Summaries:" ],
                          T.p [ T.a ( href = '/reports/123456/2007/01' ) [ "Summary for account 123456" ] ],
                          T.p [ T.a ( href = '/reports/234567/2006/02' ) [ "Summary for account 234567" ] ],
                          T.p [ T.a ( href = '/reports/345678/2005/03' ) [ "Summary for account 345678" ] ]
                 ],
               ]
        )

    child_reports = ReportsPage()

######################################################################
# Nevow Boilerplate
######################################################################

application = service.Application ( "nevowdemo" )
port        = 8080
res         = MainPage()
site        = appserver.NevowSite ( res )
webService  = internet.TCPServer ( port, site )
webService.setServiceParent ( application )
