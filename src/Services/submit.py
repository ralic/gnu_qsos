from twisted.application    import service
from twisted.application    import strports

from nevow                  import appserver
from nevow                  import loaders
from nevow                  import rend
from nevow                  import static
from nevow                  import url
from nevow                  import tags as T

from formless               import annotate
from formless               import webform 

class ConfirmationPage(rend.Page):
    head = [ T.title ["QSOS evaluation Upload"] ]
    body = [ T.h1 [ "Your evaluation has been uploaded" ] ,
             T.a ( href = "../" ) [ "Go to Repository Main Page" ],
             " or ",
             T.a ( href = "../submit" ) [ "Upload another evaluation" ]
            ]
    docFactory = loaders.stan( T.html[ T.head [ head ], T.body [ body ] ] )
    

class NewsEditPage(rend.Page):
    docFactory = loaders.xmlstr("""
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
           "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns:n="http://nevow.com/ns/nevow/0.1">
    <head>
        <title>Example 1: A News Item Editor</title>
    </head>
    <body>
        <h1>Example 1: A News Item Editor</h1>
        <n:invisible n:render="newsInputForm" />
        
        <ol n:render="sequence" n:data="newsItems">
            <li n:pattern="item" n:render="mapping">
                <strong><n:slot name="title" /></strong>: <n:slot name="description" />
            </li>
        </ol>
    </body>
</html>
""")

#    child_form_css = webform.defaultCSS

    def __init__(self, *args, **kwargs):
        self.store = kwargs.pop('store')
        super(NewsEditPage, self).__init__(*args, **kwargs)
    
    def saveNewsItem(self, **newsItemData):
        self.store.append(newsItemData)
        return url.here.click('submit/confirmation')

    def bind_saveNewsItem(self, ctx):
        return [
            ('title', annotate.String(required=True)),
            ('description', annotate.Text(required=True)),
        ]

    def render_newsInputForm(self, ctx, data):
        return ctx.tag.clear()[
            webform.renderForms()
        ]
        
    def data_newsItems(self, ctx, name):
        return self.store
    
    children = {'confirmation'  : ConfirmationPage()}
