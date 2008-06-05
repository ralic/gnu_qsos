from twisted.application import service, internet

from nevow               import rend
from nevow               import loaders
from nevow               import tags as T
from nevow               import inevow

class AccountPage ( rend.Page ):

    def render_title ( self, ctx, data ):
        request = inevow.IRequest ( ctx )
        account_number = request.prepath [ 2 ]
        return "Account Summary for Account %s" % ( account_number, )

    def render_body ( self, ctx, data ):
        request = inevow.IRequest ( ctx )
        account_number = request.prepath [ 2 ]
        year           = request.prepath [ 3 ]
        month          = request.prepath [ 4 ]
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