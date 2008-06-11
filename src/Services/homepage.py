from twisted.application import service, internet

from nevow               import appserver
from nevow               import rend
from nevow               import loaders
from nevow               import tags as T
from nevow               import inevow

import browse
import submit

class MainPage ( rend.Page ):

    docFactory = loaders.stan (
        T.html [ T.head ( title = 'Main Page' ),
                 T.body [ T.h1 [ "This is the QSOS Repository Main Page" ],
                          T.p ["For now, you can ",
                               T.a ( href = 'repository' ) [ "browse" ],
                               " the repository or ",
                               T.a ( href = 'submit' ) [ "submit" ],
                               " an evaluation. "
                                ]
                 ],
               ]
        )

    children = {
                'repository'    : browse.MainPage(),
                'submit'        : submit.UploadPage()
                }