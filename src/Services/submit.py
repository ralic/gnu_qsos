"""
Submit module.

This module handles the repository submit page.
"""
##
#    @defgroup  submit Submit
#    @ingroup Services
#    @author Hery Randriamanamihaga
#    @todo 
#        Handle "dynamic" docFactory using Stan in UploadPage
#

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

##
#    @ingroup submit
#
class ConfirmationPage(rend.Page):
    """
    Renders confirmation page.
    
    Handles the confirmation page of a successful evaluation upload.
    Return to root page or submit another evaluation are suggested.
    """
    head = [ T.title ["QSOS evaluation Upload"] ]
    body = [ T.h1 [ "Your evaluation has been uploaded" ] ,
             T.a ( href = "../" ) [ "Go to Repository Main Page" ],
             " or ",
             T.a ( href = "../submit" ) [ "Upload another evaluation" ]
            ]
    docFactory = loaders.stan( T.html[ T.head [ head ], T.body [ body ] ] )
    

##
#    @ingroup submit
#
class UploadPage(rend.Page):
    """
    Handles upload page.
    
    This class renders the main page of submit branch of the site.
    
    """
    
    
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
    
    def submitEvaluation(self, **formData):
        "Put the uploaded evaluation into the local repository"
        core.submit(formData["File"].file)
        return url.here.click('submit/confirmation')

    def bind_submitEvaluation(self, ctx):
        "Bind the proper action to perform when submit action is invoked"
        return [
            ('File', annotate.FileUpload(required=True)),
        ]

    def render_fileUploader(self, ctx, data):
        "Renders the file uploader form"
        return ctx.tag.clear()[
            webform.renderForms()
        ]
    
    children = {'confirmation'  : ConfirmationPage()}
