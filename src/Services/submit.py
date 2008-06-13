"""
"""
##
#    @defgroup  submit Submit
#    @ingroup Services
#    @author Hery Randriamanamihaga

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

from Engine                 import splitter

class ConfirmationPage(rend.Page):
    head = [ T.title ["QSOS evaluation Upload"] ]
    body = [ T.h1 [ "Your evaluation has been uploaded" ] ,
             T.a ( href = "../" ) [ "Go to Repository Main Page" ],
             " or ",
             T.a ( href = "../submit" ) [ "Upload another evaluation" ]
            ]
    docFactory = loaders.stan( T.html[ T.head [ head ], T.body [ body ] ] )
    

class UploadPage(rend.Page):
    docFactory = loaders.xmlstr("""
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
           "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns:n="http://nevow.com/ns/nevow/0.1">
    <head>
        <title>Example 1: A News Item Editor</title>
    </head>
    <body>
        <h1>Example 1: A News Item Editor</h1>
        <n:invisible n:render="fileUploader" />
    </body>
</html>
""")
    


    def __init__(self, *args, **kwargs):
        super(UploadPage, self).__init__(*args, **kwargs)
    
    def submitEvaluation(self, **newsItemData):
        qsos = newsItemData["File"].file.read()
        qsos = "".join([line.strip() for line in (qsos.splitlines())]) 
        splitter.parse(qsos,"..")
        return url.here.click('submit/confirmation')

    def bind_submitEvaluation(self, ctx):
        return [
            ('File', annotate.FileUpload(required=True)),
        ]

    def render_fileUploader(self, ctx, data):
        return ctx.tag.clear()[
            webform.renderForms()
        ]
    
    children = {'confirmation'  : ConfirmationPage()}
